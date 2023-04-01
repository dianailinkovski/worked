var async = require('async');

/**Runs a function (fn) for every member of an array
 * fn needs the following input parameters: (err, args, state, callback)
 * args has the following members: data_object, setter_function
 * May pass in a state object through options.
 * also in options:
 * parallel: t/f (default false)
 */
function for_each_async(array, fn, options, state, callback) {
    if (!array) {
        return callback();
    }
   
   
    var args = {
        array: array,
        func: fn,
        options: options
    };

    args.options = set_options(args.options, {
        parallel: false,
        skip_nulls: false
    }, true);

      console.log("foe each");
    if (!args.options.parallel) {
		 
        return for_each_async_series(args, state, callback);
    }
    else {
        return for_each_async_parallel(args, state, callback);
    }
}

/**Runs a function (fn) for every member of an array, using async.series.
 * fn needs the following input parameters: (err, args, state, callback)
 * args has the following members: data_object, setter_function
 * May pass in a state object through options.
 */
function for_each_async_series(args, state, callback) {
    var array = args.array;
    var fn = args.func;
    var options = set_options(args.options, {
        skip_nulls: false
    }, true);

    var function_array = [];
    var error_state = {};

    if (!Array.isArray(array)) {
        var dictionary = array; //I assume it's iterable.
        array = null;
        var func_maker = function (_field_name) {
            return function (cb) {
                var args = {
                    data_object: dictionary[_field_name],
                    setter_function: function (new_value) {
                        dictionary[_field_name] = new_value;
                    }
                };
                var err = error_state.err;
                if (err) {
                    return callback(err); //quit on error
                }
                if(dictionary[_field_name] == null && options.skip_nulls) {
                    return cb();
                }
                else {
                    return fn(err, args, state, function (_err) {
                        error_state.err = _err;
                        cb();
                    });
                }

            };
        };
        for (var field_name in dictionary) {
            if ((dictionary[field_name] && (typeof dictionary[field_name] != "function")) || (!dictionary[field_name])) { //allows null/undefined members, but not functions.
                function_array.push(func_maker(field_name));
            }
        }
    }
    else {
        var func_maker = function (index) {
            return function (cb) {
                var args = {
                    data_object: array[index],
                    setter_function: function (new_value) {
                        array[index] = new_value;
                    },
                    getter_function: function () {
                        return array[index];
                    }
                };
                var err = error_state.err;
                if (err) {
                    return callback(err); //quit on error.
                }
                if(array[index] == null && options.skip_nulls) {
                    return cb();
                }
                else {
                    return fn(err, args, state, function (_err) {
                        error_state.err = _err;
                        return cb();
                    });
                }
            };
        };
        for (var i = 0; i < array.length; i++) {
            function_array.push(func_maker(i));
        }
    }

    if(function_array.length == 0) {
        return callback();
    }

    return async.series(function_array, function () {
        return callback(error_state.err);
    });
}


/**Runs a function (fn) for every member of an array, using async.parallel.
 * fn needs the following input parameters: (err, args, state, callback)
 * args has the following members: data_object, setter_function
 * May pass in a state object through options.
 */
