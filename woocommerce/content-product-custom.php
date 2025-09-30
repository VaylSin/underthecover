<?php
/**
 * Template produit personnalisé utilisant exclusivement card-product-item
 *
 * @package Siklane
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Vérification visibilité produit
if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}

// UNIQUEMENT notre composant - pas de hooks WooCommerce
get_template_part( 'components/card-product-item', null, array(
    'product'      => $product,
    'post_id'      => get_the_ID(),
    'permalink'    => get_permalink(),
    'thumbnail'    => get_the_post_thumbnail( get_the_ID(), 'large', array(
        'class' => 'card-img-top object-fit-cover',
        'style' => 'aspect-ratio:1/1;object-fit:cover;width:100%;'
    ) ),
    'price_html'   => $product->get_price_html(),
    'avg_rating'   => $product->get_average_rating(),
    'rating_count' => $product->get_rating_count(),
) );
