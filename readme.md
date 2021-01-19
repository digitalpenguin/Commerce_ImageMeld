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

1. 

