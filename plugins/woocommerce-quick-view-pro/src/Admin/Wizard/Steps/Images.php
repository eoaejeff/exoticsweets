<?php
/**
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\WC_Quick_View_Pro\Admin\Wizard\Steps;

use Barn2\Plugin\WC_Quick_View_Pro\Dependencies\Barn2\Setup_Wizard\Api;
use Barn2\Plugin\WC_Quick_View_Pro\Util\Settings,
	Barn2\Plugin\WC_Quick_View_Pro\Dependencies\Barn2\Setup_Wizard\Step,
	Barn2\Plugin\WC_Quick_View_Pro\Dependencies\Barn2\Setup_Wizard\Util as SW_Util;

use function Barn2\Plugin\WC_Quick_View_Pro\wqv;

class Images extends Step {

	/**
	 * Configure the step.
	 */
	public function __construct() {
		$this->set_id( 'images' );
		$this->set_name( __( 'Images', 'woocommerce-quick-view-pro' ) );
		$this->set_description( __( 'Customize the product images in the quick view lightbox', 'woocommerce-quick-view-pro' ) );
		$this->set_title( __( 'Product images', 'woocommerce-quick-view-pro' ) );
		$this->set_hidden( true );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {
		$settings = Settings::get_plugin_settings( wqv() );

		$pluck_keys = [
			'enable_zoom',
			'enable_gallery',
			'gallery_style',
		];

		foreach ( $pluck_keys as $key ) {
			$field = SW_Util::pluck_wc_settings(
				Settings::get_settings( wqv() ),
				[
					Settings::OPTION_NAME . "[$key]",
				]
			);

			$fields[ $key ] = reset( $field );
		}

		$fields['gallery_style']['conditions'] = [
			'enable_gallery' => [
				'op'    => 'eq',
				'value' => true,
			],
		];

		array_walk(
			$fields,
			function( &$f, $k ) use ( $settings ) {
				$f['description'] = '';
				$f['value']       = $settings[ $k ];
			}
		);

		// $fields = [
		// 	'shop_display'        => [
		// 		'type'  => 'heading',
		// 		'size'  => 'h3',
		// 		'label' => __( 'Shop display', 'woocommerce-quick-view-pro' ),
		// 	],
		// 	'enable_button'       => $fields['enable_button'],
		// 	'enable_hover_button' => $fields['enable_hover_button'],
		// 	'enable_product_link' => $fields['enable_product_link'],
		// 	'button_title'        => [
		// 		'type'  => 'heading',
		// 		'size'  => 'h3',
		// 		'label' => __( 'Quick View button', 'woocommerce-quick-view-pro' ),
		// 	],
		// 	'button_text'         => $fields['button_text'],
		// 	'show_button_icon'    => $fields['show_button_icon'],
		// ];

		return $fields;
	}

	/**
	 * {@inheritdoc}
	 */
	public function submit( array $values ) {
		$settings = Settings::get_plugin_settings( wqv() );

		foreach ( $this->get_fields() as $key => $field ) {
			if ( 'checkbox' === $field['type'] ) {
				$settings[ $key ] = filter_var( $values[ $key ], FILTER_VALIDATE_BOOLEAN ) ? 'yes' : 'no';
			} elseif ( 'heading' === $field['type'] ) {
				continue;
			} else {
				$settings[ $key ] = $values[ $key ];
			}
		}

		Settings::update_plugin_settings( $settings );

		return Api::send_success_response();
	}

}
