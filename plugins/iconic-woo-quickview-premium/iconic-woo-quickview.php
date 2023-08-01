<?php
/**
 * Plugin Name: WooCommerce Quickview by Iconic
 * Plugin URI: https://iconicwp.com/products/woocommerce-quickview/
 * Description: Quickview plugin for WooCommerce
 * Version: 3.6.2
 * Update URI: https://api.freemius.com
 * Author: Iconic
 * Author Email: support@iconicwp.com
 * WC requires at least: 2.6.14
 * WC tested up to: 7.9.0
 *
 * @package iconic-quickview
 */

/**
 * Class JCKQV
 */
class JCKQV {
	/**
	 * Name.
	 *
	 * @var string $name
	 */
	public $name = 'WooCommerce Quickview';

	/**
	 * Short Name.
	 *
	 * @var string $short_name
	 */
	public $short_name = 'Quickview';

	/**
	 * Slug.
	 *
	 * @var string $slug
	 */
	public $slug = 'jckqv';

	/**
	 * Class prefix
	 *
	 * @since  1.0.0
	 * @var string $class_prefix
	 */
	protected $class_prefix = 'Iconic_WQV_';

	/**
	 * Version.
	 *
	 * @var string $version
	 */
	public $version = '3.6.2';

	/**
	 * Plugin Path.
	 *
	 * @var string $plugin_path Absolute path to this plugin folder, trailing slash
	 */
	public $plugin_path;

	/**
	 * Plugin URL.
	 *
	 * @var string $plugin_url URL to this plugin folder, no trailing slash
	 */
	public $plugin_url;

	/**
	 * Settings;
	 *
	 * @var array $settings Settings array
	 */
	public $settings;

	/**
	 * Woo Version.
	 *
	 * @var string $woo_version WooCommerce version number
	 */
	public $woo_version;

	/**
	 * Templates.
	 *
	 * @var Iconic_WQV_Core_Template_Loader $templates
	 */
	public $templates;

	/**
	 * Bundled Products.
	 *
	 * @var Iconic_WQV_Bundled_Products $bundled_products
	 */
	protected $bundled_products;

