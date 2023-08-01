<?php
/**
 * Template: Description.
 *
 * @package iconic-quickview
 */

global $product, $woocommerce;

if ( empty( $product ) ) {
	return;
}

$_post = get_post( $product->get_id() );

/**
 * QV Modal: Before description.
 *
 * @since 1.0.0
 */
do_action( $this->slug . '-before-description' );
?>

<div id="<?php echo esc_attr( $this->slug ); ?>_desc">
	<?php
	if ( 'full' === $this->settings['popup_content_showdesc'] ) {
		/**
		 * Modal content.
		 *
		 * @since 1.0.0
		 */
		echo wp_kses_post( apply_filters( 'the_content', $_post->post_content ) );
	} else {
		/**
		 * Modal short description.
		 *
		 * @since 1.0.0
		 */
		echo wp_kses_post( apply_filters( 'woocommerce_short_description', $_post->post_excerpt ) );
	}
	?>
</div>

<?php
/**
 * QV Modal: After description.
 *
 * @since 1.0.0
 */
do_action( $this->slug . '-after-description' );
