<?php
namespace Barn2\Plugin\WC_Quick_View_Pro\Integration;

use Barn2\WQV_Lib\Registerable,
	Barn2\Plugin\WC_Quick_View_Pro\Util\Util;

/**
 * Handles integration with WooCommerce Composite Products.
 *
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Composite_Products_Integration implements Registerable {

	public function register() {
		if ( ! class_exists( '\WC_Composite_Products' ) || ! Util::are_product_details_displayed() ) {
			return;
		}

		add_action( 'wc_quick_view_pro_load_scripts', [ $this, 'load_scripts' ] );
	}

	public function load_scripts() {
		if ( ! function_exists( 'WC_CP' ) ) {
			return;
		}

		if ( ! is_product() ) {
			WC_CP()->display->frontend_scripts();

			// Enqueue script.
			wp_enqueue_script( 'wc-add-to-cart-composite' );

			// Enqueue styles.
			wp_enqueue_style( 'wc-composite-single-css' );
		}
	}

}
