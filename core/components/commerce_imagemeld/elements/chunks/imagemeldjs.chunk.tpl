<script>
    /*CommerceImageMeld.onReady(function() {
        CommerceImageMeld.initialize('[[+image_tpl]]');
    });*/
</script>

[[-<script>
    function saveSrcImg() {
        var srcFile = document.getElementById("commerce-imagemeld-input-file").files[0];
        var srcFileReader = new FileReader();
        srcFileReader.addEventListener("load", function () {
            document.getElementById("commerce-imagemeld-input-src").value = srcFileReader.result;
        },false);
        if (srcFile) {
            srcFileReader.readAsDataURL(srcFile);
        }
    }

    function loadSrcImg(canvas, fabric, wrapper) {
        // Remove any loaded image first
        var loadedImg = canvas.getActiveObject();
        if(loadedImg) {
            canvas.remove(loadedImg);
        }

        var srcFile = document.getElementById("commerce-imagemeld-input-file").files[0];
        var url = URL.createObjectURL(srcFile);

        /*
        var fileType = srcFile.type;
            if (fileType === 'image/png') { //check if png
                  fabric.Image.fromURL(url, function(img) {
                     img.set({
                        width: 180,
                        height: 180
                     });
                     canvas.add(img);
                  });
               } else if (fileType === 'image/svg+xml') { //check if svg
                  fabric.loadSVGFromURL(url, function(objects, options) {
                     var svg = fabric.util.groupSVGElements(objects, options);
                     svg.scaleToWidth(180);
                     svg.scaleToHeight(180);
                     canvas.add(svg);
                  });
               }
         */

        fabric.Image.fromURL(url, function(oImg) {
            canvas.add(oImg);
            oImg.setControlsVisibility({
                mt: false,
                mb: false,
                ml: false,
                mr: false,
            });
            oImg.scaleToHeight(canvas.height);
        },{
            top: 0,
            left: Math.round(wrapper.offsetWidth / 2),
            originX: 'center',
            originY: 'top'
        });
    }

    window.onload = function () {
        var tplWidth;
        var tplHeight;
        var scale;
        var wrapper = document.getElementById("commerce-imagemeld-wrapper");
        var overlayImg = '[[+image_tpl]]';

        var tmpImg = new Image();

        tmpImg.onload = function() {
            tplWidth = this.width;
            tplHeight = this.height;

            var canvas = new fabric.Canvas('commerce-imagemeld-canvas');
            //console.log(canvas.width);
            //console.log(tplWidth);

            // Must have a height set in CSS or on element!
            canvas.setHeight(wrapper.offsetHeight);

            canvas.backgroundColor = new fabric.Pattern({source: cimBgImg});
            canvas.controlsAboveOverlay = true;

            canvas.setOverlayImage(overlayImg, function() {
                canvas.overlayImage.scaleToHeight(canvas.height);

                scale = canvas.overlayImage.scaleY;

                if(wrapper.offsetWidth < tplWidth * scale) {
                    canvas.setWidth(wrapper.offsetWidth);
                } else {
                    canvas.setWidth(tplWidth * scale);
                }
                canvas.renderAll.bind(canvas);

                canvas.overlayImage.top = 0;
                canvas.overlayImage.left = Math.round(wrapper.offsetWidth / 2);
                canvas.overlayImage.originX = 'center';
                canvas.overlayImage.originY = 'top';
            });

            document.getElementById("commerce-imagemeld-input-file").addEventListener("change", function(e) {
                loadSrcImg(canvas,fabric, wrapper);
            });

            document.getElementById("commerce-imagemeld-preview").addEventListener("click", function(e) {
                canvas.backgroundColor = '#000000';
                var multiplyBy = 1 / scale;
                var dataURL = canvas.toDataURL({multiplier: multiplyBy});
                document.getElementById("commerce-imagemeld-input-meld").value = dataURL;

                var data = atob( dataURL.substring( "data:image/png;base64,".length ) ),
                    asArray = new Uint8Array(data.length);

                for( var i = 0, len = data.length; i < len; ++i ) {
                    asArray[i] = data.charCodeAt(i);
                }

                var blob = new Blob( [ asArray.buffer ], {type: "image/png"} );

                var img = document.createElement("img");
                img.src = (window.webkitURL || window.URL).createObjectURL( blob );

                document.body.appendChild(img);
                canvas.backgroundColor = new fabric.Pattern({source: cimBgImg});
            });

        }
        tmpImg.src = overlayImg;

    }
</script>]]