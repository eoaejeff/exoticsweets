<?php
namespace Barn2\Plugin\WC_Quick_View_Pro\Integration;

use Barn2\WQV_Lib\Registerable,
	Barn2\Plugin\WC_Quick_View_Pro\Util\Util;

/**
 * Handles integration with WooCommerce Product Addons.
 *
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Addons_Integration implements Registerable {

	public function register() {
		if ( ! class_exists( '\WC_Product_Addons' ) || ! Util::are_product_details_displayed() ) {
			return;
		}

		add_action( 'wc_quick_view_pro_load_scripts', [ $this, 'scripts' ] );
	}

	public function scripts() {
		// Addons styles - we can't call WC_Product_Addons_Display->styles() as this runs on certain pages only, so we enqueue these ourselves.
		if ( ! wp_script_is( 'woocommerce-addons-css' ) ) {
			$addons_version = defined( 'WC_PRODUCT_ADDONS_VERSION' ) ? \WC_PRODUCT_ADDONS_VERSION : '1.0';
			$addons_url     = defined( 'WC_PRODUCT_ADDONS_PLUGIN_URL' ) ? \WC_PRODUCT_ADDONS_PLUGIN_URL : plugins_url( 'woocommerce-product-addons' );

			wp_enqueue_style( 'woocommerce-addons-css', $addons_url . '/assets/css/frontend.css', [ 'dashicons' ], $addons_version );
			wp_enqueue_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', [ 'jquery' ], \WC_VERSION, true );
		}

		// Addons scripts - triggering this action will queue the scripts.
		do_action( 'wc_quick_view_enqueue_scripts' );
	}

}
