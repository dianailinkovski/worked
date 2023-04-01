var EditorHtml = React.createClass({
    render: function() {
        var context = this;
        return <div ref={function(ref){context.domElement = ref}} />;
    },
    componentDidMount:function()
    {
        var context = this;
        context.oldValue = this.props.value;
        $(this.domElement).html(this.props.value).tinymce({
            readonly:this.props.readOnly,
            setup : function(ed){
                ed.on('NodeChange', function(e){
                    var content = ed.getContent();
                    if (context.oldValue == content)
                        return;
                    context.oldValue = content;
                    context.props.onChange(content);
                });
            }
        });
    }
});
