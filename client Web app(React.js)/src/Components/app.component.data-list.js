var DataListComponent = React.createClass({
    getInitialState: function () {
        var state = this.initialize_data_list();

        if (!state) { //placeholder while waiting for response.
            state = {};
        }
        state.grid_id = makeid();
        state.scrollViewContentElementId = makeid();
        return state;
    },
    initialize_data_list: function () {
        if (this.props.skip_init) {
            var state_object = {};
            state_object.app_object = this.props.app_object;
            state_object.entity_attributes = this.props.entity_attributes;
            state_object.entity_instances = this.props.entity_instances;
            return state_object;
        }

        var request = {};
        request.entity = "app_object";
        request.method = "get_by_code";
        request.data = {app_object_code: this.props.app_object_code, conditions: this.props.conditions};

        var context = this;

        console.log(JSON.stringify(request));

        do_authenticated_http_call({
            method: "POST",
            url: "/api/entity/invoke_method",
            contentType: "application/json",
            dataType: 'json',
            data: JSON.stringify(request),
            success: function (data) {
                console.log(data.data);
                context.setState({
                    app_object: data.data.app_object,
                    entity_attributes: data.data.entity_attributes,
                    entity_instances: []
                });
                push_href_location(data.data.app_object.name + " - " + (R.client_application_title || "Xenforma"), "/data_list?code=" + data.data.app_object.code);
            },
            error: function (error) {
                if (error.responseJSON) {
                    console.log(error.responseJSON);
                    context.setState({error: error.responseJSON.message});
                }
            }
        });
    },
    handle_close: function (event) {
        if (event && event.preventDefault) {
            event.preventDefault();
        }

        var context = this;

        current_navigation_listener = undefined;
        context.props.navigation_handler("home");
    },
    updateDimensions: function () {
        $("#" + this.state.grid_id).dxDataGrid("instance").repaint()
    },
    componentDidMount: function () {
        window.addEventListener("resize", this.updateDimensions);
        if (this.state.app_object) {
            this.init_grid();
        }
        $(this.popupContentElement).dxScrollView({
            useNative: true,
            scrollByThumb: true
        }).dxScrollView("instance");
        this.popup = $(this.popupElement).dxPopup({
            showTitle: true,
        }).dxPopup("instance");
    },
    componentWillUnmount: function () {
        $(this.popupElement).remove();
        window.removeEventListener("resize", this.updateDimensions);
    },
    componentDidUpdate: function () {
        if (this.state.app_object && !this.props.skip_init) {
            this.init_grid();
        }
    },
    init_grid: function () {
        var grid_properties = {};
        var context = this;
        //grid_properties.dataSource = this.state.entity_instances;

        var send_data = {
            conditions: {},
            last_id: undefined
        };

        var error_func = function (error) {
            if (error.responseJSON) {
                console.log(error.responseJSON);
                context.setState({error: error.responseJSON.message});
            }
        };
        var data_source;
        if (this.props.skip_init) {
            data_source = this.state.entity_instances;
        }
        else {
            data_source = new DevExpress.data.DataSource({
                load: function (load_options) {
                    var d = new $.Deferred();
                    var success_func = function (data) {
                        console.log(data);
                        if (!load_options.sort) {
                            var grid_length = data.entity_instances.length;
                            if ((grid_length > 0) && data.entity_instances[grid_length - 1]._id) {
                                send_data.last_id = data.entity_instances[grid_length - 1]._id;
                            }
                        }
                        d.resolve(data.entity_instances);
                    };
                    if (!load_options.skip) { //null or 0 --happens when filter is changed.
                        send_data.last_id = undefined;
                        send_data.skip = undefined;
                        var conditions = context.props.conditions || {};
                        if (load_options.filter && Array.isArray(load_options.filter)) {
                            send_data.conditions = make_conditions_from_devextreme_filter(load_options.filter) || {};
                            for (var field_path in conditions) { //these filters override custom filters
                                send_data.conditions[field_path] = conditions[field_path];
                            }
                        }
                        else {
                            send_data.conditions = conditions;
                        }
                        if (load_options.sort && Array.isArray(load_options.sort)) {
                            send_data.sort = make_sorting_conditions_from_devextreme_sort(load_options.sort);
                        }
                        else {
                            send_data.sort = undefined;
                        }
                    }
                    else {
                        if (load_options.sort && Array.isArray(load_options.sort)) {
                            send_data.skip = load_options.skip;
                            send_data.last_id = undefined; //not used in sorted paging
                        }
                    }
                    context.state.export_grid_conditions = send_data.conditions;
                    invoke_method(context.state.entity_attributes.entity, "find_paged", send_data, success_func, error_func);
                    return d.promise();
                },
                remove: function (key) {
                    $("#" + context.state.grid_id).dxDataGrid("instance").refresh();
                },
                insert: function (values) {
                    $("#" + context.state.grid_id).dxDataGrid("instance").refresh();
                },
                update: function (key, values) {
                    $("#" + context.state.grid_id).dxDataGrid("instance").refresh();
                }
            });
            grid_properties.paging = {pageSize: this.state.app_object.page_size || 10}; //infinite scroll requires pageSize.
        }

        var custom_properties = this.state.app_object.custom_properties ? this.state.app_object.custom_properties : {};
        var multiSelect = custom_properties.grid_multi_select == undefined ? false : custom_properties.grid_multi_select;

        grid_properties.dataSource = data_source;
        if (!this.props.skip_init) {
            grid_properties.height = function (e) {
                return $(window).height() - 180;
            };
        }
        grid_properties.loadPanel = true;
        grid_properties.columnAutoWidth = true;
        grid_properties.allowColumnResizing = true;
        grid_properties.allowColumnReordering = true;
        grid_properties.showRowLines = true;
        grid_properties.wordWrapEnabled = true;
        grid_properties.rowAlternationEnabled = true;
        grid_properties.headerFilter = {visible: true};
        grid_properties.searchPanel = {visible: !this.props.skip_init};
        grid_properties.filterRow = {visible: !this.props.skip_init};
        grid_properties.export = {
            enabled: !this.props.skip_init,
            fileName: this.state.app_object.name,
            allowExportSelectedData: multiSelect
        };
        grid_properties.stateSorting = {enabled: true, type: 'localStorage', storageKey: 'xenforma'};
        grid_properties.scrolling = {mode: "infinite"};
        grid_properties.columns = this.state.columns;
        grid_properties.selection = {
            mode: multiSelect ? "multiple" : "none",
            showCheckBoxesMode: multiSelect ? "always" : "none"
        };
        grid_properties.onEditorPreparing = function (info) {
            if (info.parentType == 'filterRow') {
                if (info.format == 'shortTime') {
                    info.editorOptions.format = "time";
                } else if (info.format == 'shortdateshorttime') {
                    info.editorOptions.format = "datetime";
                }
            }
        };
        var nested_popup_func;

        if (grid_properties.export.enabled && custom_properties.custom_export) {
            grid_properties.onContentReady = function () {
                $('.dx-datagrid-export-button').each(function () {
                    if (typeof $(this).data("custom-export") == "undefined") {
                        $(this).data("custom-export", '');
                        $("#" + context.state.grid_id).dxDataGrid("instance")._controllers.export.selectionOnly(true);
                        $(this).on("click", function () {
                            // hack for devExtreme dxContextMenu
                            if ($("#" + context.state.grid_id).dxDataGrid("instance")._views.headerPanel._exportContextMenu._dataAdapter.getData().lenght == 3)
                                return; // we already added an item
                            $("#" + context.state.grid_id).dxDataGrid("instance")._views.headerPanel._exportContextMenu._dataAdapter.getData().push({
                                icon: "doc",
                                text: custom_properties.custom_export.caption,
                                internalFields: {childrenKeys: []}
                            });
                            $('.dx-datagrid-export-menu .dx-submenu ul.dx-menu-items-container').each(function () {
                                var children = this.children[0];
                                $(children).after('<li class="dx-menu-item-wrapper dx-menu-item-wrapper-custom-import"><div class="dx-item dx-menu-item dx-menu-item-has-text dx-menu-item-has-icon"><div class="dx-item-content dx-menu-item-content"><i class="dx-icon dx-icon-doc"></i><span class="dx-menu-item-text">' + custom_properties.custom_export.caption + '</span></div></div></li>');
                            });
                            $('.dx-menu-item-wrapper-custom-import').on("click", function (e) {
                                // to prevent devExtreme event handler
                                e.stopPropagation();
                                send_data = {};
                                send_data.format = custom_properties.custom_export.format;
                                if ($("#" + context.state.grid_id).dxDataGrid("instance")._views.headerPanel._exportController.selectionOnly()) {
                                    send_data.data = $("#" + context.state.grid_id).dxDataGrid("instance").getSelectedRowKeys().map(
                                        function (currentValue, index, arr) {
                                            return currentValue._id;
                                        }
                                    );
                                }
                                else {
                                    send_data.gridConditions = context.stats.export_grid_conditions;
                                }
                                var error = function (err) {
                                    context.setState({error: err.message});
                                };
                                var success = function (data) {
                                    $("#" + context.state.grid_id).dxDataGrid("instance")._views.headerPanel._exportContextMenu.hide();
                                };
                                invoke_method(context.state.entity_attributes.entity, "custom_export", send_data, success, error);
                            });
                        });
                    }
                });
            };
        }

        if (this.state.app_object.edit_app_object) {
            if (this.props.skip_init) {
                if (!custom_properties.edit_in_grid) {
                    nested_popup_func = function (data_row, is_new) {
                        var app_object = {};
                        app_object.code = context.state.app_object.edit_app_object;
                        app_object.type = "edit_form";
                        app_object._id = data_row._id;
                        app_object = $.extend({}, context.state.app_object, app_object);
                        var data = {};
                        data.app_object = app_object;
                        data.entity_attributes = context.state.entity_attributes;
                        data.nested_list_entities = context.props.nested_list_entities;
                        data.workflow_states = app_object.workflow_states;
                        data.entity_instance = data_row;
                        data.parent_entity_array = context.props.parent_entity_array;
                        data.parent_entity_field_attribute = context.props.parent_entity_field_attribute;
                        data.parent_react_entity = context.props.parent_react_entity;
                        data.is_new_instance = is_new;
                        return context.props.nested_entity_handler(data);
                    };
                    grid_properties.onEditingStart = function (event) {
                        event.cancel = true;
                        var data_row = event.data;
                        if (data_row._id) {
                            data_row = $.extend(true, {}, data_row);
                            return nested_popup_func(data_row);
                        }
                    };
                }
            }
            else {
                grid_properties.onEditingStart = function (event) {
                    event.cancel = true;
                    var data_row = event.data;

                    if (data_row._id) {
                        var app_object = {};
                        app_object.code = context.state.app_object.edit_app_object;
                        app_object.type = "edit_form";
                        app_object._id = data_row._id;
                        context.props.app_object_handler(app_object);
                    }
                };

                var temp_grid_properties = $.extend({}, grid_properties);
                context.state.entity_instances = context.state.entity_instances || [];
                temp_grid_properties.dataSource = context.state.entity_instances;
                temp_grid_properties.visible = false;
                $("#" + "hidden_export_" + this.state.grid_id).dxDataGrid(temp_grid_properties);

                var export_success_func = function (data) {
                    context.state.entity_instances.length = 0; //delete all of these
                    context.state.entity_instances.push.apply(context.state.entity_instances, data.entity_instances);
                    var temp_grid = $("#" + "hidden_export_" + context.state.grid_id).dxDataGrid("instance");
                    temp_grid.refresh();
                    temp_grid.selectAll();
                    temp_grid.exportToExcel(true);
                    $("#loader").dxLoadPanel({
                        message: R.label_loading,
                        showIndicator: false,
                        visible: false,
                        position: {of: "#" + context.state.grid_id},
                        shadingColor: "rgba(0,0,0,0.4)"
                    });
                };

                var export_error_func = function (error) {
                    if (error.responseJSON) {
                        console.log([R.client_export_to_excel_failed, error.responseJSON]);
                        context.setState({error: error.responseJSON.message});
                    }
                };

                grid_properties.onExporting = function (event) {
                    event.cancel = true;
                    $("#loader").dxLoadPanel({
                        message: R.label_loading,
                        showIndicator: false,
                        visible: true,
                        position: {of: "#" + context.state.grid_id},
                        shadingColor: "rgba(0,0,0,0.4)"
                    });

                    invoke_method(context.state.entity_attributes.entity, "find_all", send_data, export_success_func, export_error_func);
                };
            }
        }

        for (var property in custom_properties) {
            grid_properties[property] = custom_properties[property];
        }


        grid_properties.editing = {
            editMode: "row",
            texts: {
                editRow: (custom_properties.editable) ? R.edit : R.view
            },
            editEnabled: true,
            removeEnabled: ((!custom_properties.disable_delete) && (custom_properties.editable)),
            insertEnabled: ((!custom_properties.disable_create) && (custom_properties.editable))
        };

        if (!this.props.skip_init) {
            var success = function (data) {
                if (context.state.entity_attributes && context.state.entity_attributes.entity == 'translation') {
                    load_translations(false, context.forceUpdate);
                }
                Notify(data.message, 'bottom-right', 20000, 'green', 'fa-check', true);
            };

            var complete = function () {
                $("#loader").dxLoadPanel({
                    message: R.label_loading,
                    showIndicator: false,
                    visible: false,
                    position: {of: "#" + context.state.grid_id},
                    shadingColor: "rgba(0,0,0,0.4)"
                });
            };

            grid_properties.onRowUpdating = function (e) {
                $("#loader").dxLoadPanel({
                    message: R.label_loading,
                    showIndicator: false,
                    visible: true,
                    position: {of: "#" + context.state.grid_id},
                    shadingColor: "rgba(0,0,0,0.4)"
                });

                var _id = e.key._id;
                var update_data = e.newData;
                update_data._id = _id;

                var entity = context.state.entity_attributes.entity;

                var error = function (err) {
                    context.setState({error: err.message});
                    e.component.cancelEditData();
                };

                invoke_method(entity, "upsert", update_data, success, error, complete);
                return true;
            };

            grid_properties.onRowInserting = function (e) {
                $("#loader").dxLoadPanel({
                    message: R.label_loading,
                    showIndicator: false,
                    visible: true,
                    position: {of: "#" + context.state.grid_id},
                    shadingColor: "rgba(0,0,0,0.4)"
                });

                var document = e.data;

                var entity = context.state.entity_attributes.entity;

                var error = function (err) {
                    context.setState({error: err.message});
                    e.component.cancelEditData();
                };

                invoke_method(entity, "upsert", document, success, error, complete);
            };

            grid_properties.onRowRemoving = function (e) {
                $("#loader").dxLoadPanel({
                    message: R.label_loading,
                    showIndicator: false,
                    visible: true,
                    position: {of: "#" + context.state.grid_id},
                    shadingColor: "rgba(0,0,0,0.4)"
                });

                var _id = e.data._id;

                var entity = context.state.entity_attributes.entity;

                var error = function (err) {
                    context.setState({error: err.message});
                };

                invoke_method(entity, "delete", {_id: _id}, success, error, complete);
            };

            grid_properties.onContentReady = function (e) {
                $("#" + context.state.grid_id + ' *[aria-label="edit-button-addrow"]').click(function () {
                    var app_object = {};
                    app_object.code = context.state.app_object.edit_app_object;
                    app_object.type = "edit_form";
                    context.props.app_object_handler(app_object);
                });
            };
        }
        else {
            var on_change = function (e) {
                if (context.props.on_change) {
                    context.props.on_change(context.state.entity_instances);
                }
            };

            grid_properties.onRowUpdating = on_change;
            grid_properties.onRowInserting = on_change;
            grid_properties.onRowRemoving = on_change;
            if (nested_popup_func) {
                grid_properties.onContentReady = function (e) {
                    $("#" + context.state.grid_id + ' *[aria-label="edit-button-addrow"]').click(function () {
                        var data_row = {};
                        return nested_popup_func(data_row, true);
                    });
                };
            }
        }


        $("#" + this.state.grid_id).dxDataGrid(grid_properties);
    },
    render: function () {
        var columns = [];
        var context = this;
        for (var i = 0; this.state.entity_attributes && i < this.state.entity_attributes.attributes.length; i++) {
            var attribute = this.state.entity_attributes.attributes[i];

            if (!attribute.list_visible) {
                continue;
            }

            if (attribute.type == "Mixed" || attribute.is_array) {
                continue;
            }

            var column = {dataField: attribute.field_path, caption: attribute.caption};

            if (attribute.list_of_values) {
                var tempAttribute = attribute.list_of_values;
                column.allowHeaderFiltering = true;
                column.headerFilter = {
                    dataSource: {
                        load: function (loadOptions) {
                            var res = [];
                            for (var i = 0; i < tempAttribute.length; i++) {
                                res.push({text: tempAttribute[i].value, value: tempAttribute[i].code});
                            }
                            return res;
                        }
                    }
                }
            }
            else {
                column.allowHeaderFiltering = false;
            }

            var type = attribute.attribute_type == null ? attribute.db_type : attribute.attribute_type;

            switch (type) {
                case "Image":
                    column.cellTemplate = function (container, options) {
                        if (options.value) {
                            $("<img style='max-width:50px;max-height:50px'/>")
                                .attr("src", options.value)
                                .appendTo(container);
                        }
                    };
                    column.allowFiltering = false;
                    break;
                case "Recurrent":
                    column.allowFiltering = false;
                    column.cellTemplate = function (container, options) {
                        if (options.value) {
                            var value = options.value;
                            if (value.slice(0, 6) == 'RRULE:') {
                                value = value.substr(6);
                            }
                            (container).html(RRule.fromString(value).toText());
                        }
                    };
                    break;
                case "Html":
                    column.cellTemplate = function (container, options) {
                        if (options.value && options.value != '') {
                            $("<a href='#'>View</a>")
                                .on('click', function (e) {
                                    context.popup.option('title', options.column.caption);
                                    context.popup.show();
                                    $('#' + context.state.scrollViewContentElementId).html(options.value);
                                    e.preventDefault();
                                })
                                .appendTo(container);
                        }
                    }
                    break;
                case "YesNo":
                    column.falseText = R.label_no;
                    column.trueText = R.label_yes;
                    column.cellTemplate = function (container, options) {
                        if (options.value && options.value != '') {
                            (container).html(R.label_yes);
                        }
                        else {
                            (container).html(R.label_no);
                        }
                    }
                    break;
                case "Boolean":
                    break;
                case "CheckBox":
                    break;
                case "MaskedTextBox":
                    break;
                case "MultiLineTextBox":
                    // https://www.devexpress.com/Support/Center/Question/Details/T298005
                    break;
                case "Date":
                    column.dataType = 'date';
                    break;
                case "DateTime":
                    var tempColumn = column;
                    column.format = 'shortdateshorttime';
                    column.dataType = 'date';
                    column.calculateFilterExpression = function (filterValue, selectedFilterOperation) {
                        filterValue.setSeconds(0);
                        return [function () {
                            return tempColumn.dataField
                        }, selectedFilterOperation || '=', filterValue];
                    };
                    break;
                case "Time":
                    column.format = 'shortTime';
                    column.dataType = 'date';
                    break;
                case "user":
                {
                    if (attribute.is_nested_entity && attribute.data_is_nested == false && !attribute.is_array) {
                        column.cellTemplate = function (container, options) {
                            if (typeof options.value == "object" && options.value != null) {
                                var name = options.value.caption || options.value.entity_id;
                                container.html(name);
                                if (options.value.image) {
                                    $("<span>&nbsp;</span>").prependTo(container);
                                    $("<img style='max-width:15px;max-height:15px'/>")
                                        .attr("src", options.value.image)
                                        .prependTo(container);
                                }
                                options.displayValue = name;
                            } else {
                                return options.value;
                            }
                        }
                    }
                    break;
                }
                case "ref_sub":
                {
                    column.cellTemplate = function (container, options) {
                        if (typeof options.value == "object" && options.value != null) {
                            var name = options.value.caption || options.value.entity_id;
                            container.html(name);
                            options.displayValue = name;
                        } else {
                            return options.value;
                        }
                    }
                    break;
                }
                default:
                    break;
            }


            columns.push(column);
        }

        this.state.columns = columns;


        var app_object_name = this.state.app_object ? this.state.app_object.name : "";

        var error_component = "";
        if (this.state.error) {
            error_component =
                <div><ErrorNotificationComponent message={this.state.error} on_close={this.on_error_close}/><br />
                </div>;
        }

        var export_component = "";

        var close_button;

        if (!this.props.skip_init) { //add a close button
            close_button = (<button onClick={this.handle_close} className="btn btn-close">{'X'}</button>);
            close_button = (<div><span className="widget-caption">{close_button}</span></div>);
            export_component = (<div id={"hidden_export_" + this.state.grid_id}/>);
        }
        var context = this;
        return (<div className="widget">
            <div className="widget-header bordered-bottom bordered-palegreen">
                <span className="widget-caption">{app_object_name}</span>
                <div className="widget-buttons">{close_button}</div>
            </div>
            <div className="widget-body">
                {error_component}
                <div id="loader"/>
                <div id={this.state.grid_id}/>
                {export_component}
            </div>
            <div ref={function(ref){context.popupElement = ref}}>
                <div ref={function(ref){context.popupContentElement = ref}}>
                    <div id={this.state.scrollViewContentElementId}/>
                </div>
            </div>
        </div>);
    }
});