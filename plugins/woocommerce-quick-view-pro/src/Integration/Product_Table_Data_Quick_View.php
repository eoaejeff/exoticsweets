<?php
namespace Barn2\Plugin\WC_Quick_View_Pro\Integration;

use Barn2\Plugin\WC_Quick_View_Pro\Button_Display,
	WC_Product,
	Barn2\Plugin\WC_Product_Table\Data\Abstract_Product_Data;

if ( ! class_exists( 'Barn2\Plugin\WC_Product_Table\Data\Abstract_Product_Data' ) ) {
	return;
}

/**
 * Required for the WooCommerce Product Table integration. Gets data for the 'quick-view' column to display in the product table.
 *
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Table_Data_Quick_View extends Abstract_Product_Data {

	private $button_display;

	public function __construct( WC_Product $product, Button_Display $button_display ) {
		parent::__construct( $product );

		$this->button_display = $button_display;
	}

	public function get_data() {
		$button = $this->button_display->get_button( $this->product );
		return apply_filters( 'wc_product_table_data_quick_view', $button, $this->product );
	}

}
