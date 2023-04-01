'use strict';

var ForerunnerDB = require("forerunnerdb");
var mobile_utils = require('./mobile_utils');
var fs = require("react-native-fs");
var file_upload = require("NativeModules").FileUpload;
var network = require("./network");
var server_auth = require("./server_auth");

var fdb = new ForerunnerDB();
var db = fdb.db('xenforma');

var SERVER_URL = "http://10.1.10.117:1337/";

var SYNC_RUNNING = false;

var synchronize = function() {
    return new Promise(function(resolve, reject) {
        if (SYNC_RUNNING) { // Locking mechanism. Multiple synchronization sessions shouldn't be taking place at once
            return reject("SYNC_IN_PROGRESS");
        }

        SYNC_RUNNING = true;

        network.is_device_online(function(online) { // Make sure that we have internet conneectivity before we begin synchronization
            if (online) {
                get_entity_info(function(err, entity_info, entity_info_collection) { // Retrieves all information client has about entities
                    if (err) { // If error has occurred, let the user know
                        SYNC_RUNNING = false;
                        return reject(err);
                    }
                  
					
                    begin_synchronization(entity_info, entity_info_collection, {}, function(err) {
					
                        SYNC_RUNNING = false;
                        if (err) {
                            return reject(err);
                        }
						
						get_menu_items(entity_info,function(err){
						 if(err){
							  return reject(err);
						 }
						
						 return resolve();
					    });
					
						//console.log("begin sysnce");
                       // return resolve();
                    });
					
					
                });
            }
            else {
                SYNC_RUNNING = false;
                return reject("DEVICE_IS_OFFLINE"); // We know the device doesn't have internet connectivity, let the user know
            }
        });
    });
};

var get_entity_info = function(callback) {
    var xenforma_entity_info = db.collection('xenforma_entity_info'); // All entity metadata is stored in this collection
    xenforma_entity_info.load(function(err) {
        if (err) {
            return callback(err);
        }

        var entity_info = xenforma_entity_info.find({}); // Get all entity metadata
        var entity_info_return_object = {};
     
		
        mobile_utils.for_each_async(entity_info, function(err, args, _state, _callback) { // Iterate through all entity metadata
            var entity_info_instance = args.data_object;
			
			
            // Retrieve version_number and max_version_number and last_id if they are defined
            entity_info_return_object[entity_info_instance.entity_name] = {};
            entity_info_return_object[entity_info_instance.entity_name].version_number = entity_info_instance.version_number;
	        entity_info_return_object[entity_info_instance.entity_name].max_version_number = entity_info_instance.max_version_number == null ? undefined : entity_info_instance.max_version_number;
            entity_info_return_object[entity_info_instance.entity_name].last_id = entity_info_instance.last_id == null ? undefined : entity_info_instance.last_id;
			
            // Now let's get the changes that the client has made to the entity
            get_entity_changes(entity_info_instance, entity_info_return_object[entity_info_instance.entity_name], function(err) {
                return _callback(err);
            });
        }, {parallel:false}, null, function(_err) {
            if (_err) {
                return callback(_err);
            }

            return callback(false, entity_info_return_object, xenforma_entity_info);
        });
    });
};

