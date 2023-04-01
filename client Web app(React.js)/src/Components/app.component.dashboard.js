var DashboardComponent = React.createClass({
    components: {},
    getInitialState: function() {
        this.initialize_dashboard();
        return {};
    },
    componentDidUpdate: function() {

        if (Object.keys(this.components).length == 0) {
            return;
        }

        var context = this;

        var options = {
            animate: true,
            float: true,
            width: 12,
            cell_height: 80,
            vertical_margin: 10
        };

        var gridstack = $("#dashboard-dashboards").gridstack(options).data('gridstack');

        for (var key in this.components) {
            var component = this.components[key];

            gridstack.add_widget('<div class="widget no-header dashboard-part"><div class="grid-stack-item-content widget-body"><div id="'+component.div_id+'"></div></div></div>', 0, 0, component.dashboard_part.width, component.dashboard_part.row_number, true);

            var type = component.dashboard_part.visualization_type;
            switch (type) {
                case "pie_chart":
                    var properties = {};
                    properties.dataSource = component.dashboard_part.aggregation_data;
                    properties.title = component.dashboard_part.caption;
                    properties.legend = {visible:false};
                    properties.animation = {enabled: true};
                    properties.resolveLabelOverlapping = "shift";
                    properties.series = [{
                        argumentField: component.dashboard_part.custom_properties.argumentField,
                        valueField: component.dashboard_part.custom_properties.valueField,
                        label: {
                            visible: true,
                            customizeText: function(arg) {
                                return arg.argumentText + " (" + arg.valueText + ")";
                            }
                        },
                    }];

                    if (component.dashboard_part.linked_app_object_code) {
                        var context = this;
                        properties.onPointClick = function(event) {
                            var app_object = {};
                            app_object.type = "data_list";
                            app_object.code = component.dashboard_part.linked_app_object_code;
                            return context.props.app_object_handler(app_object);
                        }
                    }

                    $("#"+component.div_id).dxPieChart(properties);
                    break;
                case "line_chart":
                    var properties = {};
                    properties.dataSource = component.dashboard_part.aggregation_data;
                    properties.title = component.dashboard_part.caption;
                    properties.legend = {verticalAlignment:"bottom", horizontalAlignment:"center", itemTextPosition:"bottom"};
                    properties.animation = {enabled: true};
                    properties.resolveLabelOverlapping = "shift";
                    properties.commonSeriesSettings = {argumentField:component.dashboard_part.custom_properties.argumentField, type:"line"};
                    properties.series = [{
                        valueField: component.dashboard_part.custom_properties.valueField,
                        name: component.dashboard_part.custom_properties.name_caption
                    }];

                    if (component.dashboard_part.linked_app_object_code) {
                        var context = this;
                        properties.onPointClick = function(event) {
                            var app_object = {};
                            app_object.type = "data_list";
                            app_object.code = component.dashboard_part.linked_app_object_code;
                            return context.props.app_object_handler(app_object);
                        }
                    }

                    $("#"+component.div_id).dxChart(properties);
                    break;
                case "bar_chart":
                    var properties = {};
                    properties.dataSource = component.dashboard_part.aggregation_data;
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
                        var context = this;
                        properties.onPointClick = function(event) {
                            var app_object = {};
                            app_object.type = "data_list";
                            app_object.code = component.dashboard_part.linked_app_object_code;
                            return context.props.app_object_handler(app_object);
                        }
                    }

                    $("#"+component.div_id).dxChart(properties);
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
                    break;
                default:
                    break;
            }
        }
    },
    initialize_dashboard: function() {
        var context = this;

        var success = function(data) {
            context.setState({app_object:data.app_object});
            console.log(data.app_object);
            push_href_location(data.app_object.name + " - " + (R.client_application_title || "Xenforma"), "/dashboard?code="+data.app_object.code);
        };

        var error = function(err) {
            context.setState({error:err.message});
        };

        get_app_object(this.props.app_object_code, {}, success, error);
    },
    render: function() {
        this.components = {};
        for (var i = 0; this.state.app_object && i < this.state.app_object.dashboard_parts.length; i++) {
            var dashboard_part = this.state.app_object.dashboard_parts[i];
            var div_id = makeid();
            this.components[div_id] = {};
            this.components[div_id].div_id = div_id;
            this.components[div_id].dashboard_part = dashboard_part;
        }

        var error_component = "";
        if (this.state.error) {
            error_component = <div><ErrorNotificationComponent message={this.state.error} on_close={this.on_error_close}/><br /></div>
        }

        return (
            <div className="form-group">
                {error_component}
                <div id="dashboard-dashboards" className="grid-stack"></div>
            </div>
        );
    }
});