	/**
	 * Construct
	 */
	public function __construct() {
		$this->define_constants();
		$this->load_classes();

		if ( ! Iconic_WQV_Core_Licence::has_valid_licence() ) {
			return;
		}

		$this->woo_version = $this->get_woo_version_number();

		// Hook up to the init action.
		add_action( 'init', array( $this, 'before_initiate' ), 0 );
		// Priority 11 to ensure settings have been set by set_settings() before 'initiate' is run.
		add_action( 'init', array( $this, 'initiate' ), 11 );
		add_action( 'init', array( 'Iconic_WQV_Shortcodes', 'init' ) );
		add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );
	}

	/**
	 * Define Constants.
	 */
	private function define_constants() {
		$this->define( 'ICONIC_WQV_PATH', plugin_dir_path( __FILE__ ) );
		$this->define( 'ICONIC_WQV_INC_PATH', ICONIC_WQV_PATH . 'inc/' );
		$this->define( 'ICONIC_WQV_VENDOR_PATH', ICONIC_WQV_INC_PATH . 'vendor/' );
		$this->define( 'ICONIC_WQV_URL', plugin_dir_url( __FILE__ ) );
		$this->define( 'ICONIC_WQV_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'ICONIC_WQV_IS_ENVATO', false );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Load Classes
	 */
	private function load_classes() {
		require_once ICONIC_WQV_INC_PATH . 'class-core-autoloader.php';

		Iconic_WQV_Core_Autoloader::run(
			array(
				'prefix'   => 'Iconic_WQV_',
				'inc_path' => ICONIC_WQV_INC_PATH,
			)
		);

		Iconic_WQV_Core_Licence::run(
			array(
				'basename' => ICONIC_WQV_BASENAME,
				'urls'     => array(
					'product'  => 'https://iconicwp.com/products/woocommerce-quickview/',
					'settings' => admin_url( 'admin.php?page=jckqv-settings' ),
					'account'  => admin_url( 'admin.php?page=jckqv-settings-account' ),
				),
				'paths'    => array(
					'inc'    => ICONIC_WQV_INC_PATH,
					'plugin' => ICONIC_WQV_PATH,
				),
				'freemius' => array(
					'id'         => '1037',
					'slug'       => 'iconic-woo-quickview',
					'public_key' => 'pk_cbcb0552db131fd591137450c33d7',
					'menu'       => array(
						'slug' => 'jckqv-settings',
					),
				),
			)
		);

		Iconic_WQV_Core_Settings::run(
			array(
				'vendor_path'   => ICONIC_WQV_VENDOR_PATH,
				'title'         => $this->name,
				'version'       => $this->version,
				'menu_title'    => $this->short_name,
				'settings_path' => ICONIC_WQV_INC_PATH . 'admin/settings.php',
				'option_group'  => $this->slug,
				'docs'          => array(
					'collection'      => '/collection/158-woocommerce-quickview',
					'troubleshooting' => '/collection/158-woocommerce-quickview',
					'getting-started' => '/category/162-getting-started',
				),
				'cross_sells'   => array(
					'iconic-woo-show-single-variations',
					'iconic-woothumbs',
				),
			)
		);

		if ( ! Iconic_WQV_Core_Licence::has_valid_licence() ) {
			return;
		}

		Iconic_WQV_Settings::run();

		add_action( 'plugins_loaded', array( 'Iconic_WQV_Core_Onboard', 'run' ), 10 );
		$this->templates = new Iconic_WQV_Core_Template_Loader( $this->slug, 'jck-woo-quickview', ICONIC_WQV_PATH );
		Iconic_WQV_WPML::run();
		Iconic_WQV_Compat_Composite_Products::run();
		Iconic_WQV_Compat_BodyCommerce::run();
	}

	/**
	 * Set settings.
	 */
	public function set_settings() {
		$this->settings = Iconic_WQV_Core_Settings::$settings;
	}

	/**
	 * Runs just before the normal "init" method
	 */
	public function before_initiate() {
		add_filter( 'wcml_multi_currency_is_ajax', array( $this, 'add_ajax_action' ) );
	}

	/**
	 * Runs on "init" hook
	 */
	public function initiate() {
		// Setup localization.
		load_plugin_textdomain( 'jckqv', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		if ( is_admin() ) {
			// Ajax.
			add_action( 'wp_ajax_jckqv', array( $this, 'modal' ) );
			add_action( 'wp_ajax_nopriv_jckqv', array( $this, 'modal' ) );
			add_action( 'wp_ajax_jckqv_add_to_cart', array( $this, 'add_to_cart' ) );
			add_action( 'wp_ajax_nopriv_jckqv_add_to_cart', array( $this, 'add_to_cart' ) );

			// Setup Modal (Ajax).
			add_action( 'jck_qv_summary', array( $this, 'modal_part_sale_flash' ), 5, 3 );
			add_action( 'jck_qv_summary', array( $this, 'modal_part_title' ), 10, 3 );
			add_action( 'jck_qv_summary', array( $this, 'modal_part_rating' ), 15, 3 );
			add_action( 'jck_qv_summary', array( $this, 'modal_part_price' ), 20, 3 );
			add_action( 'jck_qv_summary', array( $this, 'modal_part_desc' ), 25, 3 );
			add_action( 'jck_qv_summary', array( $this, 'modal_part_add_to_cart' ), 30, 3 );
			add_action( 'jck_qv_summary', array( $this, 'modal_part_meta' ), 35, 3 );

			add_action( 'jck_qv_images', array( $this, 'modal_part_styles' ), 5, 3 );
			add_action( 'jck_qv_images', array( $this, 'modal_part_images' ), 10, 3 );

			add_action( 'jck_qv_after_summary', array( $this, 'modal_part_close' ), 5, 3 );
			add_action( 'jck_qv_after_summary', array( $this, 'modal_part_adding_to_cart' ), 10, 3 );

			$this->setup_shop_the_look();
		} else {
			$this->register_scripts_and_styles();

			// Show Button.
			if ( '1' === $this->settings['trigger_position_autoinsert'] ) {
				if ( 'beforeitem' === $this->settings['trigger_position_position'] ) {
					add_action( 'woocommerce_before_shop_loop_item', array( $this, 'display_button' ) );
				} elseif ( 'beforetitle' === $this->settings['trigger_position_position'] ) {
					add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'display_button' ) );
				} elseif ( 'aftertitle' === $this->settings['trigger_position_position'] ) {
					add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'display_button' ) );
				} elseif ( 'afteritem' === $this->settings['trigger_position_position'] ) {
					add_action( 'woocommerce_after_shop_loop_item', array( $this, 'display_button' ) );
				}
			}
		}
	}

	/**
	 * Setup Shop the look
	 */
	public function setup_shop_the_look() {
		global $jck_shop_the_look;

		if ( isset( $jck_shop_the_look ) && $jck_shop_the_look ) {
			add_action( 'jck_qv_summary', array( $jck_shop_the_look, 'shop_the_look_display' ), 30 );
		}
	}

	/**
	 * Frontend: Modal Part: Close Button
	 */
	public function modal_part_close() {
		echo '<button title="Close (Esc)" type="button" class="mfp-close">×</button>';
	}

	/**
	 * Frontend: Modal Part: Adding to Cart Icon
	 */
	public function modal_part_adding_to_cart() {
		echo '<div id="addingToCart"><div><i class="iconic-wqv-icon-cw animate-spin"></i> <span>' . esc_html__( 'Adding to Cart...', 'jckqv' ) . '</span></div></div>';
	}

	/**
	 * Frontend: Modal Part: Styles
	 */
	public function modal_part_styles() {
		include $this->templates->locate_template( 'styles.php' );
	}

	/**
	 * Frontend: Modal Part: Images
	 */
	public function modal_part_images() {
		global $iconic_woothumbs_class;

		if ( $iconic_woothumbs_class && apply_filters( 'woothumbs_enabled', $iconic_woothumbs_class->is_enabled() ) ) {
			$iconic_woothumbs_class->show_product_images();
		} else {
			include $this->templates->locate_template( 'images.php' );
		}
	}

	/**
	 * Frontend: Modal Part: Sale Flash
	 */
	public function modal_part_sale_flash() {
		if ( $this->settings['popup_content_showbanner'] ) {
			include $this->templates->locate_template( 'sale-flash.php' );
		}
	}

	/**
	 * Frontend: Modal Part: Title
	 */
	public function modal_part_title() {
		if ( $this->settings['popup_content_showtitle'] ) {
			include $this->templates->locate_template( 'title.php' );
		}
	}

	/**
	 * Frontend: Modal Part: Rating
	 */
	public function modal_part_rating() {
		if ( $this->settings['popup_content_showrating'] ) {
			include $this->templates->locate_template( 'rating.php' );
		}
	}

	/**
	 * Frontend: Modal Part: Price
	 */
	public function modal_part_price() {
		if ( $this->settings['popup_content_showprice'] ) {
			include $this->templates->locate_template( 'price.php' );
		}
	}

	/**
	 * Frontend: Modal Part: Description
	 */
	public function modal_part_desc() {
		if ( $this->settings['popup_content_showdesc'] ) {
			include $this->templates->locate_template( 'desc.php' );
		}
	}

	/**
	 * Frontend: Modal Part: Add to Cart
	 */
	public function modal_part_add_to_cart() {
		if ( $this->settings['popup_content_showatc'] ) {
			include $this->templates->locate_template( $this->get_add_to_cart_filename() );
		}
	}

	/**
	 * Frontend: Modal Part: Meta
	 */
	public function modal_part_meta() {
		if ( $this->settings['popup_content_showmeta'] ) {
			include $this->templates->locate_template( 'meta.php' );
		}
	}

	/**
	 * Add Ajax Action
	 *
	 * Adds 'jckqv' and 'jckqv_add_to_cart' actions in 'wcml_multi_currency_is_ajax'
	 * to apply multi-currency filters (to convert prices to current currency)
	 *
	 * @param array $actions Array of AJAX action hooks.
	 *
	 * @return array
	 */
	public function add_ajax_action( $actions ) {
		$actions[] = 'jckqv';
		$actions[] = 'jckqv_add_to_cart';

		return $actions;
	}

	/**
	 * Admin: Sanitize settings on save
	 *
	 * @param array $settings Array of plugin settings.
	 */
	public function sanitize_settings( $settings ) {
		// Validate Margins.
		$i = 0;
		foreach ( $settings['trigger_position_margins'] as $mar_val ) {
			$settings['trigger_position_margins'][ $i ] = ( '' !== $mar_val ) ? preg_replace( '/[^\d-]+/', '', $mar_val ) : 0;
			$i ++;
		}

		return $settings;
	}

	/**
	 * Diplay quickview button.
	 *
	 * @param bool  $product_id Product ID.
	 * @param array $args       Array of arguments.
	 * @param null  $text       Styling text.
	 * @param null  $icon       Styling icon.
	 * @param bool  $style      Whether or not to apply styling.
	 *
	 * @return string
	 */
	public function display_button( $product_id = false, $args = array(), $text = null, $icon = null, $style = true ) {
		global $post, $product;

		$defaults = array(
			'content' => $this->settings['trigger_styling_text'],
			'icon'    => $this->settings['trigger_styling_icon'],
			'style'   => true,
			'align'   => $this->settings['trigger_position_align'],
		);

		$args       = wp_parse_args( $args, $defaults );
		$product_id = ( $product_id ) ? $product_id : $post->ID;

		if ( ! $product_id ) {
			return '';
		}

		if ( ! $product && false !== $product_id ) {
			$product = wc_get_product( $product_id );
		}

		if ( ! $product ) {
			return '';
		}

		if ( $product->is_type( 'variation' ) ) {
			$parent_id  = wp_get_post_parent_id( $product_id );
			$product_id = ( $parent_id ) ? sprintf( '%d:%d', $parent_id, $product_id ) : $product_id;
		}

		$classes = array(
			'iconic-wqv-button--no-style',
		);

		if ( $args['style'] ) {
			$classes = array(
				'iconic-wqv-button',
				'iconic-wqv-button--align-' . $args['align'],
			);

			if ( $this->settings['trigger_styling_autohide'] ) {
				$classes[] = 'iconic-wqv-button--auto-hide';
			}
		}

		$classes = apply_filters( 'iconic_wqv_button_classes', $classes );

		if ( 'a' === $this->settings['trigger_general_tag'] ) {
			echo '<a href="' . esc_url( $product->get_permalink() ) . '" data-jckqvpid="' . esc_attr( $product_id ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '">' . ( ( 'none' !== $args['icon'] ) ? '<i class="iconic-wqv-icon-' . esc_attr( $args['icon'] ) . '"></i>' : '' ) . ' ' . wp_kses_post( $args['content'] ) . '</a>';
		} else {
			echo '<button data-jckqvpid="' . esc_attr( $product_id ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '">' . ( ( 'none' !== $args['icon'] ) ? '<i class="iconic-wqv-icon-' . esc_attr( $args['icon'] ) . '"></i>' : '' ) . ' ' . wp_kses_post( $args['content'] ) . '</button>';
		}
	}

	/**
	 * Frontend: The Modal Request
	 *
	 * Triggered via AJAX, this is what gets displayed when the
	 * quickview button is clicked!
	 */
	public function modal() {
		global $product;

		switch_to_locale( get_locale() );

		$pid          = $this->get_product_id();
		$product_post = get_post( $pid );

		setup_postdata( $product_post );

		$product = wc_get_product( $pid );
		$classes = apply_filters(
			'jck_qv_modal_classes',
			array(
				'cf',
				'product',
				sprintf( 'product-type-%s', $product->get_type() ),
			)
		);

		echo '<div id="' . esc_attr( $this->slug ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '">';

		do_action( 'jck_qv_images', $pid, $product_post, $product );

		echo '<div id="' . esc_attr( $this->slug ) . '_summary">';

		do_action( 'jck_qv_summary', $pid, $product_post, $product );

		echo '</div>';

		do_action( 'jck_qv_after_summary', $pid, $product_post, $product );

		echo '</div>';

		wp_reset_postdata();

		die;
	}

	/**
	 * Helper: Get product ID
	 *
	 * @return int
	 */
	public function get_product_id() {
		if ( ! self::get_filtered_input( 'product_id', 'int' ) ) {
			return false;
		}

		$product_id = explode( ':', self::get_filtered_input( 'product_id', 'int' ) );

		if ( $product_id && isset( $product_id[1] ) ) {
			$this->get_selected_variation_request( $product_id[1] );
		}

		return $product_id[0];
	}

	/**
	 * Get filtered input.
	 *
	 * @param string $key         Key.
	 * @param string $type        String|int|etc.
	 * @param string $nonce_key   Nonce key.
	 * @param string $nonce_value Nonce Value.
	 *
	 * @return array|int|mixed|string
	 */
	public static function get_filtered_input( $key, $type = 'string', $nonce_key = '', $nonce_value = '' ) {
		if ( $nonce_key ) {
			check_admin_referer( $nonce_key, $nonce_value );
		}

		if ( ! isset( $_REQUEST[ $key ] ) ) {
			return false;
		}

		if ( 'string' === $type ) {
			$input = sanitize_text_field( htmlspecialchars( sanitize_text_field( wp_unslash( $_REQUEST[ $key ] ) ) ) );
		} elseif ( 'int' === $type ) {
			$input = (int) sanitize_text_field( filter_var( wp_unslash( $_REQUEST[ $key ] ), FILTER_SANITIZE_NUMBER_INT ) );
		} else {
			$input = filter_var( wp_unslash( $_REQUEST[ $key ] ) );
		}

		return $input;
	}

	/**
	 * Helper: Get Selected Variation Request
	 *
	 * @param int $variation_id Product variation ID.
	 */
	public function get_selected_variation_request( $variation_id ) {
		$variation_id = absint( $variation_id );

		$variation = wc_get_product( $variation_id );

		// Set variation ID for WooThumbs.
		$_POST['variation_id'] = $variation_id;

		if ( $variation ) {
			$attributes = $variation->get_variation_attributes();

			if ( $attributes && ! empty( $attributes ) ) {
				foreach ( $attributes as $name => $value ) {
					$_REQUEST[ $name ] = $value;
				}
			}
		}
	}

	/**
	 * Ajax: Add to cart
	 */
	public function add_to_cart() {
		global $woocommerce;

		$var_id           = self::get_filtered_input( 'variation_id', 'int' );
		$_GET['quantity'] = ( self::get_filtered_input( 'quantity', 'array' ) ) ? self::get_filtered_input( 'quantity', 'array' ) : 1;
		$quantity         = ( self::get_filtered_input( 'quantity', 'array' ) ) ? self::get_filtered_input( 'quantity', 'array' ) : 1;
		$variation        = array();

		foreach ( $_GET as $key => $value ) {
			if ( substr( $key, 0, 10 ) == 'attribute_' ) {
				$variation[ rawurldecode( $key ) ] = rawurldecode( $value );
			}
		}

		if ( is_array( $quantity ) ) {
			foreach ( $quantity as $product_id => $prod_qty ) {
				if ( $prod_qty > 0 ) {
					$atc = $woocommerce->cart->add_to_cart( $product_id, $prod_qty, $var_id, $variation );
					if ( $atc ) {
						continue;
					} else {
						break;
					}
				}
			}
		} else {
			$atc = $woocommerce->cart->add_to_cart( self::get_filtered_input( 'product_id', 'int' ), $quantity, $var_id, $variation );
		}

		if ( $atc ) {
			$woocommerce->cart->maybe_set_cart_cookies();
			$wc_ajax = new WC_AJAX();
			$wc_ajax->get_refreshed_fragments();
		} else {
			header( 'popup_content-Type: application/json' );

			$sold_indv = get_post_meta( self::get_filtered_input( 'product_id', 'int' ), '_sold_individually', true );

			if ( 'yes' === $sold_indv ) {
				$response            = array( 'result' => 'individual' );
				$response['message'] = __( 'Sorry, that item can only be added once.', 'jckqv' );
			} else {
				$response            = array( 'result' => 'fail' );
				$response['message'] = __( 'Sorry, something went wrong. Please try again.', 'jckqv' );
			}

			$response['get'] = $_GET;

			echo wp_json_encode( $response );
		}

		die;
	}

	/**
	 * Frontend: Register scripts and styles
	 */
	public function register_scripts_and_styles() {
		if ( ! is_admin() ) {
			$rtl = is_rtl();
			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_script( 'jquery-ui-spinner' );

			$this->load_file( 'magnific', '/assets/vendor/magnific/jquery.magnific-popup.js', true, array( 'jquery' ) );
			$this->load_file( 'slick', '/assets/vendor/slick/slick' . $min . '.js', true, array( 'jquery' ) );
			$this->load_file( 'imagesloaded', '/assets/vendor/imagesloaded/imagesloaded.pkgd' . $min . '.js', true, array( 'jquery' ) );

			$this->load_file(
				$this->slug . '-script',
				'/assets/frontend/js/main.min.js',
				true,
				array(
					'jquery',
					'jquery-effects-core',
					'wp-util',
					'magnific',
					'slick',
					'imagesloaded',
				)
			);

			$css = $rtl ? 'main.min-rtl.css' : 'main' . $min . '.css';
			$this->load_file( $this->slug . '-minstyles', '/assets/frontend/css/' . $css );

			$imgsizes              = array();
			$imgsizes['catalog']   = get_option( 'shop_catalog_image_size' );
			$imgsizes['single']    = get_option( 'shop_single_image_size' );
			$imgsizes['thumbnail'] = get_option( 'shop_thumbnail_image_size' );

			$script_vars = array(
				'ajaxurl'  => admin_url( 'admin-ajax.php', 'relative' ),
				'nonce'    => wp_create_nonce( 'jckqv' ),
				'settings' => $this->settings,
				'imgsizes' => $imgsizes,
				'url'      => get_bloginfo( 'url' ),
				'text'     => array(
					'added'   => __( 'Added!', 'jckqv' ),
					'adding'  => __( 'Adding to Cart...', 'jckqv' ),
					'loading' => __( 'Loading...', 'jckqv' ),
				),
				'rtl'      => $rtl,
			);

			wp_localize_script( $this->slug . '-script', 'jckqv_vars', $script_vars );

			add_action( 'wp_head', array( $this, 'dynamic_css' ) );
		}
	}

	/**
	 * Frontend: Add dynamic CSS
	 *
	 * This is CSS that uses values from the settings
	 */
	public function dynamic_css() {
		include $this->templates->locate_template( 'button-styles.php' );
	}

	/**
	 * Helper: Register and enqueue scripts and styles
	 *
	 * @param string $name      The ID to register with WordPress.
	 * @param string $file_path Path to the file.
	 * @param bool   $is_script Whether this a script enqueue.
	 * @param array  $deps      Array of script dependencies.
	 * @param bool   $in_footer Whether to enqueue in the footer.
	 */
	private function load_file( $name, $file_path, $is_script = false, $deps = array( 'jquery' ), $in_footer = true ) {
		$url  = plugins_url( $file_path, __FILE__ );
		$file = untrailingslashit( plugin_dir_path( __FILE__ ) ) . $file_path;

		if ( file_exists( $file ) ) {
			if ( $is_script ) {
				wp_register_script( $name, $url, $deps, $this->version, $in_footer ); // depends on jQuery.
				wp_enqueue_script( $name );
			} else {
				wp_register_style( $name, $url, array(), $this->version );
				wp_enqueue_style( $name );
			}
		}
	}

	/**
	 * Helper: Get WooCommerce Version number
	 *
	 * @return string
	 */
	public function get_woo_version_number() {
		// If get_plugins() isn't available, require it.
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Create the plugins folder and file variables.
		$plugin_folder = get_plugins( '/woocommerce' );
		$plugin_file   = 'woocommerce.php';

		// If the plugin version number is set, return it.
		if ( isset( $plugin_folder[ $plugin_file ]['Version'] ) ) {
			return $plugin_folder[ $plugin_file ]['Version'];
		} else {
			// Otherwise return null.
			return null;
		}
	}

	/**
	 * Helper: Get Product images
	 *
	 * @param WC_Product $product WC_Product object.
	 *
	 * @return array
	 */
	public function get_product_images( $product ) {
		$prod_images = array();
		$product_id  = $product->get_id();

		if ( has_post_thumbnail( $product_id ) ) {
			$img_id        = get_post_thumbnail_id( $product_id );
			$img_src       = wp_get_attachment_image_src( $img_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
			$img_thumb_src = wp_get_attachment_image_src( $img_id, 'thumbnail' );

			$prod_images[ $img_id ]['slideId'][]     = '-0-';
			$prod_images[ $img_id ]['img_src']       = $img_src[0];
			$prod_images[ $img_id ]['img_width']     = $img_src[1];
			$prod_images[ $img_id ]['img_height']    = $img_src[2];
			$prod_images[ $img_id ]['img_thumb_src'] = $img_thumb_src[0];
		} else {
			$prod_images[0]['slideId'][]     = '-0-';
			$prod_images[0]['img_src']       = woocommerce_placeholder_img_src();
			$prod_images[0]['img_width']     = 800;
			$prod_images[0]['img_height']    = 800;
			$prod_images[0]['img_thumb_src'] = woocommerce_placeholder_img_src();
		}

		// Additional Images.

		$attachment_ids   = Iconic_WQV_Product::get_gallery_image_ids( $product );
		$attachment_count = count( $attachment_ids );

		if ( ! empty( $attachment_ids ) ) {
			foreach ( $attachment_ids as $attachment_id ) {
				$img_src       = wp_get_attachment_image_src( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
				$img_thumb_src = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );

				$prod_images[ $attachment_id ]['slideId'][]     = '-0-';
				$prod_images[ $attachment_id ]['img_src']       = $img_src[0];
				$prod_images[ $attachment_id ]['img_width']     = $img_src[1];
				$prod_images[ $attachment_id ]['img_height']    = $img_src[2];
				$prod_images[ $attachment_id ]['img_thumb_src'] = $img_thumb_src[0];
			}
		}

		// !If is Varibale product

		if ( $product->is_type( 'variable' ) ) {
			$product_variations = $product->get_available_variations();

			if ( ! empty( $product_variations ) ) {
				foreach ( $product_variations as $product_variation ) {
					if ( has_post_thumbnail( $product_variation['variation_id'] ) ) {
						$variation_thumbnail_id = get_post_thumbnail_id( $product_variation['variation_id'] );
						$img_src                = wp_get_attachment_image_src( $variation_thumbnail_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
						$img_thumb_src          = wp_get_attachment_image_src( $variation_thumbnail_id, 'thumbnail' );

						$prod_images[ $variation_thumbnail_id ]['slideId'][]     = '-' . $product_variation['variation_id'] . '-';
						$prod_images[ $variation_thumbnail_id ]['img_src']       = $img_src[0];
						$prod_images[ $variation_thumbnail_id ]['img_width']     = $img_src[1];
						$prod_images[ $variation_thumbnail_id ]['img_height']    = $img_src[2];
						$prod_images[ $variation_thumbnail_id ]['img_thumb_src'] = $img_thumb_src[0];
					}
				}
			}
		}

		return $prod_images;
	}

	/**
	 * Helper: Get Add to Cart Filename
	 *
	 * @return string
	 */
	public function get_add_to_cart_filename() {
		if ( version_compare( $this->woo_version, '2.1', '<' ) ) {
			return 'add-to-cart-old-2-1-down.php';
		} elseif ( version_compare( $this->woo_version, '2.5.0', '<' ) ) {
			return 'add-to-cart-old-2-5-down.php';
		}

		return 'add-to-cart.php';
	}

	/**
	 * Declare compatiblity with HPOS/Custom order tables feature of WooCommerce.
	 */
	public function declare_hpos_compatibility() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
}

$jckqv = new JCKQV();
