<?php
/**
 * WPML.
 *
 * @package iconic-quickview
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Iconic_WPML.
 */
class Iconic_WQV_WPML {
	/**
	 * Run.
	 */
	public static function run() {
		self::add_filters();
	}

	/**
	 * Add filters.
	 */
	public static function add_filters() {
		add_filter( 'wcml_multi_currency_ajax_actions', array( __CLASS__, 'add_action_to_multi_currency_ajax' ), 10, 1 );
	}

	/**
	 * Add quickview action to multi currency.
	 *
	 * @param array $ajax_actions AJAX actions.
	 *
	 * @return array
	 */
	public static function add_action_to_multi_currency_ajax( $ajax_actions ) {
		$ajax_actions[] = 'jckqv';
		$ajax_actions[] = 'jckqv_add_to_cart';

		return $ajax_actions;
	}
}
