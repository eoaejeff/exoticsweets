<?php
namespace Barn2\Plugin\WC_Quick_View_Pro;

use Barn2\Plugin\WC_Quick_View_Pro\Util\Settings,
	Barn2\WQV_Lib\Service,
	Barn2\WQV_Lib\Registerable,
	Barn2\WQV_Lib\Conditional,
	Barn2\WQV_Lib\Util as Lib_Util,
	WC_Product;

/**
 * Handles the display of the Quick View button in the shop.
 *
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Button_Display implements Service, Registerable, Conditional {

	private $scripts;
	private $settings;

	public function __construct( Frontend_Scripts $scripts ) {
		$this->scripts  = $scripts;
		$this->settings = Settings::get_plugin_settings();
	}

	public function is_required() {
		return Lib_Util::is_front_end();
	}

	public function register() {
		add_action( 'template_redirect', [ $this, 'add_button_hooks' ] );
		add_action( 'template_redirect', [ $this, 'add_hover_button_hooks' ] );
		add_filter( 'wc_quick_view_pro_is_category_allowed', [ $this, 'is_category_allowed' ], 10, 2 );
		add_filter( 'woocommerce_post_class', [ $this, 'add_product_qvp_class' ], 10, 2 );
	}

	public function add_product_qvp_class( $classes, $product ) {

		if ( ! apply_filters( 'wc_quick_view_pro_is_category_allowed', true, $product ) ) {
			$classes[] = 'qvp-disabled';
		}

		return $classes;

	}

	public function is_category_allowed( $setting, $the_product = null ) {

		global $product;

		if ( empty( $the_product ) ) {
			$the_product = $product;
		}
		if ( ! ( $the_product instanceof WC_Product ) ) {
			return $setting;
		}

		$product_cats    = $the_product->get_category_ids();
		$processed_trees = [];

		// this loop is admittedly a bit weird, the goal here is to build a complete tree of the categories
		// a product belongs to. sometimes if a product was assigned to both a category and that category's parent,
		// the parent category would override the setting on a child category. this loop ensures that the child
		// category always gets setting priority.
		foreach ( $product_cats as $cat_id ) {
			if ( in_array( $cat_id, $processed_trees ) ) {
				continue;
			}
			$this_id     = $cat_id;
			$this_option = '';
			$override_ok = false;
			$tree        = [];
			while ( $cat = get_term( $this_id, 'product_cat' ) ) {
				$option = get_term_meta( $cat->term_id, '_qvp_enabled', true ) ?: '';
				$tree[] = [
					'term'  => $cat,
					'value' => $option
				];
				if ( ! $cat->parent ) {
					break;
				}
				$this_id = $cat->parent;
			}
			if ( $this_id && ( ! isset( $processed_trees[ $this_id ] ) || count( $tree ) > count( $processed_trees[ $this_id ] ) ) ) {
				$processed_trees[ $this_id ] = $tree;
			}
		}

		foreach ( $processed_trees as $tree ) {
			foreach ( $tree as $cat ) {
				if ( $cat['value'] === 'global' ) {
					return true;
				}
				if ( $cat['value'] === 'disabled' ) {
					return false;
				}
			}
		}

		return $setting;

	}

	public function add_button_hooks() {
		if ( ! $this->quick_view_button_enabled() ) {
			return;
		}

		/**
		 * Hook: wc_quick_view_pro_shop_loop_button_hook - The hook to display the Quick View button.
		 * Default: woocommerce_after_shop_loop_item
		 */
		$button_hook = apply_filters( 'wc_quick_view_pro_shop_loop_button_hook', 'woocommerce_after_shop_loop_item' );

		/**
		 * Hook: wc_quick_view_pro_shop_loop_button_hook_priority - The priority for the Quick View button hook.
		 * Default: 5
		 */
		$button_hook_priority = apply_filters( 'wc_quick_view_pro_shop_loop_button_hook_priority', 5 );

		// Register the hook to display the Quick View button in the shop.
		add_action( $button_hook, [ $this, 'show_button' ], $button_hook_priority );

	}

	public function add_hover_button_hooks() {

		if ( ! $this->quick_view_hover_button_enabled() ) {
			return;
		}

		$thumbnail_hook     = apply_filters( 'wc_quick_view_pro_shop_loop_hover_button_hook', 'woocommerce_before_shop_loop_item_title' );
		$thumbnail_priority = apply_filters( 'wc_quick_view_pro_shop_loop_hover_button_priority', 11 );

		$thumbnail_wrapper_open_priority  = apply_filters( 'wc_quick_view_pro_shop_loop_thumbnail_wrapper_open_priority', 9 );
		$thumbnail_wrapper_close_priority = apply_filters( 'wc_quick_view_pro_shop_loop_thumbnail_wrapper_close_priority', 12 );

		add_action( $thumbnail_hook, [ $this, 'show_hover_button' ], $thumbnail_priority );

		if ( apply_filters( 'wc_quick_view_pro_shop_loop_use_thumbnail_wrapper', true ) ) {
			add_action( $thumbnail_hook, [ $this, 'add_thumbnail_wrapper_open' ], $thumbnail_wrapper_open_priority );
			add_action( $thumbnail_hook, [ $this, 'add_thumbnail_wrapper_close' ], $thumbnail_wrapper_close_priority );
		}

	}

	public function add_thumbnail_wrapper_open() {

		$wrapper_html = '<span class="woocommerce-loop-product__thumbnail-wrapper">';
		$wrapper_html = apply_filters( 'wc_quick_view_pro_shop_loop_thubmanil_wrapper_open', $wrapper_html );

		echo $wrapper_html;

	}

	public function add_thumbnail_wrapper_close() {

		$wrapper_html = '</span>';
		$wrapper_html = apply_filters( 'wc_quick_view_pro_shop_loop_thubmanil_wrapper_close', $wrapper_html );

		echo $wrapper_html;

	}

	public function quick_view_hover_button_enabled() {
		return apply_filters( 'wc_quick_view_pro_hover_button_allowed', $this->settings['enable_hover_button'] );
	}

	public function quick_view_button_enabled() {
		return apply_filters( 'wc_quick_view_pro_show_button_in_shop', $this->settings['enable_button'] );
	}

	public function show_button( $product = null ) {
		echo $this->get_button( $product );
	}

	public function show_hover_button( $product = null ) {
		echo $this->get_button( $product, true );
	}

	public function get_button( $the_product = null, $for_hover = false ) {
		global $product;

		if ( ! $the_product || ! ( $the_product instanceof WC_Product ) ) {
			$the_product = $product;
		}

		if ( ! apply_filters( 'wc_quick_view_pro_is_category_allowed', true, $the_product ) ) {
			return '';
		}

		if ( apply_filters( 'wc_quick_view_pro_show_button_for_product', true, $the_product ) ) {
			$this->scripts->load_scripts();
			return $this->get_button_html( $the_product, $for_hover );
		}

		return '';
	}

	public function get_button_html( $product, $for_hover = false ) {
		$button_class = [ 'wc-quick-view-button' ];

		$tag = 'a';

		if ( $this->settings['show_button_icon'] ) {
			$button_class[] = 'with-icon';
		}
		if ( empty( $this->settings['button_text'] ) ) {
			$button_class[] = 'no-text';
		}
		if ( $for_hover ) {
			$button_class[] = 'qvp-show-on-hover';
			$tag            = 'span';
		}

		if ( apply_filters( 'wc_quick_view_pro_use_default_button_classes', true ) ) {
			$button_class[] = 'button';
			$button_class[] = 'btn';
			$button_class[] = 'alt';
		}

		$button_class            = apply_filters( 'wc_quick_view_pro_button_class_array', $button_class, $product );
		$quick_view_button_class = apply_filters( 'wc_quick_view_pro_button_class', implode( ' ', $button_class ), $product );
		$quick_view_text         = apply_filters( 'wc_quick_view_pro_button_text', $this->get_button_text(), $product );

		$button = sprintf(
			'<%5$s href="%4$s" data-product_id="%1$s" data-action="quick-view" class="%2$s">%3$s</%5$s>',
			esc_attr( $product->get_id() ),
			esc_attr( $quick_view_button_class ),
			$quick_view_text,
			esc_url( $product->get_permalink() ),
			esc_html( $tag )
		);

		return apply_filters( 'wc_quick_view_pro_quick_view_button', $button, $product );
	}

	private function get_button_text() {
		$button_text = __( 'Quick View', 'woocommerce-quick-view-pro' );

		if ( isset( $this->settings['button_text'] ) ) {
			$button_text = $this->settings['button_text'];
		}

		return $button_text;
	}

}
