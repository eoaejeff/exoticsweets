<?php
/**
 * Template: Title.
 *
 * @package iconic-quickview
 */

do_action( $this->slug . '-before-title' ); ?>

<?php
if ( isset( $_REQUEST['product_id'] ) ) {
	echo '<h1>' . esc_html( get_the_title( sanitize_text_field( wp_unslash( $_REQUEST['product_id'] ) ) ) ) . '</h1>';
}
?>

<?php
do_action( $this->slug . '-after-title' );
