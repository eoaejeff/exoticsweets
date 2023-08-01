<?php
/**
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\WC_Quick_View_Pro\Admin\Wizard\Steps;

use Barn2\Plugin\WC_Quick_View_Pro\Dependencies\Barn2\Setup_Wizard\Steps\Ready;

class Completed extends Ready {

	public function __construct() {
		parent::__construct();
		$this->set_name( esc_html__( 'Ready', 'woocommerce-quick-view-pro' ) );
		$this->set_title( esc_html__( 'Complete Setup', 'woocommerce-quick-view-pro' ) );
		$this->set_description(
			sprintf(
				'%s %s',
				__( 'Congratulations, you have finished setting up the plugin!', 'woocommerce-quick-view-pro' ),
				__( 'If you have enabled quick view globally then the buttons will start appearing in your store straight away, or you can enable/disable them for individual categories as needed.', 'woocommerce-quick-view-pro' )
			)
		);
	}
}
