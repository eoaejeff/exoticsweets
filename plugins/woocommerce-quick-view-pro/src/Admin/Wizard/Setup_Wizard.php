<?php
/**
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\WC_Quick_View_Pro\Admin\Wizard;

use Barn2\Plugin\WC_Quick_View_Pro\Admin\Wizard\Steps\License_Verification,
	Barn2\Plugin\WC_Quick_View_Pro\Admin\Wizard\Steps\Buttons,
	Barn2\Plugin\WC_Quick_View_Pro\Admin\Wizard\Steps\Lightbox,
	Barn2\Plugin\WC_Quick_View_Pro\Admin\Wizard\Steps\Images,
	Barn2\Plugin\WC_Quick_View_Pro\Admin\Wizard\Steps\Upsell,
	Barn2\Plugin\WC_Quick_View_Pro\Admin\Wizard\Steps\Completed,
	Barn2\Plugin\WC_Quick_View_Pro\Util\Settings,
	Barn2\WQV_Lib\Plugin\License\EDD_Licensing,
	Barn2\WQV_Lib\Plugin\License\Plugin_License,
	Barn2\WQV_Lib\Plugin\Licensed_Plugin,
	Barn2\WQV_Lib\Registerable,
	Barn2\WQV_Lib\Util as Lib_Util;
use Barn2\Plugin\WC_Quick_View_Pro\Dependencies\Barn2\Setup_Wizard\Setup_Wizard as Wizard;

class Setup_Wizard implements Registerable {

	private $plugin;

	private $wizard;

	public function __construct( Licensed_Plugin $plugin ) {

		$this->plugin = $plugin;

		$steps = [
			new License_Verification(),
			new Buttons(),
			new Lightbox(),
			new Images(),
			new Upsell(),
			new Completed(),
		];

		$wizard = new Wizard( $this->plugin, $steps );

		$wizard->configure(
			[
				'skip_url'        => admin_url( 'admin.php?page=wc-settings&tab=products&section=quick-view-pro' ),
				'license_tooltip' => esc_html__( 'The licence key is contained in your order confirmation email.', 'woocommerce-quick-view-pro' ),
				'utm_id'          => 'wqv',
			]
		);

		$wizard->add_edd_api( EDD_Licensing::class );
		$wizard->add_license_class( Plugin_License::class );
		$wizard->add_restart_link( Settings::SECTION_SLUG, 'quick_view_license_section' );

		$wizard->add_custom_asset(
			$plugin->get_dir_url() . 'assets/js/admin/wizard.min.js',
			Lib_Util::get_script_dependencies( $this->plugin, 'admin/wizard.min.js' )
		);

		$this->wizard = $wizard;

	}

	public function register() {
		$this->wizard->boot();

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_wizard_style' ], 21 );
	}

	public function enqueue_wizard_style( $hook_suffix ) {
		if ( 'toplevel_page_' . $this->wizard->get_slug() !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style( 'wcqvp-setup-wizard', $this->plugin->get_dir_url() . 'assets/css/admin/wizard.min.css', [ $this->wizard->get_slug() ], $this->plugin->get_version() );
	}

}
