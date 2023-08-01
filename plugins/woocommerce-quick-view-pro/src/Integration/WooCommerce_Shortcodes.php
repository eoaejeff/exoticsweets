<?php
namespace Barn2\Plugin\WC_Quick_View_Pro\Integration;

use Barn2\Plugin\WC_Quick_View_Pro\Frontend_Scripts,
	Barn2\WQV_Lib\Registerable,
	Barn2\WQV_Lib\Conditional,
	Barn2\WQV_Lib\Util as Lib_Util;

/**
 *
 * @package   Barn2\woocommerce-quick-view-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WooCommerce_Shortcodes implements Registerable, Conditional {

	/**
	 * @var Frontend_Scripts $scripts A front-end scripts controller.
	 */
	private $scripts;

	/**
	 * @var boolean $scripts_loaded
	 */
	private $scripts_loaded = false;

	public function __construct( Frontend_Scripts $scripts ) {
		$this->scripts = $scripts;
	}

	public function is_required() {
		return Lib_Util::is_front_end();
	}

	public function register() {
		add_filter( 'do_shortcode_tag', [ $this, 'do_shortcode_tag' ], 10, 4 );
	}

	public function do_shortcode_tag( $output, $tag, $attr, $m ) {
		if ( ! $this->scripts_loaded && in_array( $tag, $this->get_supported_shortcodes(), true ) ) {
			// Enable the scripts for this page which contains the shortcode.
			add_filter( 'wc_quick_view_pro_scripts_enabled_on_page', '__return_true', 5 );

			$this->scripts->load_scripts();
			$this->scripts_loaded = true;
		}

		return $output;
	}

	private function get_supported_shortcodes() {
		return apply_filters(
			'wc_quick_view_pro_supported_woocommerce_shortcodes',
			[
				'product',
				'product_category',
				'products',
				'recent_products',
				'sale_products',
				'best_selling_products',
				'top_rated_products',
				'featured_products',
				'product_attribute',
				'related_products',
			]
		);
	}

}
