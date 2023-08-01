=== WooCommerce Quick View Pro ===
Contributors: andykeith, barn2media
Tags: woocommerce, quickview, quick, modal, popup
Requires at least: 5.2
Tested up to: 6.1.0
Requires PHP: 7.2
Stable tag: 1.6.13
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

View and purchase WooCommerce products from a lightbox.

== Description ==

WooCommerce Quick View Pro is a plugin that allows you to disaply your WooCommerce products in a lightbox (modal window).

The lightbox allows customers to quickly purchase products, without visiting the individual product pages, and can be configured
to display or hide any of the content normally displayed on the single product page.

You can also use the lightbox to display just product images, turning it into an interactive gallery for the product.

You can configure the plugin to display a 'Quick View' button in your shop, or to display the lightbox when clicking on the
product name or image in your shop listing. It also works alongside our WooCommerce Product Table plugin to show a lightbox directly
from your product tables.

== Installation ==

1. Download the plugin from the Order Confirmation page or using the link in your order email
1. Go to Plugins -> Add New -> Upload and select the plugin ZIP file.
1. Once installed, click to Activate the plugin
1. Configure the plugin settings at WooCommerce -> Settings -> Products -> Quick view

== Frequently Asked Questions ==

Please refer to [our Knowledge Base](https://barn2.com/kb-categories/woocommerce-quick-view-pro-kb/).

== Changelog ==

= 1.6.13 =
Release date 12 November 2022

 * Fix: PHP fatal error when using attribute names ending with `quantity` in WooCommerce Composite Products
 * Tweak: Added support for the new shop links selectors in Woodmart
 * Dev: Tested up to WP 6.1 and WC 7.1

<!-- more -->

= 1.6.12 =
Release date 27 September 2022

 * Fix: PHP Warning about undefined variable on the back end
 * Dev: Tested up to WP 6.0.2 and WC 6.9.4

= 1.6.11 =
Release date 12 July 2022

 * Fix: Use of descriptions for product variations
 * Dev: Tested up to WP 6.0.0 and WC 6.7.0

= 1.6.10 =
Release date 18 May 2022

 * Fix: Cart form is duplicated in a WPT cart column when displaying separate variations
 * Fix: Additional Information tab being displayed even if no product attribute is visible
 * Tweak: Minor styling adjustments
 * Dev: Tested up to WP 5.9.3 and WC 6.5.1

= 1.6.9 =
Release date 24 November 2021

 * Fix: Resolved issue where backend styles and scripts are not enqueued when `SCRIPT_DEBUG` is `true`
 * Dev: Added support for Setup Wizard

= 1.6.8 =
Release date 2 November 2021

 * Fix: Improved integration with WooCommerce Bulk Variations
 * Fix: Resolved issue with Advanced Product Fields for WooCommerce

= 1.6.7 =
Release date 6 October 2021

 * New: Added support for Fast Cart plugin
 * Fix: Resolved issue where only the last product addon is added to the cart
 * Fix: Improved integration with the Astra theme

= 1.6.6 =
Release date 14 September 2021

 * New: Added `sku` parameter to the [quick_view] shortcode
 * Fix: Resolved issue where trying to add to the cart children of a Grouped product always returns a zero-quantity error

= 1.6.5 =
Release date 20 May 2021

 * Fix: Resolved issues where closing the photo modal would also close Quick View Pro modal

= 1.6.4 =
Release date 5 May 2021

 * Fix: Resolved issue where click events within Quick View Pro were prevented from propagating
 * New: Added compatibility with AJAX add-to-cart functionality in Goya theme

= 1.6.3 =
Release date 28 April 2021

 * New: 'Add to Cart' compatibility with WooCommerce Product Tables when variations are set to 'separate'

= 1.6.2 =
Release date 4 April 2021

 * New: Updated internal error handling to provide compatibility with WooCommerce Quantity Manager

= 1.6.1 =
Release date 3 March 2021

 * Fix: Issue with tabs in Quick View when thumbnails are not enabled.
 * Fix: Tabs will no longer appear when "Image Only" mode is selected.
 * Fix: Style issue with focus outline clipping on some themes.
 * New: Added Quick View Pro to new WooCommerce Admin extensions sidebar

= 1.6 =
Release date 25 February 2021

 * New: Ability to show/hide the Quick View button on hover.
 * New: Ability to preview some features within your theme without changing the live site.
 * New: Clicking outside the Quick View modal will close the feature.
 * New: Add long description, reviews, and product attributes to the quick view modal.
 * New: Option to enable/disable quick view for specific categories.
 * Fix: Enabling bullets for Quick View gallery will no longer break the thumbnails on single product pages.

= 1.5.3 =
Release date 9 December 2020

 * Tweak: Improve compatibility with Flatsome theme.


= 1.5.2 =
Release date 30 November 2020

 * Fix: Bug with add to cart button for External/Affiliate Products.

= 1.5.1 =
Release date 17 November 2020

 * Fix: Default variations were not displayed in the Quick View for variable products.
 * Fix: Issues opening Quick View in Avada and Enfold themes.
 * Tweak: Improve compatibility with Avada, Divi and Enfold themes.
 * Tweak: Improve RTL language support.

= 1.5 =
Release date 2 October 2020

 * New: Added a new [quick_view] shortcode to display the Quick View button anywhere on your website.
 * New: Added a new option to display a 'View product details' link in the Quick View.
 * Fix: A bug introduced in version 1.4 which prevented the Quick View working for products displayed using the WooCommerce shortcodes.
 * Fix: Issue when closing the Quick View while the add to cart action was in progress.
 * Fix: Conflict with WooCommerce Composite Products when viewing the single product page.
 * Fix: Conflict with Bootstrap Modal plugin used in Avada and other themes.
 * Tweak: Removed the check for product visibility for the Quick View button and when adding to the cart as this is handled by WooCommerce.
 * Tweak: Improved error handling and validation when adding to the cart.
 * Tweak: Minor styling and theme improvements.

= 1.4.1 =
Release date 21 August 2020

 * Fix: A bug that prevented the Quick View loading for Related Products.
 * Tweak: Added a fallback for Quick View buttons when Javascript is disabled.

= 1.4 =
Release date 14 August 2020

 * New: Added support for the WooCommerce Blocks plugin.
 * Tweak: Improved compatibility with various themes (Avada, Storefront, Total, Bridge, Enfold, Genesis, etc).
 * Fix: Fixed an issue in Storefront and other themes where the cart widget and fragments were not updated correctly when adding to the cart.
 * Fix: Fixed an error when the quick view was closed quickly after adding a product to the cart.
 * Fix: Fixed an error in Flatsome where the setting to show the quick view by clicking the product name could not be disabled.
 * Fix: Fixed an issue which prevented product tables working inside the Quick View.
 * Tweak: Minor improvements to the plugin settings page.
 * Dev: Tested with WordPress 5.5 and WooCommerce 4.4.
 * Dev: Fixed an issue which prevented the wc_quick_view_pro_scripts_enabled_on_page filter working when using quick view in product tables.
 * Dev: Added a body class when the quick view button is displayed in the shop.
 * Dev: Added filters wc_quick_view_pro_shop_loop_button_hook_priority and wc_quick_view_pro_use_default_button_classes.

= 1.3.1 =
Release date 1 May 2020

 * Test: Compatibility with WooCommerce 4.1 and WordPress 5.4.1.
 * Dev: Added Composer support.

= 1.3 =
Release date 7 April 2020

 * Fix: Bug displaying add to cart error messages in the quick view.
 * Tweak: Styling improvements for WooCommerce Composite Products.
 * Tweak: Minor improvements to settings page.
 * Dev: Add new license system and refactor to use new architecture.
 * Dev: Deprecate Barn2\Plugin\WC_Quick_View_Pro\Quick_View_Plugin class and wc_quick_view_pro() function.
 * Dev: Update jQuery Modal to 0.9.2.

= 1.2.2 =
Release date 12 March 2020

 * Dev: Tested up to WooCommerce 4.0 and WordPress 5.4.

= 1.2.1 =
Release date 21 January 2020

 * Dev: Fully tested with WordPress 5.3.2 and WooCommerce 3.9.

= 1.2 =
Release date 30 October 2019

 * Dev: Tested up to WordPress 5.3 and WooCommerce 3.8.
 * Dev: Added 'wc_quick_view_pro_quick_view_after_product_details' hook.
 * Dev: Refactored code to use updated plugin library.

= 1.1 =
Release date 16 April 2019

 * New: Enfold theme support.
 * New: Support for WooCommerce 3.6.
 * Fix: Bug adding variable products when attribute names contain non-Latin characters.
 * Fix: Set nonce header for add to cart REST request to ensure proper authentication.

= 1.0 =
Release date 27 February 2019

 * Initial release.
