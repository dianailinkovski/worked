var ForgotPasswordComponent = React.createClass({
    getInitialState: function() {
        return {error:undefined, disable_submit:true};
    },
    handle_submit: function(event) {
        event.preventDefault();

        var request = {};
        request.secret_answer = this.state.secret_answer;

        if (this.state.username.indexOf("@") == -1) {
            request.login_name = this.state.username;
        }
        else {
            request.email_address = this.state.username;
        }

        var context = this;
        $.post("/api/auth/reset_password", request, function (data) {
            if (data) {
                if (data.success) {
                    context.props.on_complete();

                    Notify(R.message_forgot_pass_email, 'bottom-right', 20000, 'green', 'fa-check', true);
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
        });
    },
    on_username_change: function(event) {
        this.setState({username:event.target.value});
    },
    on_username_blur: function(event) {
        var lookup = {};
        if (this.state.username.indexOf("@") == -1) {
            lookup.login_name = this.state.username;
        }
        else {
            lookup.email_address = this.state.username;
        }

        var context = this;
        do_authenticated_http_call({
            url:"/api/auth/get_secret_question",
            method: "POST",
            data: lookup,
            success: function(data) {
                console.log(data);
                if (data.success && data.secret_question) {
                    context.setState({secret_question:data.secret_question, disable_submit:false});
                }
                else {
                    context.setState({secret_question:undefined});
                }
            },
            error: function(error) {
                var response = error.responseJSON;
                context.setState({error:response.message});
            }
        })
    },
    on_answer_change: function(event) {
        this.setState({secret_answer:event.target.value});
    },
    componentWillUnmount: function()
    {
        document.body.className = "";
    },
    render: function() {
        document.body.className = "register-body";
        var error_component = "";
        if (this.state.error) {
            var on_close = function(context) {
                return function() {
                    context.setState({error:undefined});
                };
            }(this);
            error_component = <ErrorNotificationComponent message={this.state.error} on_close={on_close} />;
        }

        var secret_question = "";
        if (this.state.secret_question) {
            secret_question = <div className="registerbox-textbox">
                <p><b>{R.label_secret_question}:</b> {this.state.secret_question}</p>
                <input type="password" className="form-control" placeholder={R.label_secret_answer} onChange={this.on_answer_change} />
            </div>;
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
                                {R.label_reset_password}
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
                                            <input id="username" type="text" className="form-control" data-bv-notempty="true" data-bv-notempty-message="Hh" placeholder={R.placeholder_username} onChange={this.on_username_change} onBlur={this.on_username_blur} />
                                        </div>
                                        {secret_question}
                                        <div className="registerbox-submit">
                                            <input type="submit" className="btn btn-primary btn-block" value={R.label_submit}/>
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