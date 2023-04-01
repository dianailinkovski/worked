var SearchComponent = React.createClass({
    getInitialState: function() {
        this.initialize_search();
        return {};
    },
    initialize_search: function() {
        var search_string = this.props.search_string;

        var request = {};
        request.data = {search_string:search_string};

        var context = this;

        do_authenticated_http_call({
            method: "POST",
            url: "/api/entity/full_text_search",
            contentType: "application/json",
            dataType: 'json',
            data: JSON.stringify(request),
            success: function(data) {
                context.setState({search_results: data.data});

                console.log(data.data);

                push_href_location(search_string + " - " + (R.client_application_title || "Xenforma"), "/search?search_string="+search_string);
            },
            error: function(error) {
                if (error.responseJSON) {
                    console.log(error.responseJSON);
                    context.setState({error:error.responseJSON.message})
                }
            },
            complete: function() {
            }
        });
    },
    componentDidMount: function() {
        if (this.state.search_results) {
            $('.highlightee').highlight(this.props.search_string);
        }
    },
    componentDidUpdate: function() {
        if (this.state.search_results) {
            $('.highlightee').highlight(this.props.search_string);
        }
    },
    handle_search_result_click: function(search_result, event) {
        event.preventDefault();

        if (search_result.entity_id) {
            var app_object = {};
            app_object.code = search_result.app_object_code;
            app_object.type = "edit_form";
            app_object._id = search_result.entity_id;
            this.props.app_object_handler(app_object);
        }
    },
    render: function() {
        var columns = [];
        columns.push({dataField:"entity_name", caption:R.label_entity_name});
        columns.push({dataField:"caption", caption:R.label_caption});

        this.state.columns = columns;

        var input_elements = [];
        for (var i = 0; this.state.search_results && i < this.state.search_results.length; i++) {
            var search_result = this.state.search_results[i];

            var descriptors = [];

            for (var j = 0; j < search_result.descriptors.length; j++) {
                //var descriptor = <div><b>{search_result.descriptors[j].caption}</b>: <span className="highlightee">{search_result.descriptors[j].value}</span></div>;
                var value = search_result.descriptors[j].value;
                if (typeof value == "undefined")
                {
                    continue;
                }
                if (typeof search_result.descriptors[j].value == "object")
                {
                    continue;
                }
                var descriptor = <tr><td><b>{search_result.descriptors[j].caption}</b></td><td><span className="highlightee">{value}</span></td></tr>;

                descriptors.push(descriptor);
            }

            var descriptor_insert = "";

            if (descriptors.length > 0) {
                descriptor_insert = <table className="table"><tbody>{descriptors}</tbody></table>;
            }
            var caption = search_result.caption;
            if (!caption)
            {
                caption = search_result.entity_id;
            }
            var input_element = <div className="widget no-header radius-bordered" style={{"overflow":"hidden"}} key={caption}>
                                    <a href="" onClick={this.handle_search_result_click.bind(this, search_result)}><h4><b>{caption}</b></h4></a>
                                    <div>
                                        <div className="col-sm-1">
                                            <img src={search_result.image} />
                                        </div>
                                        <div className="col-sm-11">
                                            {descriptor_insert}
                                        </div>
                                    </div>
                                </div>;

            input_elements.push(input_element);
        }

        if (input_elements.length == 0) {
            input_elements = R.label_no_data;
        }

        return (<div className="widget">
            <div className="widget-header bordered-bottom bordered-palegreen">
                <span className="widget-caption">{R.label_search_description}</span>
            </div>
            <div className="widget-body">
                {input_elements}
            </div>
        </div>);
    }
});
