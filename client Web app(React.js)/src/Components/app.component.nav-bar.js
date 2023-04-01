var NavBarComponent = React.createClass({
    handle_setting_click: function(event) {
        event.preventDefault();
        this.props.navigation_handler("settings");
    },
    handle_logout_click: function(event) {
        event.preventDefault();
        this.props.navigation_handler("logout");
    },
    sidebar_click:function(){
            $(".sidebar-collapse").toggleClass("active");
            this.props.menu_click($(".sidebar-collapse").hasClass("active"))
    },
    render: function() {
        var first_name = "";
        var last_name = "";
        var email_address = "";

        if (this.props.user_info) {
            first_name = this.props.user_info.first_name;
            last_name = this.props.user_info.last_name;
            email_address = this.props.user_info.email_address;
        }

        console.log(this.props.user_avatar);
        var avatar = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABsAAAAdCAYAAABbjRdIAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADdYAAA3WAZBveZwAAAAHdElNRQffCwwXAhldjvVEAAAAGHRFWHRTb2Z0d2FyZQBwYWludC5uZXQgNC4wLjVlhTJlAAAC3klEQVRIS81WS2sTURSu9Q2CoGAXIriqS3Xlxk3B/oP+AkFw4VJXInHlRnDlVlBQkOCyuHERsJA0Nn0lk0zmlZm5M3nMI6koCNrW63cvd2KSTnQmqeAHH3N655zvzLnn3JvO/NfI5egxYf47bCjOVdnynlW09stqo31jW3av5HK5w02claQTkt56pJHgh+6EFM+f4D64p9p+WG149yXJOyPcp4Pc6DxhCViicVRsv5CXyDkRMhkkxb0GMV7R36hY/kqJ0uMiND1ko/M2TngcJdVZEqHpUKy55zU76MaJjmPd9N+J8HTYxBaiV7txouNYt/ytbDZ7VEgkB0Z8gU1dnOg4KpansukVEskxmEwlAVXtgAtWtBaVTY/bGP3+OiP8DVVVTwqJZCiUjTkIWZHIhkxoqWZzG2eOk9mlqkXXa2Qw2S4O/AMhkwyyyasaOlv4mz9Xy41+NRr5vR5Rsb2GkEkGdr4gcqBftUaHrknW0Noo65b3UcgkQ4bSWfTlw6gQ61Fh26BFXp0/9E683y0r7i0hkxybCrmIhMsjYn2bbd/gFuLKMqt66y6l9IiQSIctvX0Bgl8jwWLFpGW1Sctak1eIBFHivYrmLIiwySEb7ccQ48OigZLR5tPIhiP6CNyLhanuxQj5PDmNpucj4VGqxO9tGe68cJ8equUtsX4N9ojZbA3JdkzTPCVcpwNruE6CV1ECbBnn0M3hdO8I98nBvhi9uQfB75FwLEkQsuqlNPciqpg1zZ3L2JrbEHmNgegcEP4DUbmpO/5To9m7afR6Z4XsQWhu9xKqWEaCb3FCacinlwQ+7OemSYf7abj+PBJ5o0GHw+BF/6BnMplZJHof7zg9UeW+0QwXeTLV9a8je6pf5LREwhVeneaGD+McDpOaE3w2Op25Gc3x32BhfYAlnYQlDMoa7E+ouoh/fIr4ulU0vTBEt8ueq8yHxcFnE3FltEWGbeCdAwbQ+6La4eIv5tjoJ5lkNqsAAAAASUVORK5CYII=";
        if (this.props.user_avatar)
        {
            avatar = this.props.user_avatar;
        }
        return (    <div className="navbar">
                <div className="navbar-inner">
                    <div className="navbar-container">
                        <div className="navbar-header pull-left">
                            <a href="#" className="navbar-brand">
                                <small>
                                    <img src="images/logo.png" alt="" />
                                </small>
                            </a>
                        </div>
                        <div className="sidebar-collapse" id="sidebar-collapse" onClick={this.sidebar_click}>
                            <i className="collapse-icon fa fa-bars"/>
                        </div>
                        <div className="navbar-header pull-right">
                            <div className="navbar-account">
                                <ul className="account-area">
                                    <li>
                                        <a className="login-area dropdown-toggle" data-toggle="dropdown">
                                            <div className="avatar" title="View your public profile">
                                                <img src={avatar} />
                                            </div>
                                        </a>
                                        <ul className="pull-right dropdown-menu dropdown-arrow dropdown-login-area">
                                            <li className="email">
                                                <span className="menu-user-name">{first_name + " " + last_name}</span>
                                                <a>{email_address}</a></li>
                                            <li className="dropdown-footer">
                                                <a href="" className="pull-left" onClick={this.handle_setting_click}>Settings</a>
                                                <a href="" onClick={this.handle_logout_click}>
                                                    Logout
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
});