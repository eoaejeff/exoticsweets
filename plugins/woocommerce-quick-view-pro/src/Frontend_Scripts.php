<?php
namespace Barn2\Plugin\WC_Quick_View_Pro;

use Barn2\Plugin\WC_Quick_View_Pro\Util\Util,
	Barn2\Plugin\WC_Quick_View_Pro\Util\Settings,
	Barn2\WQV_Lib\Service,
	Barn2\WQV_Lib\Registerable,
	Barn2\WQV_Lib\Conditional,
	Barn2\WQV_Lib\Rest\Rest_Server,
	Barn2\WQV_Lib\Util as Lib_Util;

/**
 * Loads the various scripts and styles needed for the quick view.
 *
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
final class Frontend_Scripts implements Service, Registerable, Conditional {

	const SCRIPT_HANDLE = 'wc-quick-view-pro';

	private $version;
	private $rest_server;
	private $settings;

	public function __construct( $version, Rest_Server $rest_server ) {
		$this->version     = $version;
		$this->rest_server = $rest_server;
		$this->settings    = Settings::get_plugin_settings();
	}

	public function is_required() {
		return Lib_Util::is_front_end();
	}

	public function register() {
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ], 5 );
		add_action( 'wp_enqueue_scripts', [ $this, 'load_scripts' ] );
	}

	public function register_scripts() {
		// jQuery Modal styles
		wp_register_style( 'jquery-modal', Util::get_asset_url( 'css/jquery-modal/jquery.modal.min.css' ), [], '0.9.1' );
		wp_register_style(
			self::SCRIPT_HANDLE,
			Util::get_asset_url( "css/front-end.min.css" ),
			[ 'jquery-modal' ],
			SCRIPT_DEBUG ? filemtime( Util::get_asset_path( "css/front-end.min.css" ) ) : $this->version
		);

		// Add RTL data - we need suffix to correctly format RTL stylesheet when minified.
		wp_style_add_data( self::SCRIPT_HANDLE, 'rtl', 'replace' );
		wp_style_add_data( self::SCRIPT_HANDLE, 'suffix', '.min' );

		// jQuery Modal scripts
		wp_register_script( 'jquery-modal', Util::get_asset_url( "js/jquery-modal/jquery.modal.min.js" ), [ 'jquery' ], '0.9.1', true );
		wp_register_script( self::SCRIPT_HANDLE, Util::get_asset_url( "js/wc-quick-view-pro.min.js" ), [ 'jquery-modal' ], $this->version, true );

		$script_data = [
			'rest_url'              => untrailingslashit( esc_url_raw( rest_url() ) ),
			'rest_endpoints'        => $this->rest_server->get_endpoints(),
			'rest_nonce'            => wp_create_nonce( 'wp_rest' ),
			'enable_product_link'   => apply_filters( 'wc_quick_view_pro_enable_product_link', $this->settings['enable_product_link'] ),
			'product_link_selector' => apply_filters( 'wc_quick_view_pro_product_link_selector', '.woocommerce-loop-product__link' ),
			'messages'              => [
				'open_error'      => __( 'Sorry, there was an error opening a Quick View for this product.', 'woocommerce-quick-view-pro' ),
				'cart_error'      => __( 'There was an error adding this product to the cart.', 'woocommerce-quick-view-pro' ),
				'no_product_id'   => __( 'No product ID was found for this product.', 'woocommerce-quick-view-pro' ),
				'zero_quantity'   => __( 'Please enter a quantity greater than 0.', 'woocommerce-quick-view-pro' ),
				'no_product_id'   => __( 'No product ID was found for this product.', 'woocommerce-quick-view-pro' ),
				'option_required' => __( 'Please select all required options.', 'woocommerce-quick-view-pro' ),
			],
			'navStyle'              => ( 'bullets' === $this->settings['gallery_style'] ) ? true : 'thumbnails',
			'debug'                 => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG,
		];

		wp_localize_script( self::SCRIPT_HANDLE, 'wc_quick_view_pro_params', apply_filters( 'wc_quick_view_pro_script_params', $script_data ) );

		do_action( 'wc_quick_view_pro_register_scripts' );

		if ( $this->settings['enable_button'] && apply_filters( 'wc_quick_view_pro_show_button_in_shop', true ) ) {
			add_filter( 'body_class', [ $this, 'add_qvp_body_class' ] );
		}

	}

	public function load_scripts() {
		if ( ! $this->should_load_scripts() ) {
			return;
		}

		$this->enqueue_styles();
		$this->enqueue_scripts();

		do_action( 'wc_quick_view_pro_load_scripts' );
	}

	private function should_load_scripts() {
		return apply_filters( 'wc_quick_view_pro_scripts_enabled_on_page', is_woocommerce() );
	}

	private function enqueue_styles() {
		wp_enqueue_style( self::SCRIPT_HANDLE );
	}

	private function enqueue_scripts() {
		if ( Util::is_product_image_displayed() ) {
			// Load product gallery scipts.
			if ( $this->settings['enable_zoom'] ) {
				wp_enqueue_script( 'zoom' );

				// Force-enable zoom, regardless of theme support.
				add_filter( 'woocommerce_single_product_zoom_enabled', '__return_true' );
			}
			if ( $this->settings['enable_gallery'] ) {
				wp_enqueue_script( 'flexslider' );
			}

			// We need this for the $.wc_product_gallery() function.
			wp_enqueue_script( 'wc-single-product' );

			// Force-enable flexslider, regardless of theme support.
			add_filter( 'woocommerce_single_product_flexslider_enabled', '__return_true' );
		}

		if ( Util::are_product_tabs_displayed() ) {
			wp_enqueue_script( 'wc-single-product' );
		}

		if ( Util::are_product_details_displayed() ) {
			if ( $this->settings['show_cart'] ) {
				// Load cart form scripts
				wp_enqueue_script( 'wc-add-to-cart' );
				wp_enqueue_script( 'wc-add-to-cart-variation' );
			}

			if ( $this->settings['show_description'] ) {
				if ( 'mediaelement' === apply_filters( 'wp_audio_shortcode_library', 'mediaelement' ) ) {
					// Audio and video scripts
					wp_enqueue_style( 'wp-mediaelement' );
					wp_enqueue_script( 'wp-mediaelement' );

					// Playlist scripts
					wp_enqueue_script( 'wp-playlist' );
					add_action( 'wp_footer', 'wp_underscore_playlist_templates', 0 );
				}
			}
		}

		wp_enqueue_script( self::SCRIPT_HANDLE );
	}

	public function add_qvp_body_class( $classes ) {
		if ( ! in_array( 'qvp-enabled', $classes ) ) {
			$classes[] = 'qvp-enabled';
		}
		return $classes;
	}

	/**
	 * @deprecated 1.2 Renamed load_scripts().
	 */
	public function load() {
		_deprecated_function( __METHOD__, '1.2', 'load_scripts()' );
		$this->load_scripts();
	}

	/**
	 * @deprecated 1.3 No longer a singleton.
	 */
	public static function instance() {
		_deprecated_function( __METHOD__, '1.3', 'Barn2\Plugin\WC_Quick_View_Pro\wqv()->get_service(\'frontend_scripts\')' );
		return new self();
	}

}
