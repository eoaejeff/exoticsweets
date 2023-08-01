<?php
namespace Barn2\Plugin\WC_Quick_View_Pro\Rest;

use Barn2\Plugin\WC_Quick_View_Pro\Util\Util,
	Barn2\WQV_Lib\Rest\Base_Route,
	Barn2\WQV_Lib\Registerable,
	WP_Error,
	WP_REST_Server,
	WP_REST_Request;

/**
 * REST handler for the 'get quick view' route.
 *
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Quick_View_Route extends Base_Route implements Registerable {

	protected $rest_base  = 'view';
	private $post_globals = [];

	public function register() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<product_id>\d+)',
			[
				'args'                => [
					'product_id' => [
						'type'        => 'integer',
						'required'    => true,
						'minimum'     => 1,
						'description' => __( 'The product ID to retrieve the quick view for.', 'woocommerce-quick-view-pro' )
					]
				],
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_quick_view' ],
				'permission_callback' => '__return_true'
			]
		);
	}

	public function get_quick_view( WP_REST_Request $request ) {
		$this->check_prerequisites();

		$product_id = ! empty( $request['product_id'] ) ? absint( $request['product_id'] ) : 0;
		$product    = wc_get_product( $product_id );

		// Check product exists.
		if ( ! $product ) {
			return new WP_Error( 'rest_invalid_product_id', Util::format_rest_error( esc_html__( 'The requested product does not exist.', 'woocommerce-quick-view-pro' ) ) );
		}

		$this->setup_post_globals( $product );

		do_action( 'wc_quick_view_pro_before_quick_view_template', $product );

		ob_start();

		// Output the Quick View template.
		Util::load_template( 'quick-view.php', [ 'product' => $product ] );

		$quick_view = trim( ob_get_clean() );

		do_action( 'wc_quick_view_pro_after_quick_view_template', $product );

		$response = [
			'product_id' => $product_id,
			'quick_view' => $quick_view
		];

		$this->reset_post_globals();

		return rest_ensure_response( $response );
	}

	private function check_prerequisites() {
		if ( defined( 'WC_ABSPATH' ) ) {
			// WC 3.6+ - Template hooks are not included during a REST request.
			include_once WC_ABSPATH . 'includes/wc-template-hooks.php';
		}
	}

	private function setup_post_globals( $the_product ) {
		global $product, $post, $post_id;

		// Store current globals.
		$this->post_globals = [
			'post_id' => $post_id,
			'post'    => $post,
			'product' => $product
		];

		// Overwrite globals.
		$post_id = $the_product->get_id();
		$post    = get_post( $the_product->get_id() ); // post should be in cache at this point.
		$product = $the_product;

		setup_postdata( $post ); // WooCommerce globals are set on this call.
	}

	private function reset_post_globals() {
		if ( ! empty( $this->post_globals['post_id'] ) ) {
			$GLOBALS['post_id'] = $this->post_globals['post_id'];
		}
		if ( ! empty( $this->post_globals['post'] ) ) {
			$GLOBALS['post'] = $this->post_globals['post'];
		}
		if ( ! empty( $this->post_globals['product'] ) ) {
			$GLOBALS['product'] = $this->post_globals['product'];
		}
	}

}
