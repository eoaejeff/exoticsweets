<?php
/**
 * Settings.
 *
 * @package iconic-quickview
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Iconic_WQV_Settings.
 *
 * Settings for Quickview.
 *
 * @class    Iconic_WQV_Settings
 * @version  1.0.0
 */
class Iconic_WQV_Settings {
	/**
	 * Run.
	 */
	public static function run() {
		add_action( 'init', array( __CLASS__, 'init' ) );
		self::transition_settings();
	}

	/**
	 * Init.
	 */
	public static function init() {
		global $jckqv;

		if ( empty( $jckqv ) ) {
			return;
		}

		$jckqv->set_settings();
	}

	/**
	 * Settings: Transition old settings to new
	 */
	public static function transition_settings() {
		$new_settings = get_option( 'jckqv_settings' );
		$old_settings = get_option( 'jckqvsettings_settings' );

		if ( $old_settings && ! $new_settings ) {
			$new_settings = array();

			foreach ( $old_settings as $field_id => $value ) {
				$field_id = str_replace( array( 'popup_', 'trigger_' ), '', $field_id );

				$new_settings[ $field_id ] = $value;
			}

			update_option( 'jckqv_settings', $new_settings );
		}
	}
}
