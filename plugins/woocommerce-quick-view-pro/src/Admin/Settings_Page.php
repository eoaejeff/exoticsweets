<?php
namespace Barn2\Plugin\WC_Quick_View_Pro\Admin;

use Barn2\WQV_Lib\Registerable,
	Barn2\WQV_Lib\Plugin\Licensed_Plugin,
	Barn2\Plugin\WC_Quick_View_Pro\Util\Settings,
	Barn2\WQV_Lib\WooCommerce\Admin\Custom_Settings_Fields,
	Barn2\WQV_Lib\WooCommerce\Admin\Plugin_Promo;

/**
 * Provides functions for the plugin settings page in the WordPress admin.
 *
 * Settings are registered under: WooCommerce -> Settings -> Products -> Quick view.
 *
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Settings_Page implements Registerable {

	private $plugin;

	public function __construct( Licensed_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	public function register() {
		$fields = new Custom_Settings_Fields( $this->plugin );
		$fields->register();

		// Add sections & settings
		add_filter( 'woocommerce_get_sections_products', [ $this, 'add_section' ] );
		add_filter( 'woocommerce_get_settings_products', [ $this, 'add_settings' ], 10, 2 );

		// Save license setting
		$license_setting = $this->plugin->get_license_setting();
		add_filter( 'woocommerce_admin_settings_sanitize_option_' . $license_setting->get_license_setting_name(), [ $license_setting, 'save_license_key' ] );

		// Plugin promo
		( new Plugin_Promo( $this->plugin, 'products', Settings::SECTION_SLUG ) )->register();
	}

	public function add_section( $sections ) {
		$sections[ Settings::SECTION_SLUG ] = __( 'Quick view', 'woocommerce-quick-view-pro' );
		return $sections;
	}

	public function add_settings( $settings, $current_section ) {
		// Check we're on the correct settings section
		if ( Settings::SECTION_SLUG !== $current_section ) {
			return $settings;
		}

		return Settings::get_settings( $this->plugin );
	}

}
