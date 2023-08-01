<?php
namespace Barn2\Plugin\WC_Quick_View_Pro\Integration;

use Barn2\Plugin\WC_Quick_View_Pro\Button_Display,
	Barn2\Plugin\WC_Quick_View_Pro\Util\Settings,
	Barn2\WQV_Lib\Registerable,
	WC_Product;

/**
 * Integration with the WooCommerce Blocks plugin.
 *
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WooCommerce_Blocks implements Registerable {

	private $button_renderer;
	private $settings;
	private $button_filters_added = false;

	public function __construct( Button_Display $button_renderer ) {
		$this->button_renderer = $button_renderer;
		$this->settings        = Settings::get_plugin_settings();
	}

	public function register() {
		if ( ! class_exists( 'Automattic\WooCommerce\Blocks\Package' ) ) {
			return;
		}

		if ( $this->settings['enable_button'] ) {
			add_filter( 'woocommerce_blocks_product_grid_item_html', [ $this, 'product_grid_item_html' ], 10, 3 );
		}

		if ( $this->settings['enable_product_link'] ) {
			add_filter( 'wc_quick_view_pro_product_link_selector', [ $this, 'product_link_selector' ], 50 );
		}
	}

	public function product_grid_item_html( $html, $data, $product ) {
		$this->register_button_filters();
		return $this->append_quick_view_button( $html, $data, $product );
	}

	public function product_link_selector( $selector ) {
		return $selector . ', .wc-block-grid__product-link';
	}

	private function register_button_filters() {
		if ( $this->button_filters_added ) {
			return;
		}

		// Enable QVP scripts for this page which contains the Woo Products Block.
		add_filter( 'wc_quick_view_pro_scripts_enabled_on_page', '__return_true', 5 );

		// Buttons in the Woo Products Block (e.g. Add To Cart) have the 'wp-block-button__link' class, so we add this to the Quick View
		// button too so that any specific button styles are picked up.
		add_filter(
			'wc_quick_view_pro_button_class_array',
			function( $classes ) {
				$classes[] = 'wp-block-button__link';
				return $classes;
			},
			5
		);

		add_filter( 'wc_quick_view_pro_use_default_button_classes', '__return_false' );

		$this->button_filters_added = true;
	}

	private function append_quick_view_button( $html, $data, WC_Product $product ) {
		// Don't add quick view button if product block button is disabled/empty.
		if ( empty( $data->button ) ) {
			return $html;
		}

		$block_button     = '<div class="wp-block-button wc-block-grid__product-add-to-cart">';
		$block_button_pos = strpos( $html, $block_button );

		// Insert Quick View button in product block HTML output.
		if ( false !== $block_button_pos ) {
			$block_button_pos += strlen( $block_button );
			$html              = substr_replace( $html, $this->get_quick_view_button( $product ), $block_button_pos, 0 );
		}

		// Add product ID data attribute to product link, to enable QV when clicking on product name or image.
		if ( $this->settings['enable_product_link'] ) {
			$product_link_class_pos = strpos( $html, 'class="wc-block-grid__product-link"' );

			if ( false !== $product_link_class_pos ) {
				$html = substr_replace( $html, $this->get_data_product_id( $product ), $product_link_class_pos, 0 );
			}
		}

		return $html;
	}

	private function get_quick_view_button( WC_Product $product ) {
		return apply_filters( 'wc_quick_view_pro_quick_view_button_products_block', $this->button_renderer->get_button( $product ), $product );
	}

	private function get_data_product_id( WC_Product $product ) {
		return sprintf( 'data-product_id="%u" ', $product->get_id() );
	}

}
