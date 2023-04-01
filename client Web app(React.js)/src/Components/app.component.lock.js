var LockComponent = React.createClass({
    getInitialState: function() {
        return {};
    },
    handle_submit: function(event) {
        event.preventDefault();

        if (this.state.logging_in) {
            return;
        }

        this.setState({logging_in:true});

        var login_name = this.props.user_info.login_name;
        if (!login_name) {
            login_name = this.props.user_info.email_address;
        }

        var credentials = login_name + ":" + this.state.password;
        credentials = window.btoa(credentials);

        var context = this;

        do_authenticated_http_call({
            method: "POST",
            url: "/api/auth/login",
            headers: {
                Authorization: "Basic " + credentials
            },
            success: function(data) {
                if (context.state.error) {
                    context.setState({error:undefined});
                }

                context.setState({success:true});

                console.log(data);
                context.props.on_success();
            },
            error: function(error) {
                if (error.responseJSON) {
                    return context.setState({error:error.responseJSON});
                }
            },
            complete: function() {
                if (!context.state.success) {
                    context.setState({logging_in: false});
                }
            }
        });
    },
    on_password_change: function(event) {
        this.setState({password:event.target.value});
    },
    render: function() {
        var error_component = "";
        if (this.state.error) {
            error_component = <ErrorNotificationComponent message={this.state.error.message} />
        }

        return (<div className="lock-container animated fadeInDown">
            <div className="lock-box text-align-center">
                <div className="lock-username">{this.props.user_info.first_name + " " + this.props.user_info.last_name}</div>
                <img src={this.props.user_avatar} alt={this.props.user_info.first_name + " " + this.props.user_info.last_name} />
                <div className="lock-password">
                    {error_component}
                    <form role="form" className="form-inline" onSubmit={this.handle_submit}>
                        <div className="form-group">
                                <span className="input-icon icon-right">
                                        <input className="form-control" placeholder={R.placeholder_password} type="password" onChange={this.on_password_change} />
                                            <i className="glyphicon glyphicon-log-in themeprimary"></i>
                                </span>
                            <input hidden type="submit" />
                        </div>
                    </form>
                </div>
            </div>
        </div>);
    }
});