var get_entity_changes = function(entity_info, entity_info_ret, callback) {
    // If client made no changes, there are no changes to be retrieved
     
	if ((!entity_info.changes || entity_info.changes.length == 0) &&
        (!entity_info.workflow_changes || entity_info.workflow_changes.length == 0) &&
        (!entity_info.removals || entity_info.removals.length == 0)) {
        return callback();
    }
    var entity_collection = db.collection(entity_info.entity_name); // Collection of the actual entity from where the data will be retrieved from
    entity_collection.load(function(err) {
        if (err) {
            return callback(err);
        }
      
	
        var get_entity_instance_changes = function(field_changes, push_to_array) {
            for (var i = 0; i < field_changes.length; i++) {
                var change = field_changes[i];
                var _id = change._id;
                var single_entity_result = {_id:_id}; // object of the actual entity instance to be sent to server. This is what we will be working with
                var entity_instance = entity_collection.find({_id:_id})[0];
                for (var j = 0; j < change.fields.length; j++) { // Iterate through all fields that have been changed by the client
                    var field = change.fields[j].split(":");

                    var get_value_for_field = function(field, data) { // Code taken from Server Sync. This is a recursive method for retrieving individual field values (both nested and non-nested)
                        var current_field = field[0];
                        field.splice(0, 1);

                        var data_value = {};

                        if (data === undefined) {
                            data_value[current_field] = data;
                        }
                        else if (field.length > 0) {
                            if (current_field.substring(0, 1) != "[") {
                                data_value[current_field] = get_value_for_field(field, data[current_field]);
                            }
                            else {
                                var data_id = current_field.substring(1, current_field.length - 1);
                                for (var k = 0; k < data.length; k++) {
                                    if (data[k]._id == data_id) {
                                        data_value = get_value_for_field(field, data[k]);
                                        data_value._id = data_id;
                                        data_value = [data_value];
                                    }
                                }
                            }
                        }
                        else {
                            if (current_field.substring(0, 1) != "[") {
                                data_value[current_field] = data[current_field];
                            }
                            else {
                                var data_id = current_field.substring(1, current_field.length - 1);
                                for (var k = 0; k < data.length; k++) {
                                    if (data[k]._id == data_id) {
                                        data_value = [data[k]];
                                    }
                                }
                            }
                        }

                        if (data_value[current_field] === undefined) {
                            data_value[current_field] = null;
                        }

                        return data_value;
                    };

                    // Extend the entity instance object that we will send to the server with the field value that we have just retrieved
                    mobile_utils.extend_object(single_entity_result, get_value_for_field(field, entity_instance), {ignore_null_and_undefined: false, extend_arrays: true, array_match_field: "_id"});
                }

                // Push the entity instance to array of changes we will send
                push_to_array.push(single_entity_result);
            }
        };

        // Get entity changes if there were any
        if (entity_info.changes && entity_info.changes.length > 0) {
            if (entity_info_ret.changes === undefined) {
                entity_info_ret.changes = [];
            }
            get_entity_instance_changes(entity_info.changes, entity_info_ret.changes);
        }

        // Get entity workflow changes if there were any
        if (entity_info.workflow_changes && entity_info.workflow_changes.length > 0) {
            if (entity_info_ret.workflow_changes === undefined) {
                entity_info_ret.workflow_changes = [];
            }
            get_entity_instance_changes(entity_info.workflow_changes, entity_info_ret.workflow_changes);
        }

        // Get entity removals if there were any
        if (entity_info.removals && entity_info.removals.length > 0) {
            entity_info_ret.removals = entity_info.removals;
        }

        return callback();
    });
};

// Method used for contacting Xenforma Web Server for synchronization
var send_sync_request = function(entity_data, callback) {
    var sync_data = {data:entity_data};
      server_auth.do_authenticated_http_call('api/sync/get_latest_data', {
        method: "POST",
        headers: {
            "Accept":"application/json",
            "Content-Type":"application/json"
        },
        body: JSON.stringify(sync_data)
    }).then((response) => {
		
        response.json().then(function(json_response) {
           
            callback(false, json_response);
        }).catch(function(err) {
            callback(err);
        });
    }).catch((err) => {
       
        callback(err);
    });
};

var begin_synchronization = function(entity_versions, entity_info_collection, shared_object, callback) {
    send_sync_request(entity_versions, function(err, response_data) { // Begin sync by sending the initial sync request with data we have about all entities
        if (err) {
            return callback(err);
        }

        var entities_to_process = [];
        for (var entity_name in response_data) {
            if (entity_name == "continue") {
                continue; // Design flaw by me. I put 'continue' indication value on the same level with entity data. Should be changed
            }
            entities_to_process.push(entity_name); // Convert json object returned by server to array
        }

        var do_continue = response_data.continue; // Lets us know whether we need to send any more requests for synchronization (in case all data didn't fit in a single response)
        
        mobile_utils.for_each_async(entities_to_process, function(err, args, _state, _callback) { // Start iterating through all entities sent over by server
            var entity_name = args.data_object;
           
            process_entity_changes(entity_versions[entity_name], response_data[entity_name], entity_name, entity_info_collection, function(_err, should_continue) {
                if (!do_continue) { // If individual entities aren't done synchronizing, we need to know to send another sync request
                    do_continue = should_continue;
                }
                return _callback(_err);
            });
        }, {parallel:false}, null, function(_err) {
            if (_err) {
				
                return callback(_err); // Cancel entire operation if there were any critical errors
            }
	
	    
            // We are now done processing all entity changes. Time to update the metadata about our entities
            update_entity_info(response_data, entity_info_collection, function(err, entity_info) {
                if (err) {
                    return callback(err);
                }
				
                if (do_continue) { // Because there are more changes to be received, let's make another sync request until we are done
                   
					return begin_synchronization(entity_info, entity_info_collection, shared_object, callback);
                }
                else {
					 
                    return process_file_data(entity_info_collection, function(err) { // Files are downloaded/uploaded last after all data is synchronized
                      
					   return callback(err); // This is the exit point of synchronization
                    });
                }
            });
        });
    });
};

