var EditorDevExtremeTagBox = React.createClass({
    getInitialState: function() {
        return {};
    },
    render: function() {
        var context = this;
        var className = "";
        if (this.props.add_button)
        {
            className = "add-button-margin";
        }
        return <div className={className} ref={function(ref){context.domElement = ref}} />;
    },
    componentWillReceiveProps:function(nextProps) {
        if (nextProps.values)
        {
            this.state.tagbox_element._valuesData = this.parseValues(nextProps.values);
            this.state.tagbox_element.repaint();
        }
    },
    parseValues:function(inputValues)
    {
        var values = [];
        for (var i=0;i<inputValues.length;i++)
        {
            var item = inputValues[i];
            if (typeof item == "object")
            {
                values.push(item[this.props.valueExpr]);
            }
            else {
                values.push(item);
            }
        }
        return inputValues;
    },
    componentDidMount:function()
    {
        var context = this;
        var tagbox_element = $(this.domElement);

        var values = this.parseValues(context.props.values);

        this.state.tagbox_element = tagbox_element.dxTagBox({
            readOnly: context.props.readOnly,
            values: values,
            displayExpr: context.props.displayExpr,
            valueExpr: context.props.valueExpr,
            dataSource: context.props.dataSource,
            searchEnabled: true,
            onValueChanged: function(data) {
                context.props.onChange(data.values);
            }
        }).dxTagBox("instance");
        // Handle adding item on enter
        this.state.tagbox_element.registerKeyHandler("enter", function(e) {
            e.preventDefault();
            if(this.option("fieldEditEnabled") && !this._$list.find(".dx-state-focused").length) {
                this._completeSelection();
            } else {
                if (this.option("opened"))
                {
                    this._selectFirstItem();
                    this._completeSelection();
                }
                else {
                    this.option("opened") && e.preventDefault();
                    this._keyboardProcessor._childProcessors[0].process(e);
                }
            }
        });
    }
});
