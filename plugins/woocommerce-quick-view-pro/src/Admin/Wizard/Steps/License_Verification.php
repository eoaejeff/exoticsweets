<?php
/**
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\WC_Quick_View_Pro\Admin\Wizard\Steps;

use Barn2\Plugin\WC_Quick_View_Pro\Dependencies\Barn2\Setup_Wizard\Steps\Welcome;

class License_Verification extends Welcome {

	public function __construct() {
		parent::__construct();
		$this->set_name( esc_html__( 'Welcome', 'woocommerce-quick-view-pro' ) );
		$this->set_title( esc_html__( 'Welcome to WooCommerce Quick View Pro', 'woocommerce-quick-view-pro' ) );
		$this->set_description( esc_html__( 'Add quick view lightboxes in minutes', 'woocommerce-quick-view-pro' ) );
		$this->set_tooltip( esc_html__( 'Use this setup wizard to quickly configure the most popular quick view options. You can easily change these options later on the plugin settings page or by relaunching the setup wizard.', 'woocommerce-quick-view-pro' ) );
	}

}
