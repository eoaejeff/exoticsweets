<?php
/**
 * The template for displaying the product image in the quick view.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/quick-view-pro/product-image.php.
 *
 * @version 1.0.0
 */
namespace Barn2\Plugin\WC_Quick_View_Pro;

use Barn2\Plugin\WC_Quick_View_Pro\Util\Util,
	Barn2\Plugin\WC_Quick_View_Pro\Util\Settings;

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_thumbnail_id = $product->get_image_id();
$columns           = apply_filters( 'wc_quick_view_pro_product_thumbnails_columns', 6 );
$wrapper_classes   = [
	'wc-quick-view-product-gallery',
	'woocommerce-product-gallery',
	'woocommerce-product-gallery--' . ( $product->get_image_id() ? 'with-images' : 'without-images' ),
	'images',
];

if ( $product->get_gallery_image_ids() ) {
	$settings          = Settings::get_plugin_settings();
	$wrapper_classes[] = 'woocommerce-product-gallery--columns-' . absint( $columns );
	$wrapper_classes[] = 'woocommerce-product-gallery--control-nav-' . $settings['gallery_style'];
}

$wrapper_classes = apply_filters( 'wc_quick_view_pro_product_gallery_classes', $wrapper_classes );
?>
<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">
	<figure class="woocommerce-product-gallery__wrapper">
		<?php
		if ( $product->get_image_id() ) {
			$html = Util::get_gallery_image_html( $post_thumbnail_id );
		} else {
			$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
			$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'woocommerce-quick-view-pro' ) );
			$html .= '</div>';
		}

		echo apply_filters( 'wc_quick_view_pro_product_image_thumbnail_html', $html, $post_thumbnail_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped

		do_action( 'wc_quick_view_pro_product_thumbnails', $product );
		?>
	</figure>
</div>
