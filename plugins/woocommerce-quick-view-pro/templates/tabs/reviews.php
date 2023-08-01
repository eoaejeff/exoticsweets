<?php
/**
 * The template for displaying reviews in the quick view modal tabs.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/quick-view-pro/tabs/reviews.php
 *
 * @version 1.6
 */

 // Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $product ) ) {
	return;
}

$comments = get_comments( [ 'post_id' => $product->get_id() ] );

?>

<div id="reviews" class="woocommerce-Reviews">
	<div id="comments">

		<h2 class="woocommerce-Reviews-title">
			<?php
			$count = $product->get_review_count();
			if ( $count && wc_review_ratings_enabled() ) {
				/* translators: 1: reviews count 2: product name */
				$reviews_title = sprintf( esc_html( _n( '%1$s review for %2$s', '%1$s reviews for %2$s', $count, 'woocommerce' ) ), esc_html( $count ), '<span>' . get_the_title() . '</span>' );
				echo apply_filters( 'woocommerce_reviews_title', $reviews_title, $count, $product ); // WPCS: XSS ok.
			} else {
				esc_html_e( 'Reviews', 'woocommerce' );
			}
			?>
		</h2>

		<?php if ( ! empty( $comments ) ) : ?>
			<ol class="commentlist">
				<?php

				wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', [ 'callback' => 'woocommerce_comments' ] ), $comments );

				?>
			</ol>

			<?php
			if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
				echo '<nav class="woocommerce-pagination">';
				paginate_comments_links(
					apply_filters(
						'woocommerce_comment_pagination_args',
						[
							'prev_text' => '&larr;',
							'next_text' => '&rarr;',
							'type'      => 'list',
						]
					)
				);
				echo '</nav>';
			endif;
			?>
		<?php else : ?>
			<p class="woocommerce-noreviews"><?php esc_html_e( 'There are no reviews yet.', 'woocommerce' ); ?></p>
		<?php endif; ?>
		
	</div>
</div>

<?php wp_reset_postdata(); ?>
