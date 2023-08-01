<?php defined( 'ABSPATH' ) || die; ?>

<div class="form-field term-name-wrap">
	<label for="category-qvp-enabled"><?php _e( 'Quick view', 'woocommerce-quick-view-pro' ); ?></label>
	<select name="qvp-enabled" id="category-qvp-enabled" class="postform">
		<option value="" class="if-parent-selected" ><?php _e( 'Use global setting', 'woocommerce-quick-view-pro' ); ?></option>
		<option value="" class="if-child-selected" style="display:none"><?php _e( 'Inherit from parent category', 'woocommerce-quick-view-pro' ); ?></option>
		<option value="global" class="if-child-selected"  style="display:none"><?php _e( 'Use global setting for this category and child categories', 'woocommerce-quick-view-pro' ); ?></option>
		<option value="disabled"><?php _e( 'Disable for this category and child categories', 'woocommerce-quick-view-pro' ); ?></option>
	</select>
</div>
<script>
	jQuery( function( $ ) {
		$( 'select#parent' ).on( 'change', function ( e ) {
			var $self = $( e.currentTarget );
			if ( $self.val() == '-1' ) {
				$( "select#category-qvp-enabled .if-parent-selected" ).show().prop( 'disabled', false );
				$( "select#category-qvp-enabled .if-child-selected" ).hide().prop( 'disabled', true );
				if ( $( "select#category-qvp-enabled" ).val() === 'global' ) {
					$( "select#category-qvp-enabled" ).val('');
				}
				else if ( ! $( "select#category-qvp-enabled" ).val() ) {
					$( 'select#category-qvp-enabled option[value=""].if-parent-selected' ).prop( 'selected', true );
				}
			} else {
				$( "select#category-qvp-enabled .if-parent-selected" ).hide().prop( 'disabled', true );
				$( "select#category-qvp-enabled .if-child-selected" ).show().prop( 'disabled', false );
				if ( ! $( "select#category-qvp-enabled" ).val() ) {
					$( 'select#category-qvp-enabled option[value=""].if-child-selected' ).prop( 'selected', true );
				}
			}
		} );
	} );
</script>