var process_entity_changes = function(initial_entity_info, entity_info, entity_name, entity_info_collection, callback) {
    var entity_collection = db.collection(entity_name); // Loads the actual entity data collection
    entity_collection.load(function(err) {
        if (err) {			
            return callback(err);
        }

        var changes_made = false; // Keep track of this to make sure we don't save entity collection to disk if we don't need to

	    //Downloaded - All data downloaded from server
        var changes = entity_info.data;
        var removals = entity_info.removals;

	    //Uploaded - Data that we already uploaded to server. This is metadata about our changes
        var successful_changes = entity_info.successful_changes;
        var successful_workflow_changes = entity_info.successful_workflow_changes;
        var successful_removals = entity_info.successful_removals;
        var upload_urls = entity_info.upload_urls;
        var sync_errors = entity_info.errors;

        if (removals && removals.length > 0) { // Get rid of any entity instances the server told us to remove
            entity_collection.remove({"_id":{"$in":removals}});
            if (entity_name == "file") {
                add_file_to_delete_queue(removals, entity_info_collection);
            }
            changes_made = true;
        }

        if (changes && changes.length > 0) { // Update entities that the server has a new version for
            for (var i = 0; i < changes.length; i++) {
                var change = changes[i];

                if (entity_name == "file") {
                    if (change.download_url) {
                        change.downloaded = false; // If entity is a file and has a new download_url, we need to mark the file entity instance for download
                    }
                }

                var document = entity_collection.find({"_id":change._id}); // Get the current entity instance if it already exists
                if (document.length > 0) {
                    document = document[0]; // Current object in the database

                    // Extends and replaces fields of the current entity instance with the new changes
                    mobile_utils.extend_object(document, change, {replace_shallow_fields: true, ignore_null_and_undefined:false, extend_arrays: true, array_match_field: "_id"});
                    entity_collection.remove({"_id":document._id}); // Remove and insert the new entity instance to prevent update problems
                    entity_collection.insert(document);
                }
                else {
                    entity_collection.insert(change);
                }
            }
            changes_made = true
        }

        // These changes were successful, they need to be removed from send queue
        if (successful_changes && successful_changes.length) {
            entity_info_collection.update({"entity_name":entity_name}, {"$pull":{"changes._id":{"$in":successful_changes}}});
        }

        if (successful_workflow_changes && successful_workflow_changes.length) {
            entity_info_collection.update({"entity_name":entity_name}, {"$pull":{"workflow_changes._id":{"$in":successful_workflow_changes}}});
        }

        if (successful_removals && successful_removals.length) {
            entity_info_collection.update({"entity_name":entity_name}, {"$pull":{"removals":{"$in":successful_removals}}});
        }

        // We have sync errors, the client needs to retain these
        if (sync_errors && sync_errors.length > 0) {
            entity_info_collection.update({"entity_name":entity_name}, {"$push":{"errors":{"$each":sync_errors}}});
        }

        // if entity is a file and there are upload urls for it, we need to mark our file entity instances to be uploaded
        if (entity_name == "file" && upload_urls && upload_urls.length > 0) {
            for (var i = 0; i < upload_urls.length; i++) {
                var upload_info = upload_urls[i];
                entity_collection.update({"_id":upload_info.file_id}, {"$set":{"upload_url":upload_info.url}});
            }
            changes_made = true;
        }

        // Do we need to send another sync request after this?
        // If we do, we need the correct max_version_number and version_number to be set
        if (entity_info.continue) {
	        entity_info.max_version_number = entity_info.version_number;
            if (initial_entity_info === undefined) {
		        entity_info.version_number = 0;
            }
            else {
                entity_info.version_number = initial_entity_info.version_number;
            }
        }

        if (entity_info.data) {
            delete entity_info.data;
        }

        if (entity_info.removals) {
            delete entity_info.removals;
        }

        if (entity_info.successful_changes) {
            delete entity_info.successful_changes;
        }

        if (entity_info.successful_workflow_changes) {
            delete entity_info.successful_workflow_changes;
        }

        if (entity_info.successful_removals) {
            delete entity_info.successful_removals;
        }

        if (entity_info.upload_urls) {
            delete entity_info.upload_urls;
        }

        if (entity_info.errors) {
            delete entity_info.errors;
        }

        if (changes_made) { // We have made changes to the entity, let's save them
            entity_collection.save(function(err) {
                return callback(err, entity_info.continue);
            });
        }
        else {
            return callback();
        }
    });
};

