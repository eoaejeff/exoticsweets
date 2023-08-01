<?php

namespace Barn2\Plugin\WC_Quick_View_Pro\Admin;

use Barn2\WQV_Lib\Registerable,
	Barn2\Plugin\WC_Quick_View_Pro\Util\Util,
	WP_Term;

/**
 * Responsible for the default quantity settings for product categories in the admin.
 *
 * @package   Barn2/woocommerce-default-quantity
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Category_Manager implements Registerable {

	private $plugin;

	public function __construct() {}

	public function register() {
		\add_action( 'product_cat_add_form_fields', [ $this, 'render_add_qvp_disabled_field' ], 99, 1 );
		\add_action( 'product_cat_edit_form_fields', [ $this, 'render_edit_qvp_disabled_field' ], 99, 1 );
		\add_action( 'created_product_cat', [ $this, 'save_qvp_disabled_field' ], 10, 2 );
		\add_action( 'edit_product_cat', [ $this, 'save_qvp_disabled_field' ], 10, 2 );
	}

	/**
	 * Save default option for category
	 *
	 * @param int $category_id
	 */
	public function save_qvp_disabled_field( $category_id ) {
		if ( isset( $_POST['qvp-enabled'] ) ) {
			$category_qvp_setting = sanitize_title( $_POST['qvp-enabled'] );
			\update_term_meta( $category_id, '_qvp_enabled', $category_qvp_setting );
		}
	}

	/**
	 * Get default option for category
	 *
	 * @param int $category_id
	 *
	 * @return int|false
	 */
	public static function get_for_category( $category_id ) {
		return \get_term_meta( $category_id, '_qvp_enabled', true );
	}

	/**
	 * Render default option field for add category page
	 */
	public function render_add_qvp_disabled_field() {
		Util::load_template( 'admin/disable-qvp-field-add.php', [] );
	}

	/**
	 * Render default option field for edit category page
	 *
	 * @param WP_Term $category
	 */
	public function render_edit_qvp_disabled_field( WP_Term $category ) {
		Util::load_template( 'admin/disable-qvp-field-edit.php', [ 'category' => $category ] );
	}

}
