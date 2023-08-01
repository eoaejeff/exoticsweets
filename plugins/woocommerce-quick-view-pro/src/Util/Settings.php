<?php

namespace Barn2\Plugin\WC_Quick_View_Pro\Util;

use Barn2\WQV_Lib\Util as Lib_Util;

/**
 * Utility functions for the product table plugin settings.
 *
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
final class Settings {
	/* Option names for our plugin settings (i.e. the option keys used in wp_options) */

	/* The section name within the main WooCommerce Settings */
	const SECTION_SLUG = 'quick-view-pro';
	const OPTION_NAME  = 'wc_quick_view_pro_settings';

	/**
	 * The array with the plugin settings
	 *
	 * @var $settings
	 */
	private static $settings;

	public static function get_default_settings() {
		return [
			'enable_button'          => true,
			'enable_product_link'    => false,
			'enable_hover_button'    => false,
			'show_button_icon'       => true,
			'display_type'           => 'both',
			'enable_gallery'         => true,
			'enable_zoom'            => true,
			'gallery_style'          => 'thumbs',
			'show_reviews'           => true,
			'show_price'             => true,
			'show_description'       => true,
			'show_cart'              => true,
			'show_meta'              => true,
			'show_details_link'      => false,
			'tab_enable_reviews'     => false,
			'tab_enable_description' => false,
			'tab_enable_attrs'       => false,
		];
	}

	public static function get_plugin_settings() {
		if ( is_null( self::$settings ) ) {
			$option         = get_option( self::OPTION_NAME, [] );
			self::$settings = Util::convert_settings_from_wc_format( wp_parse_args( $option, self::get_default_settings() ) );
			self::$settings = apply_filters( 'wc_quick_view_pro_settings', self::$settings, $option );
		}
		return self::$settings;
	}

	public static function update_plugin_settings( $settings ) {
		$settings = array_map(
			function( $s ) {
				if ( is_bool( $s ) ) {
					return $s ? 'yes' : 'no';
				}

				return $s;
			},
			$settings
		);

		update_option( self::OPTION_NAME, $settings );
	}

	public static function get_settings( $plugin ) {
		$shop_page_url = get_permalink( wc_get_page_id( 'shop' ) );

		$settings = [
			[
				'id'    => 'quick_view_settings',
				'type'  => 'settings_start',
				'class' => 'barn2-plugins-settings',
			],
			// License key settings.
			[
				'title' => __( 'Quick View', 'woocommerce-quick-view-pro' ),
				'type'  => 'title',
				'id'    => 'quick_view_license_section',
				'desc'  => '<p>' . __( 'The following options control the WooCommerce Quick View Pro extension.', 'woocommerce-quick-view-pro' ) . '<p>'
				. '<p>'
				. Lib_Util::format_link( $plugin->get_documentation_url(), __( 'Documentation', 'woocommerce-quick-view-pro' ), true ) . ' | '
				. Lib_Util::format_link( $plugin->get_support_url(), __( 'Support', 'woocommerce-quick-view-pro' ), true )
				. '</p>'
			],
			$plugin->get_license_setting()->get_license_key_setting(),
			$plugin->get_license_setting()->get_license_override_setting(),
			[
				'type' => 'sectionend',
				'id'   => 'quick_view_license_section'
			],
			// Main quick view settings.
			[
				'title' => __( 'Options', 'woocommerce-quick-view-pro' ),
				'type'  => 'title',
				'id'    => 'quick_view_display_section',
			],
			[
				'title'         => __( 'Shop display', 'woocommerce-quick-view-pro' ),
				'type'          => 'checkbox',
				'id'            => self::OPTION_NAME . '[enable_button]',
				'desc'          => __( 'Show a Quick View button for each product', 'woocommerce-quick-view-pro' ),
				'default'       => 'yes',
				'checkboxgroup' => 'start'
			],
			[
				'type'          => 'checkbox',
				'id'            => self::OPTION_NAME . '[enable_hover_button]',
				'desc'          => __( 'Display Quick View button on hover', 'woocommerce-quick-view-pro' ),
				'default'       => 'no',
				'checkboxgroup' => '',
				'desc_tip'      => '<em>'
					. __( 'May not work with all themes -', 'woocommerce-quick-view-pro' )
					. sprintf( ' <a target="_blank" href="%s">', add_query_arg( '_b2-preview', 'qvp-hover', $shop_page_url ) )
					. __( 'try it with yours', 'woocommerce-quick-view-pro' )
					. ''
					. '</a><span class="dashicons dashicons-external" style="vertical-align:middle;width:1.2em;height:1.2em;font-size:1.2em"></span></em>',
			],
			[
				'type'          => 'checkbox',
				'id'            => self::OPTION_NAME . '[enable_product_link]',
				'desc'          => __( 'Open the Quick View by clicking the product name or image', 'woocommerce-quick-view-pro' ),
				'desc_tip'      => apply_filters( 'wc_quick_view_pro_settings_enable_product_link_description', '' ),
				'default'       => 'no',
				'checkboxgroup' => 'end',
				'desc_tip'      => '<em>'
					. __( 'May not work with all themes -', 'woocommerce-quick-view-pro' )
					. sprintf( ' <a target="_blank" href="%s">', add_query_arg( '_b2-preview', 'qvp-full-click', $shop_page_url ) )
					. __( 'try it with yours', 'woocommerce-quick-view-pro' )
					. ''
					. '</a><span class="dashicons dashicons-external" style="vertical-align:middle;width:1.2em;height:1.2em;font-size:1.2em"></span></em>',
			],
			[
				'title'   => __( 'Button text', 'woocommerce-quick-view-pro' ),
				'type'    => 'text',
				'id'      => self::OPTION_NAME . '[button_text]',
				'default' => __( 'Quick View', 'woocommerce-quick-view-pro' ),
				'css'     => 'width:160px'
			],
			[
				'title'   => __( 'Button icon', 'woocommerce-quick-view-pro' ),
				'type'    => 'checkbox',
				'id'      => self::OPTION_NAME . '[show_button_icon]',
				'desc'    => __( 'Show the button icon', 'woocommerce-quick-view-pro' ),
				'default' => 'yes'
			],
			[
				'title'   => __( 'Quick View content', 'woocommerce-quick-view-pro' ),
				'type'    => 'select',
				'id'      => self::OPTION_NAME . '[display_type]',
				'options' => [
					'image_only'   => __( 'Image only', 'woocommerce-quick-view-pro' ),
					'details_only' => __( 'Product details only', 'woocommerce-quick-view-pro' ),
					'both'         => __( 'Image and product details', 'woocommerce-quick-view-pro' ),
				],
				'default' => 'both',
				'class'   => 'wc-enhanced-select'
			],
			[
				'title'         => __( 'Product details', 'woocommerce-quick-view-pro' ),
				'type'          => 'checkbox',
				'id'            => self::OPTION_NAME . '[show_reviews]',
				'desc'          => __( 'Show star ratings', 'woocommerce-quick-view-pro' ),
				'default'       => 'yes',
				'class'         => 'product-details',
				'checkboxgroup' => 'start'
			],
			[
				'type'          => 'checkbox',
				'id'            => self::OPTION_NAME . '[tab_enable_reviews]',
				'desc'          => __( 'Show full reviews', 'woocommerce-quick-view-pro' ),
				'default'       => 'no',
				'checkboxgroup' => '',
			],
			[
				'type'          => 'checkbox',
				'id'            => self::OPTION_NAME . '[show_price]',
				'desc'          => __( 'Show price', 'woocommerce-quick-view-pro' ),
				'default'       => 'yes',
				'checkboxgroup' => '',
			],
			[
				'type'          => 'checkbox',
				'id'            => self::OPTION_NAME . '[show_description]',
				'desc'          => __( 'Show short description', 'woocommerce-quick-view-pro' ),
				'default'       => 'yes',
				'checkboxgroup' => ''
			],
			[
				'type'          => 'checkbox',
				'id'            => self::OPTION_NAME . '[tab_enable_description]',
				'desc'          => __( 'Show full description', 'woocommerce-quick-view-pro' ),
				'default'       => 'no',
				'checkboxgroup' => '',
			],
			[
				'type'          => 'checkbox',
				'id'            => self::OPTION_NAME . '[show_cart]',
				'desc'          => __( 'Show add to cart button', 'woocommerce-quick-view-pro' ),
				'default'       => 'yes',
				'checkboxgroup' => ''
			],
			[
				'type'          => 'checkbox',
				'id'            => self::OPTION_NAME . '[show_meta]',
				'desc'          => __( 'Show meta information', 'woocommerce-quick-view-pro' ),
				'default'       => 'yes',
				'checkboxgroup' => ''
			],
			[
				'type'          => 'checkbox',
				'id'            => self::OPTION_NAME . '[tab_enable_attrs]',
				'desc'          => __( 'Show attributes', 'woocommerce-quick-view-pro' ),
				'default'       => 'no',
				'checkboxgroup' => ''
			],
			[
				'type'          => 'checkbox',
				'id'            => self::OPTION_NAME . '[show_details_link]',
				'desc'          => __( 'Show product details link', 'woocommerce-quick-view-pro' ),
				'default'       => 'no',
				'checkboxgroup' => 'end'
			],
			[
				'title'         => __( 'Product image', 'woocommerce-quick-view-pro' ),
				'type'          => 'checkbox',
				'id'            => self::OPTION_NAME . '[enable_zoom]',
				'desc'          => __( 'Enable image zoom', 'woocommerce-quick-view-pro' ),
				'default'       => 'yes',
				'class'         => 'product-image',
				'checkboxgroup' => 'start',
			],
			[
				'type'          => 'checkbox',
				'id'            => self::OPTION_NAME . '[enable_gallery]',
				'desc'          => __( 'Show gallery thumbnails', 'woocommerce-quick-view-pro' ),
				'default'       => 'yes',
				'checkboxgroup' => 'end'
			],
			[
				'title'   => __( 'Gallery thumbnail style', 'woocommerce-quick-view-pro' ),
				'type'    => 'select',
				'id'      => self::OPTION_NAME . '[gallery_style]',
				'options' => [
					'thumbs'  => __( 'Thumbnails', 'woocommerce-quick-view-pro' ),
					'bullets' => __( 'Bullets', 'woocommerce-quick-view-pro' ),
				],
				'default' => 'thumbs',
				'css'     => 'width:150px;',
				'class'   => 'wc-enhanced-select'
			],
			[
				'type' => 'sectionend',
				'id'   => 'quick_view_display_section'
			],
			[
				'id'   => 'quick_view_settings_end',
				'type' => 'settings_end'
			]
		];

		$section_id = self::SECTION_SLUG;

		return apply_filters( "woocommerce_get_settings_{$section_id}", $settings );
	}
}