var update_entity_info = function(entity_info, entity_info_collection, callback) {
    for (var entity_name in entity_info) {
        if (entity_name == "continue") {
            delete entity_info["continue"];
            continue;
        }
        var single_entity_details = entity_info_collection.find({entity_name:entity_name}); // Find metadata record for our current entity
        if (single_entity_details.length == 0) {
            single_entity_details = {entity_name:entity_name};
            entity_info_collection.insert(single_entity_details); // insert it if the metadata doesn't exist for the entity yet
        }

        // Update version_number, max_version_number and last_id for our entity metadata
        var version_number = entity_info[entity_name].version_number;
        var max_version_number = entity_info[entity_name].max_version_number === undefined ? null : entity_info[entity_name].max_version_number;
        var last_id = entity_info[entity_name].last_id === undefined ? null : entity_info[entity_name].last_id;

        entity_info_collection.update({entity_name:entity_name}, {"$overwrite":{version_number:version_number, max_version_number:max_version_number, last_id:last_id}});
    }

    // Commit all metadata changes to database
    entity_info_collection.save(function(err) {
        var updated_entity_info = {}; // We need the metadata for the entities because our file processing will need it
        var entity_info_data = entity_info_collection.find({});
        for (var i = 0; i < entity_info_data.length; i++) {
            var entity_info_instance = entity_info_data[i];
            updated_entity_info[entity_info_instance.entity_name] = {version_number:entity_info_instance.version_number, max_version_number:entity_info_instance.max_version_number, last_id:entity_info_instance.last_id};
        }
        return callback(err, updated_entity_info);
    });
};

