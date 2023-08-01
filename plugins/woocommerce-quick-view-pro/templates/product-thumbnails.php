<?php

/**
 * The template for displaying the product gallery thumbnails in the quick view.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/quick-view-pro/product-thumbnails.php.
 *
 * @version 1.0.0
 */

namespace Barn2\Plugin\WC_Quick_View_Pro;

use Barn2\Plugin\WC_Quick_View_Pro\Util\Util;

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attachment_ids = $product->get_gallery_image_ids();

if ( $attachment_ids && $product->get_image_id() ) {
	foreach ( $attachment_ids as $attachment_id ) {
		echo apply_filters( 'wc_quick_view_pro_product_image_thumbnail_html', Util::get_gallery_image_html( $attachment_id ), $attachment_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
	}
}
