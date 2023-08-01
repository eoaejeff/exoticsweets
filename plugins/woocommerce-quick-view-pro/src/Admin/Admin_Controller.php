<?php
namespace Barn2\Plugin\WC_Quick_View_Pro\Admin;

use Barn2\Plugin\WC_Quick_View_Pro\Util\Util,
	Barn2\WQV_Lib\Util as Lib_Util,
	Barn2\WQV_Lib\Service,
	Barn2\WQV_Lib\Registerable,
	Barn2\WQV_Lib\Conditional,
	Barn2\WQV_Lib\Service_Container,
	Barn2\WQV_Lib\Plugin\Licensed_Plugin,
	Barn2\WQV_Lib\Plugin\Admin\Admin_Links,
	Barn2\WQV_Lib\WooCommerce\Admin\Navigation;

/**
 * Handles general admin functions, such as adding links to our settings page in the Plugins menu.
 *
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Admin_Controller implements Service, Registerable, Conditional {

	use Service_Container;

	private $plugin;

	public function __construct( Licensed_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	public function is_required() {
		return Lib_Util::is_admin();
	}

	public function register() {
		$this->register_services();

		add_action( 'admin_enqueue_scripts', [ $this, 'load_admin_scripts' ] );
	}

	public function get_services() {
		$this->services = [
			'category_manager' => new Category_Manager(),
			'settings_page'    => new Settings_Page( $this->plugin ),
			'admin_links'      => new Admin_Links( $this->plugin ),
			'navigation'       => new Navigation( $this->plugin, 'quick-view-pro', __( 'Quick View Pro', 'woocommerce-quick-view-pro' ) ),
		];
		return $this->services;
	}

	public function load_admin_scripts( $hook_suffix ) {
		$script_version = $this->plugin->get_version();

		wp_enqueue_style( 'wc-quick-view-pro-admin', Util::get_asset_url( 'css/admin/admin.min.css' ), [], $script_version );

		if ( 'woocommerce_page_wc-settings' === $hook_suffix ) {
			wp_enqueue_script( 'wc-quick-view-pro-admin', Util::get_asset_url( 'js/admin/admin.min.js' ), [ 'jquery' ], $script_version, true );
		}
	}

}
