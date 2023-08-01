<?php
namespace Barn2\Plugin\WC_Quick_View_Pro\Rest;

use Barn2\WQV_Lib\Registerable,
	Barn2\WQV_Lib\Service,
	Barn2\WQV_Lib\Rest\Rest_Server,
	Barn2\WQV_Lib\Rest\Base_Server;

/**
 * Registers the quick view REST routes.
 *
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Rest_Controller extends Base_Server implements Service, Registerable, Rest_Server {

	const REST_NAMESPACE = 'wc-quick-view-pro/v1';

	/**
	 * @var Route[] The list of REST route objects handled by this server.
	 */
	private $routes = [];

	public function __construct() {
		$this->routes = [
			new Quick_View_Route( self::REST_NAMESPACE ),
			new Add_To_Cart_Route( self::REST_NAMESPACE )
		];
	}

	public function register() {
		parent::register();
		add_filter( 'nonce_user_logged_out', [ $this, 'nonce_user_logged_out' ], 50, 2 );
	}

	public function get_namespace() {
		return self::REST_NAMESPACE;
	}

	public function get_routes() {
		return $this->routes;
	}

	/**
	 * Prevent WooCommerce overriding the user ID for logged out users as this breaks our nonce validation.
	 *
	 * @param int $uid The user ID
	 * @param string $action The nonce action
	 * @return int The user ID when logged out
	 */
	public function nonce_user_logged_out( $uid, $action ) {
		if ( 'wp_rest' === $action && ! is_user_logged_in() ) {
			return 0;
		}
		return $uid;
	}

}
