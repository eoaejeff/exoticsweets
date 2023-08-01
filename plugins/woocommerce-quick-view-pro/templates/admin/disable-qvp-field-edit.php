<?php defined( 'ABSPATH' ) || die;
/**
* @var $default_quantity int
*/

$has_parent = ! is_null( $category ) && $category->parent;

if ( $has_parent ) {
	$parent = $category;
	while ( $parent->parent && $parent = get_term( $parent->parent, 'product_cat' ) ) {
		$option = get_term_meta( $parent->term_id, '_qvp_enabled', true );
		if ( ! empty( $option ) ) {
			$parent_option = $option;
			break;
		}
	}
}

if ( ! is_null( $category ) ) {
	$value = get_term_meta( $category->term_id, '_qvp_enabled', true );
}

?>
<tr>
	<th scope="row" valign="top"><label for="category-qvp-enabled"><?php _e( 'Quick view', 'woocommerce-quick-view-pro' ); ?></label></th>
	<td>
		<select name="qvp-enabled" id="category-qvp-enabled" class="postform">
			<option value="" class="if-child-selected" <?php echo $has_parent ? '' : 'style="display:none"'; ?>><?php _e( 'Inherit from parent category', 'woocommerce-quick-view-pro' ); ?>
			<option value="" class="if-parent-selected" <?php echo $has_parent ? 'style="display:none"' : ''; ?>><?php _e( 'Use global setting', 'woocommerce-quick-view-pro' ); ?>
			<?php if ( $has_parent ) : ?>
				<option value="global" class="if-child-selected" <?php selected( $value, 'global' ); ?>><?php _e( 'Use global setting for this category and child categories', 'woocommerce-quick-view-pro' ); ?></option>
			<?php endif; ?>
			<option value="disabled" <?php selected( $value, 'disabled' ); ?>><?php _e( 'Disable for this category and child categories', 'woocommerce-quick-view-pro' ); ?></option>
		</select>
		<?php if ( ! empty( $parent_option ) ) : ?>
		<p class="description">
			<?php
			if ( $parent_option === 'global' ) {
				_e( 'Quick view is enabled in the parent category', 'woocommerce-quick-view-pro' );
			} else {
				_e( 'Quick view is disabled in the parent category', 'woocommerce-quick-view-pro' );
			}
			?>
		</p>
		<?php endif; ?>
	</td>
</tr>
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
