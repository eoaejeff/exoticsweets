<?php
/**
 * Template: Button Styles.
 *
 * @package iconic-quickview
 */

$margins = array( $this->settings['trigger_position_margins'][0] . 'px', $this->settings['trigger_position_margins'][1] . 'px', $this->settings['trigger_position_margins'][2] . 'px', $this->settings['trigger_position_margins'][3] . 'px' );
$padding = array( $this->settings['trigger_styling_padding'][0] . 'px', $this->settings['trigger_styling_padding'][1] . 'px', $this->settings['trigger_styling_padding'][2] . 'px', $this->settings['trigger_styling_padding'][3] . 'px' );
?>

<style>
	/* QV Button */

	.iconic-wqv-button {
		margin: <?php echo esc_attr( implode( ' ', $margins ) ); ?>;
		padding: <?php echo esc_attr( implode( ' ', $padding ) ); ?>;
		<?php if ( 'none' !== $this->settings['trigger_styling_btnstyle'] ) { ?>
			<?php if ( 'flat' === $this->settings['trigger_styling_btnstyle'] ) { ?>
				background: <?php echo esc_attr( $this->settings['trigger_styling_btncolour'] ); ?>;
			<?php } else { ?>
				border: 1px solid #fff;
				border-color: <?php echo esc_attr( $this->settings['trigger_styling_btncolour'] ); ?>;
			<?php } ?>
			color: <?php echo esc_attr( $this->settings['trigger_styling_btntextcolour'] ); ?>;
		<?php } ?>
		border-top-left-radius: <?php echo esc_attr( $this->settings['trigger_styling_borderradius'][0] ); ?>px;
		border-top-right-radius: <?php echo esc_attr( $this->settings['trigger_styling_borderradius'][1] ); ?>px;
		border-bottom-right-radius: <?php echo esc_attr( $this->settings['trigger_styling_borderradius'][2] ); ?>px;
		border-bottom-left-radius: <?php echo esc_attr( $this->settings['trigger_styling_borderradius'][3] ); ?>px;
	}

	.iconic-wqv-button:hover {
	<?php if ( 'none' !== $this->settings['trigger_styling_btnstyle'] ) { ?>
		<?php if ( 'flat' === $this->settings['trigger_styling_btnstyle'] ) { ?>
			background: <?php echo esc_attr( $this->settings['trigger_styling_btnhovcolour'] ); ?>;
		<?php } else { ?>
			border-color: <?php echo esc_attr( $this->settings['trigger_styling_btnhovcolour'] ); ?>;
		<?php } ?>
		color: <?php echo esc_attr( $this->settings['trigger_styling_btntexthovcolour'] ); ?>;
	<?php } ?>
	}

	/* Magnific Specific */

	.mfp-bg {
		background: <?php echo esc_attr( $this->settings['popup_general_overlaycolour'] ); ?>;
		-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=<?php echo esc_attr( $this->settings['popup_general_overlayopacity'] ) * 10; ?>)";
		filter: alpha(opacity=<?php echo esc_attr( $this->settings['popup_general_overlayopacity'] ) * 10; ?>);
		-moz-opacity: <?php echo esc_attr( $this->settings['popup_general_overlayopacity'] ); ?>;
		-khtml-opacity: <?php echo esc_attr( $this->settings['popup_general_overlayopacity'] ); ?>;
		opacity: <?php echo esc_attr( $this->settings['popup_general_overlayopacity'] ); ?>;
	}
</style>
