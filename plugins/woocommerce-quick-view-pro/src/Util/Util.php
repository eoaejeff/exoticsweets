<?php

namespace Barn2\Plugin\WC_Quick_View_Pro\Util;

use function Barn2\Plugin\WC_Quick_View_Pro\wqv;

/**
 * Utility functions for WooCommerce Quick View Pro.
 *
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
final class Util {

	// FORMATTING

	public static function absfloat( $val ) {
		return abs( floatval( $val ) );
	}

	public static function convert_settings_from_wc_format( $settings ) {
		if ( empty( $settings ) ) {
			return $settings;
		}

		foreach ( $settings as $key => $setting ) {
			if ( 'yes' === $setting ) {
				$settings[ $key ] = true;
			} elseif ( 'no' === $setting ) {
				$settings[ $key ] = false;
			}
		}

		return $settings;
	}

	public static function convert_settings_to_wc_format( $settings ) {
		if ( empty( $settings ) ) {
			return $settings;
		}

		foreach ( $settings as $key => $setting ) {
			if ( true === $setting ) {
				$settings[ $key ] = 'yes';
			} elseif ( false === $setting ) {
				$settings[ $key ] = 'no';
			}
		}

		return $settings;
	}

	// SCRIPTS

	public static function get_asset_url( $path = '' ) {
		return plugins_url( 'assets/' . ltrim( $path, '/' ), wqv()->get_file() );
	}

	public static function get_asset_path( $path = '' ) {
		return trailingslashit( dirname( wqv()->get_file() ) ) . 'assets/' . ltrim( $path, '/' );
	}

	// REST

	public static function format_rest_error( $error_message ) {
		return sprintf( '%s: %s', wqv()->get_name(), $error_message );
	}

	// TEMPLATES

	public static function load_template( $template_name, $args ) {
		wc_get_template( $template_name, $args, WC()->template_path() . 'quick-view-pro/', wqv()->get_dir_path() . 'templates/' );
	}

	// QUICK VIEW CONTENT

	public static function get_modal_class( $product, $class = '' ) {
		if ( $class && ! is_array( $class ) ) {
			$class = explode( ' ', $class );
		}

		$modal_class = array_filter( array_merge( [ 'wc-quick-view-modal', 'woocommerce', 'single-product' ], (array) $class ) );
		return implode( ' ', apply_filters( 'wc_quick_view_pro_modal_container_class', $modal_class, $product ) );
	}

	public static function get_modal_data_attributes( $product ) {
		$atts = [];

		$image_width = wc_get_theme_support( 'single_image_width', get_option( 'woocommerce_single_image_width', false ) );

		if ( $image_width ) {
			$atts['data-image-width'] = $image_width;
		}

		return self::build_html_attributes( apply_filters( 'wc_quick_view_pro_modal_data_attributes', $atts, $product ) );
	}

	public static function get_modal_product_class( $product, $class = '' ) {
		$product_class = wc_get_product_class( $class, $product );
		return implode( ' ', apply_filters( 'wc_quick_view_pro_quick_view_product_class', $product_class, $product ) );
	}

	public static function build_html_attributes( $atts = [] ) {
		if ( empty( $atts ) || ! is_array( $atts ) ) {
			return '';
		}

		$result = '';

		foreach ( $atts as $prop => $value ) {
			$result .= sprintf( '%s="%s" ', $prop, esc_attr( $value ) );
		}

		return trim( $result );
	}

	public static function is_product_image_displayed() {
		$settings = Settings::get_plugin_settings();
		return ( 'details_only' !== $settings['display_type'] );
	}

	/**
	 * @deprecated 1.5 Use are_product_details_displayed instead.
	 */
	public static function is_product_details_displayed() {
		_deprecated_function( __METHOD__, 1.5, self::class . '::are_product_details_displayed' );
		return self::are_product_details_displayed();
	}

	public static function are_product_details_displayed() {
		$settings = Settings::get_plugin_settings();
		return ( 'image_only' !== $settings['display_type'] );
	}

	public static function are_product_tabs_displayed() {
		$settings = Settings::get_plugin_settings();

		if ( 'image_only' === $settings['display_type'] ) {
			return false;
		}

		foreach ( $settings as $key => $value ) {
			if ( strpos( $key, 'tab_' ) === 0 && $value ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get HTML for a gallery image.
	 *
	 * @param int  $attachment_id Attachment ID.
	 * @return string
	 */
	public static function get_gallery_image_html( $attachment_id ) {
		$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
		$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', [ $gallery_thumbnail['width'], $gallery_thumbnail['height'] ] );
		$image_size        = apply_filters( 'woocommerce_gallery_image_size', 'woocommerce_single' );
		$full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
		$thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
		$full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
		$image             = wp_get_attachment_image(
			$attachment_id,
			$image_size,
			false,
			[
				'title'                   => get_post_field( 'post_title', $attachment_id ),
				'data-caption'            => get_post_field( 'post_excerpt', $attachment_id ),
				'data-src'                => $full_src[0],
				'data-large_image'        => $full_src[0],
				'data-large_image_width'  => $full_src[1],
				'data-large_image_height' => $full_src[2],
				'class'                   => 'wp-post-image',
			]
		);

		return '<div data-thumb="' . esc_url( $thumbnail_src[0] ) . '" class="woocommerce-product-gallery__image">' . $image . '</div>';
	}

	// NOTICES

	public static function get_wc_notices( $notice_type ) {
		$notices = wc_get_notices( $notice_type );
		wc_clear_notices();

		// WC > 3.8 uses nested arrays for each notice.
		if ( ! empty( $notices ) && isset( $notices[0]['notice'] ) ) {
			$notices = wp_list_pluck( $notices, 'notice' );
		}

		return array_filter( $notices );
	}

}
