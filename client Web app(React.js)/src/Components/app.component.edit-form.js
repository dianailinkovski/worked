var EditFormComponent = React.createClass({
    getInitialState: function () {
        var state_object = this.initialize_edit_form();
        if (state_object) {
            return state_object;
        }
        return {workflow_comment: "", update_fields: {}};
    },
    componentDidUpdate: function () {
        return this.custom_post_render_logic();
    },
    componentDidMount: function () {
        return this.custom_post_render_logic();
    },
    custom_post_render_logic: function () {
        $('.date-picker').datepicker();

        for (var i = 0; this.state.entity_attributes && i < this.state.entity_attributes.attributes.length; i++) {
            var attribute = this.state.entity_attributes.attributes[i];
            if (!attribute.form_visible) {
                continue;
            }

            var info_id = "#info_" + attribute.field_path.replace(/:/g, '-');

            var popover_desc = $(info_id + "_desc").dxPopover({
                target: info_id,
                position: "top",
                width: 300
            }).dxPopover("instance");

            var appear = function (popover) {
                return function () {
                    popover.show();
                }
            };
            var disappear = function (popover) {
                return function () {
                    popover.hide();
                }
            };

            $(info_id).hover(appear(popover_desc), disappear(popover_desc));
        }

        if (this.state.workflow_states && !this.state.workflow_comment_popup) {
            this.state.workflow_comment_popup = $("#workflow_comment_popup").dxPopup({
                width: 400,
                height: 250,
                showTitle: false
            }).dxPopup("instance");
        }
    },
    componentWillUnmount: function () {
        //current_navigation_listener = undefined;
        if (this.state.workflow_states) {
            var workflow_comment_popup = $("#workflow_comment_popup").dxPopup("instance");
            if (workflow_comment_popup) {
                workflow_comment_popup.hide();
            }
        }
        if (this.state.modified) {
            this.state.modified = false;
        }
    },
    initialize_edit_form: function () {
        var context = this;
        var given_data = this.props.data;
        var is_new_instance = false;

        var success = function (data) {

            data = data.data;
            console.log("workflow:data");
            console.log(data);

            data = $.extend({}, given_data, data);
            data.is_new_instance = is_new_instance;

            if (is_new_instance) {
                context.populate_update_fields(data.entity_attributes, data.values);
            }

            if (data.app_object && data.app_object.workflow_states) {
                data.workflow_states = data.app_object.workflow_states;
                delete data.app_object.workflow_states;
            }

            if (data.values) {
                data.entity_instance = data.values;
                delete data.values;
            }

            var caption = (data.app_object.name && (data.app_object.name.trim() != "")) ? data.app_object.name : data.entity_attributes.caption_singular;
            if (data.entity_attributes.caption_fields && data.entity_attributes.caption_fields.length > 0) {
                data.caption_function = function (entity_data) {
                    return make_friendly_caption(data.entity_attributes.caption_fields, entity_data);
                };
                if (!is_new_instance) {
                    caption = data.caption_function(data.entity_attributes.attributes);
                }
            }

            context.setState(data);

            var _id;
            _id = request._id;

            _id = _id ? "&_id=" + _id : "";

            if (!data.parent_react_entity) {
                push_href_location(caption + " - " + (R.client_application_title || "Xenforma"), "/edit_form?code=" + (data.app_object_code || data.app_object.code) + _id);
            }

            context.post_init();
        };

        var error = function (err) {
            context.setState({error: err.message});
        };

        if (given_data && !given_data.is_reference_nest) { //nested edit_form
            if (given_data.is_new_instance) {
                is_new_instance = true;
                //given_data.update_fields = context.populate_update_fields(given_data.entity_attributes, {});
            }
            else {
                given_data.update_fields = {};
                context.post_init(given_data.entity_attributes);
                var caption = given_data.entity_attributes.caption_singular;
                if (given_data.entity_attributes.caption_fields && given_data.entity_attributes.caption_fields.length > 0) {
                    given_data.caption_function = function (entity_data) {
                        return make_friendly_caption(given_data.entity_attributes.caption_fields, entity_data);
                    };
                }
                return given_data;
            }
        }
        else { //top-level edit_form
            current_navigation_listener = function (callback) {
                if (context.state.modified) {
                    return display_yes_no_dialogue(R.label_confirm, R.message_changes_discarded, function (confirmation) {
                        if (confirmation) {
                            return callback(true);
                        }
                        else {
                            return callback(false);
                        }
                    });
                }
                else {
                    return callback(true);
                }
            };
        }

        var request = {
            app_object_code: this.props.app_object_code || this.props.data.app_object_code || this.props.data.app_object.code,
            separate_values: true
        };
        if (this.props._id) {
            request._id = this.props._id;
        }
        else if (this.state && this.state.is_new_instance != undefined) {
            request._id = this.state._id;
            is_new_instance = this.state.is_new_instance;
        }
        else {
            is_new_instance = true;
        }

        return invoke_method("app_object", "get_by_code", request, success, error);
    },
    post_init: function (entity_attributes) {
        var context = this;

        if (!entity_attributes) {
            entity_attributes = this.state.entity_attributes;
        }
        for (var i = 0; entity_attributes && i < entity_attributes.attributes.length; i++) {
            var attribute = entity_attributes.attributes[i];

            if (!attribute.form_visible) {
                continue;
            }

            if (attribute.is_nested_entity && attribute.data_is_nested == false) {
                //if (attribute.is_array)
                this.refresh_reference_grid(attribute);
            }
        }
    },
    refresh_reference_grid: function (attribute, callback) {
        var context = this;
        //if (attribute.is_nested_entity && attribute.data_is_nested == false && attribute.is_array) {
        var entity = attribute.db_type;

        var request = {};
        request.app_object_code = this.props.app_object_code || this.props.data.app_object_code || this.props.data.app_object.code;

        var success = function (attribute) {
            return function (data) {
                data = data.data;
                if (!attribute.select_options) {
                    attribute.select_options = [];
                }
                attribute.select_options.length = 0;
                attribute.select_options.push.apply(attribute.select_options, data);
                //attribute.select_options = data;
                if (callback) {
                    callback(data);
                }
                context.forceUpdate();
            }
        }(attribute);

        var error = function (error) {
            console.log(error);
        };

        invoke_method(entity, "read_with_nested_permissions", request, success, error);
        //}
    },
    populate_update_fields: function (entity_attributes, entity_instance_data, out_val) {
        if (!out_val) {
            out_val = this.state.update_fields || {};
        }
        for (var i = 0; i < entity_attributes.attributes.length; i++) {
            var attribute = entity_attributes.attributes[i];

            if (attribute.form_visible) {
                var attribute_value;

                attribute_value = entity_instance_data[attribute.field_path];

                out_val[attribute.field_path] = attribute_value;
            }
        }
        return out_val;
    },
    on_file_name_change: function (attribute, file) {
        if (file) {
            this.state.update_fields['file_name'] = file.name;
            this.state.entity_instance['file_name'] = file.name;
        }
        else {
            this.state.update_fields['file_name'] = null;
            this.state.entity_instance['file_name'] = null;
        }
        this.setState({fileToUpload: file});
    },
    on_file_upload_end: function (attribute) {
        this.state.update_fields['date_uploaded'] = new Date();
        this.state.entity_instance['date_uploaded'] = new Date();
        this.state.update_fields['uploaded'] = true;
        this.state.entity_instance['uploaded'] = true;
        this.setState({fileToUpload: undefined, uploadFileModal: false, resetFileUpload: true});
        this.handle_submit()
    },
    on_file_upload_cancel: function (attribute, event) {
        this.setState({uploadFileModal: false});
    },
    on_field_change: function (attribute, event) {
        if (!this.state.modified) {
            this.state.modified = true;
        }

        if (attribute) {
            var new_value;
            var type = attribute.attribute_type == null ? attribute.db_type : attribute.attribute_type;

            if (event == null) {
                new_value = event; // event is data
            } else if (event.target) {
                switch (type) {
                    case "Boolean":
                        new_value = event.target.checked;
                        break;
                    default:
                        new_value = event.target.value;
                        break;
                }
            }
            else if (event.component) { // Data coming from entity reference lookup
                new_value = event.component._valuesData; // Data is coming from DevExtreme component
            }
            else {
                new_value = event; // event is data
            }
            this.state.update_fields[attribute.field_path] = new_value;

            this.state.entity_instance[attribute.field_path] = new_value;

            if (attribute.revalidate_on_field_changed) {
                attribute.modified = true;
            }

            this.forceUpdate();
        }
    },
    on_field_blur: function (attribute, event) {
        if (attribute.revalidate_on_field_changed && attribute.modified) {
            delete attribute.modified;
            var attribute_value = this.state.entity_instance[attribute.field_path];
            var entity_name = this.state.entity_attributes.entity;

            var request = {};
            request.field_path = attribute.field_path;
            request.entity_instance = {};
            if (attribute.validation_requires_full_entity) {
                request.entity_instance = this.state.entity_instance;
            }
            else {
                request.entity_instance[attribute.field_path] = attribute_value;
            }
            var context = this;
            var success = function (data) {
                if (data.success) {
                    var updated_view_data = data.updated_view_data;
                    var entity_attributes = context.state.entity_attributes;
                    var modify_state = false;

                    for (var field_path in updated_view_data) {
                        var entity_attribute_update = updated_view_data[field_path];
                        var entity_attribute = get_attribute_for_field(entity_attributes, field_path);
                        if (entity_attribute) {
                            for (var field_attribute in entity_attribute_update) {
                                if (field_attribute != "errors") {
                                    entity_attribute[field_attribute] = entity_attribute_update[field_attribute];
                                    modify_state = true;
                                }
                                else {
                                    if (entity_attribute_update.errors && entity_attribute_update.errors.length > 0) {
                                        context.state.error = entity_attribute_update.errors[0]; // TODO: Handling for multiple errors
                                        modify_state = true;
                                    }
                                }
                            }
                        }
                    }

                    if (modify_state) {
                        context.forceUpdate();
                    }
                }
            };
            var error = function (err) {
                console.log(err);
            };

            invoke_method(entity_name, "execute_business_rule", request, success, error);
        }
    },
    delete_nested_entity: function (nested_entity_field_attribute, nested_entity_array, entity_id) {
        var nested_entity_index = find_index_by_id(nested_entity_array, entity_id);
        if (nested_entity_index != null) {
            nested_entity_array.splice(nested_entity_index, 1);
        }
        this.close_nested_entity_view();
        return this.on_field_change(nested_entity_field_attribute, nested_entity_array);
    },
    upsert_nested_entity: function (nested_entity_field_attribute, nested_entity_array, entity_inst_deltas) {
        var entity_id = entity_inst_deltas._id;
        var has_change = true;
        var nested_entity_index = find_index_by_id(nested_entity_array, entity_id);
        if (nested_entity_index != null) {
            has_change = false;
            for (var field_name in entity_inst_deltas) {
                if (field_name != "_id") {
                    has_change = true;
                    break;
                }
            }

            $.extend(nested_entity_array[nested_entity_index], entity_inst_deltas);
        }
        else {
            nested_entity_array.push(entity_inst_deltas);
        }

        this.close_nested_entity_view();
        if (has_change) {
            return this.on_field_change(nested_entity_field_attribute, nested_entity_array);
        }
    },
    close_nested_entity_view: function (added_id) {
        delete this.state.child_entity_state_data;
        var context = this;
        if (this.state.nested_reference_entity_data) {
            var attribute = this.state.nested_reference_entity_data.attribute;
            this.refresh_reference_grid(attribute, function (data_list) {
                if (data_list) {
                    for (var i = 0; i < context.state.entity_instance[attribute.field_path].length; i++) {
                        if (typeof context.state.entity_instance[attribute.field_path][i] == "object") {
                            context.state.entity_instance[attribute.field_path][i] = context.state.entity_instance[attribute.field_path][i]["entity_id"];
                        }
                    }
                    for (var i = 0; i < data_list.length; i++) {
                        if (data_list[i].entity_id == added_id) { //add to tagbox list:
                            context.state.entity_instance[attribute.field_path].push(added_id);
                            context.forceUpdate();
                            return;
                        }
                    }
                }
            });
            delete this.state.nested_reference_entity_data;
        }
        this.state.areButtonsDisabled = false;
        this.setState(this.state);
    },
    handle_close: function (event) {
        if (event && event.preventDefault) {
            event.preventDefault();
        }

        var context = this;
        var close_func;
        if (context.props.data && context.props.data.parent_react_entity) {
            if (context.state.modified) {
                return display_yes_no_dialogue(R.label_confirm, R.message_changes_discarded, function (confirmation) {
                    if (confirmation) {
                        return context.props.data.parent_react_entity.close_nested_entity_view();
                    }
                });
            }
            else {
                return context.props.data.parent_react_entity.close_nested_entity_view();
            }
        }
        else {
            close_func = function () {
                current_navigation_listener = undefined;
                if (window.history.length && (window.history.length > 1)) {
                    window.history.back();
                }
                else {
                    context.props.navigation_handler("home");
                }
            };
        }
        if (current_navigation_listener) {
            return current_navigation_listener(function (result) {
                if (result) {
                    return close_func();
                }
            });
        }
        else {
            return close_func();
        }
    },
    handle_submit: function (event, view_options) {
        if (event && event.preventDefault) {
            event.preventDefault();
        }
        var context = this;
        var entity_method;
        var send_data = this.state.update_fields;
        context.state.update_fields._id = this.state.entity_instance._id;
        var error = function (err) {
            context.setState({error: err.message})
        };
        var success;
        if (context.props.data && (context.props.data.parent_react_entity && !context.props.data.is_reference_nest)) { //don't save directly, but do validate first.
            if (!context.state.update_fields) {
                return context.props.data.parent_react_entity.close_nested_entity_view();
            }

            send_data = context.state.entity_instance; //validate whole entity as a stand-alone (for now).
            success = function (data) {
                return context.props.data.parent_react_entity.upsert_nested_entity(context.state.parent_entity_field_attribute, context.state.parent_entity_array, context.state.update_fields);
            };
        }
        else {
            success = function (data) {
                if (context.state.is_new_instance) {
                    if (data.data && data.data._id) {
                        context.state._id = data.data._id;
                    }
                }

                if (context.state.fileToUpload) {
                    context.setState({uploadFileModal: true});
                    return;
                }

                if(view_options && view_options.confirmation_message) {
                    Notify(view_options.confirmation_message, 'bottom-right', 20000, 'green', 'fa-check', true);
                }
                else {
                    Notify(data.message, 'bottom-right', 20000, 'green', 'fa-check', true);
                }

                if (context.state.entity_attributes && context.state.entity_attributes.entity == 'translation') {
                    load_translations(false, context.forceUpdate);
                }

                if (context.state.app_object && context.state.app_object.custom_properties && context.state.app_object.custom_properties.refresh_user_data) {
                    context.props.update_avatar();
                    context.props.update_user_info();
                }

                if (context.props.data && context.props.data.is_reference_nest) { //nested edit form
                    context.props.data.parent_react_entity.close_nested_entity_view(context.state._id);
                    return context.props.data.parent_react_entity.on_field_change(context.state.parent_entity_field_attribute, context.state.parent_entity_array);
                }
                else {
                    if (view_options) {
                        if (view_options.skip_refresh) {
                            return context.props.navigation_handler("home");
                        }
                    }
                    context.setState({
                        update_fields: {},
                        error: undefined,
                        modified: false,
                        workflow_comment: "",
                        workflow_action: undefined,
                        workflow_constructor: undefined,
                        is_new_instance: false,
                        resetFileUpload: undefined
                    });
                    context.handle_close(event);
                    context.initialize_edit_form();
                }
            };

        }

        entity_method = "upsert";

        if (context.state.workflow_constructor || context.state.workflow_action) {
            if (!context.props.data || context.props.data.is_reference_nest) { //direct nests don't invoke workflow
                entity_method = "change_workflow_state";
            }

            var comment;

            if (context.state.workflow_constructor) {
                context.state.update_fields.constructor_used = context.state.workflow_constructor.name;

                if (context.state.workflow_constructor.comment) {
                    comment = context.state.workflow_comment;
                }
            }
            else if (context.state.workflow_action) {
                if (context.state.workflow_action.comment) {
                    comment = context.state.workflow_comment;
                }
            }

            if (comment) {
                context.state.update_fields.workflow_comment = comment;
            }
        }

        console.log("send_data");
        console.log(send_data);

        invoke_method(context.state.entity_attributes.entity, entity_method, send_data, success, error);
    },
    handle_delete: function (event) {
        event.preventDefault();

        var context = this;
        display_yes_no_dialogue(R.label_delete_title, R.message_delete_entity, function (confirmation) {
            if (confirmation) {
                if (context.props.data && context.props.data.parent_react_entity) {
                    return context.props.data.parent_react_entity.delete_nested_entity(context.state.parent_entity_field_attribute, context.state.parent_entity_array, context.state.entity_instance._id);
                }
                else {
                    var success = function (data) {
                        context.setState({update_fields: {}, error: undefined});
                        Notify(data.message, 'bottom-right', 20000, 'green', 'fa-check', true);
                        if (context.state.entity_attributes && context.state.entity_attributes.entity == 'translation') {
                            load_translations(false, context.forceUpdate);
                        }
                        context.setState({modified: false});
                        context.props.navigation_handler("home");
                    };

                    var error = function (err) {
                        context.setState({error: err.message});
                    };

                    var data = {_id: context.state.entity_instance._id};

                    return invoke_method(context.state.entity_attributes.entity, "delete", data, success, error);
                }
            }
        });
    },
    handle_workflow_action_click: function (action, status, event, is_constructor) {
        event.preventDefault();

        this.state.update_fields.workflow_status = status;
        if (is_constructor) {
            this.state.workflow_constructor = action;
        }
        else {
            this.state.workflow_action = action;
        }

        if(action && action.view_options) {
            if(action.confirmation_message) {
                action.view_options.confirmation_message = action.confirmation_message; //for easy passing to handle_submit
            }
        }

        if (action && action.comment) {
            var context = this;
            $("#workflow_comment_popup").dxPopup("instance").show();

            $("#workflow_comment_popup_submit").click(function () {
                if (context.state.workflow_comment.length == 0) {
                    Notify(R.message_enter_workflow_comment, 'bottom-right', 10000, 'red', 'fa-check', true);
                }
                else {
                    $("#workflow_comment_popup").dxPopup("instance").hide();
                    context.handle_submit(null, action.view_options);
                }
            });
            $("#workflow_comment_popup_cancel").click(function () {
                $("#workflow_comment_popup").dxPopup("instance").hide();
            });
            $("#workflow_comment_text").keyup(function () {
                context.state.workflow_comment = $("#workflow_comment_text").val();
            });
        }
        else {
            if (action) {
                this.handle_submit(null, action.view_options);
            }
            else {
                this.handle_submit();
            }
        }
    },
    nested_entity_handler: function (child_entity_state_data) {
        this.state.child_entity_state_data = child_entity_state_data;
        this.state.areButtonsDisabled = true;
        return this.forceUpdate();
    },
    reference_entity_handler: function (reference_entity_attribute, reference_entity_id) {
        this.state.nested_reference_entity_data = {
            reference_entity_name: reference_entity_attribute.db_type,
            reference_entity_id: reference_entity_id,
            attribute: reference_entity_attribute,
            value: this.state.entity_instance[reference_entity_attribute.field_path]
        };
        this.state.areButtonsDisabled = true;
        return this.forceUpdate();
    },
    form_is_read_only: function() {
        return (this.state && this.state.app_object && this.state.app_object.custom_properties && !this.state.app_object.custom_properties.editable);
    },
    render: function () {
        var edit_form = null;
        var input_array = [];
        var context = this;

        if (this.state.nested_reference_entity_data) { //popup reference entity edit form
            var ref_data = {
                is_reference_nest: true,
                parent_react_entity: this,
                app_object_code: this.state.nested_reference_entity_data.reference_entity_name + "_default_edit_form",
                key: this.state.nested_reference_entity_data.reference_entity_name + "_default_edit_form" + this.state.reference_entity_id,
                parent_entity_field_attribute: this.state.nested_reference_entity_data.attribute,
                parent_entity_array: this.state.nested_reference_entity_data.value
            };
            var input_element;
            //if(this.state.reference_entity_id) {
            input_element = <EditFormComponent data={ref_data} app_object_handler={this.props.app_object_handler}
                                               _id={this.state.reference_entity_id}
                                               key={this.state.reference_entity_id}
                                               navigation_handler={this.props.navigation_handler}/>;
            //}
            //else {
            //    input_element = <EditFormComponent data = {ref_data} app_object_handler = {this.props.app_object_handler} navigation_handler = {this.props.navigation_handler} />;
            //}

            var input_div = <div className="form-group" key={"div-"+input_element.key}>
                <div className={"col-sm-12"}>
                    {input_element}
                </div>
            </div>;
            edit_form = input_div;
        }
        else if (this.state.child_entity_state_data) {
            /*var data = {
             app_object,
             entity_attributes,
             nested_list_entities,
             workflow_states,
             entity_instance,
             parent_entity_array,
             parent_entity_field_attribute,
             parent_react_entity,
             is_new_instance
             };*/
            var input_element = <EditFormComponent data={context.state.child_entity_state_data}/>;
            var input_div = <div className="form-group" key={"div-child-entity-"+input_element.key}>

                <div className={"col-sm-12"}>
                    {input_element}
                </div>
            </div>;
            edit_form = input_div;
        }
        else {
            var row_name;
            var row_array = null;
            var same_row_count;
            for (var i = 0; this.state.entity_attributes && i < this.state.entity_attributes.attributes.length; i++) {
                var attribute = this.state.entity_attributes.attributes[i];

                if (!attribute.form_visible) {
                    continue;
                }
                var attribute_value;
                var attribute_caption_image;
                attribute_value = this.state.entity_instance[attribute.field_path];
                attribute_caption_image = null;

                if (attribute_value && (typeof attribute_value == "object") && attribute.is_nested_entity && attribute.data_is_nested == false && !attribute.is_array) {
                    if (attribute_value.image) {
                        attribute_caption_image = attribute_value.image;
                    }
                    attribute_value = attribute_value.caption || attribute_value.entity_id;
                }

                var same_row = attribute.same_row;
                if (same_row) {
                    row_name = same_row;
                    same_row_count = 0;
                    for (var k = 0; k < this.state.entity_attributes.attributes.length; k++) {
                        if (this.state.entity_attributes.attributes[k].same_row && (this.state.entity_attributes.attributes[k].same_row == row_name)) {
                            same_row_count++;
                            if (row_array == null) {
                                row_array = [];
                            }
                        }
                    }
                }

                var input_element;
                var omit_caption = attribute.hide_caption;

                if (attribute.is_array && attribute.is_nested_entity && attribute.data_is_nested) {
                    var entity = attribute.db_type;
                    var nested_list_entities = this.state.nested_list_entities;
                    if (this.props.data && this.props.data.nested_list_entities) {
                        nested_list_entities = this.props.data.nested_list_entities;
                    }
                    var data_list_object = nested_list_entities[entity];
                    var app_object = data_list_object.app_object;
                    if(this.form_is_read_only()) {
                        app_object.custom_properties.editable = false;
                    }
                    else {
                        app_object.custom_properties.editable = !attribute.read_only;
                    }
                    var entity_attributes = data_list_object.entity_attributes;
                    var entity_instances = attribute_value;
                    if (!entity_instances) {
                        this.state.entity_instance[attribute.field_path] = [];
                        entity_instances = this.state.entity_instance[attribute.field_path];
                    }

                    input_element = <DataListComponent skip_init={true} read_only={(attribute.read_only || this.form_is_read_only())}
                                                       nested_list_entities={nested_list_entities}
                                                       parent_react_entity={this}
                                                       parent_entity_field_attribute={attribute}
                                                       parent_entity_array={entity_instances} app_object={app_object}
                                                       entity_attributes={entity_attributes}
                                                       entity_instances={entity_instances} key={app_object.code}
                                                       app_object_handler={this.props.app_object_handler}
                                                       nested_entity_handler={this.nested_entity_handler}
                                                       on_change={this.on_field_change.bind(this, attribute)}/>;
                    omit_caption = true;
                }
                else if (attribute.is_nested_entity && attribute.data_is_nested == false) {
                    if (attribute.is_array) {
                        if (!attribute_value) {
                            this.state.entity_instance[attribute.field_path] = [];
                            attribute_value = this.state.entity_instance[attribute.field_path];
                        }
                        if (attribute.select_options) {
                            //var mount_callback = function(att) {return function() {context.refresh_reference_grid(att);};}(attribute);

                            if (this.state.entity_attributes.reference_entities[attribute.db_type] && this.state.entity_attributes.reference_entities[attribute.db_type].entity_access && (this.state.entity_attributes.reference_entities[attribute.db_type].entity_access.indexOf('c') != -1)) { //add a + sign.
                                input_element = function (local_attribute) {
                                    var add_func = function () {
                                        context.reference_entity_handler(local_attribute)
                                    };
                                    var addButton = '';
                                    if (!(local_attribute.read_only || context.form_is_read_only())) {
                                        addButton = <button className="edit-controls-add-button dx-button"
                                                            onClick={add_func}>
                                            <div className='dx-button-content'><i
                                                className='dx-icon dx-icon-edit-button-addrow'></i></div>
                                        </button>;
                                    }
                                    return (<div><EditorDevExtremeTagBox key={local_attribute.field_path}
                                                                         tagbox_holder={local_attribute}
                                                                         dataSource={local_attribute.select_options}
                                                                         displayExpr="caption" valueExpr="entity_id"
                                                                         values={context.state.entity_instance[local_attribute.field_path]}
                                                                         onChange={context.on_field_change.bind(context, local_attribute)}
                                                                         add_button={addButton==''?false:true}
                                                                         readOnly={(local_attribute.read_only || context.form_is_read_only())}/>
                                        {addButton}
                                    </div>)
                                }(attribute);
                            }
                            else {
                                input_element =
                                    <EditorDevExtremeTagBox key={attribute.field_path} tagbox_holder={attribute}
                                                            dataSource={attribute.select_options} displayExpr="caption"
                                                            valueExpr="entity_id" values={attribute_value}
                                                            onChange={context.on_field_change.bind(context, attribute)}
                                                            readOnly={(attribute.read_only || this.form_is_read_only())}/>;
                            }
                        }
                        else {

                            continue;
                        }
                    } else {
                        if (attribute.select_options) {
                            attribute_value = this.state.entity_instance[attribute.field_path];
                            if (attribute_value) {
                                attribute_value = attribute_value.entity_id;
                            }
                            input_element = function (local_attribute) {
                                var addButton = '';
                                if (context.state.entity_attributes.reference_entities[local_attribute.db_type] && context.state.entity_attributes.reference_entities[local_attribute.db_type].entity_access && (context.state.entity_attributes.reference_entities[local_attribute.db_type].entity_access.indexOf('c') != -1)) { //add a + sign.
                                    var add_func = function () {
                                        context.reference_entity_handler(local_attribute)
                                    };
                                    if (!(local_attribute.read_only || context.form_is_read_only())) {
                                        addButton = <button className="edit-controls-add-button dx-button"
                                                            onClick={add_func}>
                                            <div className='dx-button-content'><i
                                                className='dx-icon dx-icon-edit-button-addrow'></i></div>
                                        </button>;
                                    }
                                }
                                return (<div><EditorDevExtremeSelectBox dataSource={attribute.select_options}
                                                                        displayExpr="caption"
                                                                        valueExpr="entity_id"
                                                                        value={attribute_value}
                                                                        onChange={context.on_field_change.bind(context, attribute)}
                                                                        add_button={addButton==''?false:true}
                                                                        readOnly={(attribute.read_only || context.form_is_read_only())}/>
                                    {addButton}
                                </div>)
                            }(attribute);
                        }
                    }
                }
                else if (attribute.list_of_values) {
                    var list_of_values = attribute.list_of_values;

                    if (attribute.is_array) {
                        input_element =
                            <EditorDevExtremeTagBox dataSource={list_of_values} displayExpr="value" valueExpr="code"
                                                    values={attribute_value}
                                                    onChange={context.on_field_change.bind(context, attribute)}
                                                    readOnly={(attribute.read_only || this.form_is_read_only())}/>;
                    }
                    else {
                        var options = [];
                        for (var j = 0; j < list_of_values.length; j++) {
                            var lov = list_of_values[j];

                            var option = <option key={lov.code} value={lov.code}>{lov.value}</option>;

                            options.push(option);
                        }
                        input_element =
                            <select className="form-control" defaultValue={attribute_value} key={attribute.field_path}
                                    readOnly={(attribute.read_only || this.form_is_read_only())} disabled={(attribute.read_only || this.form_is_read_only())}
                                    onChange={this.on_field_change.bind(this, attribute)}>
                                {options}
                            </select>;
                        if (attribute_value == null && list_of_values.length > 0) {
                            var lov = list_of_values[0];
                            this.state.update_fields[attribute.field_path] = lov.code;
                            this.state.entity_instance[attribute.field_path] = lov.code;
                        }
                    }
                }
                else {
                    var type = attribute.attribute_type == null ? attribute.db_type : attribute.attribute_type;
                    switch (type) {
                        case "Image":
                            var resizeWidth = null;
                            var resizeHeight = null;
                            if (attribute.custom_properties) {
                                if (attribute.custom_properties.max_height && attribute.custom_properties.max_width) {
                                    resizeWidth = attribute.custom_properties.max_width;
                                    resizeHeight = attribute.custom_properties.max_height;
                                }
                            }
                            input_element =
                                <EditorDevExtremeImageUpload onChange={this.on_field_change.bind(this, attribute)}
                                                             value={attribute_value} readOnly={(attribute.read_only || this.form_is_read_only())}
                                                             resizeWidth={resizeWidth} resizeHeight={resizeHeight}
                                                             key={attribute.field_path}
                                                             selectButtonText={R_loc.select_image}/>;
                            break;
                        case "File":
                            input_element =
                                <EditorFileUpload
                                    onFileNameChange={this.on_file_name_change.bind(this, attribute)}
                                    onChange={this.on_field_change.bind(this, attribute)}
                                    fileName={this.state.entity_instance["file_name"]}
                                    value={this.state.entity_instance._id}
                                    resetFileUpload={this.state.resetFileUpload}
                                    key={attribute.field_path}
                                    readOnly={(attribute.read_only || this.form_is_read_only())}/>;
                            break;
                        case "Html":
                            input_element =
                                <EditorHtml onChange={this.on_field_change.bind(this, attribute)}
                                            key={attribute.field_path}
                                            value={attribute_value} readOnly={(attribute.read_only || this.form_is_read_only())}/>;
                            break;
                        case "YesNo":
                            input_element =
                                <EditorDevExtremeSwitchYesNo onChange={this.on_field_change.bind(this, attribute)}
                                                             key={attribute.field_path}
                                                             value={attribute_value} readOnly={(attribute.read_only || this.form_is_read_only())}/>;
                            break;
                        case "Boolean":
                            input_element = <label>
                                <input className="form-control checkbox-slider colored-blue" type="checkbox"
                                       key={attribute.field_path}
                                       onChange={this.on_field_change.bind(this, attribute)} checked={attribute_value}
                                       readOnly={(attribute.read_only || this.form_is_read_only())} disabled={(attribute.read_only || this.form_is_read_only())}/>
                                <span className="text"></span>
                            </label>;
                            break;
                        case "CheckBox":
                            input_element =
                                <EditorDevExtremeCheckBox onChange={this.on_field_change.bind(this, attribute)}
                                                          key={attribute.field_path}
                                                          value={attribute_value} readOnly={(attribute.read_only || this.form_is_read_only())}/>;
                            break;
                        case "MaskedTextBox":
                            var mask_data = attribute.mask_data || {};
                            var mask_rules = mask_data.mask_rules;
                            if (mask_rules) {
                                for (var field_name in mask_rules) {
                                    mask_rules[field_name] = try_parse_regexp(mask_rules[field_name]);
                                }
                            }
                            input_element =
                                <EditorDevExtremeMaskedTextBox onChange={this.on_field_change.bind(this, attribute)}
                                                               key={attribute.field_path}
                                                               value={attribute_value} readOnly={(attribute.read_only || this.form_is_read_only())}
                                                               mask={mask_data.mask} maskRules={mask_data.mask_rules}
                                                               maskChar={mask_data.mask_char || "_"}
                                                               useMaskedValue={mask_data.use_masked_value}
                                                               rtlEnabled={attribute.right_to_left || false}/>;
                            break;
                        case "Recurrent":
                            input_element =
                                <EditorRecurrenceInput onChange={this.on_field_change.bind(this, attribute)}
                                                       value={attribute_value} readOnly={(attribute.read_only || this.form_is_read_only())}
                                                       key={attribute.field_path}
                                                       placeholder={attribute.caption}/>;
                            break;
                        case "MultiLineTextBox":
                            input_element =
                                <EditorDevExtremeMultiLineTextBox onChange={this.on_field_change.bind(this, attribute)}
                                                                  value={attribute_value} readOnly={(attribute.read_only || this.form_is_read_only())}
                                                                  key={attribute.field_path}
                                                                  placeholder={attribute.caption}/>;
                            break;
                        case "Date":
                            input_element =
                                <EditorDevExtremeDatePicker onChange={this.on_field_change.bind(this, attribute)}
                                                            key={attribute.field_path}
                                                            value={attribute_value} readOnly={(attribute.read_only || this.form_is_read_only())}/>;
                            break;
                        case "DateTime":
                            input_element =
                                <EditorDevExtremeDateTimePicker onChange={this.on_field_change.bind(this, attribute)}
                                                                value={attribute_value}
                                                                key={attribute.field_path}
                                                                readOnly={(attribute.read_only || this.form_is_read_only())}/>;
                            break;
                        case "Time":
                            input_element =
                                <EditorDevExtremeTimePicker onChange={this.on_field_change.bind(this, attribute)}
                                                            key={attribute.field_path}
                                                            value={attribute_value} readOnly={(attribute.read_only || this.form_is_read_only())}/>;
                            break;
                        default:
                            var style = {};
                            if (attribute_caption_image) {
                                style = {
                                    "backgroundImage": "url(" + attribute_caption_image + ")",
                                    "backgroundRepeat": "no-repeat",
                                    "backgroundSize": "34px auto",
                                    "textIndent": "28px"
                                };
                            }
                            input_element = <input type="text" className="form-control" key={attribute.field_path}
                                                   placeholder={attribute.caption} value={attribute_value}
                                                   onBlur={this.on_field_blur.bind(this, attribute)}
                                                   onChange={this.on_field_change.bind(this, attribute)}
                                                   readOnly={(attribute.read_only || this.form_is_read_only())}
                                                   style={style}/>;
                            break;
                    }
                }
                var info_id = "info_" + attribute.field_path.replace(/:/g, '-');

                var caption_label = "";
                if (!omit_caption) {
                    if (!same_row) {
                        caption_label =
                            <div className="col-sm-2 no-padding-right" style={{"textAlign":"right"}}><label id={info_id}
                                                                                                            className="control-label">{attribute.caption}</label>
                            </div>;
                    }
                    else {
                        caption_label = <div className="col-sm-2 no-padding-right"
                                             style={{"textAlign":"right"}}>
                            <label id={info_id} className="control-label">{attribute.caption}</label></div>;
                    }
                    console.log(info_id);
                }

                var class_name = "col-sm-10";
                if (omit_caption) {
                    class_name = "col-sm-12";
                }

                var input_div;
                if (!same_row) {

                    if (row_name) { //ending a previous row
                        input_div = <div className="form-group"
                                         key={"QK-input-wrapper-row-name-"+row_name.toString()+"-"+info_id}>{row_array}</div>;
                        input_array.push(input_div);
                        row_array = null;
                        row_name = null;
                    }
                    input_div = <div className="form-group" key={"QK-input-wrapper-"+info_id}>
                        {caption_label}

                        <div className={class_name}>
                            {input_element}
                        </div>
                    </div>;
                    input_array.push(input_div);
                    info_id += "_desc";
                    input_array.push(<div id={info_id}
                                          key={"QK-input-wrapper-info-id"+info_id}>{attribute.description}</div>);
                }
                else { //same_row
                    var rowsCount = 1;
                    if (same_row_count == 2) {
                        rowsCount = 4;
                    }
                    if (same_row_count == 3) {
                        rowsCount = 2;
                    }
                    if (omit_caption) {
                        rowsCount = rowsCount + 2;
                    }
                    class_name = "col-sm-" + rowsCount;
                    input_div = <div key={"QK-row-array-"+info_id}>
                        {caption_label}

                        <div className={class_name}>
                            {input_element}
                        </div>
                    </div>;
                    row_array.push(input_div);
                    info_id += "_desc";
                    row_array.push(<div id={info_id}
                                        key={"QK-row-array-info-id"+info_id}>{attribute.description}</div>);
                }

            }
            if (row_name) { //ending a previous row
                input_div = <div className="form-group"
                                 key={"QK-input-wrapper-row-name-"+row_name.toString()+"-"}>{row_array}</div>;
                input_array.push(input_div);
                row_array = null;
                row_name = null;
            }
        }

        var error_component = "";
        if (this.state.error) {
            error_component =
                <div><ErrorNotificationComponent message={this.state.error} on_close={this.on_error_close}/><br />
                </div>;
        }

        var app_object_name, caption;
        if ((this.state.caption_function)) {
            if (this.state.entity_instance) {
                caption = this.state.caption_function(this.state.entity_instance);
            }
            else {
                caption = this.state.caption_function(this.state.entity_attributes.attributes);
            }
        }
        if (this.state.app_object && this.state.app_object.name) {
            app_object_name = this.state.app_object.name;
        }
        if (!caption || (caption.trim() == "")) {
            caption = null;
        }
        if(this.state.entity_attributes && this.state.entity_attributes.caption_singular) {
            caption = this.state.entity_attributes.caption_singular + ": " + (caption || " ");
        }
        var workflow_status = "";
        var workflow_comment_popup = "";
        var skip_save = (this.form_is_read_only() || (this.state.app_object && this.state.app_object.disable_save) || (this.state.is_new_instance && this.state.workflow_states)); //workflow-enabled entities must declare a constructor.

        var close_button = (<button onClick={this.handle_close} className="btn btn-close">{'X'}</button>);
        var form_buttons = [];

        if (this.state.is_new_instance) {
            if (!caption || (caption.trim() == "")) {
                caption = app_object_name;
            }
            else {
                caption = R.label_new + " " + caption;
            }

            if (this.state.workflow_states) {
                for (var status in this.state.workflow_states) {
                    var workflow_state = this.state.workflow_states[status];
                    if (workflow_state.constructors) {
                        for (var workflow_constructor_key in workflow_state.constructors) {
                            var workflow_constructor = workflow_state.constructors[workflow_constructor_key];
                            skip_save = true;
                            workflow_constructor.name = workflow_constructor_key;
                            form_buttons.push(<button disabled={this.state.areButtonsDisabled}
                                                      onClick={this.handle_workflow_action_click.bind(this, workflow_constructor, status)}
                                                      key={"QK-form-buttons-workflow-"+workflow_constructor_key}
                                                      className="btn btn-primary shiny workflow_button_margin">{workflow_constructor.button_caption}</button>);
                        }
                    }
                }
            }
        }
        else {
            if (this.state.workflow_states) {
                var entity_instance_status = this.state.entity_instance.workflow_status;
                if (entity_instance_status) {
                    for (var status in this.state.workflow_states) {
                        var workflow_state = this.state.workflow_states[status];

                        if (status == entity_instance_status) {
                            workflow_status = workflow_state.caption;
                            for (var workflow_action_status in workflow_state.workflow_actions) {
                                var workflow_action = workflow_state.workflow_actions[workflow_action_status];
                                if (workflow_action.button_caption) {
                                    form_buttons.push(<button disabled={this.state.areButtonsDisabled}
                                                              onClick={this.handle_workflow_action_click.bind(this, workflow_action, workflow_action_status)}
                                                              key={"QK-form-buttons-workflow-action-"+workflow_action.button_caption}
                                                              className="btn btn-primary shiny workflow_button_margin">{workflow_action.button_caption}</button>);
                                }
                            }
                            break;
                        }
                    }
                }
            }
            if (!(this.form_is_read_only() || (this.state.app_object && this.state.app_object.custom_properties && this.state.app_object.custom_properties.disable_delete))) {
                form_buttons.push(<button onClick={this.handle_delete} disabled={this.state.areButtonsDisabled}
                                          key={"QK-form-buttons-delete"}
                                          className="btn btn-danger shiny workflow_button_margin">{R.label_delete}</button>);
            }
        }

        if (!skip_save) {
            if (this.props.data && this.props.data.parent_react_entity) {
                form_buttons.unshift(<button onClick={this.handle_submit} disabled={this.state.areButtonsDisabled}
                                             className="btn btn-success shiny workflow_button_margin"
                                             key="QK-form-buttons-save">{R.label_save}</button>);
            }
            else {
                form_buttons.unshift(<button type="submit" disabled={this.state.areButtonsDisabled}
                                             className="btn btn-success shiny workflow_button_margin"
                                             key="QK-form-buttons-save">{R.label_save}</button>);
            }
        }

        if (workflow_status && (workflow_status.trim() != "")) {
            workflow_status = "(" + workflow_status + ")";
        }

        var cancel_button = (
            <button onClick={this.handle_close} className="btn btn-default shiny"
                    disabled={this.state.areButtonsDisabled} key={"QK-form-buttons-cancel"}>{R.label_cancel}</button>);

        form_buttons.push(cancel_button);

        if (this.state.workflow_states) {
            workflow_comment_popup = <div id="workflow_comment_popup">
                <textarea id="workflow_comment_text" rows="10" cols="42" placeholder="Please enter comment..."/>
                <br/>
                <button id="workflow_comment_popup_submit"
                        className="btn btn-success shiny workflow_button_margin">{R.label_save}</button>
                <button id="workflow_comment_popup_cancel"
                        className="btn btn-primary shiny workflow_button_margin">{R.label_cancel}</button>
            </div>;
        }

        var app_object_desc = (this.state.app_object ? this.state.app_object.description : "") || "";
        if (app_object_desc.length > 0) {
            app_object_desc = <div>{app_object_desc}</div>;
        }
        var uploadFileModal = null;
        if (this.state.uploadFileModal) {
            uploadFileModal =
                <EditorFileUploadModel file={this.state.fileToUpload} fileId={this.state.entity_instance._id}
                                       onUploadEnd={this.on_file_upload_end.bind(this, attribute)}
                                       onUploadCancel={this.on_file_upload_cancel.bind(this, attribute)}/>
        }
        return (<div className="widget">
            {uploadFileModal}
            <div className="widget-header bordered-bottom bordered-palegreen">
                <div className="col-sm-6 widget-caption"><span className="widget-caption"><b>{caption || app_object_name || " "}</b> <span
                    className="status-caption">{workflow_status}</span></span></div>
                <div className="col-sm-6 widget-caption" style={{"textAlign":"right", "paddingRight":"0px"}}></div>
                <div className="widget-buttons"><span
                    className="widget-caption">{close_button}</span></div>
            </div>
            <div className="widget-body">
                <div>
                    {error_component}
                    <div>
                        {app_object_desc}
                    </div>
                    <hr className="wide"/>
                    {edit_form}
                    <form id="edit_form" className="form-horizontal form-bordered" role="form"
                          onSubmit={this.handle_submit}>
                        {input_array}
                        <div className="form-group">
                            <div className="col-sm-12" style={{"textAlign":"right", "paddingRight":"20px"}}>
                                {form_buttons}
                            </div>
                        </div>
                    </form>
                    {workflow_comment_popup}
                </div>
            </div>
        </div>);
    }
});
