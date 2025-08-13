<?php
/**
 * The header for our theme
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$bootstrap_version = get_theme_mod( 'understrap_bootstrap_version', 'bootstrap4' );
$navbar_type       = get_theme_mod( 'understrap_navbar_type', 'collapse' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php wp_head(); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>

<body <?php body_class(); ?>>

<div class="site" id="page">

    <!-- ******************* The Navbar Area ******************* -->
    <header id="wrapper-navbar"
        class="w-100 position-absolute top-0 start-0"
        style="z-index:100;">
        <?php do_action( 'wp_body_open' ); ?>
        <a class="skip-link <?php echo understrap_get_screen_reader_class( true ); ?>" href="#content">
            <?php esc_html_e( 'Skip to content', 'understrap' ); ?>
        </a>
        <div class="container-fluid py-3 px-5">
            <div class="row align-items-center h-100" style="min-height: 4rem;">
                <!-- Colonne gauche : Menu principal -->
                <div class="col-4 text-start d-flex align-items-center h-100">
                    <div class="nav menu_container align-items-center h-100">
                        <div class="nav-item dropdown position-static">
                            <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>"
                               class="nav-link text-uppercase px-3 fw-semibold"
                               id="produitsDropdown"
                               data-bs-toggle="dropdown"
                               aria-expanded="false">
                                Boutique
                            </a>
                            <div class="dropdown-menu mega-menu" aria-labelledby="produitsDropdown">
                                <div class="row gx-5">
                                    <?php
                                    $product_categories = get_terms([
                                        'taxonomy' => 'product_cat',
                                        'hide_empty' => false
                                    ]);
                                    $col_count = max(1, min(4, count($product_categories)));
                                    foreach ($product_categories as $cat):
                                        $products = get_posts([
                                            'post_type' => 'product',
                                            'posts_per_page' => -1,
                                            'tax_query' => [
                                                [
                                                    'taxonomy' => 'product_cat',
                                                    'field'    => 'term_id',
                                                    'terms'    => $cat->term_id,
                                                ]
                                            ]
                                        ]);
                                    ?>
                                    <div class="col-12 col-md-<?php echo intval(12/$col_count); ?>">
                                        <h6 class="fw-bold mb-2 text-uppercase">
                                            <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="text-dark text-decoration-none">
                                                <?php echo esc_html($cat->name); ?>
                                            </a>
                                        </h6>
                                        <ul class="list-unstyled">
                                            <?php foreach ($products as $prod): ?>
                                                <li>
                                                    <a href="<?php echo get_permalink($prod->ID); ?>" class="dropdown-item px-0 py-1 text-lowercase">
                                                        <?php echo esc_html(get_the_title($prod->ID)); ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php
                        // Affiche les autres éléments du menu principal
                        wp_nav_menu( array(
                            'theme_location' => 'left-menu',
                            'menu_class'     => 'nav',
                            'container'      => false,
                            'fallback_cb'    => false,
                        ) );
                        ?>
                    </div>
                </div>
                <!-- Colonne centrale : Logo -->
                <div class="col-4 text-center d-flex align-items-center justify-content-center h-100">
                    <?php the_custom_logo(); ?>
                </div>
                <!-- Colonne droite : Menu droit -->
                <div class="col-4 text-end d-flex align-items-center justify-content-end h-100">
                    <?php
                    wp_nav_menu( array(
                        'theme_location' => 'right-menu',
                        'menu_class'     => 'nav justify-content-end align-items-center menu_container',
                        'container'      => false,
                    ) );
                    ?>
                </div>
            </div>
        </div>
    </header><!-- #wrapper-navbar -->
