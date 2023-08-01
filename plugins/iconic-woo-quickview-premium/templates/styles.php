<?php
/**
 * Template: Styles.
 *
 * @package iconic-quickview
 */

?>

<style>
	/* Add to Cart */
	#jckqv .quantity {
		display: <?php echo ( 1 === $this->settings['popup_content_showqty'] ) ? 'inline' : 'none !important'; ?>;
	}

	<?php
	if ( 1 !== $this->settings['popup_content_themebtn'] ) {
		?>
		#jckqv .button {
			background: <?php echo esc_html( $this->settings['popup_content_btncolour'] ); ?>;
			color: <?php echo esc_html( $this->settings['popup_content_btntextcolour'] ); ?>;
		}

		#jckqv .button:hover {
			background: <?php echo esc_html( $this->settings['popup_content_btnhovcolour'] ); ?>;
			color: <?php echo esc_html( $this->settings['popup_content_btntexthovcolour'] ); ?>;
		}
		<?php
	}
	?>
</style>
