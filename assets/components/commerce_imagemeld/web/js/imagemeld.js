window.CommerceImageMeld = (function(){
    var wrapper = false;
    var canvas = false;

    var tplWidth = 0;
    var tplHeight = 0;

    var scale = 0;
    var overlayImg = false;
    var tmpImg = false;

    var fileInput = false;
    var srcFile = false;
    var saveBtn = false;
    var previewEl = false;

    var lastUsedImage = false;

    var zoomInBtn = false;
    var zoomOutBtn = false;
    var rotateCCWBtn = false;
    var rotateCWBtn = false;
    var moveLeftBtn = false;
    var moveRightBtn = false;
    var moveUpBtn = false;
    var moveDownBtn = false;

    var api = {
        initialize: function() {
            overlayImg = cimOverlayImg;
            this.getCanvas();
            this.getWrapper();

            tmpImg = new Image();
            tmpImg.src = overlayImg;
            tmpImg.onload = function() {
                tplWidth = this.width;
                tplHeight = this.height;

                canvas.setHeight(wrapper.offsetHeight);
                canvas.backgroundColor = new fabric.Pattern({source: cimBgImg});
                canvas.controlsAboveOverlay = true;

                canvas.setOverlayImage(overlayImg, function () {
                    canvas.overlayImage.scaleToHeight(canvas.height);
                    scale = canvas.overlayImage.scaleY;

                    if (wrapper.offsetWidth < tplWidth * scale) {
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
            }

        },

        getWrapper: function() {
            if (wrapper) {
                return wrapper;
            }
            wrapper = document.getElementById('commerce-imagemeld-wrapper');
            return wrapper;
        },

        getFileInput: function() {
            if (fileInput) {
                return fileInput;
            }
            fileInput = document.getElementById('commerce-imagemeld-input-file');
            return fileInput;
        },

        getCanvas: function() {
            if (canvas) {
                return canvas;
            }
            canvas = new fabric.Canvas('commerce-imagemeld-canvas');
            return canvas;
        },

        getSaveBtn: function() {
            if (saveBtn) {
                return saveBtn;
            }
            saveBtn = document.getElementById('commerce-imagemeld-save-btn');
            return saveBtn;
        },

        getZoomInBtn: function() {
            if (zoomInBtn) {
                return zoomInBtn;
            }
            zoomInBtn = document.getElementById('commerce-imagemeld-zoomin');
            return zoomInBtn;
        },

        getZoomOutBtn: function() {
            if (zoomOutBtn) {
                return zoomOutBtn;
            }
            zoomOutBtn = document.getElementById('commerce-imagemeld-zoomout');
            return zoomOutBtn;
        },

        getRotateCCWBtn: function() {
            if (rotateCCWBtn) {
                return rotateCCWBtn;
            }
            rotateCCWBtn = document.getElementById('commerce-imagemeld-rotateccw');
            return rotateCCWBtn;
        },

        getRotateCWBtn: function() {
            if (rotateCWBtn) {
                return rotateCWBtn;
            }
            rotateCWBtn = document.getElementById('commerce-imagemeld-rotatecw');
            return rotateCWBtn;
        },

        getMoveLeftBtn: function() {
            if (moveLeftBtn) {
                return moveLeftBtn;
            }
            moveLeftBtn = document.getElementById('commerce-imagemeld-moveleft');
            return moveLeftBtn;
        },

        getMoveRightBtn: function() {
            if (moveRightBtn) {
                return moveRightBtn;
            }
            moveRightBtn = document.getElementById('commerce-imagemeld-moveright');
            return moveRightBtn;
        },

        getMoveUpBtn: function() {
            if (moveUpBtn) {
                return moveUpBtn;
            }
            moveUpBtn = document.getElementById('commerce-imagemeld-moveup');
            return moveUpBtn;
        },

        getMoveDownBtn: function() {
            if (moveDownBtn) {
                return moveDownBtn;
            }
            moveDownBtn = document.getElementById('commerce-imagemeld-movedown');
            return moveDownBtn;
        },

        getPreviewEl: function() {
            if (previewEl) {
                return previewEl;
            }
            previewEl = document.getElementById('commerce-imagemeld-preview');
            return previewEl;
        },

        getSrcImg: function() {
            // Remove any loaded image first
            if(lastUsedImage) {
                canvas.remove(lastUsedImage);
            }

            srcFile = this.getFileInput().files[0];
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
                lastUsedImage = oImg;
            },{
                top: Math.round(wrapper.offsetHeight / 2),
                left: Math.round(wrapper.offsetWidth / 2),
                originX: 'center',
                originY: 'center',
                centeredScaling: true
            });



            var srcFileReader = new FileReader();
            srcFileReader.addEventListener("load", function () {
                document.getElementById("commerce-imagemeld-input-src").value = srcFileReader.result;
            },false);
            if (srcFile) {
                srcFileReader.readAsDataURL(srcFile);
            }

        },

        saveMeldImg: function() {
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

            previewEl = this.getPreviewEl();
            previewEl.innerHTML = '';
            previewEl.appendChild(img);
            canvas.backgroundColor = new fabric.Pattern({source: cimBgImg});
        },

        zoomIn: function() {
            var obj = canvas._objects[0];
            if(obj) {
                obj.scale(obj.scaleX * 1.05);
                canvas.requestRenderAll();
            }
        },

        zoomOut: function() {
            var obj = canvas._objects[0];
            if(obj) {
                obj.scale(obj.scaleX / 1.05);
                canvas.requestRenderAll();
            }
        },

        rotateCCW: function() {
            var obj = canvas._objects[0];
            if(obj) {
                obj.rotate(obj.angle - 5);
                canvas.requestRenderAll();
            }
        },

        rotateCW: function() {
            var obj = canvas._objects[0];
            if(obj) {
                obj.rotate(obj.angle + 5);
                canvas.requestRenderAll();
            }
        },

        moveUp: function() {
            var obj = canvas._objects[0];
            if(obj) {
                obj.set('top', obj.get('top') - 5);
                canvas.requestRenderAll();
            }
        },

        moveDown: function() {
            var obj = canvas._objects[0];
            if(obj) {
                obj.set('top', obj.get('top') + 5);
                canvas.requestRenderAll();
            }
        },

        moveLeft: function() {
            var obj = canvas._objects[0];
            if(obj) {
                obj.set('left', obj.get('left') - 5);
                canvas.requestRenderAll();
            }
        },

        moveRight: function() {
            var obj = canvas._objects[0];
            if(obj) {
                obj.set('left', obj.get('left') + 5);
                canvas.requestRenderAll();
            }
        },

        onReady: function(callback) {
            if (document.readyState !== 'loading') {
                callback();
            }
            else if (document.addEventListener) {
                document.addEventListener('DOMContentLoaded', callback);
            }
            else {
                document.attachEvent('onreadystatechange', function() {
                    if (document.readyState !== 'loading') {
                        callback();
                    }
                });
            }
        },

        addEventListener: function (el, eventName, handler) {
            if (el.addEventListener) {
                el.addEventListener(eventName, handler);
            }
            else {
                el.attachEvent('on' + eventName, function(){
                    handler.call(el);
                });
            }
        }

    };

    // STARTS HERE
    api.onReady(function() {
        api.initialize();
        var fileInput = api.getFileInput();
        if (fileInput) {
            api.addEventListener(fileInput, 'change', function (e) {
                api.getSrcImg();
            });
        }

        var saveBtn = api.getSaveBtn();
        if(saveBtn) {
            api.addEventListener(saveBtn, 'click', function (e) {
                api.saveMeldImg();
            });
        }

        var zoomInBtn = api.getZoomInBtn();
        if(zoomInBtn) {
            api.addEventListener(zoomInBtn, 'click', function (e) {
                api.zoomIn();
            });
        }

        var zoomOutBtn = api.getZoomOutBtn();
        if(zoomOutBtn) {
            api.addEventListener(zoomOutBtn, 'click', function (e) {
                api.zoomOut();
            });
        }

        var rotateCCWBtn = api.getRotateCCWBtn();
        if(rotateCCWBtn) {
            api.addEventListener(rotateCCWBtn, 'click', function (e) {
                api.rotateCCW();
            });
        }

        var rotateCWBtn = api.getRotateCWBtn();
        if(rotateCWBtn) {
            api.addEventListener(rotateCWBtn, 'click', function (e) {
                api.rotateCW();
            });
        }

        var moveUpBtn = api.getMoveUpBtn();
        if(moveUpBtn) {
            api.addEventListener(moveUpBtn, 'click', function (e) {
                api.moveUp();
            });
        }

        var moveDownBtn = api.getMoveDownBtn();
        if(moveDownBtn) {
            api.addEventListener(moveDownBtn, 'click', function (e) {
                api.moveDown();
            });
        }

        var moveLeftBtn = api.getMoveLeftBtn();
        if(moveLeftBtn) {
            api.addEventListener(moveLeftBtn, 'click', function (e) {
                api.moveLeft();
            });
        }

        var moveRightBtn = api.getMoveRightBtn();
        if(moveRightBtn) {
            api.addEventListener(moveRightBtn, 'click', function (e) {
                api.moveRight();
            });
        }
    });

    return api;
})();