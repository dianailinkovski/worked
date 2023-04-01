var EditorFileUpload = React.createClass({
    getInitialState: function () {
        return {
            downloadUrl: undefined
        };
    },
    componentWillReceiveProps:function(nextProps) {
        if (nextProps.resetFileUpload)
        {
            this.fileUploadReset();
            if (nextProps.value) {
                this.getDownloadUrl(this.props.value);
            }
        }
    },
    render: function () {
        if (this.props.readOnly) {
            return <div></div>
        }
        var downloadUrl = '';
        if (this.state.downloadUrl) {
            downloadUrl = <a href={this.state.downloadUrl} download>{R_loc.download}</a>;
        }
        var context = this;
        return <div style={{"marginTop":"6px"}}>
            <input ref={function(ref){context.domElement = ref}} type="file" style={{"display":"inline"}}/>
            {downloadUrl}
        </div>;
    },
    componentDidMount: function () {
        var context = this;
        if (this.props.readOnly)
            return;
        $(this.domElement).on('change', function (e) {
            var file = e.target.files[0];
            context.props.onFileNameChange(file);
        });
        if (this.props.value) {
            this.getDownloadUrl(this.props.value);
        }
        if (this.props.fileName) {
            this.setState({fileName: this.props.fileName});
        }
    },
    getDownloadUrl: function (fileId) {
        var context = this;
        var getFileCompleted = function (data) {
            context.setState({downloadUrl: data.data.url});
        };
        invoke_method('file', 'get_file', {file_id: fileId}, getFileCompleted, null);

    },
    fileUploadReset: function () {
        $(this.domElement).wrap('<form>').closest('form').get(0).reset();
        $(this.domElement).unwrap();
    },
});
