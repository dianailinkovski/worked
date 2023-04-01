var ErrorNotificationComponent = React.createClass({
    render: function() {
        return (<div className="alert alert-danger fade in">
            <button type="button" className="close" onClick={this.props.on_close}>
                ×
            </button>
            {this.props.message}
        </div>);
    }
});
