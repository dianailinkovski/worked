var EditorDevExtremeMultiLineTextBox = React.createClass({
    render: function() {
        var context = this;
        return <div ref={function(ref){context.domElement = ref}} />;
    },
    componentDidMount:function()
    {
        var context = this;
        var element = $(this.domElement).dxTextArea({
            height: this.props.height,
            width: this.props.width,
            readOnly: this.props.readOnly,
            value: this.props.value,
            placeholder: this.props.placeholder,
            onValueChanged: function(data) {
                context.props.onChange(data.value);
            }
        });
    }
});
