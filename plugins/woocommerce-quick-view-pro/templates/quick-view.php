<?php
/**
 * The template for displaying the contents of the quick view modal.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/quick-view-pro/quick-view.php
 *
 * @version 1.1
 */
namespace Barn2\Plugin\WC_Quick_View_Pro;

use Barn2\Plugin\WC_Quick_View_Pro\Util\Util;

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'wc_quick_view_pro_before_quick_view', $product );

$show_tabs = \wc_qvp_show_tabs( $product );

$modal_class = 'wc-quick-view-product';
if ( $show_tabs ) {
	$modal_class .= ' with-qvp-tabs';
}

?>

<?php if ( apply_filters( 'wc_quick_view_pro_can_view_quick_view_content', true, $product ) ) : ?>

	<?php
	$modal_class           = Util::get_modal_class( $product, $modal_class );
	$modal_data_attributes = Util::get_modal_data_attributes( $product ); // attributes escaped
	$product_class         = Util::get_modal_product_class( $product );
	?>

	<div id="quick-view-<?php echo esc_attr( $product->get_id() ); ?>" class="<?php echo esc_attr( $modal_class ); ?>" <?php echo $modal_data_attributes; ?>>

		<?php do_action( 'wc_quick_view_pro_before_quick_view_product', $product ); ?>

		<div id="product-<?php echo esc_attr( $product->get_id() ); ?>" class="<?php echo esc_attr( $product_class ); ?>">

			<?php do_action( 'wc_quick_view_pro_quick_view_before_product_details', $product ); ?>

			<?php if ( apply_filters( 'wc_quick_view_pro_show_product_details', true, $product ) ) : ?>
				<div class="wc-quick-view-product-summary summary entry-summary">
					<?php do_action( 'wc_quick_view_pro_quick_view_product_details', $product ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $show_tabs ) : ?>
				<?php do_action( 'wc_quick_view_pro_quick_view_product_tabs', $product ); ?>
			<?php endif; ?>

			<?php do_action( 'wc_quick_view_pro_quick_view_after_product_details', $product ); ?>

		</div>

		<?php do_action( 'wc_quick_view_pro_after_quick_view_product', $product ); ?>

	</div>

<?php else : ?>

	<div class="wc-quick-view-modal wc-quick-view-content-hidden wc-quick-view-message-only">

		<?php do_action( 'wc_quick_view_pro_quick_view_content_hidden', $product ); ?>

	</div>

<?php endif; ?>

<?php
do_action( 'wc_quick_view_pro_after_quick_view', $product );