function for_each_async_parallel(args, state, callback) {
    var array = args.array;
    var fn = args.func;
    var options = set_options(args.options, {
        skip_nulls: false
    }, true);

    var function_array = [];
    var func_results;

    if (!Array.isArray(array)) {
        func_results = {};
        var dictionary = array; //I assume it's iterable.
        array = null;
        var func_maker = function (_field_name) {
            return function (cb) {
                var args = {
                    data_object: dictionary[_field_name],
                    setter_function: function (new_value) {
                        dictionary[_field_name] = new_value;
                    }
                };
                var err = func_results[_field_name];

                if(dictionary[_field_name] == null && options.skip_nulls) {
                    return cb();
                }
                else {
                    return fn(err, args, state, function (_err) {
                        func_results[_field_name] = _err;
                        return cb();
                    });
                }
            };
        };
        for (var field_name in dictionary) {
            if ((dictionary[field_name] && (typeof dictionary[field_name] != "function")) || (!dictionary[field_name])) { //allows null/undefined members, but not functions.
                function_array.push(func_maker(field_name));
            }
        }
    }
    else {
        func_results = [];
        var func_maker = function (index) {
            return function (cb) {
                var args = {
                    data_object: array[index],
                    setter_function: function (new_value) {
                        array[index] = new_value;
                    },
                    getter_function: function () {
                        return array[index];
                    }
                };
                var err = func_results[index];

                if(array[index] == null && options.skip_nulls) {
                    return cb();
                }
                else {
                    return fn(err, args, state, function (_err) {
                        func_results[index] = _err;
                        return cb();
                    });
                }
            };
        };
        for (var i = 0; i < array.length; i++) {
            function_array.push(func_maker(i));
        }
    }

    return async.parallel(function_array, function () {
        var error_arr = [];
        for(field_name in func_results) {
            if(func_results[field_name]) {
                error_arr.push(func_results[field_name]);
            }
        }
        if(error_arr.length > 0) {
            if(error_arr.length == 1) {
                return callback([error_arr[0]]);
            }
            var error_message_string = "";
            for(var i = 0; i < error_arr.length; i++) {
                error_message_string = error_message_string + "<br />" + "{" + i + "}";
            }
            //return callback([R('multiple_errors_occurred'), [error_message_string, error_arr]]);
        }
        return callback();
    });
}

/**Extends a given options object with a set of default options*/
function set_options(options, default_options, shallow_extend) {
    if (!options) {
        options = {};
    }
    if (shallow_extend) {
        for (var field_name in default_options) {
            if (options[field_name] == null) {
                options[field_name] = default_options[field_name];
            }
        }
    }
    else {
        extend_object(options, default_options);
    }
    return options;
}

/**Extends a base object with the members of the extender object, and returns the base object.
 * Allows the following options to customize the extender behavior:
 shallow_copy: false,
 replace_whole_obj_fields: false,
 replace_shallow_fields: false,
 ignore_functions: false,
 ignore_null_and_undefined: true,
 trigger_deletes_on_extender_nulls: false, --Fields set to null in the extender object will be REMOVED from base object.
 extend_arrays: false --"extends" arrays by adding missing members.
 */
function extend_object(base_object, extender_object, options) {
    if (base_object === extender_object) {
        return base_object;
    }
    if (!base_object) {
        if(!is_object_or_array(extender_object)) {
            return extender_object;
        }
        if(Array.isArray(extender_object)) {
            base_object = [];
        }
        else {
            base_object = {};
        }
    }

    options = set_options(options, {
        shallow_copy: false,
        replace_whole_obj_fields: false,
        replace_shallow_fields: false,
        ignore_functions: false,
        ignore_null_and_undefined: true,
        trigger_deletes_on_extender_nulls: false,
        extend_arrays: false,
        array_match_field: ""
    }, true);
    options.set_func_array = [];
    var obj_struc_options = { include_null: true, include_undefined: true, include_functions: !options.ignore_functions, traverse_arrays: true, mark_object_ref_duplicates: true };
    var obj_structure = extract_object_structure(extender_object, obj_struc_options);
    extend_object_recursive_helper(obj_structure, base_object, extender_object, options, obj_struc_options.obj_ref_arr);

    for (var i = 0; i < options.set_func_array.length; i++) {
        options.set_func_array[i]();
    }

    return base_object;
}