var process_file_data = function(entity_info_collection, callback) {
	
    var file_entity_collection = db.collection("file"); // file entity collection
    
	file_entity_collection.load(function(err) {
        if (err) {			
            return callback(err);
        }
        
        // Locking mechanism. All of these processes have to be done before we are done processing file data.
        var file_downloads_complete = false;
        var file_uploads_complete = false;
        var file_removals_complete = false;

        var changes_made = false; // Tracks if we changed any entity data. Prevents us from making an unnecessary disk write.

        var errors = [];

        var files_directory_path = fs.DocumentDirectoryPath + "/xenforma_files"; // This is the directory where we store all of our files

        var complete_process = function() {
            if (file_downloads_complete && file_uploads_complete && file_removals_complete) {
                var save_changes = function() {
                    if (changes_made) {
                        return file_entity_collection.save(function(err) {
                            if (err) {
                                errors.push(err);
                            }

                            if (errors.length == 0) {
                                errors = undefined;
                            }

                            return callback(errors);
                        });
                    }
                    else {
                        if (errors.length == 0) {
                            errors = undefined;
                        }
                        return callback(errors);
                    }
                };

                // We need to notify the server of any uploaded files
                var files_to_mark_uploaded = file_entity_collection.find({"mark_as_uploaded":true});
                if (files_to_mark_uploaded.length > 0) {
                    mobile_utils.for_each_async(files_to_mark_uploaded, function(err, args, _state, _callback) {
                        var file_entity = args.data_object;

                        var request_data = {"entity":"file", "method":"mark_as_uploaded","data":{"file_id":file_entity._id}};

                        server_auth.do_authenticated_http_call("api/entity/invoke_method", {
                            method: "POST",
                            headers: {
                                "Accept":"application/json",
                                "Content-Type":"application/json"
                            },
                            body: JSON.stringify(request_data)
                        }).then((response) => {
							
                            response.json().then(function(json_response) {
                                if (json_response.success) {
                                    file_entity_collection.update({_id:file_entity._id}, {"$unset":{"mark_as_uploaded":""}});
                                    changes_made = true;
                                    _callback();
                                }
                                else {
									
                                    _callback(json_response.error);
                                }
                            }).catch(function(err) {
								
                                _callback(err);
                            });
                        }).catch((err) => {
                          
                            _callback(err);
                        });
                    }, {parallel:false}, null, function(_err) {
                        if (_err) {
							
                            errors.push(_err);
                        }

                        return save_changes();
                    });
                }
                else {
                    return save_changes();
                }
            }
        };

        // Download any files if they aren't downloaded
        var files_to_download = file_entity_collection.find({"downloaded":false});
        if (files_to_download.length > 0) {
            fs.readDir(fs.DocumentDirectoryPath).then((dirs) => {
                var found = false;
                for (var i = 0; i < dirs.length; i++) {
                    if (dirs[i].name == "xenforma_files") {
                        found = true;
                        break;
                    }
                }

                var download_files = function() {
                    mobile_utils.for_each_async(files_to_download, function(err, args, _state, _callback) {
                        var file_entity = args.data_object;
					
						
						/**
                        fs.downloadFile(file_entity.download_url, files_directory_path + "/" + file_entity._id,
                        function(job_id, status_code, content_length, headers) { // Begin callback
                        },
                        function(content_length, bytes_written) { // Progress callback
                        }).then(function(){
                            file_entity_collection.update({_id:file_entity._id}, {"$overwrite":{"downloaded":true}, "$unset":{"download_url":""}});
                            changes_made = true;
                            return _callback();
                        }).catch(function(err){
							
                            return _callback(err);
                        });
                    }, {parallel:false}, null, function(_err) {
                        if (_err) {
                            errors.push(_err);
                        }
 **/
                        file_downloads_complete = true;
                        return complete_process();
                    });
                };
           
                if (!found) { // If our file directory doesn't exist create it and begin file downloads
                     console.log("not found");
				   fs.mkdir(files_directory_path).then(function() {
                        download_files();
                    }).catch(function(err) {
                        errors.push(err);
                        file_downloads_complete = true;
                        return complete_process();
                    });
                }
                else {
					
                    download_files();
                }
            }).catch(function(err) {
                errors.push(err);
                file_downloads_complete = true;
                return complete_process();
            });
        }
        else {
            file_downloads_complete = true;
        }

        var files_to_upload = file_entity_collection.find({"upload_url":{"$exists":true}});
        if (files_to_upload.length > 0) {
            fs.readDir(fs.DocumentDirectoryPath).then((dirs) => {
                var found = false;
                for (var i = 0; i < dirs.length; i++) {
                    if (dirs[i].name == "xenforma_files") {
                        found = true;
                        break;
                    }
                }

                if (!found) { // Our file directory is missing which means we cannot have any files to upload
                    errors.push("xenforma_files folder missing");
                    file_uploads_complete = true;
                    return complete_process();
                }
                else {
                    mobile_utils.for_each_async(files_to_upload, function(err, args, _state, _callback) {
                        var file_entity = args.data_object;

                        var upload_options = {
                            uploadUrl: file_entity.upload_url,
                            method: "POST",
                            headers: {
                                "Accept":"application/json"
                            },
                            files: [
                                {
                                    filename: file_entity.file_name,
                                    filepath: files_directory_path + "/" + file_entity._id
                                }
                            ]
                        };

                        file_upload.upload(upload_options, function(err, result) {
                            if (err) {
                                return _callback(err);
                            }

                            if (!result.success) {
                                return _callback(result.error);
                            }

                            file_entity_collection.update({_id:file_entity._id}, {"$unset":{"upload_url":""}, "$set":{"mark_as_uploaded":true}});

                            return _callback();
                        });
                    }, {parallel:false}, null, function(_err) {
                        if (_err) {
                            errors.push(_err);
                        }

                        file_uploads_complete = true;
                        return complete_process();
                    });
                }
            }).catch(function(err) {
                errors.push(err);
                file_uploads_complete = true;
                return complete_process();
            });
        }
        else {
            file_uploads_complete = true;
        }

        var files_to_delete = entity_info_collection.find({"type":"file_delete_queue"});
        if (files_to_delete.length > 0) {
            files_to_delete = files_to_delete[0].delete_queue;
            if (files_to_delete.length > 0) {
                mobile_utils.for_each_async(files_to_delete, function(err, args, _state, _callback) {
                    var file_id = args.data_object;

                    // Let's attempt to remove the file if it's there. We will remove the file from delete queue regardless of the error.
                    fs.unlink(files_directory_path + "/" + file_id)
                        .finally(function() {
                            entity_info_collection.update({"type":"file_delete_queue"}, {"$pull":{"delete_queue":file_id}});
                        });
                }, {parallel:false}, null, function(_err) {
                    if (_err) {
                        errors.push(_err);
                    }

                    entity_info_collection.save(function(err) {
                        if (err) {
                            errors.push(err);
                        }

                        file_removals_complete = true;
                        return complete_process();
                    });
                });
            }
            else {
                file_removals_complete = true;
            }
        }
        else {
            file_removals_complete = true;
        }

        return complete_process();
    });
};
var get_menu_items= function (entity_info,callback) {
	var xenforma_menu_info = db.collection('xenforma_menu_info');
	var xenforma_submenu_first = db.collection('xenforma_submenu_first');
	var xenforma_submenu_second = db.collection('xenforma_submenu_second');
	
	var request = {};
        request.entity = "app_object";
        request.method = "get_menu_items";      
       server_auth.do_authenticated_http_call('api/entity/invoke_method', {
        method: "POST",				
        headers: {          
            "Content-Type":"application/json"
        },
        body: JSON.stringify(request)
    }).then((response) => {		
        response.json().then(function(json_response) {                
				
		var id=0; 
		var menu_item=[];
		xenforma_menu_info.insert({idd:0,menuitem:'Home'});
		for (var module_key in json_response.data) {
            var module = json_response.data[module_key];
            var module_name = module.name;
			id++;
			menu_item.idd=id;
			menu_item.name=module_name;			
			var main_menu={idd:id,menuitem:module_name};
			xenforma_menu_info.insert(main_menu);
			    var app_object=[];
				for (var k = 0; k < module.app_objects.length; k++) { //first sub menue					
					  var app_object = module.app_objects[k];
					    
					   if (app_object.workflow_states && app_object.workflow_states.length > 0) {
						    var workflows = [];
							for (var j = 0; j < app_object.workflow_states.length; j++) { // second sub menu
								var workflow_state = app_object.workflow_states[j];
								var sub_menu_second={idd:app_object.name,pidd:module_name,submenu:workflow_state.caption};
								xenforma_submenu_second.insert(sub_menu_second);
							}
					   }
				    var sub_menu_first={idd:module_name,submenu:app_object.name};			  	
					xenforma_submenu_first.insert(sub_menu_first);						
				}			
		}		
		    xenforma_menu_info.save(function(err) {
                 if (err) {
                     return reject(err);
                  }  							
            });
			xenforma_submenu_first.save(function(err) {
                  if (err) {
                      return reject(err);
                   }  							
            });
			xenforma_submenu_second.save(function(err) {
                  if (err) {
                       return reject(err);
                  }  							
            });
			
						
		var result =xenforma_menu_info.find({});
		// m_result=JSON.stringify(result);
		return callback(false);           
        }).catch(function(err) {
               context.setState({error: err.json_response.message});
        }); 
		
    }).catch((err) => {
         // callback(err);
    });
      
    };
var add_file_to_delete_queue = function(file_ids, entity_info_collection) {
    var file_delete_queue = entity_info_collection.find({"type":"file_delete_queue"});
    if (file_delete_queue.length == 0) {
        file_delete_queue.insert({"type":"file_delete_queue", "delete_queue":[]});
    }

    file_delete_queue.update({"type":"file_delete_queue"}, {"$push":{"delete_queue":{"$each":file_ids}}});
};

var is_sync_running = function() {
    return SYNC_RUNNING;
};

exports.synchronize = synchronize;
exports.is_sync_running = is_sync_running;
