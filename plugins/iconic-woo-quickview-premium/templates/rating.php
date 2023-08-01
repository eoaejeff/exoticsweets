<?php
/**
 * Template: Rating.
 *
 * @package iconic-quickview
 */

global $post, $product, $woocommerce;

do_action( $this->slug . '-before-rating' );
?>

<?php
if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
	return;
}

$count   = $product->get_rating_count();
$average = $product->get_average_rating();

if ( $count > 0 ) :

	$rating_text = sprintf(
		/* Translators: the rating score out of 5 */
		esc_attr__( 'Rated %s out of 5', 'woocommerce' ),
		esc_html( $average )
	);
	$kses_args = array(
		'span' => array(
			'itemprop' => array(),
			'count'    => array(),
		),
	);
	?>

	<div class="woocommerce-product-rating" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
		<div class="star-rating" title="<?php echo esc_attr( $rating_text ); ?>">
			<span style="width:<?php echo esc_attr( ( ( $average / 5 ) * 100 ) ); ?>%">
				<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $average ); ?></strong> <?php esc_html_e( 'out of 5', 'woocommerce' ); ?>
			</span>
		</div>
		<div class="text-rating">(
			<?php
				wp_kses(
					sprintf(
						/* Translators: the number of ratings */
						_n(
							'%s rating',
							'%s ratings',
							$count,
							'woocommerce'
						),
						'<span itemprop="ratingCount" class="count">' . esc_html( $count ) . '</span>'
					),
					$kses_args
				);
			?>
		)</div>
	</div>

<?php endif; ?>

<?php do_action( $this->slug . '-after-rating' ); ?>