function extend_object_recursive_helper(extend_object_structure, base_object, extender_object, options, obj_ref_arr) {
    if (!extend_object_structure || !base_object) {
        return;
    }

    if (base_object === extender_object) {
        return base_object;
    }

    var my_index = -1;
    if (extender_object && is_object_or_array(extender_object)) {
        my_index = obj_ref_arr.indexOf(extender_object);
    }
    if (my_index >= 0) {
        obj_ref_arr[my_index] = base_object; //replace references in this array as you cross them.
    }

    for (var field_name in extend_object_structure) {
        if (extend_object_structure[field_name] && typeof extend_object_structure[field_name] == "number") { //reference object

            options.set_func_array.push(function (f_name) {
                return function () {
                    if((base_object[f_name] == null) || options.replace_whole_obj_fields) {
                        base_object[f_name] = obj_ref_arr[extend_object_structure[f_name]];
                    }
                    else if (options.replace_shallow_fields) { //TODO: Test this thoroughly.
                        var temp = options.set_func_array;
                        delete options.set_func_array;
                        extend_object(base_object[f_name], obj_ref_arr[extend_object_structure[f_name]]);
                        options.set_func_array = temp;
                    }
                };
            }(field_name));
        }
        else {
            if (extender_object[field_name] == null) {
                if (options.trigger_deletes_on_extender_nulls) {
                    delete base_object[field_name];
                }
                if (!options.ignore_null_and_undefined) {
                    base_object[field_name] = extender_object[field_name];
                }
            }
            else if (options.ignore_functions && typeof extender_object[field_name] == "function") {
                //do nothing.
            }
            else if (base_object[field_name] == null || (options.replace_whole_obj_fields && !options.shallow_copy)) {
                if (options.shallow_copy) {
                    base_object[field_name] = extender_object[field_name];
                }
                else if (Array.isArray(extend_object_structure[field_name])) {
                    base_object[field_name] = [];
                    extend_object_recursive_helper(extend_object_structure[field_name], base_object[field_name], extender_object[field_name], options, obj_ref_arr);
                }
                else if (is_object_or_array(extend_object_structure[field_name])) {
                    base_object[field_name] = {};
                    extend_object_recursive_helper(extend_object_structure[field_name], base_object[field_name], extender_object[field_name], options, obj_ref_arr);
                }
                else {
                    base_object[field_name] = extender_object[field_name];
                }
            }
            else if (options.replace_whole_obj_fields && options.shallow_copy) {
                if (is_object_or_array(extender_object[field_name])) {
                    var my_index = obj_ref_arr.indexOf(extend_object_structure);
                    if (my_index >= 0) {
                        obj_ref_arr[my_index] = extender_object[field_name]; //replace references in this array as you cross them.
                    }
                }
                base_object[field_name] = extender_object[field_name];
            }
            else if (options.replace_shallow_fields) {
                if (!is_object_or_array(base_object[field_name]) || !is_object_or_array(extender_object[field_name])) { //replace directly if either entity has a shallow type.
                    base_object[field_name] = extender_object[field_name];
                }
                else {
                    extend_object_recursive_helper(extend_object_structure[field_name], base_object[field_name], extender_object[field_name], options, obj_ref_arr);
                }
            }
            else if (is_object_or_array(extender_object[field_name])) {
                if (Array.isArray(base_object[field_name])) {  //don't touch existing arrays unless extend_arrays is set to true.
                    if (options.extend_arrays && Array.isArray(base_object[field_name]) && Array.isArray(extender_object[field_name])) {
                        extend_list(base_object[field_name], extender_object[field_name], options.array_match_field);
                    }
                }
                else {
                    extend_object_recursive_helper(extend_object_structure[field_name], base_object[field_name], extender_object[field_name], options, obj_ref_arr);
                }
            }
        }
    }
    return base_object;
}


/**Adds to base_arr items missing from base_arr, but present in extend_arr*/
function extend_list(base_arr, extend_arr, array_match_field) {
    if (!base_arr) { base_arr = []; }
    if(!extend_arr || !Array.isArray(base_arr) || !Array.isArray(extend_arr)) {
        return;
    }
    for (var i = 0; i < extend_arr.length; i++) {
        if (array_match_field && array_match_field.length > 0) {
            var extend_arr_value = extend_arr[i][array_match_field];
            if (extend_arr_value) {
                var found = false;
                for (var j = 0; j < base_arr.length; j++) {
                    if (base_arr[j][array_match_field] == extend_arr_value) {
                        found = true;
                        for (var key in extend_arr[i]) {
                            if (base_arr[j][key] == undefined) {
                                base_arr[j][key] = extend_arr[i][key];
                            }
                        }
                        break;
                    }
                }

                if (!found) {
                    if (base_arr.indexOf(extend_arr[i]) == -1) {
                        base_arr.push(extend_arr[i]);
                    }
                }
            }
            else {
                if (base_arr.indexOf(extend_arr[i]) == -1) {
                    base_arr.push(extend_arr[i]);
                }
            }
        }
        else {
            if (base_arr.indexOf(extend_arr[i]) == -1) {
                base_arr.push(extend_arr[i]);
            }
        }
    }
    return base_arr;
}

