'use strict';

function invoke_method(entity, method, data, success, error, complete) {
    var request = {};
    request.entity = entity;
    request.method = method;

    request.data = data;

    do_authenticated_http_call({
        method: "POST",
        url: "/api/entity/invoke_method",
        contentType: "application/json",
        dataType: 'json',
        data: JSON.stringify(request),
        success: success,
        error: function(err) {
            if (err.responseJSON && error && typeof error === "function") {
                error(err.responseJSON);
            }

            console.log(err);
        },
        complete: complete
    });
}

function log_error(error) {
    var request = {};
    request.entity = "error";
    request.method = "log_error";

    request.data = {error:error};

    do_authenticated_http_call({
        method: "POST",
        url: "/api/entity/invoke_method",
        contentType: "application/json",
        dataType: 'json',
        data: JSON.stringify(request)
    });
}

function get_app_object(app_object_code, extra, success, error, complete) {
    if (!extra) {
        extra = {};
    }
    extra.app_object_code = app_object_code;
    invoke_method("app_object", "get_by_code", extra, function(data) { success(data.data); }, error, complete);
}

function make_friendly_caption(caption_fields, entity_values) {
    var ret_val = "";
    for(var i = 0; i < caption_fields.length; i++) {
        var complex_path = caption_fields[i];
        var field_value = entity_values[complex_path];
        if(field_value) {
            ret_val = ((ret_val) ? ret_val + " " + field_value : field_value);
        }
    }
    return ret_val;
}

function make_conditions_from_devextreme_filter(filter_arr) {
    //format1: [3]
    //[3] == att.field_path, "contains", filter_box.value
    //alternates for "contains":  "=", "<>", ">", ">=", "<", "<=", "startswith", "endswith", "contains", "notcontains"
    //format2: [[3], "and", [3], "or", [3], ...]

    var ret_val;
    ret_val = filter_arr[0];
    if(typeof ret_val == "string") {
        ret_val = parse_condition_obj(filter_arr);
    } else if (typeof ret_val == "function")
	{
        ret_val = parse_condition_obj(filter_arr);
	}

    else if((Object.prototype.toString.call(ret_val) === '[object Array]') || (Object.prototype.toString.call(ret_val) === '[object Object]')) { //an array of conditions joined by and/or
        ret_val = parse_condition_obj(ret_val);

        for (var i = 1; i < filter_arr.length; i++) {
            var condition = filter_arr[i];
            if (condition) {
                if (Object.prototype.toString.call(condition) === '[object Array]') {
                    //implies AND
                    condition = parse_condition_obj(condition);
                    ret_val = add_and_condition(ret_val, condition);
                }
                else if (typeof condition == "string") {
                    if (condition == "and") {
                        i++;
                        condition = parse_condition_obj(filter_arr[i]);
                        ret_val = add_and_condition(ret_val, condition);
                    }
                    else if (condition == "or") {
                        i++;
                        condition = parse_condition_obj(filter_arr[i]);
                        ret_val = add_or_condition(ret_val, condition);
                    }
                }
            }
        }
    }
    return ret_val;
}

function make_sorting_conditions_from_devextreme_sort(sort_arr) {
    var ret_val = {};

    for(var i = 0; i < sort_arr.length; i++) { //usually only 1
        var field_path = sort_arr[i].selector;
        var descending = sort_arr[i].desc;
        ret_val[field_path] = (descending)? -1 : 1;
    }

    return ret_val;
}

var filter_conditions_mongo_equivalent_lookup = {
    "=":"$eq",
    "<>": "$ne",
    ">": "$gt",
    ">=": "$gte",
    "<": "$lt",
    "<=": "$lte"//,
    //"contains": "$in",
    //"notcontains": "$nin",
    //"startswith": "",
    //"endswith": ""
};

function try_parse_regexp(value) {
    if(value && typeof value == "string") {
        var m = value.match(/\/(.*)\/(.*)?/);
        return new RegExp(m[1], m[2] || "");
    }
    return value;
}

function parse_condition_obj(condition_obj) {
    //[3] == att.field_path, "contains", filter_box.value
    //alternates for "contains":  "=", "<>", ">", ">=", "<", "<=", "startswith", "endswith", "contains", "notcontains"
    if (!condition_obj || (typeof condition_obj != "object") || (!Array.isArray(condition_obj)) || condition_obj.length !== 3) {
        return null;
    }
    var field_path = condition_obj[0];
    if (typeof field_path == "function")
    {
        field_path = field_path();
    }
    var operator = condition_obj[1];
    var comparison_value = condition_obj[2];

    var sub_doc = {};
    switch (operator) {
        /*case "=":
        {
            sub_doc[filter_conditions_mongo_equivalent_lookup[operator]] = comparison_value;
            break;
        }
        case "<>":
        {

            break;
        }
        case ">":
        {

            break;
        }
        case ">=":
        {

            break;
        }
        case "<":
        {

            break;
        }
        case "<=":
        {

            break;
        }*/
        case "startswith":
        {
            sub_doc["$regex"] = "^" + comparison_value.replace(/ /g, '\\s');
            sub_doc["$options"] = "i";
            break;
        }

        case "endswith":
        {
            sub_doc["$regex"] = comparison_value.replace(/ /g, '\\s') + "$";
            sub_doc["$options"] = "i";
            break;
        }
        case "contains":
        {
            sub_doc["$regex"] = comparison_value.replace(/ /g, '\\s');
            sub_doc["$options"] = "i";
            break;
        }
        case "notcontains":
        {
            sub_doc["$regex"] = comparison_value.replace(/ /g, '\\s');
            sub_doc["$options"] = "i";
            sub_doc = {
                "$not": sub_doc
            };
            break;
        }

        default:
        {
            sub_doc[filter_conditions_mongo_equivalent_lookup[operator]] = comparison_value;
            break;
        }
    }

    var ret_val = {};
    ret_val[field_path] = sub_doc;
    return ret_val;
}

function add_and_condition(ret_val, condition) {
    if(!condition) {
        return;
    }
    if(!ret_val["$and"]) {
        var old_ret = ret_val;
        ret_val = {};
        ret_val["$and"] = [old_ret];
    }
    ret_val["$and"].push(condition);
    return ret_val;
}

function add_or_condition(ret_val, condition) {
    if(!condition) {
        return;
    }
    if(!ret_val["$or"]) {
        var old_ret = ret_val;
        ret_val = {};
        ret_val["$or"] = [old_ret];
    }
    ret_val["$or"].push(condition);
    return ret_val;
}
