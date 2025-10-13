<?php
/**
 * Composant réutilisable : Dropdown de recherche
 *
 * @param array $args {
 *     @type string $id           ID unique pour le dropdown (ex: 'searchDropdown', 'mobileSearchDropdown')
 *     @type string $close_btn_id ID unique pour le bouton de fermeture (ex: 'closeSearch', 'mobileCloseSearch')
 *     @type bool   $show_popular Afficher les produits populaires (true/false)
 *     @type string $container_class Classe CSS supplémentaire pour le container
 * }
 */

// Valeurs par défaut des arguments
$args = wp_parse_args( $args, array(
    'id'              => 'searchDropdown',
    'close_btn_id'    => 'closeSearch',
    'show_popular'    => true,
    'container_class' => ''
) );

// Extraction des variables
extract( $args );
?>

<!-- Dropdown de recherche -->
<div id="<?php echo esc_attr( $id ); ?>" class="search-dropdown <?php echo esc_attr( $container_class ); ?>">
    <button type="button" class="close-search" id="<?php echo esc_attr( $close_btn_id ); ?>" aria-label="Fermer">
        &times;
    </button>
    <div class="search-form-container container mb-4">
        <?php the_widget('WC_Widget_Product_Search'); ?>
    </div>

    <?php if ( $show_popular ) : ?>
        <?php
        // Récupérer les 4 produits les plus recherchés (par popularité WooCommerce)
        $args_query = array(
            'post_type'      => 'product',
            'posts_per_page' => 4,
            'meta_key'       => 'total_sales',
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
            'post_status'    => 'publish',
        );
        $popular_products = new WP_Query($args_query);
        if ( $popular_products->have_posts() ) : ?>
            <div class="popular-products col col-md-8 mt-4">
                <h4 class="h3 logo_h3_content maj_title my-4">Nos recherches les plus populaires</h4>
                <div class="row popular-products-row g-3">
                    <?php while ( $popular_products->have_posts() ) : $popular_products->the_post(); global $product;
                        // Préparer les $args pour le composant
                        if ( has_post_thumbnail() ) {
                            $image_html = woocommerce_get_product_thumbnail();
                        } else {
                            $image_html = '<img src="' . esc_url( get_site_url() . '/wp-content/uploads/woocommerce-placeholder-350x350.webp' ) . '" alt="' . esc_attr__( 'Image produit par défaut', 'siklane' ) . '" class="img-fluid" />';
                        }
                        $product_args = array(
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
                        <div class="col-6 col-md-3">
                            <?php get_template_part( 'components/card-product-item', null, $product_args ); ?>
                        </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
