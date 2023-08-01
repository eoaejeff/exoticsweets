<?php
namespace Barn2\Plugin\WC_Quick_View_Pro;

use Barn2\WQV_Lib\Service,
	Barn2\WQV_Lib\Registerable,
	Barn2\WQV_Lib\Conditional,
	Barn2\WQV_Lib\Util as Lib_Util;

/**
 * Handles the registration and output for the [quick_view] shortcode.
 *
 * @package   Barn2\
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Shortcode implements Service, Registerable, Conditional {

	private $button_display;

	public function __construct( Button_Display $button_display ) {
		$this->button_display = $button_display;
	}

	public function is_required() {
		return Lib_Util::is_front_end();
	}

	public function register() {
		add_shortcode( 'quick_view', [ $this, 'do_shortcode' ] );
	}

	public function do_shortcode( $atts ) {
		$atts = shortcode_atts(
			[
				'id'   => 0,
				'sku'  => '',
				'text' => ''
			],
			$atts,
			'quick_view'
		);

		$product = wc_get_product( $atts['id'] );

		if ( ! $product ) {
			if ( '' === $atts['sku'] ) {
				return __( 'Product ID not specified.', 'woocommerce-quick-view-pro' );
			} else {
				$product_id = wc_get_product_id_by_sku( trim( $atts['sku'] ) );
				$product    = wc_get_product( $product_id );
			}
		}

		if ( ! $product ) {
			return __( 'Product not found.', 'woocommerce-quick-view-pro' );
		}

		if ( $atts['text'] ) {
			$text = $atts['text'];

			// Filter button text using a closure bound to the current text and product.
			add_filter(
				'wc_quick_view_pro_button_text',
				function( $orig_text, $orig_product ) use ( $text, $product ) {
					if ( $orig_product->get_id() === $product->get_id() ) {
						return $text;
					}
					return $orig_text;
				},
				10,
				2
			);
		}

		// Ensure scripts load for the shortcode button.
		add_filter( 'wc_quick_view_pro_scripts_enabled_on_page', '__return_true', 5 );

		// Add an extra class for shortcode buttons.
		add_filter( 'wc_quick_view_pro_button_class', [ $this, 'add_button_class' ] );

		$button_html = $this->button_display->get_button( $product );

		return apply_filters( 'wc_quick_view_pro_quick_view_shortcode_html', $button_html, $product );
	}

	public function add_button_class( $class ) {
		$class .= ' shortcode';
		return $class;
	}

}
