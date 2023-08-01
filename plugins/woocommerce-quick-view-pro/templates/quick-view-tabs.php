<?php
/**
 * The template for displaying tabs in the quick view modal.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/quick-view-pro/quick-view-tabs.php
 *
 * @version 1.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! empty( $product_tabs ) || empty( $product ) ) : ?>
	
	<div class="wc-quick-view-product-tabs woocommerce-tabs wc-tabs-wrapper">
		<ul class="<?php echo apply_filters( 'wc_quick_view_pro_tab_list_classes', 'tabs wc-tabs' ); ?>" role="tablist">
			<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
				<li class="<?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>" role="tab" aria-controls="tab-<?php echo esc_attr( $key ); ?>">
					<a href="#tab-<?php echo esc_attr( $key ); ?>">
						<?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
			<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?> panel entry-content wc-tab" id="tab-<?php echo esc_attr( $key ); ?>" role="tabpanel" aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>">
				<?php
				if ( isset( $product_tab['callback'] ) ) {
					call_user_func( $product_tab['callback'], $product );
				}
				?>
			</div>
		<?php endforeach; ?>

		<?php do_action( 'woocommerce_product_after_tabs' ); ?>
	</div>

<?php endif; ?>
