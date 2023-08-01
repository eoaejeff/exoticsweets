<?php
/**
 * Composite Products.
 *
 * @package iconic-quickview
 */

/**
 * Composite Products compatibility Class
 *
 * @since 3.4.6
 */
class Iconic_WQV_Compat_Composite_Products {
	/**
	 * Init.
	 */
	public static function run() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
	}

	/**
	 * Load scripts.
	 */
	public static function load_scripts() {
		if ( ! function_exists( 'WC_CP' ) ) {
			return;
		}

		if ( is_product() ) {
			return;
		}

		WC_CP()->display->frontend_scripts();
		wp_enqueue_script( 'wc-single-product' );
		wp_enqueue_script( 'wc-add-to-cart-composite' );
		wp_enqueue_style( 'wc-composite-single-css' );
	}
}
