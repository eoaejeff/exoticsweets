<?php
namespace Barn2\Plugin\WC_Quick_View_Pro;

use Barn2\Plugin\WC_Quick_View_Pro\Util\Settings,
	Barn2\Plugin\WC_Quick_View_Pro\Util\Util,
	Barn2\WQV_Lib\Service,
	Barn2\WQV_Lib\Registerable,
	Barn2\WQV_Lib\Conditional,
	Barn2\WQV_Lib\Util as Lib_Util;

/**
 * Responsible for configuring which elements are displayed within the quick view.
 *
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Quick_View_Content implements Service, Registerable, Conditional {

	private $settings;

	public function __construct() {
		$this->settings = Settings::get_plugin_settings();
	}

	public function is_required() {
		return Lib_Util::is_front_end();
	}

	public function register() {
		add_action( 'wc_quick_view_pro_before_quick_view', [ $this, 'configure' ] );
		add_filter( 'wc_quick_view_pro_modal_container_class', [ $this, 'add_product_classes' ], 10, 2 );
		add_filter( 'wc_quick_view_pro_show_product_details', [ Util::class, 'are_product_details_displayed' ] );
		add_filter( 'wc_quick_view_pro_can_view_quick_view_content', [ $this, 'can_view_content' ], 10, 2 );
		add_action( 'wc_quick_view_pro_quick_view_content_hidden', [ $this, 'show_hidden_content' ] );
	}

	public function configure() {

		global $product;

		if ( Util::is_product_image_displayed() ) {
			// Display sales flash with product image if no details displayed.
			if ( ! Util::are_product_details_displayed() && apply_filters( 'wc_quick_view_pro_show_sale_flash', true ) ) {
				add_action( 'wc_quick_view_pro_quick_view_before_product_details', 'woocommerce_show_product_sale_flash', 10 );
			}

			add_action( 'wc_quick_view_pro_quick_view_before_product_details', [ $this, 'show_product_image' ], 20 );

			if ( $this->settings['enable_gallery'] ) {
				add_action( 'wc_quick_view_pro_product_thumbnails', [ $this, 'show_product_thumbnails' ] );
				// add_filter( 'woocommerce_single_product_carousel_options', array( $this, 'set_flexslider_options' ) );
			}
		}

		if ( Util::are_product_details_displayed() ) {
			// Display sales flash inside product details.
			if ( apply_filters( 'wc_quick_view_pro_show_sale_flash', true ) ) {
				add_action( 'wc_quick_view_pro_quick_view_product_details', 'woocommerce_show_product_sale_flash', 3 );
			}

			if ( apply_filters( 'wc_quick_view_pro_show_product_title', true ) ) {
				add_action( 'wc_quick_view_pro_quick_view_product_details', 'woocommerce_template_single_title', 5 );
			}

			if ( $this->settings['show_reviews'] ) {
				add_action( 'wc_quick_view_pro_quick_view_product_details', 'woocommerce_template_single_rating', 10 );
			}

			if ( $this->settings['show_price'] ) {
				add_action( 'wc_quick_view_pro_quick_view_product_details', 'woocommerce_template_single_price', 10 );
			}

			if ( $this->settings['show_description'] ) {
				if ( is_a( $product, 'WC_Product_Variation' ) ) {
					add_action( 'wc_quick_view_pro_quick_view_product_details', [ $this, 'woocommerce_product_single_description' ], 20 );
				} else {
					add_action( 'wc_quick_view_pro_quick_view_product_details', 'woocommerce_template_single_excerpt', 20 );
				}
			}

			if ( $this->settings['show_cart'] ) {
				if ( is_a( $product, 'WC_Product_Variation' ) ) {
					add_action( 'woocommerce_variation_add_to_cart', [ $this, 'add_variation_to_cart_template' ] );
				}

				add_action( 'wc_quick_view_pro_quick_view_product_details', 'woocommerce_template_single_add_to_cart', 30 );
			}

			if ( $this->settings['show_meta'] ) {
				add_action( 'wc_quick_view_pro_quick_view_product_details', 'woocommerce_template_single_meta', 40 );
			}

			if ( apply_filters( 'wc_quick_view_pro_show_social_sharing', true ) ) {
				add_action( 'wc_quick_view_pro_quick_view_product_details', 'woocommerce_template_single_sharing', 50 );
			}

			if ( $this->settings['show_details_link'] ) {
				add_action( 'wc_quick_view_pro_quick_view_product_details', [ $this, 'show_details_link' ], 60 );
			}
		}

	}

	public function can_view_content( $can_view, $product ) {
		if ( $can_view && post_password_required( $product->get_id() ) ) {
			return false;
		}
		return $can_view;
	}

	public function add_product_classes( $classes, $product ) {
		$image_displayed   = Util::is_product_image_displayed();
		$details_displayed = Util::are_product_details_displayed();

		if ( $image_displayed ) {
			$classes[] = 'with-product-image';

			if ( ! $details_displayed ) {
				$classes[] = 'product-image-only';
			}
		}

		if ( $details_displayed ) {
			$classes[] = 'with-product-details';

			if ( ! $image_displayed ) {
				$classes[] = 'product-details-only';
			}
		}

		if ( $product->is_type( 'external' ) ) {
			$classes[] = 'external-product';
		}

		return $classes;
	}

	public function show_hidden_content( $product ) {
		// Output the hidden content template.
		Util::load_template( 'product-hidden-content.php', [ 'product' => $product ] );
	}

	public function show_product_image( $product ) {
		// Output the product image template.
		Util::load_template( 'product-image.php', [ 'product' => $product ] );
	}

	public function show_product_thumbnails( $product ) {
		// Output the product thumbnails template.
		Util::load_template( 'product-thumbnails.php', [ 'product' => $product ] );
	}

	public function show_details_link( $product ) {
		$link_text = __( 'View product details', 'woocommerce-quick-view-pro' );

		if ( apply_filters( 'wc_quick_view_pro_product_details_link_show_arrow', true ) ) {
			$link_text .= ' &rarr;';
		}

		printf(
			'<p class="view-product-details"><a href="%s" class="view-product-details-link">%s</a></p>',
			esc_url( apply_filters( 'wc_quick_view_pro_product_details_link', $product->get_permalink(), $product ) ),
			apply_filters( 'wc_quick_view_pro_product_details_link_text', $link_text, $product )
		);
	}

	public function add_variation_to_cart_template() {
		global $product;

		Util::load_template( 'quick-view-variation-add-to-cart.php', [ 'product' => $product ] );
	}

	public function woocommerce_product_single_description() {
		global $product;

		echo $product->get_description();
	}

}
