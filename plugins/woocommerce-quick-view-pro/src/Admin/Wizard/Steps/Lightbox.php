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

class Lightbox extends Step {

	/**
	 * Configure the step.
	 */
	public function __construct() {
		$this->set_id( 'lightbox' );
		$this->set_name( __( 'Lightbox', 'woocommerce-quick-view-pro' ) );
		$this->set_description( __( 'Choose which information to display in the quick view lightbox', 'woocommerce-quick-view-pro' ) );
		$this->set_title( __( 'Quick view lightbox', 'woocommerce-quick-view-pro' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {
		$fields   = [];
		$settings = Settings::get_plugin_settings( wqv() );

		$pluck_keys = [
			'display_type',
			'show_reviews',
			'tab_enable_reviews',
			'show_price',
			'show_description',
			'tab_enable_description',
			'show_cart',
			'show_meta',
			'tab_enable_attrs',
			'show_details_link',
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

		$product_details_condition = [
			'display_type' => [
				'op'    => 'neq',
				'value' => 'image_only',
			],
		];

		array_walk(
			$fields,
			function( &$f, $k ) use ( $settings, $product_details_condition ) {
				$f['description'] = '';
				$f['value']       = $settings[ $k ];
				$f['conditions']  = $product_details_condition;
			}
		);

		unset( $fields['display_type']['conditions'] );

		$fields = array_merge(
			array_slice( $fields, 0, 1 ),
			[
				'details_title' => [
					'type'       => 'heading',
					'size'       => 'h3',
					'label'      => __( 'Product details', 'woocommerce-quick-view-pro' ),
					'conditions' => $product_details_condition,
				],
			],
			array_slice( $fields, 1 )
		);

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
