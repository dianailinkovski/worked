var R = {};
var R_loc = {"download":"Download","preparing_for_upload":"Preparing for upload","error":"Error","complete":"complete",
"cannot_receive_information_from_server":"Cannot receive information from server. Please try again later.",
"can_not_upload_file_to_the_server":"Can't upload file to the server","login_to_your_account":"Login to Your Account",
"the_username_can_only_consist_of_alphabetical_number_dot_and_underscore":"The username can only consist of alphabetical, number, dot and underscore",
"help_i":"Help, I","select_image":"Select image", "file_uploading": "File uploading","label_close":"Close"};

var g_popped = false;

var g_login_token = ""; // Login token used for authentication

var load_translations = function(anon, callback) {
    var url = "/api/lang/get_langs" + (anon ? "_anon" : "");

    do_authenticated_http_call({
        method: "GET",
        url: url,
        success: function(data) {
            var old_R = R;
            R = data;
            if(document.title) {
                if (document.title.indexOf("Xenforma") != -1) {
                    document.title = document.title.replace("Xenforma", R.client_application_title || "Xenforma");
                }
                if (old_R.client_application_title && (document.title.indexOf(old_R.client_application_title) != -1)) {
                    document.title = document.title.replace("Xenforma", R.client_application_title || "Xenforma");
                }
            }
            else {
                document.title = R.client_application_title || "Xenforma";
            }
            if (callback && typeof callback == "function") {
                callback();
            }
        }
    });
};

var is_logged_in = function(callback) {
    if (g_login_token == null || g_login_token == undefined || g_login_token == "") {
        g_login_token = localStorage.getItem("login_token");
    }

    if (g_login_token == null || g_login_token == undefined || g_login_token == "") {
        return callback(false);
    }

    do_authenticated_http_call({
        method:"GET",
        url:"/api/auth/is_logged_in",
        success: function(response) {
            if (response.logged_in) {
                return callback(true, response);
            }
            return callback(false, response);
        },
        error: function(error) {
            return callback(false, error);
        }
    });
};

var do_authenticated_http_call = function(options) {
    if (options === undefined) {
        options = {};
    }

    if (options.headers === undefined) {
        options.headers = {};
    }

    if (g_login_token == null || g_login_token == undefined || g_login_token == "") {
        g_login_token = localStorage.getItem("login_token");
    }

    if (g_login_token) {
        options.headers["Authorization"] = "JWT " + g_login_token;
    }

    return $.ajax(options);
};

var push_href_location = function(title, url) {
    if (!g_popped) {
        window.history.pushState({html: document.innerHTML, pageTitle: title}, "", url);
    }
    document.title = title;

    g_popped = false;
};

var get_attribute_for_field = function(entity_attributes, field) {
    for (var i = 0; i < entity_attributes.attributes.length; i++) {
        var entity_attribute = entity_attributes.attributes[i];
        if (entity_attribute.field_path == field) {
            return entity_attribute;
        }
    }
};

function makeid() {
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < 7; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}

function find_index_by_id(array, _id) {
    if(_id && array && array.length > 0) {
        var index;
        for(var i = 0; i < array.length; i++) {
            if(array[i]._id && array[i]._id == _id) {
                index = i;
                break;
            }
        }
        return index;
    }
}

function display_yes_no_dialogue(title, message, callback) {
    DevExpress.ui.dialog.confirm(message, title).done(function(result) {
        if (result) {
            return callback(true);
        }
        else {
            return callback(false);
        }
    });
}

var current_navigation_listener;

var idle_time = 0;
var idle_interval;

var get_clear_state = function () {
    var state_object = {};
    state_object.settings = false;
    state_object.invite_users = false;
    state_object.edit_form = false;
    state_object.data_list = false;
    state_object.app_object_code = false;
    state_object.edit_form_id = undefined;
    state_object.dashboard = false;
    state_object.setup_user = false;
    state_object.reset_password = false;
    state_object.forgot_password = false;
    state_object.search = false;
    state_object.search_string = undefined;
    state_object.user_data_list = false;
    state_object.home = false;
    
    return state_object;
};

function get_parameter_by_name(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

$(document).ready(function() {
    var MainElement = <MainComponent />;
    ReactDOM.render(MainElement, document.getElementById("main-container"));
});

window.onerror = function(message, url, line_number) {
    var error = {};
    error.message = message;
    error.url = url;
    error.line_number = line_number;

    log_error(error);

    return false;
};