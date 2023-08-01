<?php
namespace Barn2\Plugin\WC_Quick_View_Pro;

use Barn2\Plugin\WC_Quick_View_Pro\Rest\Rest_Controller,
	Barn2\WQV_Lib\Registerable;

use function Barn2\Plugin\WC_Quick_View_Pro\wqv;

class Back_Compat implements Registerable {

	public function register() {
		// Back compat for old version of JS which used 'rest_base_quick_view' and 'rest_base_cart' params.
		// We add these temporarily to prevent errors on sites where the JS is cached after a plugin update.
		// This can be removed in the next release after 1.4.
		add_filter(
			'wc_quick_view_pro_script_params',
			function( $params ) {
				$params['rest_base_quick_view'] = Rest_Controller::REST_NAMESPACE . '/view';
				$params['rest_base_cart']       = Rest_Controller::REST_NAMESPACE . '/cart';

				return $params;
			}
		);
	}

}

if ( function_exists( 'add_action' ) ) {
	add_action(
		'init',
		function() {
			$back_compat = new Back_Compat();
			$back_compat->register();
		}
	);
}

/**
 * @deprecated 1.3 Replaced by \Barn2\Plugin\WC_Quick_View_Pro\Plugin
 */
final class Quick_View_Plugin {

	/**
	 * @deprecated 1.3
	 */
	const VERSION = \Barn2\Plugin\WC_Quick_View_Pro\PLUGIN_VERSION;
	const FILE    = \Barn2\Plugin\WC_Quick_View_Pro\PLUGIN_FILE;

	/**
	 * @deprecated 1.3 Replaced by Barn2\Plugin\WC_Quick_View_Pro\wqv()->get_service( $service )
	 */
	public function get_helper( $helper ) {
		_deprecated_function( __METHOD__, '1.3', 'Barn2\Plugin\WC_Quick_View_Pro\wqv()->get_service( $service )' );
		return wqv()->get_service( $helper );
	}

	/**
	 * @deprecated 1.3 Replaced by Barn2\Plugin\WC_Quick_View_Pro\wqv()->has_valid_license().
	 */
	public function has_valid_license() {
		_deprecated_function( __METHOD__, '1.3', 'Barn2\Plugin\WC_Quick_View_Pro\wqv()->has_valid_license()' );
		return wqv()->has_valid_license();
	}

	/**
	 * @deprecated 1.3 Use Barn2\Plugin\WC_Quick_View_Pro\wqv() instead.
	 */
	public static function instance() {
		// @todo Deprecate this fully.
		// _deprecated_function( __METHOD__, '1.3', 'Barn2\Plugin\WC_Quick_View_Pro\wqv()' );
		return wqv();
	}

}

if ( ! function_exists( 'wc_quick_view_pro' ) ) {

	/**
	 * @deprecated 1.3 Replaced by Barn2\Plugin\WC_Quick_View_Pro\wqv().
	 */
	function wc_quick_view_pro() {
		_deprecated_function( __FUNCTION__, '1.3', 'Barn2\Plugin\WC_Quick_View_Pro\wqv()' );
		return wqv();
	}
}

class REST_Routes extends Rest\Rest_Controller {

}
