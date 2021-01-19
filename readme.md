Image Meld for Commerce on MODX
==

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


Requirements
-

- MODX CMS 2.6.5 +
  
  https://modx.com/download
  

- Commerce for MODX 1.2 +

  https://modmore.com/commerce/  


- PHP 7.1 +


Installation
-

Install via the MODX package manager.

Setup
-
1. **System Settings:** Set the `commerce_imagemeld.melds_path` and `commerce_imagemeld.melds_url` to 
your preferred location or leave as the default.

2. Create a product detail page with the add to cart form that you want to use.

2. Add the `Commerce_ImageMeld` snippet at the top of your page template.
The snippet requires an `image` parameter for the overlaid image template you want to use. You could use
   the `[[++assets_url]]` system setting along with the path to your image. Or, you could 
   use a TV. 
   
**This must be a PNG image.**
   
Example:

```
[[Commerce_ImageMeld?
    &image=`[[++assets_url]]uploads/template.png`
]]
```

or

```
[[Commerce_ImageMeld?
    &image=`[[*my_template_var]]`
]]
```

4. Add the placeholders to your HTML markup. The output is split into multiple placeholders to be
as flexible as possible. You can even modify them and add your own (see snippet section below).
   
- `[[+cim.canvas]]` - this is the editing canvas _(place anywhere)_
- `[[+cim.file_input]]` - this is the upload button customers use to add their image _(place anywhere)_
- `[[+cim.controls]]` - zoom, rotate, move and save buttons _(place anywhere)_
- `[[+cim.preview]]` - this is where the final image is shown after clicking save. _(A good place for this might 
  be a modal window that shows on save along with the add to cart form/button, but it can be placed anywhere)_
- `[[+cim.hidden_inputs]]` - this holds all the values needed to be submitted along with the add to cart form. 
  **(must be placed inside the `<form></form>` tags.)**
  
5. Success!


Snippet Parameters
-

This module has a single snippet `[[Commerce_ImageMeld]]`.
It should be added to the top of your MODX page template. The snippet doesn't return anything itself, all output is via placeholders.

There are 7 parameters available in total. Only one is required.

**Required**
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

