var HomeDashboardComponent = React.createClass({
    getInitialState: function () {
        this.initialize_dashboard();
        return {components: {}};
    },
    get_component_by_div_id: function (div_id) {
        for (var key in this.state.components) {
            var component = this.state.components[key];
            if (component.div_id && component.div_id == div_id) {
                return component;
            }
        }
    },
    get_dashboard_part_loc: function (dashboard_part_id) {
        for (var i = 0; i < this.state.home_layout.length; i++) {
            var home_layout = this.state.home_layout[i];

            if (home_layout.dashboard_part_id == dashboard_part_id) {
                return home_layout;
            }
        }
    },
    componentDidUpdate: function () {

        if (Object.keys(this.state.components).length == 0 || !this.state.home_layout || this.state.gridstack) {
            return;
        }

        var context = this;

        var update_home_layout = function () {
            if (context.state.gridstack) {
                var arr = [];
                $('.grid-stack-item').each(function () {
                    var node = $(this).data('_gridstack_node');
                    if (node == null) {
                        return;
                    }
                    var dx_id = this.children[0].children[0].getAttribute('id');
                    if (dx_id) {
                        var component = context.get_component_by_div_id(dx_id);
                        if (component == null) {
                            return;
                        }
                        var dashboard_part_id = component.dashboard_part._id;
                        arr.push(
                            {
                                dashboard_part_id: dashboard_part_id,
                                x: node.x,
                                y: node.y,
                                width: node.width,
                                height: node.height
                            }
                        );
                    }
                });
                invoke_method("user", "update_home_layout", arr);
            }
        };

        var options = {
            animate: true,
            float: true,
            width: 12,
            cell_height: 80,
            vertical_margin: 10
        };

        var gridstack = $("#home_grid").gridstack(options).data('gridstack');

        $('#home_grid').on('dragstop', function (event, ui) {
            update_home_layout();
        });

        $('#home_grid').on('resizestop', function (event, ui) {
            var dx_id = ui.element[0].children[0].children[0].getAttribute('id');
            if (dx_id) {
                var component = context.get_component_by_div_id(dx_id);
                if (component && component.visual_control && component.visual_control.render) {
                    component.visual_control.render({force: true, animated: false});
                }
            }
            update_home_layout();
        });

        for (var key in this.state.components) {
            var component = this.state.components[key];

            var dp_width = component.dashboard_part.width;
            var dp_height = component.dashboard_part.height;
            var dp_x = component.row_number;
            var dp_y = component.col_number;

            var dp_loc = this.get_dashboard_part_loc(component.dashboard_part._id);
            if (dp_loc) {
                dp_width = dp_loc.width;
                dp_height = dp_loc.height;
                dp_x = dp_loc.x;
                dp_y = dp_loc.y;
            }

            if (typeof dp_x != 'undefined' && typeof dp_y != 'undefined') {
                gridstack.add_widget('<div class="widget no-header dashboard-part"><div class="grid-stack-item-content widget-body"><div id="' + component.div_id + '"></div></div></div>', dp_x, dp_y, dp_width, dp_height, false);
            }
            else {
                gridstack.add_widget('<div class="widget no-header dashboard-part"><div class="grid-stack-item-content widget-body"><div id="' + component.div_id + '"></div></div></div>', 0, 0, dp_width, dp_height, true);
            }

            var jquery_element = $("#" + component.div_id);

            var type = component.dashboard_part.visualization_type;
            var aggregation_data = component.dashboard_part.aggregation_data;
            if (aggregation_data && Array.isArray(aggregation_data) && aggregation_data.length > 0) {

                /*Auto translate everything that is translatable*/
                for (var i = 0; i < aggregation_data.length; i++) {
                    var value_field = component.dashboard_part.custom_properties.valueField;
                    var data = aggregation_data[i][value_field];
                    if (typeof data === "string" && R[data]) {
                        data = R[data];
                    }

                    var argument_field = component.dashboard_part.custom_properties.argumentField;
                    if (typeof aggregation_data[i][argument_field] === "string" && R[aggregation_data[i][argument_field]]) {
                        aggregation_data[i][argument_field] = R[aggregation_data[i][argument_field]];
                    }
                }
                /*End of auto translate*/

                switch (type) {
                    case "pie_chart":
                        var properties = {};
                        properties.dataSource = aggregation_data;
                        properties.title = component.dashboard_part.caption;
                        properties.legend = {visible: false};
                        properties.animation = {enabled: true};
                        properties.resolveLabelOverlapping = "shift";
                        properties.series = [{
                            argumentField: component.dashboard_part.custom_properties.argumentField,
                            valueField: component.dashboard_part.custom_properties.valueField,
                            label: {
                                visible: true,
                                customizeText: function (arg) {
                                    return arg.argumentText + " (" + arg.valueText + ")";
                                }
                            }
                        }];

                        if (component.dashboard_part.linked_app_object_code) {
                            properties.onPointClick = function (event) {
                                var app_object = {};
                                app_object.type = "data_list";
                                app_object.code = component.dashboard_part.linked_app_object_code;
                                return context.props.app_object_handler(app_object);
                            }
                        }

                        jquery_element.dxPieChart(properties);
                        component.visual_control = jquery_element.dxPieChart("instance");
                        break;
                    case "line_chart":
                        var properties = {};
                        properties.dataSource = aggregation_data;
                        properties.title = component.dashboard_part.caption;
                        properties.legend = {
                            verticalAlignment: "bottom",
                            horizontalAlignment: "center",
                            itemTextPosition: "bottom"
                        };
                        properties.animation = {enabled: true};
                        properties.resolveLabelOverlapping = "shift";
                        properties.commonSeriesSettings = {
                            argumentField: component.dashboard_part.custom_properties.argumentField,
                            type: "line"
                        };
                        properties.series = [{
                            valueField: component.dashboard_part.custom_properties.valueField,
                            name: component.dashboard_part.custom_properties.name_caption
                        }];
                        if (component.dashboard_part.linked_app_object_code) {
                            properties.onPointClick = function (event) {
                                var app_object = {};
                                app_object.type = "data_list";
                                app_object.code = component.dashboard_part.linked_app_object_code;
                                return context.props.app_object_handler(app_object);
                            }
                        }

                        jquery_element.dxChart(properties);
                        component.visual_control = jquery_element.dxChart("instance");
                        break;
                    case "bar_chart":
                        var properties = {};
                        properties.dataSource = aggregation_data;
                        properties.title = component.dashboard_part.caption;
                        properties.animation = {enabled: true};
                        properties.series = [{
                            argumentField: component.dashboard_part.custom_properties.argumentField,
                            valueField: component.dashboard_part.custom_properties.valueField,
                            name: component.dashboard_part.custom_properties.name_caption,
                            type: "bar",
                            color: component.dashboard_part.custom_properties.color
                        }];

                        if (component.dashboard_part.linked_app_object_code) {
                            properties.onPointClick = function (event) {
                                var app_object = {};
                                app_object.type = "data_list";
                                app_object.code = component.dashboard_part.linked_app_object_code;
                                return context.props.app_object_handler(app_object);
                            }
                        }

                        jquery_element.dxChart(properties);
                        component.visual_control = jquery_element.dxChart("instance");
                        break;
                    case "ranked_list":
                        var value_field = component.dashboard_part.custom_properties.valueField;
                        var argument_field = component.dashboard_part.custom_properties.argumentField;
                        var ol = '<ol class="dashboard-ranked-list-ol">';
                        for (var i = 0; i < aggregation_data.length; i++) {
                            var data_point = aggregation_data[i][value_field];
                            var argument_name = aggregation_data[i][argument_field];
                            ol += '<li>' + data_point + ' - ' + argument_name + '</li>';
                        }
                        ol += '</ol>';
                        var tile_html = "<div>";
                        tile_html += "<span class='dashboard-part-tile-title' title='" + component.dashboard_part.custom_properties.name_caption + "'>" + component.dashboard_part.custom_properties.name_caption + "</span>";
                        tile_html += "<div class='bg-white no-padding'>";
                        tile_html += ol;
                        tile_html += "</div></div>";
                        jquery_element.html(tile_html);
                        if (component.dashboard_part.linked_app_object_code) {
                            $("#" + component.div_id + " .dashboard-part-tile-body").click(function (e) {
                                var app_object = {};
                                app_object.type = "data_list";
                                app_object.code = component.dashboard_part.linked_app_object_code;
                                return context.props.app_object_handler(app_object);
                            });
                        }
                        break;
                    case "tile":
                        var value_field = component.dashboard_part.custom_properties.valueField;
                        var data_point = aggregation_data[0][value_field];

                        var argument_field = component.dashboard_part.custom_properties.argumentField;
                        var argument_name = aggregation_data[0][argument_field];

                        var data_point_color;
                        if (component.dashboard_part.custom_properties && component.dashboard_part.custom_properties.data_point_color) {
                            data_point_color = component.dashboard_part.custom_properties.data_point_color;
                        }
                        else {
                            data_point_color = "black";
                        }
                        var fontSize = 4;
                        if (data_point.toString().length <= 6) {
                            fontSize = (12 - data_point.toString().length);
                        } else if (data_point.toString().length <= 8) {
                            fontSize = 6;
                        }
                        else if (data_point.toString().length <= 10) {
                            fontSize = 5;
                        }
                        var tile_html = "<div class=''>";
                        tile_html += "<div>";
                        tile_html += "<span class='dashboard-part-tile-title' title='" + argument_name + "'>" + argument_name + "</span>";
                        tile_html += "<div class='bg-white no-padding'>";

                        tile_html += "<h1 style='color:" + data_point_color + ";font-size: " + fontSize + "vw' class='dashboard-part-tile-body'>" + data_point + "</h1>";

                        tile_html += "</div></div></div>";

                        jquery_element.html(tile_html);

                        if (component.dashboard_part.linked_app_object_code) {
                            $("#" + component.div_id + " .dashboard-part-tile-body").click(function (e) {
                                var app_object = {};
                                app_object.type = "data_list";
                                app_object.code = component.dashboard_part.linked_app_object_code;
                                return context.props.app_object_handler(app_object);
                            });
                        }

                        console.log(tile_html);
                        break;
                    default:
                        break;
                }
            }
        }

        this.state.gridstack = gridstack;
    },
    initialize_dashboard: function () {
        var context = this;

        var get_dashboard_parts = function () {
            var success = function (data) {
                var components = {};
                for (var i = 0; data.app_object && data.app_object.dashboard_parts && i < data.app_object.dashboard_parts.length; i++) {
                    var dashboard_part = data.app_object.dashboard_parts[i];

                    var div_id = makeid();
                    components[dashboard_part._id] = {};
                    components[dashboard_part._id].div_id = div_id;
                    components[dashboard_part._id].dashboard_part = dashboard_part;
                }
                context.setState({app_object: data.app_object, components: components});
                push_href_location(data.app_object.name + " - " + (R.client_application_title || "Xenforma"), "/");
            };

            var error = function (err) {
                context.setState({error: err.message});
            };

            get_app_object("home", {}, success, error);
        };

        var get_home_layout = function () {
            var success = function (data) {
                data = data.data;
                context.setState({home_layout: data});
            };

            var error = function (err) {
                context.setState({error: err.message});
            };

            invoke_method("user", "get_home_layout", {}, success, error);
        };

        get_dashboard_parts();
        get_home_layout();
    },
    render: function () {

        var error_component = "";
        if (this.state.error) {
            error_component =
                <div><ErrorNotificationComponent message={this.state.error} on_close={this.on_error_close}/><br /></div>
        }

        return (
            <div className="form-group">
                {error_component}
                <div id="home_grid" className="grid-stack"></div>
            </div>

        );
    }
});
