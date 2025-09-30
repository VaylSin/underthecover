<?php
/**
 * Empty cart page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-empty.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="empty-cart-container text-center py-5">
	<!-- Message votre panier vide avec icône CSS -->
	<h2 class="mb-3 with-empty-cart-icon">Votre panier est vide</h2>
	<p class="text-muted mb-4">Découvrez nos produits populaires pour commencer vos achats</p>

	<?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
		<div class="mb-5">
			<a class="view-all-link" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
				<?php echo esc_html( apply_filters( 'woocommerce_return_to_shop_text', __( 'Continuer mes achats', 'woocommerce' ) ) ); ?>
				&nbsp;&nbsp;<i class="bi bi-bag-heart"></i>
			</a>
		</div>
	<?php endif; ?>

	<!-- Derniers produits créés en grille 2x2 -->
	<div class="empty-cart-products">
		<h3 class="maj_title mb-4 logo_before"><span class="logo_h3_content">Nos dernières créations</span></h3>

		<!-- DEBUG : Vérifier que ce template est bien utilisé -->
		<div style="background: yellow; color: black; padding: 10px; margin: 10px;">
			TEMPLATE CART-EMPTY.PHP ACTIF - Date: <?php echo current_time('Y-m-d H:i:s'); ?>
		</div>

		<?php
		// Récupérer les 4 derniers produits créés dans le back-office
		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => 4,
			'post_status'    => 'publish',
			'meta_query'     => array(
				array(
					'key'     => '_stock_status',
					'value'   => 'instock',
					'compare' => '=',
				),
			),
			'tax_query'      => array(
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => array( 'exclude-from-catalog', 'exclude-from-search' ),
					'operator' => 'NOT IN',
				),
			),
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		$latest_products = new WP_Query( $args );

		if ( $latest_products->have_posts() ) : ?>
			<div class="row g-3 justify-content-center">
				<?php
				while ( $latest_products->have_posts() ) :
					$latest_products->the_post();
					global $product;
				?>
					<div class="col-6 col-md-3">
						<?php
						get_template_part( 'components/card-product-item', null, array(
							'product'    => $product,
							'post_id'    => get_the_ID(),
							'permalink'  => get_permalink(),
							'thumbnail'  => get_the_post_thumbnail( get_the_ID(), 'large', array(
								'class' => 'card-img-top object-fit-cover',
								'style' => 'aspect-ratio:1/1;object-fit:cover;width:100%;'
							) ),
							'price_html'   => $product->get_price_html(),
							'avg_rating'   => $product->get_average_rating(),
							'rating_count' => $product->get_rating_count(),
						) );
						?>
					</div>
				<?php endwhile; ?>
			</div>
			<?php wp_reset_postdata(); ?>
		<?php else : ?>
			<p class="text-muted">Aucun produit disponible pour le moment.</p>
		<?php endif; ?>
	</div>
</div>

<?php
/*
 * @hooked wc_empty_cart_message - 10
 */
// Nous affichons notre propre message, donc on désactive le hook par défaut
// do_action( 'woocommerce_cart_is_empty' );

