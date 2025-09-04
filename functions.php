<?php
/**
 * UnderStrap functions and definitions
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// UnderStrap's includes directory.
$understrap_inc_dir = 'inc';

// Array of files to include.
$understrap_includes = array(
	'/theme-settings.php',                  // Initialize theme default settings.
	'/setup.php',                           // Theme setup and custom theme supports.
	'/widgets.php',                         // Register widget area.
	'/enqueue.php',                         // Enqueue scripts and styles.
	'/template-tags.php',                   // Custom template tags for this theme.
	'/pagination.php',                      // Custom pagination for this theme.
	'/hooks.php',                           // Custom hooks.
	'/extras.php',                          // Custom functions that act independently of the theme templates.
	'/customizer.php',                      // Customizer additions.
	'/custom-comments.php',                 // Custom Comments file.
	'/class-wp-bootstrap-navwalker.php',    // Load custom WordPress nav walker. Trying to get deeper navigation? Check out: https://github.com/understrap/understrap/issues/567.
	'/editor.php',                          // Load Editor functions.
	'/block-editor.php',                    // Load Block Editor functions.
	'/deprecated.php',                      // Load deprecated functions.
);

// Load WooCommerce functions if WooCommerce is activated.
if ( class_exists( 'WooCommerce' ) ) {
	$understrap_includes[] = '/woocommerce.php';
}

// Load Jetpack compatibility file if Jetpack is activiated.
if ( class_exists( 'Jetpack' ) ) {
	$understrap_includes[] = '/jetpack.php';
}

// Include files.
foreach ( $understrap_includes as $file ) {
	require_once get_theme_file_path( $understrap_inc_dir . $file );
}
class Walker_Nav_Menu_HTML extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth = 0, $args = [], $id = 0) {
        $output .= sprintf(
            '<li id="menu-item-%s" class="%s"><a href="%s">%s</a></li>',
            esc_attr($item->ID),
            esc_attr(implode(' ', $item->classes)),
            esc_url($item->url),
            $item->title // Affiche le HTML du titre
        );
    }
}
add_action( 'init', function() {
    if ( function_exists( 'is_woocommerce' ) ) {
        // supprime l'affichage par défaut du prix (priority 10)
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
    }
}, 20 );
/**
 * Nettoie la short description affichée : supprime les attributs style et désencapsule un span unique.
 */
add_filter( 'woocommerce_short_description', 'siklane_clean_short_description', 20 );
function siklane_clean_short_description( $html ) {
    if ( empty( $html ) ) {
        return $html;
    }

    // Utilise DOMDocument pour enlever les style="" proprement
    if ( ! class_exists( 'DOMDocument' ) ) {
        // fallback : suppression basique des style attributes
        $html = preg_replace( '/(<[^>]+)\sstyle=(["\']).*?\2/iu', '$1', $html );
        // désencapsuler un seul span englobant
        if ( preg_match( '/^\s*<span\b[^>]*>(.*)<\/span>\s*$/is', $html, $m ) ) {
            $html = $m[1];
        }
        return $html;
    }

    libxml_use_internal_errors( true );
    $doc = new DOMDocument();
    // wrapper pour conserver racine valide
    $doc->loadHTML( '<?xml encoding="utf-8" ?><div id="siklane-wrapper">' . $html . '</div>' );
    libxml_clear_errors();

    $container = $doc->getElementById( 'siklane-wrapper' );
    if ( ! $container ) {
        // fallback
        return $html;
    }

    // supprimer tous les attributs style
    $xpath = new DOMXPath( $doc );
    foreach ( $xpath->query( '//*[@style]', $container ) as $el ) {
        $el->removeAttribute( 'style' );
    }

    // récupérer innerHTML du container
    $inner = '';
    foreach ( $container->childNodes as $child ) {
        $inner .= $doc->saveHTML( $child );
    }

    // si tout est encapsulé dans un span unique, désencapsuler
    if ( preg_match( '/^\s*<span\b[^>]*>(.*)<\/span>\s*$/is', $inner, $m ) ) {
        $inner = $m[1];
    }

    // optionnel : appliquer wp_kses pour garder only safe tags si besoin
    $allowed = wp_kses_allowed_html( 'post' );
    return wp_kses( $inner, $allowed );
}
