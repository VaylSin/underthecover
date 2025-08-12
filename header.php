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
    <header id="wrapper-navbar" <?php understrap_body_attributes(); ?>>
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