function is_object_or_array(obj) {
    var string_val = Object.prototype.toString.call(obj);
    return ((string_val === '[object Array]') || (string_val === '[object Object]'));
}

/**Makes a copy of the object but ignores any repeated (multiple ref'd) objects
 Can also set options to do the following:
 * include_functions (false),
 * include_null (false),
 * include_undefined (false),
 * mark_object_ref_duplicates (false), //marks them by number and returns an array for you to index.
 * traverse_arrays (false),
 * recursive (true)
 */
function extract_object_structure(object, options) {
    var obj_ref_arr = [];
    if (object == null || !(is_object_or_array(object))) {
        if (options.mark_object_ref_duplicates) {
            options.obj_ref_arr = obj_ref_arr;
        }

        return object;
    }
    options = set_options(options, {
        include_null: false,
        include_undefined: false,
        include_functions: false,
        mark_object_ref_duplicates: false,
        traverse_arrays: false,
        recursive: true
    }, true);

    obj_ref_arr.push(object); //don't allow fields to reference the parent object.

    var copy_obj = (Array.isArray(object))? [] : {};

    if (Array.isArray(object)) {

        if (!options.traverse_arrays) {
            options.recursive = false;
        }

        for (var i = 0; i < object.length; i++) {
            extract_object_structure_helper(object, copy_obj, i, obj_ref_arr, options);
        }

        if (options.mark_object_ref_duplicates) {
            options.obj_ref_arr = obj_ref_arr;
        }

        return copy_obj;
    }
    else {
        for (var field_name in object) {
            extract_object_structure_helper(object, copy_obj, field_name, obj_ref_arr, options);
        }
    }

    if (options.mark_object_ref_duplicates) {
        options.obj_ref_arr = obj_ref_arr;
    }

    return copy_obj;
}

/**Recursive helper function for extract_object_structure.*/
function extract_object_structure_helper(object, copy_obj, prop_name, obj_ref_arr, options) {
    if (object[prop_name] == null) {
        if ((Array.isArray(object)) || ((object[prop_name] === null && options.include_null) || (object[prop_name] === undefined && options.include_undefined))) {
            copy_obj[prop_name] = true;
        }
        return;
    }

    var prop_type = Object.prototype.toString.call(object[prop_name]);

    if (!options.recursive) {
        if (((prop_type == "function") && options.include_functions) || (prop_type != "function")) {
            copy_obj[prop_name] = true;
        }
        else if (Array.isArray(object)) {
            copy_obj[prop_name] = null;
        }
        return;
    }

    switch (prop_type) {
        case "[object Object]":
        { //may be obj or array.
            var prop_obj = object[prop_name];
            var index_of_ref = obj_ref_arr.indexOf(prop_obj);
            if (index_of_ref == -1) {
                obj_ref_arr.push(prop_obj);
                copy_obj[prop_name] = {};
                for (var field_name in prop_obj) {
                    extract_object_structure_helper(prop_obj, copy_obj[prop_name], field_name, obj_ref_arr, options);
                }
            }
            else if (options.mark_object_ref_duplicates) {
                copy_obj[prop_name] = index_of_ref;
            }
            break;
        }
        case "[object Array]":
        { //may be obj or array.
            var prop_obj = object[prop_name];
            var index_of_ref = obj_ref_arr.indexOf(prop_obj);
            if (index_of_ref == -1) {
                obj_ref_arr.push(prop_obj);
                if (options.traverse_arrays) {
                    copy_obj[prop_name] = [];
                    for (var i = 0; i < prop_obj.length; i++) {
                        extract_object_structure_helper(prop_obj, copy_obj[prop_name], i, obj_ref_arr, options);
                    }
                }
                else {
                    copy_obj[prop_name] = true;
                }
            }
            else if (options.mark_object_ref_duplicates) {
                copy_obj[prop_name] = index_of_ref;
            }
            break;
        }
        case "[object Function]":
        {
            if (options.include_functions) {
                copy_obj[prop_name] = true;
            }
            else if (Array.isArray(object)) {
                copy_obj[prop_name] = null;
            }
            break;
        }
        default:
        {
            copy_obj[prop_name] = true;
            break;
        }
    }
    return;
}

exports.extend_object = extend_object;
exports.for_each_async = for_each_async;