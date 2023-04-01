var EditorDevExtremeCheckBox = React.createClass({
    render: function() {
        var context = this;
        return <div ref={function(ref){context.domElement = ref}} />;
    },
    componentDidMount:function()
    {
        var context = this;
        var element = $(this.domElement).dxCheckBox({
            readOnly: this.props.readOnly,
            value: this.props.value,
            text: this.props.text,
            onValueChanged: function(data) {
                context.props.onChange(data.value);
            }
        });
    }
});
