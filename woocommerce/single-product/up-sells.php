<?php
/**
 * Single Product Up-Sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/up-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     9.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $upsells ) : ?>

	<section class="up-sells upsells products my-5">
		<?php
		$heading = apply_filters( 'woocommerce_product_upsells_products_heading', __( 'You may also like&hellip;', 'woocommerce' ) );

		if ( $heading ) :
			?>
			<h2 class="maj_title logo_h3_content my-5"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<!-- Bootstrap Grid pour les ventes incitatives -->
		<div class="container-fluid">
			<div class="row g-3">
				<?php foreach ( $upsells as $upsell ) : ?>
					<?php
					$post_object = get_post( $upsell->get_id() );
					setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

					// Build args for card component
					$card_args = array(
						'product'      => $upsell,
						'post_id'      => $post_object->ID,
						'permalink'    => get_permalink( $post_object->ID ),
						'thumbnail'    => get_the_post_thumbnail( $post_object->ID, 'large', array(
							'class' => 'card-img-top object-fit-cover',
							'style' => 'aspect-ratio:1/1;object-fit:cover;width:100%;'
						) ),
						'price_html'   => $upsell->get_price_html(),
						'avg_rating'   => method_exists( $upsell, 'get_average_rating' ) ? $upsell->get_average_rating() : 0,
						'rating_count' => method_exists( $upsell, 'get_rating_count' ) ? $upsell->get_rating_count() : 0,
					);
					?>
					<div class="col-6 col-lg-3 mb-3">
						<div <?php wc_product_class( 'product-item-wrapper h-100', $post_object ); ?>>
							<?php get_template_part( 'components/card-product-item', null, $card_args ); ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

	</section>

	<?php
endif;

wp_reset_postdata();
