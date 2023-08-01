<?php
/**
 * Template: Meta.
 *
 * @package iconic-quickview
 */

global $post, $product, $woocommerce;

do_action( $this->slug . '-before-meta' );

$product_sku = $product->get_sku();

$kses_args = array(
	'span' => array(
		'class' => array(),
	),
	'a'    => array(
		'href' => array(),
		'rel'  => array(),
	),
);
?>

<div class="product_meta">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php if ( wc_product_sku_enabled() && ( $product_sku || $product->is_type( 'variable' ) ) ) : ?>

		<span class="sku_wrapper"><?php esc_html_e( 'SKU:', 'woocommerce' ); ?> <span class="sku" itemprop="sku">
			<?php echo esc_html( ( $product_sku ) ? $product_sku : __( 'n/a', 'woocommerce' ) ); ?></span>.
		</span>

	<?php endif; ?>

	<?php echo wp_kses( Iconic_WQV_Product::get_categories( $product ), $kses_args ); ?>
	<?php echo wp_kses( Iconic_WQV_Product::get_tags( $product ), $kses_args ); ?>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>

<?php do_action( $this->slug . '-after-meta' ); ?>
