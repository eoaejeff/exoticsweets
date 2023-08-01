<?php
namespace Barn2\Plugin\WC_Quick_View_Pro\Integration;

use Barn2\Plugin\WC_Quick_View_Pro\Util\Settings,
	Barn2\WQV_Lib\Registerable,
	Barn2\WQV_Lib\Conditional,
	Barn2\WQV_Lib\Util as Lib_Util;

/**
 * Handles integration with 3rd party themes.
 *
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Theme_Integration implements Registerable, Conditional {

	private $theme;
	private $settings;

	public function __construct() {
		$this->theme    = strtolower( get_template() );
		$this->settings = Settings::get_plugin_settings();
	}

	public function is_required() {
		return Lib_Util::is_front_end();
	}

	public function register() {
		$this->register_theme_hooks();

		add_action( 'wc_quick_view_pro_before_quick_view', [ $this, 'register_quick_view_content_hooks' ], 20 ); // after main plugin hooks are registered
		add_filter( 'body_class', [ $this, 'set_body_class' ] );
		add_filter( 'wc_quick_view_pro_modal_container_class', [ $this, 'set_quick_view_container_class' ] );
	}

	public function register_theme_hooks() {
		switch ( $this->theme ) {
			case 'astra':
				add_filter(
					'wc_quick_view_pro_product_link_selector',
					function( $selector ) {
						return 'a.ast-loop-product__link, a.woocommerce-LoopProduct-link, a.woocommerce-loop-product__link';
					}
				);
				break;

			case 'avada':
				add_filter(
					'wc_quick_view_pro_product_link_selector',
					function( $selector ) {
						return '.product a.product-images, .product .product-title a, .product a.fusion-link-wrapper, .product a.product_type_variable, .product a.show_details_button';
					}
				);

				add_filter(
					'wc_quick_view_pro_button_class',
					function( $class ) {
						// Don't add the Fusion builder classes inside Woo Product Blocks.
						if ( false !== strpos( $class, 'wp-block-button__link' ) ) {
							return $class;
						}
						return $class . ' fusion-button fusion-button-default fusion-button-small';
					}
				);

				// Wrap button text to match other Avada buttons.
				add_filter(
					'wc_quick_view_pro_button_text',
					function( $text ) {
						return '<span class="fusion-button-text">' . $text . '</span>';
					}
				);

				break;
			case 'betheme':
				add_filter(
					'wc_quick_view_pro_product_link_selector',
					function() {
						return '.product .desc a, .product .image_wrapper > a';
					}
				);

				break;
			case 'bridge':
				add_filter(
					'wc_quick_view_pro_product_link_selector',
					function( $selector ) {
						return $selector . ', .product > a.product-category';
					}
				);

				break;
			case 'divi':
				add_filter(
					'wc_quick_view_pro_quick_view_shortcode_html',
					function( $html ) {
						return '<span class="woocommerce">' . $html . '</span>';
					}
				);

				break;
			case 'enfold':
				add_filter(
					'wc_quick_view_pro_product_link_selector',
					function( $selector ) {
						return $selector . ', .product .avia_cart_buttons a';
					}
				);

				// Add inline script for Enfold to trigger 'updated_cart_totals' on QV load.
				// This will cause Enfold to add the +/- quantity buttons to the cart form.
				add_action(
					'wp_print_footer_scripts',
					function() {
						?>
					<script>
						if ( typeof jQuery !== 'undefined' ) {
							jQuery( document.body ).on( 'quick_view_pro:load', function( e, $modal ) {
								$modal.trigger( 'updated_cart_totals' );
							} );
						}
					</script>
						<?php
					},
					50
				);

				add_filter(
					'wc_quick_view_pro_shop_loop_hover_button_priority',
					function() {
						return 11;
					}
				);

				// add_filter( 'wc_quick_view_pro_shop_loop_use_thumbnail_wrapper', '__return_false' );

				break;
			case 'flatsome':
				add_filter(
					'wc_quick_view_pro_product_link_selector',
					function() {
						return '.product-small .box-image a, .product-small .box-text a';
					}
				);

				add_filter(
					'wc_quick_view_pro_enable_product_link',
					function( $enabled ) {
						// Disable product link option if using Flatsome's in-built quick view.
						$flatsome_quick_view_enabled = ! ( (bool) get_theme_mod( 'disable_quick_view', false ) );

						if ( $flatsome_quick_view_enabled ) {
							return false;
						}

						return $enabled;
					}
				);

				add_action(
					'woocommerce_single_product_flexslider_enabled',
					function ( $enabled ) {
						if ( is_product() ) {
							return false;
						}
						return $enabled;
					},
					99
				);

				add_action(
					'wc_quick_view_pro_quick_view_product_tabs',
					function () {
						echo '<div class="product-footer">';
					},
					1
				);
				add_action(
					'wc_quick_view_pro_quick_view_product_tabs',
					function () {
						echo '</div>';
					},
					99
				);
				add_filter(
					'wc_quick_view_pro_tab_list_classes',
					function ( $classes ) {
						return $classes . '  nav nav-uppercase nav-line nav-left';
					}
				);

				add_filter(
					'wc_quick_view_pro_shop_loop_hover_button_hook',
					function ( $enabled ) {
						return 'flatsome_woocommerce_shop_loop_images';
					}
				);

				add_filter( 'wc_quick_view_pro_shop_loop_use_thumbnail_wrapper', '__return_false' );

				break;
			case 'goya':
				// Add inline script for Goya to close the popup after adding to the cart
				add_action(
					'wp_print_footer_scripts',
					function() {
						?>
					<script>
						document.addEventListener( 'DOMContentLoaded', function() {
							if ( typeof jQuery !== 'undefined' ) {
								jQuery( document.body ).on( 'added_to_cart', function() {
									if ( jQuery.modal ) {
										jQuery.modal.close();
									}
								} );
							}
						} );
					</script>
					<style>
						html.qvp-modal-is-open {
							overflow: initial !important;
						}
					</style>
						<?php
					},
					50
				);

				// add_filter( 'wc_quick_view_pro_shop_loop_use_thumbnail_wrapper', '__return_false' );

				break;
			case 'jupiterx':
				add_filter(
					'wc_quick_view_pro_shop_loop_hover_button_hook',
					function() {
						return 'jupiterx_wc_loop_product_image_prepend_markup';
					}
				);

				add_filter( 'wc_quick_view_pro_shop_loop_use_thumbnail_wrapper', '__return_false' );

			case 'jupiter':
				add_filter(
					'wc_quick_view_pro_shop_loop_button_hook',
					function() {
						return 'woocommerce_after_shop_loop_item_title';
					}
				);

				add_filter(
					'wc_quick_view_pro_product_link_selector',
					function() {
						return '.product .product-title a, .product a.product-link';
					}
				);

				break;

			case 'storefront':
				// Temp: Workaround for bug in Addons 4.7.0 with Storefront theme.
				add_filter( 'storefront_handheld_footer_bar_links', [ $this, 'storefront_remove_handheld_footer_bar_cart_link' ] );

				break;

			case 'total':
				add_filter(
					'wc_quick_view_pro_shop_loop_button_hook_priority',
					function() {
						return 9;
					}
				);

				break;

			case 'woodmart':
				add_filter(
					'wc_quick_view_pro_product_link_selector',
					function() {
						return '.product .product-information .wd-entities-title a, .product a.product-image-link';
					}
				);

				add_action(
					'wc_quick_view_pro_before_quick_view_product',
					function() {
						if ( function_exists( 'WOODMART_Registry' ) ) {
							WOODMART_Registry()->pagecssfiles->enqueue_inline_style( 'woo-mod-quantity' );
						}
					}
				);

				break;
			case 'x':
				add_filter(
					'wc_quick_view_pro_product_link_selector',
					function() {
						return '.product .entry-featured a, .product .entry-header h3 a';
					}
				);

				add_filter( 'wc_quick_view_pro_hover_button_allowed', '__return_false' );

				break;
			case 'xstore':
				add_filter(
					'wc_quick_view_pro_product_link_selector',
					function() {
						return '.content-product .product-title a';
					}
				);

				add_filter( 'wc_quick_view_pro_hover_button_allowed', '__return_false' );

				break;
		}
	}

	public function register_quick_view_content_hooks() {
		switch ( $this->theme ) {
			case 'avada':
				global $avada_woocommerce;

				if ( $avada_woocommerce instanceof \Avada_Woocommerce ) {
					if ( apply_filters( 'wc_quick_view_pro_show_product_title', true ) ) {
						remove_action( 'wc_quick_view_pro_quick_view_product_details', 'woocommerce_template_single_title', 5 );
						add_action( 'wc_quick_view_pro_quick_view_product_details', [ $avada_woocommerce, 'template_single_title' ], 5 );
					}

					add_action( 'wc_quick_view_pro_quick_view_product_details', [ $avada_woocommerce, 'add_product_border' ], 19 );
					add_action( 'wc_quick_view_pro_quick_view_product_details', [ $avada_woocommerce, 'stock_html' ], 10 );
					add_action( 'wc_quick_view_pro_quick_view_product_details', [ $avada_woocommerce, 'single_product_summary_open' ], 1 );
					add_action( 'wc_quick_view_pro_quick_view_product_details', [ $avada_woocommerce, 'single_product_summary_close' ], 100 );
				}

				if ( $this->settings['show_reviews'] ) {
					remove_action( 'wc_quick_view_pro_quick_view_product_details', 'woocommerce_template_single_rating', 10 );
					add_action( 'wc_quick_view_pro_quick_view_product_details', 'woocommerce_template_single_rating', 11 );
				}
				break;
		}
	}

	public function set_body_class( $classes ) {
		$classes[] = $this->theme;
		return $classes;
	}

	public function set_quick_view_container_class( $classes ) {
		switch ( $this->theme ) {
			case 'enfold':
				$classes[] = 'main_color';
				break;
		}

		return $classes;
	}

	/**
	 * Remove 'cart' from mobile menu as it conflicts with Product Addons 4.7.0.
	 *
	 * @param array $links The links.
	 * @return array The links.
	 */
	public function storefront_remove_handheld_footer_bar_cart_link( $links ) {
		if ( isset( $links['cart'] ) ) {
			unset( $links['cart'] );
		}
		return $links;
	}
}
