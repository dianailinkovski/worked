var SideBarSection = React.createClass({
    handleClick: function (event) {
        event.preventDefault();
        if (this.props.isCompact == true)
            return;
        if (this.props.open) {
            this.props.onOpen(false);
        } else {
            this.props.onOpen(true);
        }
    },
    render: function () {
        var styles = {
            block: {
                display: "block"
            },
            none: {
                display: "none"
            },
        };
        var menuExpand = "";
        if (this.props.isCompact == false) {
            menuExpand = <i className="menu-expand"></i>;
        }
        var liClass = '';
        if (!this.props.isCompact && this.props.open) {
            liClass = 'open';
        }
        var iClassName = "menu-icon fa " + this.props.icon;
        return (
            <li className={liClass}>
                <a href="" className="menu-dropdown" onClick={this.handleClick}>
                    <i className={iClassName}></i>
                    <span className="menu-text"> {this.props.title} </span>
                    {menuExpand}
                </a>
                <ul className="submenu" style={(this.props.open == true)?styles.block:styles.none}>
                    {this.props.children}
                </ul>
            </li>
        );
    }
});

var SideBarComponent = React.createClass({
    getInitialState: function () {
        this.get_menu_items();
        /*
         //TODO: implement get_recent_app_objects using web sockets (server-side and client-side).

         this.get_recent_app_objects();
         var context = this;
         idle_interval = setInterval(function () {
         context.get_recent_app_objects();
         }, 3000);*/

        return {menu_items: {}};
    },
    get_menu_items: function () {
        var request = {};
        request.entity = "app_object";
        request.method = "get_menu_items";

        var context = this;

        do_authenticated_http_call({
            method: "POST",
            url: "/api/entity/invoke_method",
            contentType: "application/json",
            dataType: 'json',
            data: JSON.stringify(request),
            success: function (data) {
                console.log(data.data);
                context.setState({menu_items: data.data});
            },
            error: function (error) {
                if (error.responseJSON) {
                    console.log(error.responseJSON)
                    context.setState({error: error.responseJSON.message});
                }
            },
            complete: function () {
            }
        });
    },
    get_recent_app_objects: function () {
        //TODO: implement get_recent_app_objects using web sockets.
        var request = {};
        request.entity = "user";
        request.method = "get_recent_app_objects";

        var context = this;

        do_authenticated_http_call({
            method: "POST",
            url: "/api/entity/invoke_method",
            contentType: "application/json",
            dataType: 'json',
            data: JSON.stringify(request),
            success: function (data) {
                context.setState({recent_app_objects: data.data});
            },
            error: function (error) {
                if (error.responseJSON) {
                    console.log(error.responseJSON);
                    context.setState({error: error.responseJSON.message})
                }
            },
            complete: function () {
            }
        });
    },
    on_dashboard_click: function (event) {
        event.preventDefault();
        this.props.navigation_handler("home");
    },
    on_invite_users_click: function (event) {
        event.preventDefault();
        this.props.navigation_handler("invite_users");
    },
    on_user_data_list_click: function (event) {
        event.preventDefault();
        this.props.navigation_handler("user_data_list");
    },
    handle_app_object_click: function (app_object, workflow_state, event) {
        if (event && event.preventDefault) {
            event.preventDefault();
        }

        if (workflow_state) {
            if (workflow_state.work_queue_app_object_code) {
                app_object.type = "data_list";
                app_object.code = workflow_state.work_queue_app_object_code;
            }

            app_object.workflow_conditions = {workflow_status: workflow_state.status_code};

            console.log(app_object);
        }
        else {
            delete app_object.workflow_conditions;
        }

        this.props.app_object_handler(app_object);
    },
    componentWillReceiveProps: function (nextProps) {
        if (nextProps.isCompact != this.props.isCompact) {
            if (!$('#sidebar').is(':visible'))
                $("#sidebar").toggleClass("hide");

            if ($(".sidebar-menu").closest("div").hasClass("slimScrollDiv")) {
                $(".sidebar-menu").slimScroll({destroy: true});
                $(".sidebar-menu").attr('style', '');
            }
            if (nextProps.isCompact) {
                $(".open > .submenu")
                    .removeClass("open");
            } else {
                if ($('.page-sidebar').hasClass('sidebar-fixed')) {
                    var position = (readCookie("rtl-support") || location.pathname == "/index-rtl-fa.html" || location.pathname == "/index-rtl-ar.html") ? 'right' : 'left';
                    $('.sidebar-menu').slimscroll({
                        height: 'auto',
                        position: position,
                        size: '3px',
                        color: themeprimary
                    });
                }
            }
        }
    },
    handle_search_submit: function (event) {
        event.preventDefault();
        this.props.search_handler(this.state.search_string);
    },
    on_search_string_change: function (event) {
        this.state.search_string = event.target.value;
    },
    on_open: function (menuKey, open) {
        if (open) {
            this.setState({openMenyKey: menuKey});
        }
        else {
            this.setState({openMenyKey: undefined});
        }

    },
    render: function () {
        var menu_items = [];

        for (var module_key in this.state.menu_items) {
            var module = this.state.menu_items[module_key];
            var module_name = module.name;

            var app_objects = [];

            for (var i = 0; i < module.app_objects.length; i++) {
                var app_object = module.app_objects[i];

                var className = "";
                var workflow_ul = "";
                if (app_object.workflow_states && app_object.workflow_states.length > 0) {
                    className = "menu-dropdown";

                    var workflows = [];
                    for (var j = 0; j < app_object.workflow_states.length; j++) {
                        var workflow_state = app_object.workflow_states[j];

                        var workflow_li = <li key={workflow_state.status_code}
                                              onClick={this.handle_app_object_click.bind(this, app_object, workflow_state)}>
                            <a href="">
                                <span className="menu-text">{workflow_state.caption}</span>
                            </a>
                        </li>;
                        workflows.push(workflow_li);
                    }

                    workflow_ul = <ul className="submenu1" style={{"display":"block"}}>
                        {workflows}
                    </ul>;
                }

                var app_object_element = <li key={app_object.code}>
                    <a href="" onClick={this.handle_app_object_click.bind(this, app_object, undefined)}
                       className={className}>
                        <span className="menu-text">{app_object.name}</span>
                    </a>
                    {workflow_ul}
                </li>;

                app_objects.push(app_object_element);
            }
            var key = module.code;
            var isOpen = this.state.openMenyKey == key;
            var menu_item = <SideBarSection onOpen={this.on_open.bind(this, key)} isCompact={this.props.isCompact}
                                            title={module_name}
                                            open={isOpen}
                                            icon={module.font_awesome_icon}
                                            key={key}>{app_objects}</SideBarSection>;
            menu_items.push(menu_item);
        }

        if (this.state.recent_app_objects && this.state.recent_app_objects.length > 0) {
            var app_objects = [];
            for (var i = 0; i < this.state.recent_app_objects.length; i++) {
                var app_object = <li
                    key={this.state.recent_app_objects[i].code+this.state.recent_app_objects[i].timestamp}>
                    <a href="" onClick={this.handle_app_object_click.bind(this, this.state.recent_app_objects[i], null)}
                       key={this.state.recent_app_objects[i].code}>
                        <span className="menu-text">{this.state.recent_app_objects[i].name}</span>
                    </a>
                </li>;

                app_objects.push(app_object);
            }

            var key = "recent_app_objects";
            var isOpen = this.state.openMenyKey == key;
            var menu_item = <SideBarSection onOpen={this.on_open.bind(this, key)} isCompact={this.props.isCompact}
                                            open={isOpen}
                                            title={R.label_recent_app_objects} icon="fa-file"
                                            key={key}>{app_objects}</SideBarSection>;
            menu_items.push(menu_item);
        }

        var pageSideBarClass = "page-sidebar";
        if (this.props.isCompact == true) {
            pageSideBarClass = pageSideBarClass + " menu-compact";
        }

        return (<div className={pageSideBarClass} id="sidebar">
            <div className="sidebar-header-wrapper">
                <form onSubmit={this.handle_search_submit}>
                    <input type="text" className="searchinput" onChange={this.on_search_string_change}/>
                </form>
                <i className="searchicon fa fa-search"></i>
                <div className="searchhelper">{R.label_search_hint}</div>
            </div>
            <ul className="nav sidebar-menu">
                <li>
                    <a href="#" onClick={this.on_dashboard_click}>
                        <i className="menu-icon glyphicon glyphicon-home"></i>
                        <span className="menu-text"> {R.label_dashboard} </span>
                    </a>
                </li>
                {menu_items}
            </ul>
        </div>);
    }
});
