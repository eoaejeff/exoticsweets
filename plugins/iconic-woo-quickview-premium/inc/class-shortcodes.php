<?php
/**
 * Shortcodes.
 *
 * @package iconic-quickview
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Iconic_WQV_Shortcodes class
 *
 * @class Iconic_WQV_Shortcodes
 */
class Iconic_WQV_Shortcodes {
	/**
	 * Init shortcodes.
	 */
	public static function init() {
		$shortcodes = array(
			'quickview_button' => array( __CLASS__, 'quickview_button' ),
			'quickview-button' => array( __CLASS__, 'quickview_button' ),
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function, 10, 2 );
		}
	}

	/**
	 * Quickview button shortcode.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param null|string $content Shortcode content.
	 *
	 * @return string
	 */
	public static function quickview_button( $atts = array(), $content = '' ) {
		global $jckqv;

		$atts = shortcode_atts(
			array(
				'product_id' => false,
				'product-id' => false,
				'icon'       => null,
				'style'      => true,
				'align'      => null,
			),
			$atts,
			'iconic-wqv-button'
		);

		$product_id = ! empty( $atts['product_id'] ) ? $atts['product_id'] : $atts['product-id'];

		if ( empty( $product_id ) ) {
			return;
		}

		ob_start();

		$args = array(
			'style' => 'false' !== $atts['style'],
		);

		if ( ! empty( $content ) ) {
			$args['content'] = $content;
		}

		if ( $atts['icon'] ) {
			$args['icon'] = $atts['icon'];
		}

		if ( $atts['align'] ) {
			$args['align'] = $atts['align'];
		}

		$jckqv->display_button( $product_id, $args );

		return ob_get_clean();
	}
}
