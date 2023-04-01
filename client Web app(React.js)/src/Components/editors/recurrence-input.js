var EditorRecurrenceInput = React.createClass({
    render: function() {
        var context = this;
        return <textarea ref={function(ref){context.domElement = ref}} />;
    },
    componentDidMount:function()
    {
        var context = this;
        $(this.domElement).val(this.props.value);
        var element = $(this.domElement).recurrenceinput(
            {
                readOnly: context.props.readOnly
            }
        );
        $(this.domElement).on('change',function(e){
            context.props.onChange($(e.target).val());
        })
    }
});
