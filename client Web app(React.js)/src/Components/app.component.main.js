var MainComponent = React.createClass({
    measure_idle_time: function () {
        var context = this;

        //TODO: Lock disabled until it's fixed
        /*clearInterval(context.state.idle_interval);

         idle_interval = setInterval(function() {
         idle_time += 1;
         if (idle_time > 600) {
         context.setState({locked:true});
         clearInterval(context.state.idle_interval);
         context.logout();
         }
         }, 1000);

         this.state.idle_interval = idle_interval;

         $(document).ready(function() {
         $(this).mousemove(function (e) {
         idle_time = 0;
         });

         $(this).keypress(function (e) {
         idle_time = 0;
         });
         });*/
    },
    get_user_info: function () {
        var request = {};
        request.entity = "user";
        request.method = "get_info";

        var context = this;

        do_authenticated_http_call({
            method: "POST",
            url: "/api/entity/invoke_method",
            dataType: 'json',
            data: request,
            success: function (data) {
                context.setState({user_info: data.data});
            },
            error: function (error) {
                if (error.responseJSON) {
                    context.setState({error: error.responseJSON});
                }
            },
            complete: function () {
            }
        });
    },
    get_user_avatar: function () {
        var request = {};
        request.entity = "user";
        request.method = "get_avatar";

        var context = this;

        do_authenticated_http_call({
            method: "POST",
            url: "/api/entity/invoke_method",
            dataType: 'json',
            data: request,
            error: function (error) {
                if (error.responseJSON) {
                    context.setState({error: error.responseJSON});
                }
                context.setState({user_avatar: error.responseText});
            },
            complete: function () {
                console.log("get_user_avatar completed");
            }
        }).done(function (data) {
            context.setState({user_avatar: data});
        });
    },
    logout: function () {
        localStorage.removeItem("login_token");
        this.on_logged_off_handler();
    },
    initialize_state: function () {
        var context = this;
        return is_logged_in(function (logged_in, data) {
            if (logged_in) {
                context.on_login_handler();
            }
            else {
                context.on_logged_off_handler();
            }

            context.setState({startup_loading: false});
        });
    },
    on_login_handler: function () {
        var context = this;
        this.setState({logged_in: true});
        load_translations(false, function () {
            context.forceUpdate();
        });
        this.initialize_user();
        this.handle_logged_in_locations();

        window.onpopstate = function (event) {
            g_popped = true;
            context.handle_logged_in_locations();
        };
    },
    on_logged_off_handler: function () {
        var context = this;
        this.setState({logged_in: false});
        load_translations(true, function () {
            context.forceUpdate();
        });
        this.handle_locations();

        var context = this;
        window.onpopstate = function (event) {
            g_popped = true;
            context.handle_locations();
        };
    },
    initialize_user: function () {
        this.measure_idle_time();
        this.get_user_info();
        this.get_user_avatar();
    },
    getInitialState: function () {
        var context = this;
        setTimeout(function () {
            context.initialize_state()
        }, 1);
        return {startup_loading: true, isCompact: false};
    },
    menu_click: function (isCompact) {
        this.setState({isCompact: isCompact});
    },
    handle_logged_in_locations: function () {
        var state_object = get_clear_state();

        if (window.location.pathname == "/") {
            state_object.home = true;
        }
        else if (window.location.pathname == "/settings") {
            state_object.settings = true;
        }
        else if (window.location.pathname == "/edit_form") {
            if (get_parameter_by_name("code")) {
                state_object.edit_form = true;
                state_object.app_object_code = get_parameter_by_name("code");
                state_object.edit_form_id = get_parameter_by_name("_id");
            }
        }
        else if (window.location.pathname == "/data_list") {
            if (get_parameter_by_name("code")) {
                state_object.data_list = true;
                state_object.app_object_code = get_parameter_by_name("code");
            }
        }
        else if (window.location.pathname == "/dashboard") {
            if (get_parameter_by_name("code")) {
                state_object.dashboard = true;
                state_object.app_object_code = get_parameter_by_name("code");
            }
        }
        else if (window.location.pathname == "/search") {
            if (get_parameter_by_name("search_string")) {
                state_object.search = true;
                state_object.search_string = get_parameter_by_name("search_string");
            }
        }
        else if (window.location.pathname == "/user_list") {
            //this.navigation_handler("user_data_list");
            state_object.user_data_list = true;
        }

        this.setState(state_object);
    },
    handle_locations: function () {
        var state_object = get_clear_state();
        if (window.location.pathname == "/setup_user") {
            state_object.setup_user = true;

            var setup_token = get_parameter_by_name("setup_token");
            if (setup_token != "") {
                state_object.setup_token = setup_token;
            }
        }
        else if (window.location.pathname == "/reset_password") {
            state_object.reset_password = true;

            var reset_password_token = get_parameter_by_name("reset_password_token");
            if (reset_password_token != "") {
                state_object.reset_password_token = reset_password_token;
            }
        }

        this.setState(state_object);
    },
    handle_login_success: function (forgot_password) {
        if (!forgot_password) {
            this.on_login_handler();
        }
        else {
            this.setState({forgot_password: true});
        }
    },
    handle_forgot_password_completion: function () {
        this.setState({forgot_password: false});
    },
    handle_setup_user_success: function () {
        var interval = setInterval(function () {
            clearInterval(interval);
            window.location.href = window.location.protocol + "//" + window.location.host;
        }, 1000);
    },
    handle_reset_password_completion: function () {
        this.setState({reset_password: false});
    },
    navigation_handler: function (location) {
        var context = this;
        var handle_navigation = function () {
            if (location == "home") {
                var state_object = get_clear_state();
                state_object.home = true;
                context.setState(state_object);
            }
            else if (location == "settings") {
                var state_object = get_clear_state();
                state_object.settings = true;
                context.setState(state_object);
            }
            else if (location == "invite_users") {
                var state_object = get_clear_state();
                state_object.invite_users = true;
                context.setState(state_object)
            }
            else if (location == "user_data_list") {
                var state_object = get_clear_state();
                state_object.user_data_list = true;
                context.setState(state_object);
            }
            else if (location == "logout") {
                context.on_logged_off_handler();
            }
        };

        if (current_navigation_listener) {
            return current_navigation_listener(function (confirm_location_change) {
                if (confirm_location_change) {
                    handle_navigation();
                }
            });
        }
        else {
            handle_navigation();
        }
    },
    app_object_handler: function (app_object) {
        var context = this;

        var handle_app_object = function () {
            var state_object = get_clear_state();
            var code = app_object.code;
            var data_list = app_object.type == "data_list";
            var edit_form = app_object.type == "edit_form";
            var dashboard = app_object.type == "dashboard";
            var conditions = $.extend({}, app_object.conditions || {});
            delete state_object.app_object_conditions;
            if (app_object.workflow_conditions) {
                conditions.workflow_status = app_object.workflow_conditions.workflow_status; //overrides standing conditions.
            }

            state_object.data_list = data_list;
            state_object.edit_form = edit_form;
            state_object.dashboard = dashboard;
            state_object.app_object_code = code;
            if (data_list) {
                state_object.app_object_conditions = conditions;
            }
            state_object.edit_form_id = app_object._id;
            context.setState(state_object);
        };

        if (current_navigation_listener) {
            return current_navigation_listener(function (confirm_location_change) {
                if (confirm_location_change) {
                    handle_app_object();
                }
            });
        }
        else {
            return handle_app_object();
        }
    },
    search_handler: function (search_string) {
        var state_object = get_clear_state();
        state_object.search = true;
        state_object.search_string = search_string;
        this.setState(state_object);
    },
    render: function () {
        if (this.state.startup_loading) {
            return (<LoadingComponent />);
        }
        if (this.state.logged_in) {
            var page_content = "";
            if (this.state.home) {
                page_content = <HomeDashboardComponent key="home" app_object_handler={this.app_object_handler}/>;
            }
            else if (this.state.settings) {
                page_content =
                    <UserSettingsComponent update_avatar={this.get_user_avatar} update_user_info={this.get_user_info}
                                           avatar={this.state.user_avatar}/>;
            }
            else if (this.state.invite_users) {
                page_content = <InviteUsersComponent />;
            }
            else if (this.state.edit_form) {
                page_content = <EditFormComponent app_object_code={this.state.app_object_code} data={this.state.data}
                                                  navigation_handler={this.navigation_handler}
                                                  _id={this.state.edit_form_id}
                                                  key={this.state.app_object_code + this.state.edit_form_id}
                                                  app_object_handler={this.app_object_handler}
                                                  update_avatar={this.get_user_avatar}
                                                  update_user_info={this.get_user_info}/>;
            }
            else if (this.state.data_list) {
                var key = this.state.app_object_code;
                if (this.state.app_object_conditions) {
                    key += (":" + JSON.stringify(this.state.app_object_conditions));
                }
                page_content = <DataListComponent app_object_code={this.state.app_object_code} key={key}
                                                  app_object_handler={this.app_object_handler}
                                                  navigation_handler={this.navigation_handler}
                                                  conditions={this.state.app_object_conditions}/>;
            }
            else if (this.state.dashboard) {
                page_content =
                    <DashboardComponent app_object_code={this.state.app_object_code} key={this.state.app_object_code}
                                        app_object_handler={this.app_object_handler}/>;
            }
            else if (this.state.search) {
                page_content = <SearchComponent search_string={this.state.search_string} key={this.state.search_string}
                                                app_object_handler={this.app_object_handler}/>;
            }
            else if (this.state.user_data_list) {
                page_content = <UserListComponent key="user_data_list" app_object_handler={this.app_object_handler}/>;
            }


            return (<div>
                <NavBarComponent navigation_handler={this.navigation_handler} user_info={this.state.user_info}
                                 user_avatar={this.state.user_avatar} menu_click={this.menu_click}/>
                <div className="main-container container-fluid">
                    <div className="page-container">
                        <SideBarComponent navigation_handler={this.navigation_handler}
                                          app_object_handler={this.app_object_handler}
                                          search_handler={this.search_handler} isCompact={this.state.isCompact}/>
                        <div className="page-content">
                            <div className="page-body">
                                {page_content}
                            </div>
                        </div>
                    </div>
                </div>
            </div>);
        }

        if (this.state.forgot_password) {
            return (<ForgotPasswordComponent on_complete={this.handle_forgot_password_completion}/>);
        }
        else if (this.state.reset_password) {
            return (<ResetPasswordComponent on_complete={this.handle_reset_password_completion}/>);
        }
        else if (this.state.setup_user) {
            return (<SetupUserComponent on_success={this.handle_setup_user_success}/>);
        }
        else if (this.state.locked) {
            return (<LockComponent user_info={this.state.user_info} user_avatar={this.state.user_avatar}
                                   on_success={this.handle_login_success}/>);
        }
        else {
            return (<LoginComponent on_success={this.handle_login_success}/>);
        }
    }
});
