<?php
/**
 * The template for the contents of the quick view when the product is hidden.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/quick-view-pro/product-hidden-content.php.
 *
 * @version 1.1
 */
namespace Barn2\Plugin\WC_Quick_View_Pro;

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<p><?php _e( 'Sorry, this product is not available for purchase.', 'woocommerce-quick-view-pro' ); ?></p>
