<?php
/**
 * Understrap enqueue scripts
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'understrap_scripts' ) ) {
	/**
	 * Load theme's JavaScript and CSS sources.
	 */
	function understrap_scripts() {
		// Get the theme data.
		$the_theme         = wp_get_theme();
		$theme_version     = $the_theme->get( 'Version' );
		$bootstrap_version = get_theme_mod( 'understrap_bootstrap_version', 'bootstrap5' );
		$suffix            = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Grab asset urls.
		$theme_styles  = "/css/theme{$suffix}.css";
		$theme_scripts = "/js/theme{$suffix}.js";
		if ( 'bootstrap4' === $bootstrap_version ) {
			$theme_styles  = "/css/theme-bootstrap4{$suffix}.css";
			$theme_scripts = "/js/theme-bootstrap4{$suffix}.js";
		}
    wp_enqueue_style(
        'silklane-google-fonts',
        'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=SUSE:wght@100..800&display=swap',
        array(),
        null
    );
		$css_version = $theme_version . '.' . filemtime( get_template_directory() . $theme_styles );
		wp_enqueue_style( 'understrap-styles', get_template_directory_uri() . $theme_styles, array(), $css_version );

		// Fix that the offcanvas close icon is hidden behind the admin bar.
		if ( 'bootstrap4' !== $bootstrap_version && is_admin_bar_showing() ) {
			understrap_offcanvas_admin_bar_inline_styles();
		}

		wp_enqueue_script( 'jquery' );
    // Désenregistrer le bundle JS complet d'UnderStrap
    wp_deregister_script('understrap-scripts');

    // Enregistrer uniquement Bootstrap JS
    wp_register_script(
        'bootstrap-js',
        get_template_directory_uri() . '/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js',
        [], // Pas de dépendances
        '5.3.2', // Version de Bootstrap
        true // Charger en footer
    );

    // Charger Bootstrap
    wp_enqueue_script('bootstrap-js');
		$js_version = $theme_version . '.' . filemtime( get_template_directory() . $theme_scripts );
		wp_enqueue_script( 'understrap-scripts', get_template_directory_uri() . $theme_scripts, array(), $js_version, true );
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// Enqueue AOS CSS
		wp_enqueue_style(
			'aos-css',
			get_template_directory_uri() . '/node_modules/aos/dist/aos.css',
			array(),
			'3.0.0' // adapte la version si besoin
		);

		// Enqueue AOS JS
		wp_enqueue_script(
			'aos-js',
			get_template_directory_uri() . '/node_modules/aos/dist/aos.js',
			array(),
			'3.0.0', // adapte la version si besoin
			true
		);
		// Submenu boutique maintenant intégré dans custom-javascript.js

		// Menu mobile
		wp_enqueue_script(
			'mobile-menu',
			get_template_directory_uri() . '/js/mobile-menu.js',
			array(), // Pas de dépendance
			$theme_version,
			true // charge dans le footer
		);

		// Footer accordéon
		wp_enqueue_script(
			'footer-accordion',
			get_template_directory_uri() . '/js/footer-accordion.js',
			array(), // Pas de dépendance
			$theme_version,
			true // charge dans le footer
		);

		// Transition de pages (sauf homepage) - DÉSACTIVÉ
		/*
		if ( ! is_front_page() ) {
			wp_enqueue_script(
				'page-transition',
				get_template_directory_uri() . '/js/page-transition.js',
				array(), // Pas de dépendance
				$theme_version,
				true // charge dans le footer
			);
		}
		*/
	}
} // End of if function_exists( 'understrap_scripts' ).

add_action( 'wp_enqueue_scripts', 'understrap_scripts' );

if ( ! function_exists( 'understrap_offcanvas_admin_bar_inline_styles' ) ) {
	/**
	 * Add inline styles for the offcanvas component if the admin bar is visible.
	 *
	 * Fixes that the offcanvas close icon is hidden behind the admin bar.
	 *
	 * @since 1.2.0
	 */
	function understrap_offcanvas_admin_bar_inline_styles() {
		$navbar_type = get_theme_mod( 'understrap_navbar_type', 'collapse' );
		if ( 'offcanvas' !== $navbar_type ) {
			return;
		}

		$css = '
		body.admin-bar .offcanvas.show  {
			margin-top: 32px;
		}
		@media screen and ( max-width: 782px ) {
			body.admin-bar .offcanvas.show {
				margin-top: 46px;
			}
		}';
		wp_add_inline_style( 'understrap-styles', $css );
	}
}
add_action( 'wp_enqueue_scripts', function() {
    // Swiper (CDN)
    wp_enqueue_style( 'siklane-swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css', array(), '9' );
    wp_enqueue_script( 'siklane-swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js', array(), '9', true );

    // local gallery init — n'utiliser filemtime que si le fichier existe
    $local_path = get_template_directory() . '/src/js/siklane-gallery-swiper.js';
    $local_url  = get_template_directory_uri() . '/src/js/siklane-gallery-swiper.js';
    $version    = file_exists( $local_path ) ? filemtime( $local_path ) : false;

        wp_enqueue_script( 'siklane-gallery', $local_url, array( 'siklane-swiper-js' ), $version, true );

    // Support AJAX WooCommerce pour ajout au panier
    if ( class_exists( 'WooCommerce' ) ) {
        wp_localize_script( 'jquery', 'wc_add_to_cart_params', array(
            'ajax_url'                => admin_url( 'admin-ajax.php' ),
            'wc_ajax_url'             => admin_url( 'admin-ajax.php' ) . '?wc-ajax=%%endpoint%%',
            'i18n_view_cart'          => esc_attr__( 'View cart', 'woocommerce' ),
            'cart_url'                => wc_get_cart_url(),
            'is_cart'                 => is_cart(),
            'cart_redirect_after_add' => get_option( 'woocommerce_cart_redirect_after_add' )
        ) );
    }
} );
