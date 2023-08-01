<?php
/**
 * Product.
 *
 * @package iconic-quickview
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Iconic_WQV_Product class
 *
 * @class Iconic_WQV_Product
 */
class Iconic_WQV_Product {
	/**
	 * Get gallery image IDs
	 *
	 * @param WC_Product $product Product object.
	 *
	 * @return arr
	 */
	public static function get_gallery_image_ids( $product ) {
		return method_exists( $product, 'get_gallery_image_ids' ) ? $product->get_gallery_image_ids() : $product->get_gallery_attachment_ids();
	}

	/**
	 * Get categories
	 *
	 * @param WC_Product $product Product object.
	 *
	 * @return str
	 */
	public static function get_categories( $product ) {
		$product_id = $product->get_id();
		$sep        = ', ';
		$before     = '<span class="posted_in">' . _n( 'Category: ', 'Categories: ', count( get_the_terms( $product_id, 'product_cat' ) ) );
		$after      = '</span>';

		return function_exists( 'wc_get_product_category_list' ) ? wc_get_product_category_list( $product_id, $sep, $before, $after ) : $product->get_categories( $sep, $before, $after );
	}

	/**
	 * Get tags
	 *
	 * @param WC_Product $product Product object.
	 *
	 * @return str
	 */
	public static function get_tags( $product ) {
		$product_id = $product->get_id();
		$sep        = ', ';
		$before     = '<span class="tagged_as">' . _n( 'Tag: ', 'Tags: ', count( (array) get_the_terms( $product_id, 'product_tag' ) ) );
		$after      = '</span>';

		return function_exists( 'wc_get_product_tag_list' ) ? wc_get_product_tag_list( $product_id, $sep, $before, $after ) : $product->get_tags( $sep, $before, $after );
	}
}
