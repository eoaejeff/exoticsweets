<?php
/**
 * The main plugin file for WooCommerce Quick View Pro.
 *
 * This file is included during the WordPress bootstrap process if the plugin is active.
 *
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 *
 * @wordpress-plugin
 * Plugin Name:     WooCommerce Quick View Pro
 * Plugin URI:      https://barn2.com/wordpress-plugins/woocommerce-quick-view-pro/
 * Description:     The best quick view plugin for WooCommerce.
 * Version:         1.6.13
 * Author:          Barn2 Plugins
 * Author URI:      https://barn2.com
 * Text Domain:     woocommerce-quick-view-pro
 * Domain Path:     /languages
 *
 * WC requires at least: 3.7.0
 * WC tested up to: 7.1.0
 *
 * Copyright:       Barn2 Media Ltd
 * License:         GNU General Public License v3.0
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.html
 */
namespace Barn2\Plugin\WC_Quick_View_Pro;

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const PLUGIN_VERSION = '1.6.13';
const PLUGIN_FILE    = __FILE__;

// Include autoloader.
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Helper function to access the shared plugin instance.
 *
 * @return Plugin The plugin instance
 */
function wqv() {
	return Plugin_Factory::create( PLUGIN_FILE, PLUGIN_VERSION );
}

// Load the plugin.
wqv()->register();
