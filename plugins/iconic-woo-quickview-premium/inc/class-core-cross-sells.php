<?php
/**
 * Cross-sell functions.
 *
 * @package iconic-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'Iconic_WQV_Core_Cross_Sells' ) ) {
	return;
}

/**
 * Iconic_WQV_Core_Cross_Sells.
 *
 * @class    Iconic_WQV_Core_Cross_Sells
 * @version  1.0.0
 */
class Iconic_WQV_Core_Cross_Sells {
	/**
	 * Single instance of the Iconic_WQV_Core_Licence object.
	 *
	 * @var Iconic_WQV_Core_Licence
	 */
	public static $single_instance = null;

	/**
	 * Class args.
	 *
	 * @var array
	 */
	public static $args = array();

	/**
	 * Array of selected plugins.
	 *
	 * @var array
	 */
	private static $selected_plugins = array();

	/**
	 * Creates/returns the single instance Iconic_WQV_Core_Licence object.
	 *
	 * @param array $args Arguments.
	 *
	 * @return Iconic_WQV_Core_Licence
	 */
	public static function run( $args = array() ) {
		if ( null === self::$single_instance ) {
			self::$args            = $args;
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Get remote data.
	 *
	 * @param string $transient_name Transient name.
	 * @param string $url            URL.
	 *
	 * @return array
	 */
	private static function get_remote_data( $transient_name, $url ) {
		$data = get_transient( $transient_name );

		if ( ! empty( $data ) ) {
			return $data;
		}

		$request = wp_remote_get( $url );

		if ( is_wp_error( $request ) ) {
			return array(); // Bail early.
		}

		$body = wp_remote_retrieve_body( $request );
		$data = json_decode( $body, true );

		if ( empty( $data ) ) {
			return array();
		}

		set_transient( $transient_name, $data, HOUR_IN_SECONDS * 48 );

		return $data;
	}

	/**
	 * Get plugins.
	 *
	 * @return array
	 */
	private static function get_plugins() {
		$plugins = self::get_remote_data( 'iconic_get_plugins', 'https://iconicwp.com/wp-json/wp/v2/cpt_product?per_page=100' );
		return $plugins;
	}

	/**
	 * Get plugin.
	 *
	 * @return bool|stdClass
	 */
	public static function get_plugin() {
		$class_name = 'jckqv';
		$plugins    = self::get_plugins();

		if ( empty( $plugins ) ) {
			return false;
		}

		foreach ( $plugins as $plugin ) {
			if ( empty( $plugin['product'] ) || $class_name !== $plugin['product']['class_name'] ) {
				continue;
			}

			return $plugin;
		}

		return false;
	}

	/**
	 * Get selected plugins.
	 *
	 * @param int $limit Max number of plugins to fetch.
	 *
	 * @return bool|array
	 */
	public static function get_selected_plugins( $limit = 2 ) {
		$this_plugin = self::get_plugin();

		if ( empty( $this_plugin ) ) {
			return false;
		}

		$plugins          = self::get_plugins();
		$selected_plugins = array();

		foreach ( $plugins as $plugin ) {
			if ( empty( $plugin ) || ! in_array( $plugin['id'], (array) $this_plugin['product']['related'], true ) ) {
				continue;
			}

			if ( class_exists( $plugin['product']['class_name'] ) || function_exists( $plugin['product']['class_name'] ) ) {
				continue;
			}

			$selected_plugins[] = $plugin;
		}

		if ( empty( $selected_plugins ) ) {
			return false;
		}

		shuffle( $selected_plugins );

		return array_slice( $selected_plugins, 0, $limit );
	}

	/**
	 * Get the API sidebar content.
	 *
	 * @return array
	 */
	public static function get_api_sidebars() {
		$api_host = 'https://api.iconicwp.com';
		$site_url = home_url();
		/**
		 * Enable/Disable using the local API endpoint, rather than the remote.
		 */
		$local_dev = apply_filters( 'iconic_api_local_endpoint', false );
		$url       = ( $local_dev ) ? $site_url : $api_host;
		$sidebars  = self::get_remote_data( 'iconic_api_sidebars_jckqv', $url . '/wp-json/wp/v2/iconic-api-sidebars?plugin_id=jckqv' );

		return $sidebars;
	}
}
