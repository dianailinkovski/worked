var EditorDevExtremeTimePicker = React.createClass({
    render: function() {
        var context = this;
        return <div ref={function(ref){context.domElement = ref}} />;
    },
    componentDidMount:function()
    {
        var context = this;
        var element = $(this.domElement).dxDateBox({
            format: "time",
            readOnly: this.props.readOnly,
            value: this.props.value == null? null:new Date(this.props.value),
            onValueChanged: function(data) {
                context.props.onChange(data.value);
            }
        });
    }
});
