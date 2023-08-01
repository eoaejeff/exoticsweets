<?php
namespace Barn2\Plugin\WC_Quick_View_Pro\Rest;

use Barn2\Plugin\WC_Quick_View_Pro\Util\Util,
	Barn2\WQV_Lib\Rest\Base_Route,
	Barn2\WQV_Lib\Registerable,
	WP_Error,
	WP_REST_Server,
	WP_REST_Request,
	WC_Customer,
	WC_Cart;

/**
 * REST handler for the 'add to cart' route.
 *
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Add_To_Cart_Route extends Base_Route implements Registerable {

	protected $rest_base = 'cart';

	public function register() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'add_to_cart' ],
				'permission_callback' => '__return_true'
			]
		);
	}

	public function add_to_cart( WP_REST_Request $request ) {
		$this->check_prerequisites();

		$cart_products = $this->validate_add_to_cart_params( $request->get_params() );

		if ( is_wp_error( $cart_products ) ) {
			return $cart_products;
		}

		$response       = [ 'success' => false ];
		$products_added = $this->add_products_to_cart( $cart_products );

		if ( ! empty( $products_added ) ) {
			// Successful addition, so we now build the response.
			$cart_message = $this->get_add_to_cart_message( $products_added, true );

			$response = [
				'success'      => true,
				'cart_message' => $cart_message,
				'fragments'    => $this->get_add_to_cart_fragments(),
				'cart_hash'    => $this->get_add_to_cart_hash()
			];

			// If we redirecting to the cart after addition, add the cart notice so it appears after redirect.
			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wc_add_notice( $cart_message );
			}
		} else {
			// Error adding to the cart - build error message from notices.
			$errors = Util::get_wc_notices( 'error' );

			if ( ! $errors ) {
				// An unknown error occurred (e.g. product didn't pass validation).
				$errors = [ __( 'There was an error adding this product to the cart.', 'woocommerce-quick-view-pro' ) ];
			}

			if ( ! apply_filters( 'wc_quick_view_pro_show_all_cart_errors', false ) ) {
				$errors = [ reset( $errors ) ];
			}

			$message = implode(
				'',
				array_map(
					function ( $val ) {
						return "<p class=\"notice-text\">{$val}</p>";
					},
					$errors
				)
			);

			$response['error'] = $message;
		}

		return rest_ensure_response( $response );
	}

	protected function add_products_to_cart( $cart_products ) {
		if ( empty( array_filter( $cart_products ) ) ) {
			return false;
		}

		$products_added = [];

		foreach ( $cart_products as $cart_product ) {
			$cart_product = apply_filters(
				'wc_quick_view_pro_add_to_cart_params',
				$cart_product,
				wc_get_product( $cart_product['product_id'] )
			);
			$product_id   = $cart_product['product_id'];
			$quantity     = $cart_product['quantity'];
			$variation_id = 0;
			$variations   = [];

			if ( isset( $cart_product['variation_id'] ) ) {
				$variation_id = $cart_product['variation_id'];
				$variations   = $cart_product['variations'];
			}

			if ( $this->add_single_product_to_cart( $product_id, $quantity, $variation_id, $variations ) ) {
				$cart_product_id                    = $variation_id ? $variation_id : $product_id;
				$products_added[ $cart_product_id ] = $quantity;
			}
		}

		return $products_added;
	}

	protected function add_single_product_to_cart( $product_id, $quantity, $variation_id = 0, $variations = [] ) {
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
		$product_status    = get_post_status( $product_id );

		if ( $passed_validation && 'publish' === $product_status && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) ) {
			// Product successfully added.
			do_action( 'woocommerce_ajax_added_to_cart', $product_id );
			return true;
		}

		return false;
	}

	/**
	 * Custom add to cart message for the Quick View.
	 *
	 * @param int|array $products Product ID list or single product ID.
	 * @param bool      $show_qty Should qty's be shown? Added in 2.6.0.
	 * @param bool      $return   Return message rather than add it.
	 *
	 * @return mixed
	 */
	protected function get_add_to_cart_message( $products, $show_qty = false ) {
		$titles = [];
		$count  = 0;

		if ( ! $show_qty ) {
			$products = array_fill_keys( array_keys( $products ), 1 );
		}

		foreach ( $products as $product_id => $qty ) {
			/* translators: %s: product name */
			$titles[] = ( $qty > 1 ? absint( $qty ) . ' &times; ' : '' ) . apply_filters( 'woocommerce_add_to_cart_item_name_in_quotes', sprintf( _x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'woocommerce-quick-view-pro' ), strip_tags( get_the_title( $product_id ) ) ), $product_id );
			$count   += $qty;
		}

		$titles = array_filter( $titles );
		/* translators: %s: product name */
		$added_text = sprintf( _n( '%s has been added to your cart.', '%s have been added to your cart.', $count, 'woocommerce-quick-view-pro' ), wc_format_list_of_items( $titles ) );

		// Get success messages.
		$message = '';

		if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
			$return_to = apply_filters(
				'woocommerce_continue_shopping_redirect',
				wc_get_raw_referer() ? wp_validate_redirect( wc_get_raw_referer(), false )
					: wc_get_page_permalink( 'shop' )
			);
			$message   = sprintf( '<a href="%s" tabindex="1" class="button wc-forward">%s</a> %s', esc_url( $return_to ), esc_html__( 'Continue shopping', 'woocommerce-quick-view-pro' ), esc_html( $added_text ) );
		} else {
			$message = esc_html( $added_text );
		}

		return apply_filters( 'wc_add_to_cart_message_html', $message, $products, $show_qty );
	}

	protected function validate_add_to_cart_params( $params ) {
		$product_id    = apply_filters( 'woocommerce_add_to_cart_product_id', ( ! empty( $params['product_id'] ) ? absint( $params['product_id'] ) : 0 ) );
		$product       = wc_get_product( $product_id );
		$quantity      = ! empty( $params['quantity'] ) ? $params['quantity'] : 0;
		$cart_products = [];

		if ( $product ) {
			if ( 'grouped' === $product->get_type() ) {
				// For grouped products, quantity is passed as an array in the form array( product_id => quantity ).
				$quantity = array_map( [ Util::class, 'absfloat' ], (array) $quantity );

				foreach ( $quantity as $child_id => $child_quantity ) {
					$child = wc_get_product( $child_id );

					if ( $child ) {
						$cart_products[] = array_merge(
							$params,
							[
								'product_id' => $child_id,
								'quantity'   => $child_quantity,
							]
						);
					}
				}
			} else {
				// For other products we should receive a single quantity.
				if ( ! $quantity || ! is_numeric( $quantity ) ) {
					// Default to 1 if not set.
					$quantity = 1;
				}

				$quantity = wc_stock_amount( Util::absfloat( $quantity ) );

				$this_product = [
					'product_id' => $product_id,
					'quantity'   => $quantity,
				];

				$variation_id = ! empty( $params['variation_id'] ) ? absint( $params['variation_id'] ) : 0;

				if ( 'variation' === $product->get_type() ) {
					$variation_id                 = $product_id;
					$this_product['variation_id'] = $product_id;
					$this_product['product_id']   = $product->get_parent_id();
				}

				if ( $variation_id ) {
					$this_product['variations'] = $this->get_submitted_variations( $params, $product );
				}

				$cart_products[] = array_merge(
					$params,
					$this_product
				);
			}
		} elseif ( is_array( $quantity ) || isset( $params['multiple-add-to-cart'] ) ) {
			if ( isset( $params['multiple-add-to-cart'] ) ) {
				// The form submission must be coming from WooCommerce Bulk Variations version 1.x
				$quantity = array_count_values( explode( ',', $params['multiple-add-to-cart'] ) );
			}
			foreach ( $quantity as $variation_id => $variation_quantity ) {
				$variation = wc_get_product( $variation_id );

				if ( $variation ) {
					$cart_products[] = array_merge(
						$params,
						[
							'product_id'   => $variation->get_parent_id(),
							'quantity'     => $variation_quantity,
							'variation_id' => $variation_id,
							'variations'   => $variation->get_variation_attributes(),
						]
					);
				}
			}
		} else {
			// If `product_id` is undefined and `quantity` is not an array, return an error
			return new WP_Error( 'rest_invalid_product_id', Util::format_rest_error( esc_html__( 'The requested product does not exist.', 'woocommerce-quick-view-pro' ) ) );
		}

		return $cart_products;
	}

	/**
	 * Check any prerequisites required for our add to cart request.
	 */
	private function check_prerequisites() {
		if ( defined( 'WC_ABSPATH' ) ) {
			// WC 3.6+ - Cart and notice functions are not included during a REST request.
			include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
			include_once WC_ABSPATH . 'includes/wc-notice-functions.php';
			include_once WC_ABSPATH . 'includes/wc-template-hooks.php';
		}

		if ( null === WC()->session ) {
			$session_class = apply_filters( 'woocommerce_session_handler', '\WC_Session_Handler' );

			WC()->session = new $session_class();
			WC()->session->init();
		}

		if ( null === WC()->customer ) {
			WC()->customer = new WC_Customer( get_current_user_id(), true );
		}

		if ( null === WC()->cart ) {
			WC()->cart = new WC_Cart();

			// We need to force a refresh of the cart contents from session here (cart contents are normally refreshed on wp_loaded, which has already happened by this point).
			WC()->cart->get_cart();
		}
	}

	private function get_add_to_cart_fragments() {
		// Fetch the mini-cart HTML.
		ob_start();
		woocommerce_mini_cart();
		$mini_cart = ob_get_clean();

		return apply_filters( 'woocommerce_add_to_cart_fragments', [ 'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>' ] );
	}

	private function get_add_to_cart_hash() {
		$cart = WC()->cart->get_cart_for_session();
		return apply_filters( 'woocommerce_add_to_cart_hash', $cart ? md5( json_encode( $cart ) ) : '', $cart );
	}

	private function get_submitted_variations( $params, $product ) {
		if ( empty( $params ) || ! in_array( $product->get_type(), [ 'variable', 'variation' ] ) || ! method_exists( $product, 'get_variation_attributes' ) ) {
			return false;
		}

		$attributes = $product->get_variation_attributes();

		// Attributes for 'variable' products are not prefixed 'attribute_', so we do this to make it consistent with 'variation' products.
		if ( $product->is_type( 'variable' ) ) {
			$variable_attributes = [];

			foreach ( $attributes as $key => $value ) {
				$key                         = 'attribute_' . sanitize_title( $key );
				$variable_attributes[ $key ] = $value;
			}

			$attributes = $variable_attributes;
		}

		// Return the params which are valid variations.
		return array_intersect_key( $params, $attributes );
	}

}
