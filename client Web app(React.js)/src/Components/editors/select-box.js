var EditorDevExtremeSelectBox = React.createClass({
    render: function() {
        var context = this;
        var className = "";
        if (this.props.add_button)
        {
            className = "add-button-margin";
        }
        return <div className={className} ref={function(ref){context.domElement = ref}} />;
    },
    componentDidMount:function()
    {
        var context = this;

        $(this.domElement).dxSelectBox({
            readOnly: context.props.readOnly,
            value: context.props.value,
            displayExpr: context.props.displayExpr,
            valueExpr: context.props.valueExpr,
            dataSource: context.props.dataSource,
            searchEnabled: true,
            onValueChanged: function(data) {
                context.props.onChange(data.value);
            }
        });
    }
});
