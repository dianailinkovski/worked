var EditorDevExtremeSwitchYesNo = React.createClass({
    render: function() {
        var context = this;
        return <div ref={function(ref){context.domElement = ref}} />;
    },
    componentDidMount:function()
    {
        var context = this;
        var element = $(this.domElement).dxSwitch({
            readOnly: this.props.readOnly,
            value: this.props.value,
            onText: R.label_yes,
            offText: R.label_no,
            onValueChanged: function(data) {
                context.props.onChange(data.value);
            }
        });
    }
});
