<?php
/**
 * Template: Sale Flash.
 *
 * @package iconic-quikcview
 */

global $post, $product, $woocommerce;
?>

<?php
if ( $product->is_on_sale() ) :
	$kses_args = array(
		'span' => array(
			'class' => array(),
		),
	);

	$flash = apply_filters(
		'woocommerce_sale_flash',
		'<span class="onsale">' . esc_html__( 'Sale!', 'woocommerce' ) . '</span>',
		$post,
		$product
	);

	echo wp_kses( $flash, $kses_args );
endif;
