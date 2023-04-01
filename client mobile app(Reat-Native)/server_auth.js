"use strict";
/**
 ==================================================================================
 Description:       desc
 Creation Date:     1/14/16
 Author:            glib
 ==================================================================================
 Revision History
 ==================================================================================
 Rev    Date        Author           Task                Description
 ==================================================================================
 1      1/14/16     Osipe          TaskNumber          Created
 ==================================================================================
 */


var ForerunnerDB = require("forerunnerdb");
var mobile_utils = require('./mobile_utils');

var fdb = new ForerunnerDB();
var db = fdb.db('xenforma');


var auth_info; // Cached auth_info json object used for authentication

var authenticate_username_password = function(server_url, username, password) {
    return new Promise(function(resolve, reject) {
        var login_route = server_url + 'api/auth/login';

        var credentials = username + ":" + password;
       //
	     // var btoa = require('btoa')
		//, bin = credentials.toString()
		//	, b64 = btoa(bin);
       //    credentials =b64 ;
		console.log("this is a log");
	    credentials = btoa(credentials) ;
		
        console.log(login_route);
        console.log(credentials);

        fetch(login_route, {
            method: "POST",
            headers: {
                "Authorization":"Basic " + credentials,
                "Accept":"application/json",
                "Content-Type":"application/json"
            },
            body:"{}"
        }).then((response) => {
            response.json().then(function(json_response) {
                if (json_response.login_token) {
                    var xenforma_auth_info = db.collection('xenforma_auth_info');
                    xenforma_auth_info.load(function(err) {
                        if (err) {
                            return reject(err);
                        }

                        var auth_record = xenforma_auth_info.find({});
                        if (auth_record.length > 0) {
                            xenforma_auth_info.remove({});
                        }

                        xenforma_auth_info.insert({server_url:server_url, login_token:json_response.login_token, username:username}); // We might need username for future use
                        xenforma_auth_info.save(function(err) {
                            if (err) {
                                return reject(err);
                            }

                            auth_info = {server_url:server_url, login_token:json_response.login_token, username:username};
                            return resolve();
                        });

                    });
                    resolve();
                }
                else {
                    return reject(json_response.message); // Server didn't return a login_token which means that credentials were incorrect
                }
            }).catch(function(err) {
                if (response.status == 401) {
                    return reject("INVALID_CREDENTIALS"); // Response couldn't be parsed into JSON and status is 401 - means we are unauthorized
                }
                return reject(err);
            });
        }).catch(function(err) {
            console.log(err);
            console.log(JSON.stringify(err));
        });
    });
};

var is_user_authenticated = function() {
    return new Promise(function(resolve, reject) {
        var check_login_status = function() {
            do_authenticated_http_call("api/auth/is_logged_in", {
                method: "GET",
                headers: {
                    "Accept":"application/json"
                }
            }).then((response) => {
				
                response.json().then((json_response) => {
					
                    if (json_response.logged_in) {						
                        return resolve();
                    }
                    else {
                        return reject("NOT_LOGGED_IN");
                    }
                }).catch((err) => {					
                    return reject(err);
                });
            });
        };

        if (!auth_info) {
			
            var xenforma_auth_info = db.collection('xenforma_auth_info');
            xenforma_auth_info.load(function(err) {
              
			  if (err) {
                    return reject(err);
                }

                var db_auth_info = xenforma_auth_info.find({});
                console.log(db_auth_info);
				console.log("db infor view");
				if (db_auth_info.length > 0) {
                    auth_info = db_auth_info[0];
                    check_login_status();
                }
                else {
                    return reject("NOT_LOGGED_IN");
                }
            });
        }
        else {
            check_login_status();
        }
    });
};

var do_authenticated_http_call = function(url_path, options) {
     
   if (!auth_info) {	   
        return new Promise(function(resolve, reject) { return reject("MISSING_CREDENTIALS"); });
    }

    if (options === undefined) {
        options = {};
    }

    if (options.headers === undefined) {
        options.headers = {};
    }
	
	console.log("auth info server_url "+auth_info.server_url);
    options.headers["Authorization"] = "JWT " + auth_info.login_token;

    return fetch(auth_info.server_url + url_path, options);
};
var did_forget_password=function(server_url, request){
	 var request = {};
	
      
};
exports.do_authenticated_http_call = do_authenticated_http_call;
exports.is_user_authenticated = is_user_authenticated;
exports.authenticate_username_password = authenticate_username_password;