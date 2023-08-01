<?php
/**
 * BodyCommerce compatiblity.
 *
 * @package iconic-quickview
 */

/**
 * Divi BodyCommerce compatiblity class.
 */
class Iconic_WQV_Compat_BodyCommerce {
	/**
	 * Init.
	 */
	public static function run() {
		add_action( 'init', array( __CLASS__, 'hooks' ) );
	}

	/**
	 * Hooks.
	 */
	public static function hooks() {
		global $jckqv;

		if ( ! defined( 'DE_DB_WOO_VERSION' ) ) {
			return;
		}

		add_action( 'de_ajaxfilter_after_shop_loop_item', array( $jckqv, 'display_button' ) );
	}

}
