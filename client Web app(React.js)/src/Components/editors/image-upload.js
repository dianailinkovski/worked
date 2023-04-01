var EditorDevExtremeImageUpload = React.createClass({
    getInitialState: function () {
        return {
            data: this.props.value,
            initState: this.props.value
        };
    },
    render: function () {
        var deleteBtn = null;
        if (!this.props.readOnly && this.state.data != null)
        {
            deleteBtn = <span onClick={this.handle_delete} title={R.label_delete} className="delete-image-span"></span>;
        }
        var context = this;
        return <div>
            {this.state.data == null || this.state.data == "" ? null :
                <div className="img-wrap">{deleteBtn}<img style={{"maxWidth":"100","maxHeight":"100"}} src={this.state.data}/></div>}
            <div className="editor-image-upload-control" ref={function(ref){context.domElement = ref}}/>
        </div>;
    },
    handle_delete: function (e) {
        this.element.reset();
        e.preventDefault();
        this.setState({data: null});
        this.props.onChange(null);
    },
scaleCanvasWithAlgorithm: function(canvas) {
    var scaledCanvas = document.createElement('canvas');
    var scale =  Math.min((this.props.resizeWidth / canvas.width), (this.props.resizeHeight / canvas.height)) ;
    scaledCanvas.width = canvas.width * scale;
    scaledCanvas.height = canvas.height * scale;
    var srcImgData = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height);
    var destImgData = scaledCanvas.getContext('2d').createImageData(scaledCanvas.width, scaledCanvas.height);
    this.applyBilinearInterpolation(srcImgData, destImgData, scale);
    scaledCanvas.getContext('2d').putImageData(destImgData, 0, 0);
    return scaledCanvas;
},
getHalfScaleCanvas: function(canvas) {
    var halfCanvas = document.createElement('canvas');
    halfCanvas.width = canvas.width / 2;
    halfCanvas.height = canvas.height / 2;
    halfCanvas.getContext('2d').drawImage(canvas, 0, 0, halfCanvas.width, halfCanvas.height);
    return halfCanvas;
},
applyBilinearInterpolation: function(srcCanvasData, destCanvasData, scale) {
    function inner(f00, f10, f01, f11, x, y) {
        var un_x = 1.0 - x;
        var un_y = 1.0 - y;
        return (f00 * un_x * un_y + f10 * x * un_y + f01 * un_x * y + f11 * x * y);
    }
    var i, j;
    var iyv, iy0, iy1, ixv, ix0, ix1;
    var idxD, idxS00, idxS10, idxS01, idxS11;
    var dx, dy;
    var r, g, b, a;
    for (i = 0; i < destCanvasData.height; ++i) {
        iyv = i / scale;
        iy0 = Math.floor(iyv);
        // Math.ceil can go over bounds
        iy1 = (Math.ceil(iyv) > (srcCanvasData.height - 1) ? (srcCanvasData.height - 1) : Math.ceil(iyv));
        for (j = 0; j < destCanvasData.width; ++j) {
            ixv = j / scale;
            ix0 = Math.floor(ixv);
            // Math.ceil can go over bounds
            ix1 = (Math.ceil(ixv) > (srcCanvasData.width - 1) ? (srcCanvasData.width - 1) : Math.ceil(ixv));
            idxD = (j + destCanvasData.width * i) * 4;
            // matrix to vector indices
            idxS00 = (ix0 + srcCanvasData.width * iy0) * 4;
            idxS10 = (ix1 + srcCanvasData.width * iy0) * 4;
            idxS01 = (ix0 + srcCanvasData.width * iy1) * 4;
            idxS11 = (ix1 + srcCanvasData.width * iy1) * 4;
            // overall coordinates to unit square
            dx = ixv - ix0;
            dy = iyv - iy0;
            // I let the r, g, b, a on purpose for debugging
            r = inner(srcCanvasData.data[idxS00], srcCanvasData.data[idxS10], srcCanvasData.data[idxS01], srcCanvasData.data[idxS11], dx, dy);
            destCanvasData.data[idxD] = r;

            g = inner(srcCanvasData.data[idxS00 + 1], srcCanvasData.data[idxS10 + 1], srcCanvasData.data[idxS01 + 1], srcCanvasData.data[idxS11 + 1], dx, dy);
            destCanvasData.data[idxD + 1] = g;

            b = inner(srcCanvasData.data[idxS00 + 2], srcCanvasData.data[idxS10 + 2], srcCanvasData.data[idxS01 + 2], srcCanvasData.data[idxS11 + 2], dx, dy);
            destCanvasData.data[idxD + 2] = b;

            a = inner(srcCanvasData.data[idxS00 + 3], srcCanvasData.data[idxS10 + 3], srcCanvasData.data[idxS01 + 3], srcCanvasData.data[idxS11 + 3], dx, dy);
            destCanvasData.data[idxD + 3] = a;
        }
    }
},
    scale_image: function (image) {
        var img = new Image();
        img.src = image;
        var canvas = document.createElement('canvas');
        canvas.width = img.width;
        canvas.height = img.height;
        canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);

        while (canvas.width >= (2 * this.props.resizeWidth) || canvas.height >= (2 * this.props.resizeHeight)) {
            canvas = this.getHalfScaleCanvas(canvas);
        }

        if (canvas.width > this.props.resizeWidth || canvas.height > this.props.resizeHeight) {
            canvas = this.scaleCanvasWithAlgorithm(canvas);
        }

        var imageData = canvas.toDataURL('image/jpeg', 0.92);
        return imageData;
    },
    componentDidMount: function () {
        var context = this;
        if (this.props.readOnly)
            return;
        this.element = $(this.domElement).dxFileUploader({
            accept: "image/*",
            showFileList: false,
            selectButtonText: this.props.selectButtonText,
            uploadMode: 'useForm',
            onValueChanged: function (data) {
                if (data.value != null) {
                    if (!_.isUndefined(FileReader)) {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            var image = e.target.result;
                            if (context.props.resizeWidth && context.props.resizeHeight) {
                                image = context.scale_image(e.target.result);
                            }
                            context.setState({data: image});
                            context.props.onChange(image);
                        }
                        reader.readAsDataURL(data.value);
                    }
                } else {
                    context.setState({data: context.state.initState});
                    context.props.onChange(context.state.initState);
                }
            }
        }).dxFileUploader("instance");
    }
});
