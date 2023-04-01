var SetupUserComponent = React.createClass({
    getInitialState: function () {
        this.get_secret_questions();
        return {error: undefined};
    },
    get_secret_questions: function () {
        var context = this;

        $.ajax({
            method: "GET",
            url: "/api/auth/get_secret_questions",
            success: function (data) {
                context.setState({secret_questions: data});
            },
            error: function (err) {
                console.log(err);
            },
        })
    },
    handle_submit: function (event) {
        event.preventDefault();

        if (this.state.password != this.state.confirmed_password) {
            this.setState({error: R.message_passwords_dont_match});
            return;
        }

        var request = {};
        request.setup_token = get_parameter_by_name("setup_token");
        request.username = this.state.username;
        request.password = this.state.password;
        request.email_address = this.state.email;
        request.secret_question = this.state.secret_question;
        request.secret_answer = this.state.secret_answer;

        this.setState({loading: true});

        var context = this;
        $.post("/api/auth/setup_user", request, function (data) {
            if (data) {
                if (data.success) {
                    context.setState({completed: true});
                    Notify(R.message_successfully_registered, 'bottom-right', 20000, 'green', 'fa-check', true);
                    context.props.on_success();
                }
                else {
                    context.setState({error: data.message});
                }

                console.log(data);
            }
        }).fail(function (error) {
            error = error.responseJSON;
            console.log(error);
            context.setState({error: error.message});
        }).complete(function () {
            if (!context.state.completed) {
                context.setState({loading: false});
            }
        });
    },
    on_username_change: function (event) {
        this.setState({username: event.target.value});
        $("#username").bootstrapValidator();
    },
    on_password_change: function (event) {
        this.setState({password: event.target.value});
    },
    on_confirmed_password_change: function (event) {
        this.setState({confirmed_password: event.target.value});
    },
    on_email_change: function (event) {
        this.setState({email: event.target.value});
    },
    on_secret_question_change: function (event) {
        this.setState({secret_question: event.target.value});
    },
    on_secret_answer_change: function (event) {
        this.setState({secret_answer: event.target.value});
    },
    componentWillUnmount: function () {
        document.body.className = "";
    },
    render: function () {
        document.body.className = "register-body";
        var error_component = "";
        if (this.state.error) {
            var on_close = function (context) {
                return function () {
                    context.setState({error: undefined});
                };
            }(this);
            error_component = <ErrorNotificationComponent message={this.state.error} on_close={on_close}/>;
        }

        var secret_questions = [];
        if (this.state.secret_questions) {
            for (var i = 0; i < this.state.secret_questions.length; i++) {
                var secret_question = this.state.secret_questions[i];
                secret_questions.push(<option key={secret_question.code}
                                              value={secret_question.code}>{secret_question.value}</option>);
            }
        }

        var submit_button = <input type="submit" className="btn btn-primary btn-block" value={R.label_submit}/>;
        if (this.state.loading) {
            submit_button =
                <input type="submit" disabled className="btn btn-primary btn-block" value={R.label_submit}/>;
        }

        return (<div className="register-container animated fadeInDown">
            <table className="registerbox">
                <tbody>
                <tr>
                    <td></td>
                    <td>
                        <div className="registerbox-header01">
                            <div className="registerbox-header02">
                                <div style={{"textAlign":"center"}}>
                                    <img src="images/logo.png" alt=""/>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td className="registerbox-menu-border-ribbon"></td>
                    <td>
                        <div className="registerbox-menu_right">
                            <div className="registerbox-loggedout_right">
                                {R.label_register}
                            </div>
                        </div>
                    </td>
                    <td className="registerbox-menu-border-ribbon"></td>
                </tr>
                <tr>
                    <td className="registerbox-menu-left-ribbon"></td>
                    <td>
                        <div style={{"position":"relative"}}>
                            <div className="registerbox-ribbon-shadow"></div>
                            <div className="registerbox-content01">
                                <div className="registerbox-content02">
                                    <form className="bv-form" onSubmit={this.handle_submit}>
                                        {error_component}
                                        {R.label_fill_in_info}
                                        <br/>
                                        <br/>
                                        <div className="registerbox-textbox">
                                            <input id="username" type="text" className="form-control"
                                                   placeholder={R.placeholder_username}
                                                   onChange={this.on_username_change}/>
                                        </div>
                                        <div className="registerbox-textbox">
                                            <input type="text" className="form-control"
                                                   placeholder={R.placeholder_email_address}
                                                   onChange={this.on_email_change}/>
                                        </div>
                                        <div className="registerbox-textbox">
                                            <input type="password" className="form-control"
                                                   placeholder={R.placeholder_password}
                                                   onChange={this.on_password_change}/>
                                        </div>
                                        <div className="registerbox-textbox">
                                            <input type="password" className="form-control"
                                                   placeholder={R.placeholder_confirm_password}
                                                   onChange={this.on_confirmed_password_change}/>
                                        </div>
                                        <div className="registerbox-textbox">
                                            <select className="form-control" name="country" defaultValue=""
                                                    onChange={this.on_secret_question_change}>
                                                <option disabled key="" value="">{R.select_secret_question}</option>
                                                {secret_questions}
                                            </select>
                                        </div>
                                        <div className="registerbox-textbox">
                                            <input type="password" className="form-control"
                                                   placeholder={R.placeholder_secret_answer}
                                                   onChange={this.on_secret_answer_change}/>
                                        </div>
                                        <div className="registerbox-submit">
                                            {submit_button}
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td className="registerbox-menu-right-ribbon"></td>
                </tr>
                </tbody>
            </table>
        </div>);
    }
});