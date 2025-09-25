<?php
/**
 * Ajoute une banderole en haut de la boutique avec image ACF + overlay velvet,
 * titre dynamique (catégorie ou "Tous nos produits") et description catégorie.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header( 'shop' );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
// Récupérer l'image de bannière depuis les options ACF
$banner = get_field( 'banniere_entete', 'option' );
if ( is_array( $banner ) && isset( $banner['url'] ) ) {
    $banner_url = $banner['url'];
} elseif ( is_string( $banner ) ) {
    $banner_url = $banner;
} else {
	$banner_url = get_site_url() . '/wp-content/uploads/woocommerce-placeholder-350x350.webp';
}

if ( is_product_category() ) {
    $current_cat = get_queried_object();
    $cat_title = single_cat_title( '', false );
    $cat_desc  = term_description();
} else {
    $cat_title = __( 'Tous nos produits', 'siklane' );
    $cat_desc  = get_field( 'texte_entete_par_defaut_tous_nos_produits', 'option' );
}
?>

<div class="shop-banner" style="background-image: url('<?php echo esc_url( $banner_url ); ?>');">
  <div class="shop-banner-overlay"></div>
  <div class="container shop-banner-content">
    <div class="col-md-6">
		<h1><?php echo esc_html( $cat_title ); ?></h1>
    <?php if ( $cat_desc ) : ?>
      <div class="shop-banner-desc"><?php echo wp_kses_post( $cat_desc ); ?></div>
    <?php endif; ?>
	</div>
  </div>
</div>

<div class="container my-5">

    <?php
    // Affichage des catégories comme boutons centrés
    $product_categories = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
    ) );
    $current_cat = is_product_category() ? get_queried_object() : null;
    ?>
    <?php if ( ! empty( $product_categories ) && ! is_wp_error( $product_categories ) ) : ?>
        <div class="shop-categories mb-5 d-flex flex-wrap justify-content-center gap-2">
            <!-- Bouton "Tous les produits" -->
            <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"
               class="btn btn-outline-dark<?php if ( ! is_product_category() ) echo ' active'; ?>">
                Tous les produits
            </a>
            <?php foreach ( $product_categories as $cat ) : ?>
                <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"
                   class="btn btn-outline-dark<?php if ( is_product_category( $cat->slug ) ) echo ' active'; ?>">
                    <?php echo esc_html( $cat->name ); ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php do_action( 'woocommerce_before_main_content' ); ?>

    <?php if ( woocommerce_product_loop() ) : ?>
        <div class="row g-4">
            <?php
            if ( wc_get_loop_prop( 'total' ) ) {
                while ( have_posts() ) {
                    the_post();
                    if ( get_post_type() === 'product' ) {
                        global $product;
                        if ( has_post_thumbnail() ) {
                            $image_html = woocommerce_get_product_thumbnail();
                        } else {
                            $image_html = '<img src="' . esc_url( get_site_url() . '/wp-content/uploads/woocommerce-placeholder-350x350.webp' ) . '" alt="' . esc_attr__( 'Image produit par défaut', 'siklane' ) . '" class="img-fluid" />';
                        }
                        $args = array(
                            'product_id'    => $product->get_id(),
                            'product'       => $product,
                            'title'         => get_the_title(),
                            'price_html'    => $product->get_price_html(),
                            'permalink'     => get_permalink(),
                            'image_html'    => $image_html,
                            'is_on_sale'    => $product->is_on_sale(),
                            'is_in_stock'   => $product->is_in_stock(),
                        );
                        ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <?php get_template_part( 'components/card-product-item', null, $args ); ?>
                        </div>
                        <?php
                    }
                }
            }
            ?>
        </div>

        <?php woocommerce_pagination(); ?>

    <?php else : ?>
        <?php do_action( 'woocommerce_no_products_found' ); ?>
    <?php endif; ?>

    <?php do_action( 'woocommerce_after_main_content' ); ?>

</div>

<?php get_footer( 'shop' ); ?>
