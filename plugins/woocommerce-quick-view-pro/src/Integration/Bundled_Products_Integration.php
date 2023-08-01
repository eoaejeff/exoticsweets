<?php
namespace Barn2\Plugin\WC_Quick_View_Pro\Integration;

use Barn2\WQV_Lib\Registerable,
	Barn2\Plugin\WC_Quick_View_Pro\Util\Util;

/**
 * Handles integration with WooCommerce Bundled Products.
 *
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Bundled_Products_Integration implements Registerable {

	public function register() {
		if ( ! class_exists( '\WC_Bundles' ) || ! Util::are_product_details_displayed() ) {
			return;
		}

		add_action( 'wc_quick_view_pro_load_scripts', [ $this, 'load_scripts' ] );
	}

	public function load_scripts() {
		if ( ! wp_script_is( 'wc-add-to-cart-bundle' ) ) {
			wp_enqueue_script( 'wc-add-to-cart-bundle' );
		}

		if ( ! wp_script_is( 'wc-bundle-css' ) ) {
			wp_enqueue_style( 'wc-bundle-css' );
		}
	}

}
