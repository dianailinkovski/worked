var LoginComponent = React.createClass({
    getInitialState: function () {
        return {logging_in: false, username: "", password: "", success: false};
    },
    handle_login: function (event) {
        event.preventDefault();
        if (!this.state.logging_in) {
            // Validate login information here
            this.setState({logging_in: true});

            var credentials = this.state.username + ":" + this.state.password;
            credentials = window.btoa(credentials);

            var component_class = this;

            $.ajax({
                method: "POST",
                url: "/api/auth/login",
                //timeout: 5000,
                headers: {
                    Authorization: "Basic " + credentials
                },
                success: function (data) {
                    if (component_class.state.error) {
                        component_class.setState({error: undefined});
                    }

                    var login_token = data.login_token;
                    if (login_token !== undefined) {
                        localStorage.setItem("login_token", login_token);
                        g_login_token = login_token;

                        component_class.setState({success: true});
                        component_class.props.on_success();
                    }
                    else {
                        // we did not receive a login_token
                    }
                },
                error: function (error) {
                    if (error.responseJSON) {
                        return component_class.setState({error: error.responseJSON.message});
                    }

                    return component_class.setState({error: error.statusText});
                },
                complete: function () {
                    if (!component_class.state.success) {
                        component_class.setState({logging_in: false});
                    }
                }
            });
        }
    },
    handle_forgot_password: function (event) {
        event.preventDefault();
        this.props.on_success(true);
    },
    username_changed: function (event) {
        this.setState({username: event.target.value});
    },
    password_changed: function (event) {
        this.setState({password: event.target.value});
    },
    componentWillUnmount: function()
    {
        document.body.className = "";
    },
    render: function () {
        document.body.className = "login-body";
        if (this.state.logging_in) {

            return (<LoadingComponent />);
        }
        else {
            var error_component = "";
            if (this.state.error) {
                var on_close = function (context) {
                    return function () {
                        context.setState({error: undefined});
                    };
                }(this);
                error_component = <ErrorNotificationComponent message={this.state.error} on_close={on_close}/>;
            }

            return (<div className="login-container animated fadeInDown">
                    <table className="loginbox">
                        <tbody>
                        <tr>
                            <td></td>
                            <td>
                                <div className="loginbox-header01">
                                    <div className="loginbox-header02">
                                        <div style={{"textAlign":"center"}}>
                                            <img src="images/logo.png" alt=""/>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td className="loginbox-menu-border-ribbon"></td>
                            <td>
                                <div className="loginbox-menu_right">
                                    <div className="loginbox-loggedout_right">
                                        <img
                                            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABoAAAAeCAYAAAAy2w7YAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAABCJJREFUeNq8Vm9oG2UYf++uae7SXHJJbk1Su/RqJ02b1qZbF4ubuI2Nsm6IboKIWIdMhIoydIIf/CCICCI4FPwgAzdQtg8FJ34QP7gvc+qgH1a6oQzrWiYhpbHJ5XZ3+R+f58zVozO5q4oP/Li79973/T3v8/ye547a8eJHxMYCgD2AEYDQHMsDFgFXm/e21tHmHQU4BDjcK7CB6UR3YrhHkHjW1YUvlWJF/SktL/14e/3MQlq5+G+InqAocuDEQ9t3Tj0YSwl+H+PxeEhHx59LqtUqF5c0cX+i8Nn8Umb6/W+XZv4J0TjgwCv7B/ZOje8YCwaDBElcLhehadqYUK/XSaVSIYIgMD7e+2xPwNN4dW7xuVZEdIuxx6dHwgPTE4NjkUiE+P1+wjDM18ViMVUoFGjEXVWbXCvoC/gO5wz3986cm9l5bCsnkhiaEp/ZE384FAoRjuNIqVT6MJ/Pn9r39hcNy7xrSWnbxOzhXVcfSUgpURSJphc/OPv06KWTFxbrTk7Uv7svEIlFt/kwXBCeW9ls9vQmEsOuL69VXz9/+Z3VXEHGuWIoGAPHUk5Dx4/1iWFcSIEaFEX5/OC7X1VahUTWyquXry/dwrm4BogmHecoxHs8qC5QFlFV9baNcmvLqzkZ56JY3G531CkRSJhGI7VaDUNXtauRUqVaRxXiGnDQ5ZiIMmqVbMjYzhqNhgFjLUW1VR0H6Gteu62TF+6sS826amWS9SEj691wiQNWALqVCPvXaT/XER6N8t1CF+uOCF0CkiHKtcZTRxLhR2stTsZAuOI9Qticr5Vre4+ORs+oxdLvPjfzxpc31u4YJ4Wm+th9AnvsraOJ46LAe7FuoNqNIsWw5XI5AgVq5OtviRiG+Hw+EggEjBzJskyg5oiu6ySvqNlqSR974eKNNJ7I/+Su2MgD/TEvz/NGL0P14Aa4EEm9Xu9GDu7JJ5wC1yDwHknRWVRhQFHETCYzC9PeNHIUDfm7cEOzdqybgFwNODV0EoGOobOapvVuqM7qkZ26IJw/q8XySqsTbj5pZ2fnX/I2E9m2KiFH4N2JwdmPh5KnPpHOfjM/1ypvVjJzX9pJONB76Nzzydc+PW+OvXfp2ne/prNrdidrW7CbDdWXzRWs+h4GDOSVuxUnBe2YCI11MRMXTk5ONR8nDw6F+7eHvFGn6x0Rocw5jqVBlc+bDeDQSG/cw3GU+cX9T4gwoagelmVvmkRZrbqOY3Yi2nLoMOnpnPob3IrY31aySs6pEOz+gu4h+mVVfvn+kGcfPg9G/OGtEjmSDVb51PhAMiUFk8ZfJfQ2HNsK0XK9bu8athXcHPuetdU4sJpJ9ENBL1+BehiyUx6IwYBTwxoDfL8Rut19wkvwS1UGD4/Dhs53ak9ShD3n4HNxzvwekf/D/hBgAByBgt7jg8aYAAAAAElFTkSuQmCC"/>
                                        &nbsp;{R.login_to_your_account}
                                    </div>
                                </div>
                            </td>
                            <td className="loginbox-menu-border-ribbon"></td>
                        </tr>
                        <tr>
                            <td className="loginbox-menu-left-ribbon"></td>
                            <td>
                                <div style={{"position":"relative"}}>
                                    <div className="loginbox-ribbon-shadow"></div>
                                    <div className="loginbox-content01">
                                    <div className="loginbox-content02">
                                        <form id="login_form" className="bv-form" onSubmit={this.handle_login}>
                                            {error_component}
                                            <div className="loginbox-textbox">
                                                <input type="text" className="form-control" placeholder={R.placeholder_username}
                                                       onChange={this.username_changed}
                                                       data-bv-notempty="true"
                                                       data-bv-regexp="true"
                                                       data-bv-regexp-message={R.the_username_can_only_consist_of_alphabetical_number_dot_and_underscore}
                                                       data-bv-regexp-regexp="(^[a-zA-Z0-9_\.]+$)|(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|&quot;(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*&quot;)@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])"/>
                                            </div>
                                            <div className="loginbox-textbox">
                                                <input type="password" className="form-control" placeholder={R.placeholder_password}
                                                       onChange={this.password_changed}/>
                                            </div>
                                            <div className="loginbox-submit">
                                                <input type="submit" className="btn btn-primary btn-block" value={R.label_login}/>
                                            </div>
                                            <div className="loginbox-forgot">
                                                {R_loc.help_i}&nbsp;<a href="" onClick={this.handle_forgot_password}>{R.label_forgot_password}</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                </div>
                            </td>
                            <td className="loginbox-menu-right-ribbon"></td>
                        </tr>
                        </tbody>
                    </table>

            </div>);
        }
    }
});