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
            <div class="row align-items-center">
                <!-- Colonne gauche : Menu principal -->
                <div class="col-4 text-start ">
                    <?php
                    wp_nav_menu( array(
                        'theme_location' => 'left-menu',
                        'menu_class'     => 'nav menu_container',
                        'container'      => false,
                    ) );

                    // Ajout dynamique des catégories sous "Produits"
                    $menu_locations = get_nav_menu_locations();
                    if (isset($menu_locations['left-menu'])) {
                        $menu_id = $menu_locations['left-menu'];
                        $menu_items = wp_get_nav_menu_items($menu_id);
                        if ($menu_items) {
                            foreach ($menu_items as $item) {
                                if ($item->title === 'Produits') {
                                    // Récupère toutes les catégories produits
                                    $product_categories = get_terms([
                                        'taxonomy' => 'product_cat',
                                        'hide_empty' => false
                                    ]);
                                    echo '<ul class="dropdown-menu">';
                                    foreach ($product_categories as $cat) {
                                        echo '<li>
                                            <a class="dropdown-item" href="' . esc_url(get_term_link($cat)) . '">' . esc_html($cat->name) . '</a>
                                        </li>';
                                    }
                                    echo '</ul>';
                                }
                            }
                        }
                    }
                    ?>
                </div>
                <!-- Colonne centrale : Logo -->
                <div class="col-4 text-center">
                    <?php the_custom_logo(); ?>
                </div>
                <!-- Colonne droite : Autre menu -->
                <div class="col-4 text-end ">
                    <?php
                    wp_nav_menu( array(
                        'theme_location' => 'right-menu',
                        'menu_class'     => 'nav justify-content-end menu_container',
                        'container'      => false,
                    ) );
                    ?>
                </div>
            </div>
        </div>
    </header><!-- #wrapper-navbar -->
