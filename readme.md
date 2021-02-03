# Image Meld for Commerce on MODX

**Enables customers to meld/merge images to create custom designs when purchasing products.**

** *Thanks to **nopixel** for sponsoring the development of this module.*

This module takes advantage of the Fabric.js javascript canvas library http://fabricjs.com/

The snippet is designed to be used on a product detail page, where the editing canvas will be presented
with a template image selected by you, the developer (or shop owner). The template image acts
as an overlay with transparent sections where the image can be customised. Think of an item such as
sunglasses frames, a watch band, or a badge. 

The customer uploads the image/pattern which appears behind the overlaid image template, and they 
can then use the controls to zoom, rotate and move the image until they're happy with the positioning.
When the product is added to the cart, both the original source image provided by the customer, and
the newly created melded images are uploaded to the server and added to the customer's order.

When viewing the order in the manager, thumbnails of both images are shown on each product with options 
to download directly or open the full-sized image in a new window.


## Requirements: 

- [MODX CMS 2.6.5 +](https://modx.com/download)
- [Commerce for MODX 1.2 +](https://modmore.com/commerce/)
- PHP 7.1 +


## Installation

1. Install via the MODX package manager.
2. Enable the module in Commerce (Dashboard -> Configuration -> Modules)

##Setup

1. **System Settings:** Set the `commerce_imagemeld.melds_path` and `commerce_imagemeld.melds_url` to 
your preferred location or leave as the default.

2. Create a product detail page with the add to cart form that you want to use.

2. Add the `Commerce_ImageMeld` snippet at the top of your page template.
In addition to the `&productId` parameter, the snippet requires an `&image` parameter for the 
   overlaid image template you want to use. You could use the `[[++assets_url]]` system setting 
   along with the path to your image. Or, you could use a TV. 
   
**This must be a PNG image.**

The snippet must be called uncached `[[!`
   
### Example:

``` php
[[!Commerce_ImageMeld?
    &productId=`10`
    &image=`[[++assets_url]]uploads/template.png`
]]
```

or

``` php
[[!Commerce_ImageMeld?
    &productId=`10`
    &image=`[[*my_template_var]]`
]]
```

4. Add the placeholders to your HTML markup. The output is split into multiple placeholders to be
as flexible as possible. You can even modify them and add your own (see snippet section below).

- `[[+cim.product_id]]` - outputs the product id
- `[[+cim.canvas]]` - this is the editing canvas _(place anywhere)_
- `[[+cim.file_input]]` - this is the upload button customers use to add their image _(place anywhere)_
- `[[+cim.controls]]` - zoom, rotate, move and save buttons _(place anywhere)_
- `[[+cim.default_css]]` - this css shows the required size values for the canvas elements. Either use this 
  or add the same CSS rules to your stylesheet _(place in some `<style></style>` tags)_
- `[[+cim.preview]]` - this is where the final image is shown after clicking save. _(A good place for this might 
  be a modal window that shows on save along with the add to cart form/button, but it can be placed anywhere)_
- `[[+cim.hidden_inputs]]` - this holds all the values needed to be submitted along with the add to cart form. 
  **(must be placed inside the `<form></form>` tags.)**
- `[[+cim.error_msg]]`  - Outputs error msg if something goes wrong.

5. Success!


##Snippet Parameters

This module has a single snippet `[[Commerce_ImageMeld]]`.
It should be added to the top of your MODX page template. The snippet doesn't return anything itself, all output is via placeholders.

There are 8 parameters available in total. Only two are required.

**Required**
- `&productId`: **REQUIRED** The product id of the ImageMeld product.
- `&image`: **REQUIRED** as shown above, the value should be the image URL.
  
**Advanced**
- `&includeJS`: Default is `1`. Set this to `0` if you want to ignore the default JavaScript and 
write your own.

**Custom Template Chunks**
- `&tplCanvas`: value should be the name of a custom canvas chunk.
- `&tplFileInput`: value should be the name of a custom file input chunk.
- `&tplHiddenInputs`: value should be the name of a custom hidden inputs chunk.
- `&tplControls`: value should be the name of a custom controls chunk.
- `&tplPreview`: value should be the name of a custom preview chunk.


### Example:

Here's an example implementation for an imaginary custom skateboard design with screenshots and then full template code below.
This has been implemented using Foundation but can easily be refactored for Tailwind, Bootstrap etc.

1. Before uploading an image:
![screenshot0](https://user-images.githubusercontent.com/5160368/105943032-5a882000-609b-11eb-982d-a6e4c6af4f09.png)


2. After uploading an image and positioning with the controls:
![screenshot1](https://user-images.githubusercontent.com/5160368/105943066-6c69c300-609b-11eb-9550-9d44f42cda7b.png)


3. After saving the image, this implementation opens a modal where the customer can select the quantity and add to cart.
![screenshot2](https://user-images.githubusercontent.com/5160368/105943086-7db2cf80-609b-11eb-90f9-8477216059dd.png)
   

4. The order view in the Commerce manager page. Both source image and melded image are available for download:
   ![screenshot3](https://user-images.githubusercontent.com/5160368/105943108-899e9180-609b-11eb-844e-2c824d49db6f.png)
   

### Example Template

``` html
<!doctype html>
<html lang="en">
<head>
    <title>[[*pagetitle]] - [[++site_name]]</title>
    <base href="[[!++site_url]]" />
    <meta charset="[[++modx_charset]]" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/foundation-sites@6.6.3/dist/css/foundation-float.min.css" integrity="sha256-4ldVyEvC86/kae2IBWw+eJrTiwNEbUUTmN0zkP4luL4=" crossorigin="anonymous">
    
    [[Commerce_ImageMeld?
        &image=`[[++assets_url]]skateboard.png`
        &tplControls=`customControls`
        &productId=`11`
    ]]
    
    <style>
        [[+cim.default_css]]
        body { 
            padding:60px 0;
        }
        .edit-region {
            position:relative;
        }
        .commerce-imagemeld-buttons {
            position:absolute;
            top:0;
            left:0;
            z-index:10;
            display:flex;
            flex-direction:column;
        }
        .commerce-imagemeld-buttons button {
            padding:10px;
            background-color:#222;
            cursor:pointer;
        }
        .commerce-imagemeld-buttons button:hover {
            background-color:#444;
        }
        .upload-image {
            position:absolute;
            top:0;
            right:0;
            z-index:10;
        }
        
    </style>
</head>
<body>


<div class="row">
    <div class="columns medium-offset-2 medium-5">
        <div class="edit-region">
            <div class="upload-image">
                <label for="commerce-imagemeld-input-file" class="button">Upload Image</label>
                <input type="file" class="show-for-sr" id="commerce-imagemeld-input-file">
            </div>
            [[+cim.controls]]
            [[+cim.canvas]]
        </div>
    </div>
    <div class="columns medium-3 end">
        
        <label>Save and preview your design</label>
        <div class="save-image">
            <button type="button" class="button" id="commerce-imagemeld-save-btn">[[%commerce_imagemeld.form.save? &namespace=`commerce_imagemeld` &topic=`default` &language=`[[++cultureKey]]`]]</button>
        </div>
    </div>
</div>

<div class="reveal small" id="previewModal" data-reveal>
    <div class="row">
        <div class="columns medium-9">
            [[+cim.preview]]
        </div>
        <div class="columns medium-3">
            <form method="post" id="commerce-imagemeld-form" action="[[~[[++commerce.cart_resource]]]]" enctype="multipart/form-data">
                <input type="hidden" name="add_to_cart" value="1">
                
                <input type="hidden" name="commerce_imagemeld_product_id" id="commerce-imagemeld-product" value="[[+cim.product_id]]">
                <input type="hidden" name="commerce_imagemeld_product_link" value="[[*id]]">
                <input type="hidden" name="commerce_imagemeld_input_meld" id="commerce-imagemeld-input-meld">
                <input type="hidden" name="commerce_imagemeld_input_src" id="commerce-imagemeld-input-src">
                
                <label for="quantity">Quantity</label>
	            <input type="number" id="quantity" name="products[ [[+cim.product_id]] ][quantity]" value="1">
	                
                <button class="button" type="submit">Add to Cart</button>
            </form>
        </div>
    </div>
</div>

<div class="reveal" id="errorModal" data-reveal>
    [[- Add your own lexicon and text here ]]
    You must upload an image before saving your design.
</div>

<div class="reveal" id="errorReturnModal" data-reveal>
    [[+cim.error_msg]]
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/foundation-sites@6.6.3/dist/js/foundation.min.js" integrity="sha256-pRF3zifJRA9jXGv++b06qwtSqX1byFQOLjqa2PTEb2o=" crossorigin="anonymous"></script>
<script>
    $(document).foundation();
</script>
<script>
    CommerceImageMeld.onReady(function() {
        document.getElementById('commerce-imagemeld-save-btn').addEventListener('click', function (e) {
            // Check image has been uploaded
            if(document.getElementById('commerce-imagemeld-input-src').value) {
                $('#previewModal').foundation('open');
            } else {
                $('#errorModal').foundation('open');
            }
        });
        if(cimError !== false) {
            $('#errorReturnModal').foundation('open');
            
            // Optional: add the following to remove params from url to prevent modal showing again on page reload.
            var currentUrl = window.location.href;
            var newUrl = currentUrl.split('?')[0];
            window.history.replaceState({}, document.title, newUrl);
        }
    });
</script>
</body>
</html>

```

This implementation also uses a custom chunk for the controls in order to separate the save button from the other controls.
The `customControls` chunk is the same as the default just with the save button removed.
