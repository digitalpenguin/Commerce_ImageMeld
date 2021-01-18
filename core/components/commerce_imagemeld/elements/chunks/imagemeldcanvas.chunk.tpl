[[- The button to upload an image ]]
<input type="file" id="commerce-imagemeld-input-file">


[[- The canvas element ]]
<div id="commerce-imagemeld-wrapper">
    <canvas id="commerce-imagemeld-canvas"></canvas>
</div>

[[- The melded image will display in this div after clicking the preview button ]]
<div id="commerce-imagemeld-preview"></div>

[[- The control buttons used to manipulate the image on the canvas ]]
<div class="commerce-imagemeld-buttons">
    <button type="button" id="commerce-imagemeld-zoomin">
        <img src="[[++commerce_imagemeld.assets_url]]web/img/icons/zoom-in.svg">
    </button>
    <button type="button" id="commerce-imagemeld-zoomout">
        <img src="[[++commerce_imagemeld.assets_url]]web/img/icons/zoom-out.svg">
    </button>
    <button type="button" id="commerce-imagemeld-rotateccw">
        <img src="[[++commerce_imagemeld.assets_url]]web/img/icons/rotate-ccw.svg">
    </button>
    <button type="button" id="commerce-imagemeld-rotatecw">
        <img src="[[++commerce_imagemeld.assets_url]]web/img/icons/rotate-cw.svg">
    </button>
    <button type="button" id="commerce-imagemeld-moveup">
        <img src="[[++commerce_imagemeld.assets_url]]web/img/icons/arrow-up.svg">
    </button>
    <button type="button" id="commerce-imagemeld-movedown">
        <img src="[[++commerce_imagemeld.assets_url]]web/img/icons/arrow-down.svg">
    </button>
    <button type="button" id="commerce-imagemeld-moveleft">
        <img src="[[++commerce_imagemeld.assets_url]]web/img/icons/arrow-left.svg">
    </button>
    <button type="button" id="commerce-imagemeld-moveright">
        <img src="[[++commerce_imagemeld.assets_url]]web/img/icons/arrow-right.svg">
    </button>
    <button type="button" id="commerce-imagemeld-save-btn">Save</button>
</div>