<?php
namespace Barn2\Plugin\WC_Quick_View_Pro;

use Barn2\Plugin\WC_Quick_View_Pro\Admin\Wizard\Setup_Wizard;
use Barn2\Plugin\WC_Quick_View_Pro\Util\Settings,
	Barn2\WQV_Lib\Registerable,
	Barn2\WQV_Lib\Translatable,
	Barn2\WQV_Lib\Service_Provider,
	Barn2\WQV_Lib\Service_Container,
	Barn2\WQV_Lib\Plugin\Premium_Plugin,
	Barn2\WQV_Lib\Plugin\Licensed_Plugin,
	Barn2\WQV_Lib\Util as Lib_Util,
	Barn2\WQV_Lib\Admin\Notices;

/**
 * The main plugin class. Responsible for setting up to core plugin services.
 *
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Plugin extends Premium_Plugin implements Licensed_Plugin, Registerable, Translatable, Service_Provider {

	use Service_Container;

	const NAME    = 'WooCommerce Quick View Pro';
	const ITEM_ID = 97865;

	/**
	 * Constructs and initalizes the main plugin class.
	 *
	 * @param string $file The root plugin __FILE__
	 * @param string $version The current plugin version
	 */
	public function __construct( $file = null, $version = '1.0' ) {
		parent::__construct(
			[
				'name'               => self::NAME,
				'item_id'            => self::ITEM_ID,
				'version'            => $version,
				'file'               => $file,
				'is_woocommerce'     => true,
				'settings_path'      => 'admin.php?page=wc-settings&tab=products&section=' . Settings::SECTION_SLUG,
				'documentation_path' => 'kb-categories/woocommerce-quick-view-pro-kb/',
				'legacy_db_prefix'   => 'wc_quick_view_pro'
			]
		);
	}

	/**
	 * Registers the plugin with WordPress.
	 */
	public function register() {
		parent::register();

		add_action( 'plugins_loaded', [ $this, 'maybe_load_plugin' ] );
	}

	public function maybe_load_plugin() {

		if ( ! Lib_Util::is_woocommerce_active() ) {
			$this->add_missing_woocommerce_notice();
			return;
		}

		add_action( 'init', [ $this, 'load_textdomain' ], 5 );
		add_action( 'init', [ $this, 'register_services' ] );
	}

	public function load_services() {
		// Don't load anything if WooCommerce not active.

		$this->register_services();
	}

	public function get_services() {

		$services = [
			'admin' => new Admin\Admin_Controller( $this ),
			'wizard' => new Setup_Wizard( $this ),
		];

		// Create core services if license is valid.
		if ( $this->get_license()->is_valid() ) {

			// Frontend_Preview needs to be loaded first in order to load settings filter hook
			$services['frontend_preview'] = new Frontend_Preview( $this );

			$services['rest_routes']      = new Rest\Rest_Controller();
			$services['content']          = new Quick_View_Content();
			$services['frontend_scripts'] = new Frontend_Scripts( $this->get_version(), $services['rest_routes'] );
			$services['button_display']   = new Button_Display( $services['frontend_scripts'] );
			$services['tabs_display']     = new Tabs_Display( $services['frontend_scripts'] );
			$services['shortcode']        = new Shortcode( $services['button_display'] );

			// Plugin and theme integrations.
			$services['theme_integration']      = new Integration\Theme_Integration();
			$services['product_table']          = new Integration\Product_Table_Integration( $services['button_display'], $services['frontend_scripts'] );
			$services['product_addons']         = new Integration\Product_Addons_Integration();
			$services['composite_products']     = new Integration\Composite_Products_Integration();
			$services['bundled_products']       = new Integration\Bundled_Products_Integration();
			$services['woocommerce_blocks']     = new Integration\WooCommerce_Blocks( $services['button_display'] );
			$services['woocommerce_shortcodes'] = new Integration\WooCommerce_Shortcodes( $services['frontend_scripts'] );

		}

		return $services;
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'woocommerce-quick-view-pro', false, $this->get_slug() . '/languages' );
	}

	private function add_missing_woocommerce_notice() {
		if ( Lib_Util::is_admin() ) {
			$admin_notice = new Notices();
			$admin_notice->add(
				'wqv_woocommerce_missing',
				'',
				sprintf( __( 'Please %1$sinstall WooCommerce%2$s in order to use WooCommerce Quick View Pro.', 'woocommerce-quick-view-pro' ), Lib_Util::format_link_open( 'https://woocommerce.com/', true ), '</a>' ),
				[
					'type'       => 'error',
					'capability' => 'install_plugins'
				]
			);
			$admin_notice->boot();
		}
	}

}
