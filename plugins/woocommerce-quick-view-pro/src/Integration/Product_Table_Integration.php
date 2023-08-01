<?php
namespace Barn2\Plugin\WC_Quick_View_Pro\Integration;

use Barn2\WQV_Lib\Registerable,
	Barn2\WQV_Lib\Util as Lib_Util,
	Barn2\Plugin\WC_Quick_View_Pro\Frontend_Scripts,
	Barn2\Plugin\WC_Quick_View_Pro\Button_Display,
	Barn2\Plugin\WC_Product_Table\Frontend_Scripts as WPT_Scripts;
use function Barn2\Plugin\WC_Product_Table\wpt;

/**
 * Handles the integration with WooCommerce Product Table.
 *
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Table_Integration implements Registerable {

	private $button_display;
	private $scripts;

	public function __construct( Button_Display $button_display, Frontend_Scripts $scripts ) {
		$this->button_display = $button_display;
		$this->scripts        = $scripts;
	}

	public function register() {
		if ( ! Lib_Util::is_product_table_active() ) {
			return;
		}

		add_filter( 'wc_product_table_column_defaults', [ $this, 'column_defaults' ], 10, 3 );
		// Register product table data for 'quick-view' column.
		add_filter( 'wc_product_table_custom_table_data_quick-view', [ $this, 'get_quick_view_data' ], 10, 3 );

		// Quick view column is not sortable.
		add_filter( 'wc_product_table_column_sortable_quick-view', '__return_false' );

		// Load the Quick View scripts when a product table is displayed, so Quick Views can be opened from the table.
		add_action( 'wc_product_table_before_get_table', [ $this, 'load_quick_view_scripts' ] );

		// Load the product table scripts so tables inside a Quick View work correctly.
		add_action( 'wp', [ $this, 'maybe_load_table_scripts' ] );

		// Add note to product link setting on settings page.
		add_filter(
			'wc_quick_view_pro_settings_enable_product_link_description',
			function( $desc ) {
				return sprintf(
				/* translators: %s: The settings page URL for WooCommerce Product Table. */
					__( 'Excludes links inside product tables, which must be configured separately in your <a href="%s">table settings</a>.', 'woocommerce-quick-view-pro' ),
					wpt()->get_settings_page_url()
				);
			}
		);
	}

	public function load_quick_view_scripts() {
		add_filter( 'wc_quick_view_pro_scripts_enabled_on_page', '__return_true', 5 );
		$this->scripts->load_scripts();
	}

	public function maybe_load_table_scripts() {
		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'load_table_scripts' ], 30 );
		}
	}

	public function load_table_scripts() {
		wp_enqueue_style( WPT_Scripts::SCRIPT_HANDLE );
		wp_enqueue_script( WPT_Scripts::SCRIPT_HANDLE );
	}

	public function column_defaults( $column_defaults ) {
		$column_defaults['quick-view'] = [ 'heading' => __( 'Quick View', 'woocommerce-quick-view-pro' ), 'priority' => 17 ];

		return $column_defaults;
	}

	public function get_quick_view_data( $data, $product, $args ) {
		return new Product_Table_Data_Quick_View( $product, $this->button_display );
	}

}
