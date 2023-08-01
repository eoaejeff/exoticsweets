<?php
namespace Barn2\Plugin\WC_Quick_View_Pro;

use Barn2\Plugin\WC_Quick_View_Pro\Util\Util,
	Barn2\Plugin\WC_Quick_View_Pro\Util\Settings,
	Barn2\WQV_Lib\Service,
	Barn2\WQV_Lib\Registerable,
	Barn2\WQV_Lib\Conditional,
	Barn2\WQV_Lib\Util as Lib_Util;

/**
 * Handles the display of the Quick View button in the shop.
 *
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Tabs_Display implements Service, Registerable, Conditional {

	private $scripts;
	private $settings;
	private $tabs = [];

	public function __construct( Frontend_Scripts $scripts ) {
		$this->scripts  = $scripts;
		$this->settings = Settings::get_plugin_settings();
	}

	public function is_required() {
		return Lib_Util::is_front_end();
	}

	public function register() {
		add_action( 'wc_quick_view_pro_before_quick_view', [ $this, 'generate_qv_tabs' ] );
		add_action( 'wc_quick_view_pro_quick_view_product_tabs', [ $this, 'show_qv_tabs' ] );
	}

	public function generate_qv_tabs( $product ) {

		if ( 'image_only' === $this->settings['display_type'] ) {
			return;
		}

		$default_tabs = [];

		if ( ! empty( trim( $product->get_description() ) ) && ! is_a( $product, 'WC_Product_Variation' ) ) {
			$default_tabs['description'] = [
				'title'    => __( 'Description', 'woocommerce-quick-view-pro' ),
				'callback' => 'woocommerce_product_description_tab',
			];
		}

		$attributes = array_filter(
			$product->get_attributes(),
			function( $a ) {
				return is_a( $a, 'WC_Product_Attribute' ) && $a->get_visible();
			}
		);

		if ( ! empty( $attributes ) ) {
			$default_tabs['attrs'] = [
				'title'    => __( 'Additional Information', 'woocommerce-quick-view-pro' ),
				'callback' => 'wc_display_product_attributes',
			];
		}

		if ( $product->get_review_count() > 0 ) {
			$default_tabs['reviews'] = [
				'title'    => __( 'Reviews', 'woocommerce-quick-view-pro' ),
				'callback' => 'wc_qvp_display_product_reviews',
			];
		}

		$tabs_enabled = [];
		foreach ( $default_tabs as $key => $tab ) {
			if ( ! empty( $this->settings[ 'tab_enable_' . $key ] ) ) {
				$tabs_enabled[ $key ] = $tab;
			}
		}

		$tabs_enabled = apply_filters( 'wc_quick_view_pro_quick_view_tabs_enabled', $tabs_enabled );

		$this->tabs = $tabs_enabled;

		if ( ! empty( $tabs_enabled ) ) {
			add_filter( 'wc_quick_view_pro_show_tabs', '__return_true' );
		}

	}

	public function show_qv_tabs( $product ) {

		Util::load_template(
			'quick-view-tabs.php',
			[
				'product_tabs' => $this->tabs,
				'product'      => $product
			]
		);

	}

}
