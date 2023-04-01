var UserSettingsComponent = React.createClass({
    get_secret_questions: function() {
        var request = {};
        request.entity = "secret_question";
        request.method = "read";

        var context = this;

        do_authenticated_http_call({
            method: "GET",
            url: "/api/auth/get_secret_questions",
            dataType: "json",
            data: request,
            success: function(data) {
                var updated_state = {};
                updated_state.secret_questions = data;
                if (context.state.data) {
                    updated_state.loading = false;
                }
                context.setState(updated_state);
            },
            error: function (err) {

            },
            complete: function() {

            }
        })
    },
    get_user_info: function() {
        var request = {};
        request.entity = "user";
        request.method = "get_info";

        var context = this;

        do_authenticated_http_call({
            method: "POST",
            url: "/api/entity/invoke_method",
            dataType: 'json',
            data: request,
            success: function(data) {
                //context.setState({data:data.data, loading:false});

                var updated_state = {};
                updated_state.data = data.data;
                if (context.state.secret_questions) {
                    updated_state.loading = false;
                }
                context.setState(updated_state);
            },
            error: function(error) {
                if (error.responseJSON) {
                    context.setState({error:error.responseJSON});
                }
            },
            complete: function() {
            }
        });
    },
    getInitialState: function() {
        this.get_user_info();
        this.get_secret_questions();
        return {loading:true, update:{},avatar:this.props.avatar};
    },
    update_edited_field: function(field_name, value) {
        var update = this.state.update;
        var data = this.state.data;
        if (this.state.data[field_name] != value) {
            update[field_name] = value;
            data[field_name] = value;
        }
        else {
            if (update[field_name]) {
                update[field_name] = undefined;
            }
        }

        this.setState({update:update, data:data});
    },
    on_username_change: function(event) {
        this.update_edited_field("login_name", event.target.value);
    },
    on_email_address_change: function(event) {
        this.update_edited_field("email_address", event.target.value);
    },
    on_first_name_change: function (event) {
        this.update_edited_field("first_name", event.target.value);
    },
    on_last_name_change: function (event) {
        this.update_edited_field("last_name", event.target.value);
    },
    on_password_change: function (event) {
        var update = this.state.update;
        update["password"] = event.target.value;
        if (update["password"] == "") {
            update["password"] = undefined;
        }

        this.setState({update:update});
    },
    on_confirm_password_change: function (event) {
        var update = this.state.update;
        update["confirm_password"] = event.target.value;

        this.setState({update:update});
    },
    on_secret_question_change: function (event) {
        this.update_edited_field("secret_question", event.target.value);
    },
    on_secret_answer_change: function (event) {
        this.update_edited_field("secret_answer", event.target.value);
    },
    on_language_preference_change: function (event) {
        this.update_edited_field("language_preference", event.target.value);
    },
    on_profile_picture_change: function(event) {
        var update = this.state.update;
        update.avatar = event;
        this.setState({update:update});
    },
    on_error_close: function() {
        this.setState({error:undefined});
    },
    handle_submit: function(event) {
        event.preventDefault();

        if (Object.keys(this.state.update).length == 0) {
            return;
        }

        if (this.state.update.confirm_password != this.state.update.password) {
            this.setState({error:R.message_passwords_dont_match});
            return;
        }

        this.setState({loading:true});

        var request = {};
        request.entity = "user";
        request.method = "update_info";
        request.data = this.state.update;

        request.data["confirm_password"] = undefined;

        var context = this;

        console.log(JSON.stringify(request));

        do_authenticated_http_call({
            method: "POST",
            url: "/api/entity/invoke_method",
            contentType: "application/json",
            dataType: 'json',
            data: JSON.stringify(request),
            success: function(data) {
                var avatar = context.state.avatar;
	    	    if (context.state.update.avatar || context.state.update.avatar == null)
		        {
                    avatar = context.state.update.avatar;
    		    	context.props.update_avatar();
    		    }
                context.props.update_user_info();
                context.setState({update:{}, error: undefined, avatar:avatar});
                Notify(data.message, 'bottom-right', 20000, 'green', 'fa-check', true);
            },
            error: function(error) {
                if (error.responseJSON) {
                    console.log(error.responseJSON);
                    context.setState({error:error.responseJSON.message})
                }
            },
            complete: function() {
                context.setState({loading:false, update: {}});
            }
        });
    },
    render: function() {
        if (this.state.loading) {
            return (<h1>Loading...</h1>);
        }
        else {
            var error_component = "";
            if (this.state.error) {
                error_component = <ErrorNotificationComponent message={this.state.error} on_close={this.on_error_close}/>
            }

            var secret_questions = [];
            for (var i = 0; i < this.state.secret_questions.length; i++) {
                var secret_question = this.state.secret_questions[i];
                secret_questions.push(<option key={secret_question.code}
                                              value={secret_question.code}>{secret_question.value}</option>);
            }

            var avatarEditor = <EditorDevExtremeImageUpload
                                         value={this.state.avatar} readOnly={false} onChange={this.on_profile_picture_change}
                                         selectButtonText={R_loc.select_image} resizeWidth={100} resizeHeight={100} />;

            return (<div className="widget">
                <div className="widget-header bordered-bottom bordered-palegreen">
                    <span className="widget-caption">{R.label_settings}</span>
                </div>
                <div className="widget-body">
                    <div>
                        {error_component}
                        <form id="user_settings" className="form-horizontal form-bordered" role="form" onSubmit={this.handle_submit}>
                            <div className="form-group">
                                <label htmlFor="input_username"
                                       className="col-sm-2 control-label no-padding-right">{R.label_username}</label>

                                <div className="col-sm-10">
                                    <input type="text" className="form-control" id="input_username"
                                           placeholder={R.label_username} onChange={this.on_username_change}
                                           value={this.state.data.login_name}/>
                                </div>
                            </div>
                            <div className="form-group">
                                <label htmlFor="input_email"
                                       className="col-sm-2 control-label no-padding-right">{R.label_email_address}</label>

                                <div className="col-sm-10">
                                    <input type="email" className="form-control" id="input_email"
                                           placeholder={R.label_email_address} value={this.state.data.email_address}
                                           onChange={this.on_email_address_change}/>
                                </div>
                            </div>
                            <div className="form-group">
                                <label htmlFor="input_first_name"
                                       className="col-sm-2 control-label no-padding-right">{R.label_full_name}</label>

                                <div className="col-sm-5">
                                    <input type="text" className="form-control" id="input_first_name"
                                           placeholder={R.label_first_name} value={this.state.data.first_name}
                                           onChange={this.on_first_name_change}/>
                                </div>
                                <div className="col-sm-5">
                                    <input type="text" className="form-control" id="input_last_name"
                                           placeholder={R.label_last_name} value={this.state.data.last_name}
                                           onChange={this.on_last_name_change}/>
                                </div>
                            </div>
                            <div className="form-group">
                                <label htmlFor="input_password"
                                       className="col-sm-2 control-label no-padding-right">{R.label_password}</label>

                                <div className="col-sm-10">
                                    <input type="password" className="form-control" id="input_password"
                                           placeholder={R.label_password} onChange={this.on_password_change}/>
                                </div>
                            </div>
                            <div className="form-group">
                                <label htmlFor="input_confirm_password"
                                       className="col-sm-2 control-label no-padding-right">{R.label_confirm_password}</label>

                                <div className="col-sm-10">
                                    <input type="password" className="form-control" id="input_confirm_password"
                                           placeholder={R.label_confirm_password} onChange={this.on_confirm_password_change}/>
                                </div>
                            </div>
                            <div className="form-group">
                                <label htmlFor="input_secret_question"
                                       className="col-sm-2 control-label no-padding-right">{R.label_secret_question}</label>

                                <div className="col-sm-10">
                                    <select className="form-control" name="country"
                                            value={this.state.data.secret_question}
                                            onChange={this.on_secret_question_change}>
                                        {secret_questions}
                                    </select>
                                </div>
                            </div>
                            <div className="form-group">
                                <label htmlFor="input_secret_answer"
                                       className="col-sm-2 control-label no-padding-right">{R.label_secret_answer}</label>

                                <div className="col-sm-10">
                                    <input type="password" className="form-control" id="input_secret_answer"
                                           placeholder={R.label_secret_answer} onChange={this.on_secret_answer_change}/>
                                </div>
                            </div>
                            <div className="form-group">
                                <label htmlFor="input_secret_question"
                                       className="col-sm-2 control-label no-padding-right">{R.label_language_preference}</label>

                                <div className="col-sm-10">
                                    <select className="form-control" name="country"
                                            value={this.state.data.language_preference}
                                            onChange={this.on_language_preference_change}>
                                        <option value="en-US">English</option>
                                        <option value="ru">�������</option>
                                    </select>
                                </div>
                            </div>
                            <div className="form-group">
                                <label htmlFor="input_secret_answer"
                                       className="col-sm-2 control-label no-padding-right">{R.label_profile_picture}</label>
                                <div className="col-sm-10">
                                    {avatarEditor}
                                </div>
                            </div>
                            <div className="form-group">
                                <div className="col-sm-offset-2 col-sm-10">
                                    <button type="submit" className="btn btn-success shiny">{R.label_save}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>);
        }
    }
});
