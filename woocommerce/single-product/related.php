<?php
/**
 * Related Products
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     9.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( $related_products ) : ?>

    <section class="related products">

        <?php
        $heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Ils pourraient aussi vous faire de l\'oeil...', 'woocommerce' ) );

        if ( $heading ) :
            ?>
            <h2 class="maj_title logo_h3_content my-5"><?php echo esc_html( $heading ); ?></h2>

        <?php endif; ?>

        <?php woocommerce_product_loop_start(); ?>

            <?php
            // Duplicate the first related product 4 times for display (useful pour tests / layout)
            $first = reset( $related_products );
            if ( $first ) :
                $post_object = get_post( $first->get_id() );
                setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

                // Build args once and reuse
                $card_args = array(
                    'product'      => $first,
                    'post_id'      => $post_object->ID,
                    'permalink'    => get_permalink( $post_object->ID ),
                    'thumbnail'    => get_the_post_thumbnail( $post_object->ID, 'large', array(
                        'class' => 'card-img-top object-fit-cover',
                        'style' => 'aspect-ratio:1/1;object-fit:cover;width:100%;'
                    ) ),
                    'price_html'   => $first->get_price_html(),
                    'avg_rating'   => method_exists( $first, 'get_average_rating' ) ? $first->get_average_rating() : 0,
                    'rating_count' => method_exists( $first, 'get_rating_count' ) ? $first->get_rating_count() : 0,
                );

                for ( $i = 0; $i < 4; $i++ ) {
                    echo '<li ';
                    wc_product_class( '', $post_object );
                    echo '>';

                    get_template_part( 'components/card-product-item', null, $card_args );

                    echo '</li>';
                }
            endif;
            ?>

        <?php woocommerce_product_loop_end(); ?>

    </section>
    <?php
endif;

wp_reset_postdata();
