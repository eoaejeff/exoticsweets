<?php
/**
 * Template: Images.
 *
 * @package iconic-quickview
 */

global $post, $product, $woocommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

do_action( $this->slug . '-before-images' );

$prod_imgs = $this->get_product_images( $product ); ?>

	<div id="<?php echo esc_attr( $this->slug . '_images_wrap' ); ?>" 
						<?php
						if ( is_rtl() ) {
							echo 'dir="ltr"';
						}
						?>
	>
		<?php if ( ! empty( $prod_imgs ) ) : ?>

			<?php $prod_imgs_count = count( $prod_imgs ); ?>

			<div id="<?php echo esc_attr( $this->slug . '_images' ); ?>" class="jckqv_slider">

				<?php
				$i = 0;
				foreach ( $prod_imgs as $img_id => $img_data ) :
					?>
					<?php if ( $img_id ) { ?>
						<?php
						echo wp_get_attachment_image(
							$img_id,
							'woocommerce_single',
							false,
							array(
								'data-jckqv' => esc_attr( implode( ' ', $img_data['slideId'] ) ),
								'class'      => 'jckqv_image',
							)
						);
						?>
					<?php } else { ?>
						<img src="<?php echo esc_url( $img_data['img_src'] ); ?>" data-<?php echo esc_attr( $this->slug ); ?>="<?php echo esc_attr( implode( ' ', $img_data['slideId'] ) ); ?>" class="<?php echo esc_attr( $this->slug ); ?>_image">
					<?php } ?>
					<?php
					$i ++;
endforeach;
				?>

			</div>

			<?php if ( $prod_imgs_count > 1 && 'thumbnails' === $this->settings['popup_imagery_thumbnails'] ) : ?>

				<div id="<?php echo esc_attr( $this->slug . '_thumbs' ); ?>" class="jckqv_slider">

					<?php
					$i = 0;
					foreach ( $prod_imgs as $img_id => $img_data ) :
						?>

						<?php
						$classes = array();
						if ( 0 === $i ) {
							$classes[] = 'slick-main-active';
						}
						?>

						<div data-index="<?php echo esc_attr( $i ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
							<?php if ( $img_id ) { ?>
								<?php
								echo wp_get_attachment_image(
									$img_id,
									'woocommerce_single',
									false,
									array(
										'data-jckqv' => esc_attr( implode( ' ', $img_data['slideId'] ) ),
										'class'      => 'jckqv_thumb',
									)
								);
								?>
							<?php } else { ?>
								<img src="<?php echo esc_url( $img_data['img_thumb_src'] ); ?>" data-<?php echo esc_attr( $this->slug ); ?>="<?php echo esc_attr( implode( ' ', $img_data['slideId'] ) ); ?>" class="<?php echo esc_attr( $this->slug ); ?>_thumb">
							<?php } ?>
						</div>

						<?php
						$i ++;
endforeach;
					?>

				</div>

			<?php endif; ?>

		<?php endif; ?>
	</div>

<?php do_action( $this->slug . '-after-images' ); ?>
