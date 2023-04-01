var EditorFileUploadModel = React.createClass({
    displayName: "EditorFileUploadModel",
    getInitialState: function () {
        return {
            fileState: 'preparing',
            uploadProgress: 0,
            error: '',
            downloadUrl: ''
        };
    },
    uploadCompleted: function (fileId, file) {
        var context = this;
        var success = function (event) {
            context.props.onUploadEnd();
        }
        var complete = function (event) {
            if (event.status != 200) {
                context.setState({fileState: 'none', error: R_loc.cannot_receive_information_from_server});
            }
        }
        invoke_method('file', 'mark_as_uploaded', {file_id: fileId}, success, null, complete);
    },
    createFileuploadXHR: function (file, url, fileId) {
        var context = this;
        this.jqXHR = $.ajaxSettings.xhr();
        if (this.jqXHR.upload) {
            $(this.jqXHR.upload).bind('progress', function (e) {
                var oe = e.originalEvent;
                if (oe.lengthComputable) {
                    context.setState({uploadProgress: ((oe.loaded + context.alreadyLoaded) / (oe.total + context.alreadyLoaded)) * 100});
                }
            });
        }
        $(this.jqXHR).bind('abort', function (e) {
            context.setState({fileState: 'none'});
        });
        $(this.jqXHR).bind('error', function (e) {
            if (context.tries <= 5) {
                context.tries++;
                this.timeout = setTimeout(function () {
                    if (e.target.status == 0 || e.target.status >= 500) {
                        context.createRangeRequestXHR(file, url, fileId);
                        if (!context.abort) {
                            context.rangeXHR.open("PUT", url, true);
                            context.rangeXHR.setRequestHeader("Content-Range", "bytes */" + file.size.toString());
                            context.rangeXHR.send('');
                        }
                        return;
                    }
                    context.setState({fileState: 'none', error: R_loc.can_not_upload_file_to_the_server});
                }, 5000);
            }

        });
        $(this.jqXHR).bind('load', function (e) {
            if (this.status == 200 || this.status == 201) {
                context.uploadCompleted(fileId, file);
                return;
            }
            context.setState({fileState: 'none'});
        });
    },
    createRangeRequestXHR: function (file, url, fileId) {
        var context = this;
        this.rangeXHR = $.ajaxSettings.xhr();
        $(this.rangeXHR).bind('load', function (e) {
            if (this.status == 308) {
                context.createFileuploadXHR(file, url, fileId);
                context.jqXHR.open("PUT", url, true);
                context.jqXHR.setRequestHeader("Content-Type", file.type);
                var values = e.target.getResponseHeader('range').split('-');
                var start = (parseInt(values[1]) + 1);
                context.tries = 0;
                context.alreadyLoaded = start;
                context.jqXHR.setRequestHeader("Content-Range", 'bytes ' + start.toString() + '-' + (file.size - 1).toString() + '/' + file.size.toString());
                context.jqXHR.send(file.slice(start));
                return
            } else if (this.status == 200 || this.status == 201) {
                context.uploadCompleted(fileId, file);
                return
            }
            context.setState({fileState: 'none', error: R_loc.can_not_upload_file_to_the_server});
        });
        $(this.rangeXHR).bind('abort', function (e) {
            context.setState({fileState: 'none'});
        });
        $(context.rangeXHR).bind('error', function (e) {
            if (context.tries <= 5) {
                context.tries++;
                this.timeout = setTimeout(function () {
                    if (e.target.status == 0 || e.target.status >= 500) {
                        context.createRangeRequestXHR(file, url, fileId);
                        if (!context.abort) {
                            context.rangeXHR.open("PUT", url, true);
                            context.rangeXHR.setRequestHeader("Content-Range", "bytes */" + file.size.toString());
                            context.rangeXHR.send('');
                        }
                        return;
                    }
                    context.setState({fileState: 'none', error: R_loc.can_not_upload_file_to_the_server});
                }, 5000);
                return;
            }
            context.setState({fileState: 'none', error: R_loc.can_not_upload_file_to_the_server});
        });
    },
    handleCancelClick: function (e) {
        e.preventDefault();
        this.abort = true;
        this.jqXHR.abort();
        if (this.timeout) {
            clearTimeout(this.timeout)
        }
        if (this.rangeXHR) {
            this.rangeXHR.abort();
        }
        this.props.onUploadCancel();
    },
    componentDidMount: function () {
        $(this.domElement).modal('show');
        var context = this;
        this.abort = false;
        this.setState({fileState: 'preparing'});
        var success = function (event) {
            context.createFileuploadXHR(context.props.file, event.data.url, event.data.file_id);
            context.jqXHR.open("PUT", event.data.url, true);
            context.jqXHR.setRequestHeader("Content-Type", context.props.file.type);
            context.tries = 0;
            context.alreadyLoaded = 0;
            context.setState({uploadProgress: 0, error: '', fileState: 'uploading'});
            context.jqXHR.send(context.props.file);
        }
        var complete = function (event) {
            if (event.status != 200) {
                context.setState({fileState: 'none', error: R_loc.cannot_receive_information_from_server});
            }
        }
        invoke_method('file', 'get_upload_url', {file_id: this.props.fileId}, success, null, complete);
    },
    componentWillUnmount: function () {
        this.abort = true;
        if (this.jqXHR) {
            this.jqXHR.abort();
        }
        if (this.timeout) {
            clearTimeout(this.timeout)
        }
        if (this.rangeXHR) {
            this.rangeXHR.abort();
        }
        $(this.domElement).modal('hide');
    },
    render: function () {
        var progress = '';
        var cancel = '';
        var preparing = '';
        var close = '';

        if (this.state.fileState == 'preparing') {
            preparing = <span>{R_loc.preparing_for_upload}</span>;
        }
        if (this.state.fileState == 'uploading') {
            progress = <progress min="0" max="100" value={this.state.uploadProgress} type="file"
                                 style={{"width":"100%"}}>{this.state.uploadProgress}% {R_loc.complete}</progress>;
            cancel =  <div className="text-align-center"><button className='btn btn-default shiny'
                              onClick={this.handleCancelClick}>{R.label_cancel}</button></div>
        }
        if (this.state.fileState == 'none')
        {
            close = <div className="text-align-center"><button className='btn btn-default shiny'
                                                             onClick={this.handleCancelClick}>{R_loc.label_close}</button></div>
        }
        var error = '';
        if (this.state.error != '') {
            error = <div className="alert alert-danger fade in">
                <button className="close" data-dismiss="alert"> ×</button>
                <i className="fa-fw fa fa-times"></i>
                <strong>{R_loc.error}!&nbsp;</strong>
                {this.state.error}
            </div>
        }
        var context = this;

        return <div ref={function(ref){context.domElement = ref}} className="modal fade" tabIndex="-1" role="dialog" aria-labelledby="fileUploadingModalLabel">
            <div className="modal-dialog">
                <div className="modal-content">
                    <div className="modal-header">
                        <h4 className="modal-title">{R_loc.file_uploading}</h4>
                    </div>
                    <div className="modal-body">
                        {error}
                        {progress}
                        {cancel}
                        {close}
                        {preparing}
                    </div>
                </div>
            </div>
        </div>
    }
});
