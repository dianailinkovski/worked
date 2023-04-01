var EditorDevExtremeMaskedTextBox = React.createClass({
    render: function() {
        var context = this;
        return <div ref={function(ref){context.domElement = ref}} />;
    },
    componentDidMount:function()
    {
        var context = this;
        var element = $(this.domElement).dxTextBox(context.props);
        element.dxTextBox('instance').on("valueChanged", function(data) {
            context.props.onChange(data.value);
        });
    }
});
