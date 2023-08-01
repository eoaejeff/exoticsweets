<?php
/**
 * Template: Price.
 *
 * @package iconic-quickview
 */

global $post, $product, $woocommerce;

$kses_args = array(
	'span'  => array(
		'class' => array(),
	),
	'small' => array(
		'class' => array(),
	),
	'bdi'   => array(),
	'del'   => array(),
	'ins'   => array(),
);

do_action( $this->slug . '-before-price' );
?>

<?php echo '<p class="price">' . wp_kses( $product->get_price_html(), $kses_args ) . '</p>'; ?>

<?php
do_action( $this->slug . '-after-price' );
