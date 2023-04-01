var ResetPasswordComponent = React.createClass({
    getInitialState: function() {
        return {reset_password_token:get_parameter_by_name("reset_password_token")};
    },
    handle_submit: function(event) {
        event.preventDefault();

        if (this.state.password != this.state.confirm_password) {
            this.setState({error:R.message_passwords_dont_match});
            return;
        }

        var data = {};
        data.reset_password_token = this.state.reset_password_token;
        data.password = this.state.password;

        var context = this;
        $.ajax({
            url:"/api/auth/reset_password_token",
            method: "POST",
            data: data,
            success: function(data) {
                if (data.success) {
                    context.props.on_complete();
                    Notify(R.message_successfully_reset_password, 'bottom-right', 20000, 'green', 'fa-check', true);
                }
            },
            error: function(error) {
                var response = error.responseJSON;
                console.log(response);
                context.setState({error:response.message});
            }
        })
    },
    on_password_change: function(event) {
        this.setState({password:event.target.value});
    },
    on_confirm_password_change: function(event) {
        this.setState({confirm_password:event.target.value});
    },
    on_error_close: function() {
        this.setState({error:undefined});
    },
    componentWillUnmount: function()
    {
        document.body.className = "";
    },
    render: function() {
        document.body.className = "register-body";
        var error_component = "";
        if (this.state.error) {
            error_component = <ErrorNotificationComponent message={this.state.error} on_close={this.on_error_close} />;
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
                                            <input type="password" className="form-control" placeholder={R.placeholder_password} onChange={this.on_password_change} />
                                        </div>
                                        <div className="registerbox-textbox">
                                            <input type="password" className="form-control" placeholder={R.placeholder_confirm_password} onChange={this.on_confirm_password_change} />
                                        </div>
                                        <div className="registerbox-submit">
                                            <input type="submit" className="btn btn-primary btn-block" value={R.label_submit} />
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