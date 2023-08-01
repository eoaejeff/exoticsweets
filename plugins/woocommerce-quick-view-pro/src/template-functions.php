<?php
/**
 * Un-namespaced functions that are used in template action and filter hooks
 *
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

use Barn2\Plugin\WC_Quick_View_Pro\Util\Util;

function wc_qvp_show_tabs( $product = null ) {

	return apply_filters( 'wc_quick_view_pro_show_tabs', false, $product );

}

function wc_qvp_display_product_reviews( $product ) {

	Util::load_template( 'tabs/reviews.php', [ 'product' => $product ] );

}
