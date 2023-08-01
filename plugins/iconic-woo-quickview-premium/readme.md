## Installation

To install the plugin:

1. Navigate to `Plugins > Add New > Upload`.
2. Click `Choose File`, and choose the file `jck_woo_quickview.zip` from your CodeCanyon download zip.
3. Once uploaded, click activate plugin.
4. The plugin is now installed and activated.
5. The settings panel can be found under `WooCommerce > Quickview`. Here you can adjust a variety of settings, all with detailed explanations.

## Frontend Usage

Once activated, simply open a category page on your WooCommerce store. When you hover over a product image you will see the Quickview icon appear at the bottom left.

By clicking the Quickview icon, a modal box will appear. Your potential customer can then:

* View the important product information, including: Price, Description and Variations.
* Add the product to their cart, via ajax or page reload.
* View all images of the product.

## Manually Insert the Quickview Button

To insert the configured button into your product loop using PHP, simply add the following to the top of the file where you are inserting the button:

`<?php global $jckqv; ?>`

Then, to display the button in your loop (or anywhere if you pass the correct Product ID), use the following code:

`<?php $jckqv->display_button($product_id); ?>`

To Insert a button anywhere on your WooCommerce website using HTML, simply use the following code:

`<a href="http://www.yourdomain.com/yourproduct" data-jckqvpid="42">Your Link Text</a>`

* The `href` will be used in the unlikely event that a user has javascript disabled.
* `42` is the ID of your product to open in the popup.
* You can use any text, icon or image as the link.

WooCommerce Quickview will work on any element with the data-jckqvpid attribute. For example, you could use a span tag rather than an anchor.

## Available Hooks

The following is a list of hooks available in the plugin, making it easy to customise:

* jckqv-before-description
* jckqv-after-description
* jckqv-before-addtocart
* jckqv-after-addtocart
* jckqv-before-images
* jckqv-after-images
* jckqv-before-meta
* jckqv-after-meta
* jckqv-before-price
* jckqv-after-price
* jckqv-before-rating
* jckqv-after-rating
* jckqv-before-title
* jckqv-after-title

## Modifying a Template

Quickview comes with a variety of template files that build up the modal display. You can modify these by copying any of the files in `/wp-content/plugins/jck-qoo-quickview/templates` to `/wp-content/themes/yourtheme/jck-woo-quickview`.

Although it is possible to do the above, I'd recommend using the hooks, rather than modifying a whole template, in order to prevent any conflicts with future updates.