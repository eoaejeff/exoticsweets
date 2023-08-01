**v3.6.2** (24 Jul 2023)  
[fix] Fatal error caused by change in the `woocommerce_add_to_cart_redirect` filter in WooCommerce 7.9  

**v3.6.1** (21 Jul 2023)  
[update] Updated Iconic dependencies.  
[fix] Bug where product description wont display on the popup  

**v3.6** (27 June 2023)  
[update] PHPS Compliant  
[update] Compatibility with HPOS  
[fix] Show reset button on pupup  

**v3.5.1** (1 March 2022)  
[update] Update dependencies  

**v3.5.0** (21 Dec 2021)  
[update] Compatibility with Divi BodyCommerce  
[fix] Firefox issue where 2 spinners(+/- arrow) would appear on quantify field  
[fix] Use the site's default language when loading the Quickview modal  
[fix] Do not autohide the Quickview button when output by shortcode  

**v3.4.16** (16 Mar 2021)  
[update] Update dependencies  
[fix] Fix Null settings warning in WP CLI  

**v3.4.15** (12 Aug 2020)  
[update] Compatibility with WordPress 5.5  
[update] Update dependencies  
[fix] JS console error  

**v3.4.14** (24 Apr 2020)  
[update] Update dependencies  
[update] Version compatibility  
[update] Update POT file  

**v3.4.13** (20 Mar 2020)  
[update] Version compatibility  

**3.4.12** (2 Dec 2019)  
[new] Add setting to choose QuickView button HTML tag  
[update] Enhancements to the [quickview shortcode](https://docs.iconicwp.com/article/248-shortcodes)  
[fix] JS error when opening variable products with many vairations  
[fix] Error when using shortcode  

**3.4.11** (1 July 2019)  
[fix] Freemius Fix  

**3.4.10** (2 Mar 2019)  
[fix] Security Fix  

**3.4.9** (10 Jan 2019)  
[fix] Issue with template loader  

**3.4.8** (10 Jan 2019)  
[update] Update dependencies  

**3.4.7** (12 Sep 2018)  
[update] Implement Iconic core classes  
[update] Update dependencies  
[fix] Fix swatch group styling in quickview  

**3.4.6** (29 Aug 2018)  
[update] Update Freemius  
[update] Update settings framework  
[update] Separate out imagesloaded and slick dependancies  
[update] Add WC version parameters  
[fix] Grouped product button styling  
[fix] Product price when using WPML currency  
[fix] Ensure AJAX add to cart works  
[fix] sizeof error when displaying tags in quickview  

**3.4.5** (14/08/2017)  
[update] Add new licence system  
[fix] Compatibility with Show Single Variations

**3.4.4** (02/04/2017)  
[update] WooCommerce 3.0.0 compatibility

**3.4.3** (22/12/16)  
[update] Update settings framework  
[update] Add quickview_button shortcode  
[fix] Compatibility with latest WooThumbs

**3.4.2** (19/06/16)  
[fix] Fix Ajax add to cart issue when attribute name/value contains special chars  
[update] Compatibility with WooThumbs  
[update] Update dependancies  
[update] Relative admin_url

**3.4.1** (19/06/16)  
[update] Author tags  
[fix] Issue where add to cart buttons stopped working after opening quickview  
[update] Add compatibility for WooCommerce Attribute Swatches by Iconic

**3.4.0** (25/04/16)  
[fix] Ajax add to cart issues  
[update] Updated and refined all javascript  
[update] New settings framework refinements  
[update] Change SKU on variation selection

**3.3.2** (11/03/16)  
[fix] Require wp util and jquery for script  
[update] Add RTL support

**3.3.1** (10/02/16)  
[fix] Gallery next/prev not working because of WPML 3.2.6 pid fix  
[fix] Button styles

**3.3.0** (09/02/16)  
[fix] Issue where placeholder image was showing  
[update] Settings framework updated - backup before update, and re-save settings if required after update.

**3.2.6** (01/02/16)  
[fix] WPML issue - pid moved in ajax call  
[fix] Updated scripts

**3.2.5** (20/01/16)  
[update] Compatibility with Shop the Look plugin  
[fix] Updated for Woo 2.5.0

**3.2.4** (14/01/16)  
[update] Compatibility with "Show Single Variations" plugin!  
[fix] Add to cart bugs with new WooCommerce versions and variable products  
[update] Tidy and refine code

**3.2.3** (02/12/15)  
[update] New gulpfile and SCSS methods [admin]  
[fix] Remove nonce check on add to cart to prevent cache issues

**3.2.0** (12/08/15)  
[update] Add templating system to allow template overrides]  
[update] Filename change to stay in line with WP coding standards *Important*  
[update] Translations

**3.1.1** (11/08/15)  
[fix] Set spinner width explicitly  
[update] Change &$this to $this

**3.1.0** (19/05/15)  
[Fix] YITH Wishlist compatibility

**3.0.9** (01/04/15)  
[Fix] Ajax add to cart in some instances

**3.0.8** (01/04/15)  
[Fix] Set cart cookies when adding to cart, if they're not already set  
[Update] New image slider to allow for auto height and offer a better experience  
[Update] Add Plus/Minus buttons  
[Update] Refactoring classnames and CSS  
[Update] Move dynamic CSS to head  
[Fix] Fix variable add to cart when using gallery mode  
[Fix] Temporary fix for grouped products

**3.0.7** (23/07/14)  
[Fix] Removed duplicate jcksf messages  
[Update] Added error notification for "Sold Individually" products, and other errors.  
[Fix] Fixed group products when adding to cart via AJAX.  
[Updated] Tested with WooTheme swatches and Variations plugin.

**3.0.6**  
[Update] Added translation files  
[Update] Added option to close quickview after adding to cart  
[Update] Added hooks to popup for customisation

**3.0.5**  
[Fix] Changed admin scripts to only load on options page  
[Fix] Fixed ajax add to cart on some themes

**3.0.4**  
[Update] Updated the AJAX add to cart functionality to update WooCommerce Cart Widget  
[Fix] Allow for nested hover

**3.0.3**  
[Fix] Updated for Old WooCommerce  
[Fix] Fixed AJAX add to cart URL

**3.0.2**  
[Update] Added setup_postdata, and globals into includes - just in case

**3.0.0**  
[Update] Rewritten for version 2.1.2 of WooCommerce  
[Update] Overhaul of options page  
[Update] Ajax add to cart  
[Update] Default styles  
[Update] Option to manually insert button

**2.0.2**  
[Update] Minor translation update  
[Fix] Prevent QV from loading on all products in "Related"

**2.0.1**  
[Update] Changed to official AJAX method  
[Update] Added option to show full desc or excerpt  
[Fix] Moved constructor to remove error

**2.0.0**  
[Update] Responsive functionality  
[Update] Gallery functionality  
[Update] Options Page to enable/disable product data  
[Update] Now utilises ajax

**1.0.0**  
Initial Release