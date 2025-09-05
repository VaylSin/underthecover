<?php
/**
 * Add WooCommerce support
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'understrap_woocommerce_support' );
if ( ! function_exists( 'understrap_woocommerce_support' ) ) {
    function understrap_woocommerce_support() {
        add_theme_support( 'woocommerce' );
        add_theme_support( 'wc-product-gallery-lightbox' );
        add_theme_support( 'wc-product-gallery-zoom' );
        add_theme_support( 'wc-product-gallery-slider' );

        add_filter( 'woocommerce_form_field_args', 'understrap_wc_form_field_args', 10, 3 );
        add_filter( 'woocommerce_form_field_radio', 'understrap_wc_form_field_radio', 10, 4 );
        add_filter( 'woocommerce_quantity_input_classes', 'understrap_quantity_input_classes' );
        add_filter( 'woocommerce_loop_add_to_cart_args', 'understrap_loop_add_to_cart_args' );
        add_filter( 'woocommerce_loop_add_to_cart_link', 'understrap_loop_add_to_cart_link' );
        add_filter( 'woocommerce_account_menu_item_classes', 'understrap_account_menu_item_classes' );
    }
}

// First unhook the WooCommerce content wrappers.
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

// Then hook in your own functions to display the wrappers your theme requires.
add_action( 'woocommerce_before_main_content', 'understrap_woocommerce_wrapper_start', 10 );
add_action( 'woocommerce_after_main_content', 'understrap_woocommerce_wrapper_end', 10 );

if ( ! function_exists( 'understrap_woocommerce_wrapper_start' ) ) {
    function understrap_woocommerce_wrapper_start() {
        $container = get_theme_mod( 'understrap_container_type' );
        if ( false === $container ) {
            $container = '';
        }

        echo '<div class="wrapper" id="woocommerce-wrapper">';
        echo '<div class="' . esc_attr( $container ) . '" id="content" tabindex="-1">';
        echo '<div class="row">';
        get_template_part( 'global-templates/left-sidebar-check' );
        echo '<main class="site-main" id="main">';
    }
}

if ( ! function_exists( 'understrap_woocommerce_wrapper_end' ) ) {
    function understrap_woocommerce_wrapper_end() {
        echo '</main>';
        get_template_part( 'global-templates/right-sidebar-check' );
        echo '</div><!-- .row -->';
        echo '</div><!-- .container(-fluid) -->';
        echo '</div><!-- #woocommerce-wrapper -->';
    }
}

if ( ! function_exists( 'understrap_wc_form_field_args' ) ) {
    function understrap_wc_form_field_args( $args, $key, $value ) {
        $bootstrap4 = 'bootstrap4' === get_theme_mod( 'understrap_bootstrap_version', 'bootstrap4' );

        if ( $bootstrap4 ) {
            $args['class'][] = 'form-group';
        }
        $args['class'][] = 'mb-3';

        switch ( $args['type'] ) {
            case 'country':
                $args['class'][] = 'single-country';
                break;
            case 'state':
                $args['custom_attributes']['data-plugin']      = 'select2';
                $args['custom_attributes']['data-allow-clear'] = 'true';
                $args['custom_attributes']['aria-hidden']      = 'true';
                $args['input_class'][] = 'form-control';
                break;
            case 'checkbox':
                $base = $bootstrap4 ? 'custom-control' : 'form-check';
                if ( '' !== $args['label'] || $bootstrap4 ) {
                    $args['label'] = "<span class=\"{$base}-label\">{$args['label']}</span>";
                }
                $args['label_class'][] = $base;
                if ( $bootstrap4 ) {
                    $args['label_class'][] = 'custom-checkbox';
                }
                $args['input_class'][] = $base . '-input';
                break;
            case 'select':
                $args['input_class'][] = $bootstrap4 ? 'form-control' : 'form-select';
                $args['custom_attributes']['data-plugin']      = 'select2';
                $args['custom_attributes']['data-allow-clear'] = 'true';
                break;
            case 'radio':
                $base = $bootstrap4 ? 'custom-control' : 'form-check';
                $args['label_class'][] = $base . '-label';
                $args['input_class'][] = $base . '-input';
                break;
            default:
                $args['input_class'][] = 'form-control';
        }
        return $args;
    }
}

if ( ! function_exists( 'understrap_wc_form_field_radio' ) ) {
    function understrap_wc_form_field_radio( $field, $key, $args, $value ) {
        if ( 'bootstrap4' === get_theme_mod( 'understrap_bootstrap_version', 'bootstrap4' ) ) {
            $wrapper_classes = 'custom-control custom-radio';
            $label_class     = 'custom-control-label';
        } else {
            $wrapper_classes = 'form-check';
            $label_class     = 'form-check-label';
        }

        if ( '' !== $args['label'] && ! empty( $args['label_class'] ) ) {
            $strpos = strpos( $field, $label_class );
            if ( false !== $strpos ) {
                $field = substr_replace( $field, '', $strpos, strlen( $label_class ) );
                $field = str_replace( 'class=""', '', $field );
            }
        }

        $field = str_replace( '<input', "<span class=\"{$wrapper_classes}\"><input", $field );
        $field = str_replace( '</label>', '</label></span>', $field );
        if ( '' !== $args['label'] ) {
            $strpos = strpos( $field, '</label>' ) + strlen( '</label>' );
            $field  = substr_replace( $field, '', $strpos, strlen( '</span>' ) );
        }

        return $field;
    }
}

if ( ! is_admin() && ! function_exists( 'wc_review_ratings_enabled' ) ) {
    function wc_reviews_enabled() {
        return 'yes' === get_option( 'woocommerce_enable_reviews' );
    }
    function wc_review_ratings_enabled() {
        return wc_reviews_enabled() && 'yes' === get_option( 'woocommerce_enable_review_rating' );
    }
}

if ( ! function_exists( 'understrap_quantity_input_classes' ) ) {
    function understrap_quantity_input_classes( $classes ) {
        $classes[] = 'form-control';
        return $classes;
    }
}

if ( ! function_exists( 'understrap_loop_add_to_cart_link' ) ) {
    function understrap_loop_add_to_cart_link( $html ) {
        return '<div class="add-to-cart-container">' . $html . '</div>';
    }
}

if ( ! function_exists( 'understrap_loop_add_to_cart_args' ) ) {
    function understrap_loop_add_to_cart_args( $args ) {
        if ( isset( $args['class'] ) && ! empty( $args['class'] ) ) {
            if ( ! is_string( $args['class'] ) ) {
                return $args;
            }
            if ( false !== strpos( $args['class'], 'button' ) ) {
                $args['class'] = explode( ' ', $args['class'] );
                $args['class'] = array_diff( $args['class'], array( 'button' ) );
                $args['class'] = implode( ' ', $args['class'] );
            }
            $args['class'] .= ' btn btn-outline-primary';
        } else {
            $args['class'] = 'btn btn-outline-primary';
        }
        if ( 'bootstrap4' === get_theme_mod( 'understrap_bootstrap_version', 'bootstrap4' ) ) {
            $args['class'] .= ' btn-block';
        }
        return $args;
    }
}

if ( ! function_exists( 'understrap_account_menu_item_classes' ) ) {
    function understrap_account_menu_item_classes( $classes ) {
        $classes[] = 'list-group-item';
        $classes[] = 'list-group-item-action';
        if ( in_array( 'is-active', $classes, true ) ) {
            $classes[] = 'active';
        }
        return $classes;
    }
}


add_action( 'wp', function() {
    if ( function_exists( 'is_product' ) && is_product() ) {
        remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
    }
}, 9 );
add_action( 'after_setup_theme', function() {
    add_image_size( 'siklane_square', 800, 800, true ); // 800x800 hard crop
} );

/**
 * Affiche une liste de specs (2 depuis Options / répéteur 'spec', 2 depuis le produit : groups 'teste' et 'contenance')
 * Injectée entre la description (prio 20) et le bouton add-to-cart (prio 30).
 */
add_action( 'woocommerce_single_product_summary', 'siklane_render_product_specs', 25 );
function siklane_render_product_specs() {
    if ( ! function_exists( 'get_field' ) ) {
        return;
    }

    $items = array();

    // 1) Répéteur sur la page Options : 'spec' avec sous-champs 'icone' et 'libelle'
    $specs_opt = get_field( 'spec', 'option' );
    if ( is_array( $specs_opt ) ) {
        foreach ( $specs_opt as $row ) {
            $label = isset( $row['libelle'] ) ? $row['libelle'] : '';
            $icon  = isset( $row['icone'] ) ? $row['icone'] : '';
            if ( $label || $icon ) {
                $items[] = array( 'icon' => $icon, 'label' => $label );
            }
        }
    }

    // 2) Champs product-level : groupes 'teste' et 'contenance' (chacun contient 'icone' et 'libelle')
    $groups = array( 'teste', 'contenance' );
    foreach ( $groups as $g ) {
        $grp = get_field( $g );
        if ( is_array( $grp ) ) {
            $raw_label = isset( $grp['libelle'] ) ? $grp['libelle'] : ( isset( $grp['label'] ) ? $grp['label'] : '' );
            $icon      = isset( $grp['icone'] ) ? $grp['icone'] : ( isset( $grp['icon'] ) ? $grp['icon'] : '' );

            // spécifique pour 'teste' : formater la phrase
            if ( $g === 'teste' && ! empty( $raw_label ) ) {
                $label = sprintf( /* translators: %s = valeur du test */ __( 'Testé sous contrôle %s', 'siklane' ), sanitize_text_field( $raw_label ) );
            } else {
                $label = $raw_label;
            }

            if ( $label || $icon ) {
                $items[] = array( 'icon' => $icon, 'label' => $label );
            }
        } else {
            // fallback si les champs sont non-groupés (ex: teste_libelle, teste_icone)
            $label_raw = get_field( $g . '_libelle' );
            $icon_fallback  = get_field( $g . '_icone' );

            if ( $g === 'teste' && ! empty( $label_raw ) ) {
                $label = sprintf( __( 'Testé sous contrôle %s', 'siklane' ), sanitize_text_field( $label_raw ) );
            } else {
                $label = $label_raw;
            }

            if ( $label || $icon_fallback ) {
                $items[] = array( 'icon' => $icon_fallback, 'label' => $label );
            }
        }
    }

    if ( empty( $items ) ) {
        return;
    }

    // helper pour afficher l'icone : gère image array, attachment ID ou url string
    $render_icon = function( $icon ) {
        if ( empty( $icon ) ) return '';
        if ( is_array( $icon ) && ! empty( $icon['url'] ) ) {
            return '<span class="spec-icon"><img src="' . esc_url( $icon['url'] ) . '" alt="" width="28" height="28" /></span>';
        }
        if ( is_numeric( $icon ) ) {
            return '<span class="spec-icon">' . wp_get_attachment_image( intval( $icon ), array(28,28), false, array( 'class' => 'img-fluid' ) ) . '</span>';
        }
        if ( is_string( $icon ) && filter_var( $icon, FILTER_VALIDATE_URL ) ) {
            return '<span class="spec-icon"><img src="' . esc_url( $icon ) . '" alt="" width="28" height="28" /></span>';
        }
        return '<span class="spec-icon">' . $icon . '</span>';
    };

    $partial = locate_template( 'woocommerce/single-product/specs.php' );
    if ( $partial ) {
        include $partial;
    } else {
        echo "<!-- siklane: partial woocommerce/single-product/specs.php not found - fallback output -->\n";
        echo '<div class="product-specs my-3" aria-hidden="false">';
        echo '<ul class="list-unstyled d-flex flex-column gap-2 mb-0">';
        foreach ( $items as $it ) {
            $label_html = is_array( $it['label'] ) ? ( esc_html( $it['label']['label'] ?? '' ) ) : esc_html( $it['label'] );
            $icon_html  = isset( $it['icon'] ) ? $render_icon( $it['icon'] ) : '';
            echo '<li class="spec-item d-flex align-items-center">';
            if ( $icon_html ) {
                echo wp_kses( $icon_html, array( 'span' => array( 'class' => array() ), 'img' => array( 'src' => array(), 'alt' => array(), 'width' => array(), 'height' => array(), 'class' => array() ) ) );
            }
            echo '<span class="spec-label ms-2 text-muted">' . $label_html . '</span>';
            echo '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }
}


/* Register five Siklane tabs in Product data (same as before) */
add_filter( 'woocommerce_product_data_tabs', function( $tabs ) {
    $tabs['siklane_desc']        = array( 'label' => __( 'Description', 'siklane' ), 'target' => 'siklane_desc_panel', 'class' => array( 'show_if_simple','show_if_variable' ), 'priority' => 70 );
    $tabs['siklane_ingredients'] = array( 'label' => __( 'Ingrédients', 'siklane' ), 'target' => 'siklane_ingredients_panel', 'class' => array( 'show_if_simple','show_if_variable' ), 'priority' => 72 );
    $tabs['siklane_usage']       = array( 'label' => __( 'Comment s\'en servir', 'siklane' ), 'target' => 'siklane_usage_panel', 'class' => array( 'show_if_simple','show_if_variable' ), 'priority' => 74 );
    $tabs['siklane_storage']     = array( 'label' => __( 'Conservation', 'siklane' ), 'target' => 'siklane_storage_panel', 'class' => array( 'show_if_simple','show_if_variable' ), 'priority' => 76 );
    $tabs['siklane_commitment']  = array( 'label' => __( 'Notre engagement', 'siklane' ), 'target' => 'siklane_commitment_panel', 'class' => array( 'show_if_simple','show_if_variable' ), 'priority' => 78 );
    return $tabs;
}, 20 );

/* Render panels: one wp_editor per panel (visual editor) */
add_action( 'woocommerce_product_data_panels', function() {
    global $post;
    $map = array(
        'siklane_desc'        => array( 'panel_id' => 'siklane_desc_panel',        'meta' => '_siklane_desc',        'title' => __( 'Description', 'siklane' ) ),
        'siklane_ingredients' => array( 'panel_id' => 'siklane_ingredients_panel', 'meta' => '_siklane_ingredients', 'title' => __( 'Ingrédients', 'siklane' ) ),
        'siklane_usage'       => array( 'panel_id' => 'siklane_usage_panel',       'meta' => '_siklane_usage',       'title' => __( 'Comment s\'en servir', 'siklane' ) ),
        'siklane_storage'     => array( 'panel_id' => 'siklane_storage_panel',     'meta' => '_siklane_storage',     'title' => __( 'Comment le conserver', 'siklane' ) ),
        'siklane_commitment'  => array( 'panel_id' => 'siklane_commitment_panel',  'meta' => '_siklane_commitment',  'title' => __( 'Notre engagement', 'siklane' ) ),
    );

    // settings: taille suffisante, toolbar utile pour les listes
    $editor_settings = array(
        'textarea_rows' => 16,
        'media_buttons' => false,
        'tinymce' => array(
            'wpautop' => true,
            'toolbar1' => 'formatselect bold italic | bullist numlist | link',
            'toolbar2' => '',
        ),
        'quicktags' => true,
    );

    foreach ( $map as $key => $cfg ) {
        $value = get_post_meta( $post->ID, $cfg['meta'], true );
        echo '<div id="' . esc_attr( $cfg['panel_id'] ) . '" class="panel woocommerce_options_panel">';
            echo '<div class="options_group">';
                echo '<p class="form-field"><label for="' . esc_attr( $key ) . '"><strong class="siklane-panel-title">' . esc_html( $cfg['title'] ) . '</strong></label></p>';
                wp_editor( wp_kses_post( $value ), $key, $editor_settings );
                echo '<p class="description">' . esc_html__( 'Contenu éditable pour cet onglet (listes supportées).', 'siklane' ) . '</p>';
            echo '</div>';
        echo '</div>';
    }
} );

/* Save editors content */
add_action( 'woocommerce_process_product_meta', function( $post_id ) {
    $keys = array(
        'siklane_desc'        => '_siklane_desc',
        'siklane_ingredients' => '_siklane_ingredients',
        'siklane_usage'       => '_siklane_usage',
        'siklane_storage'     => '_siklane_storage',
        'siklane_commitment'  => '_siklane_commitment',
    );
    foreach ( $keys as $field => $meta ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta( $post_id, $meta, wp_kses_post( wp_unslash( $_POST[ $field ] ) ) );
        }
    }
} );

/* Ensure WP editor assets are available on product edit screen */
add_action( 'admin_enqueue_scripts', function() {
    $screen = get_current_screen();
    if ( ! $screen || 'product' !== $screen->id ) return;
    wp_enqueue_editor();
}, 20 );

/* Admin CSS: garantir hauteur confortable des éditeurs sans casser le layout */
add_action( 'admin_head', function() {
    $screen = get_current_screen();
    if ( ! $screen || 'product' !== $screen->id ) return;
    ?>
    <style type="text/css">
    /* titre */
    .siklane-panel-title { font-size:16px; font-weight:600; display:block; margin-bottom:.35rem; }

    /* ciblage des wrappers générés par wp_editor */
    #wp-siklane_desc-wrap,
    #wp-siklane_ingredients-wrap,
    #wp-siklane_usage-wrap,
    #wp-siklane_storage-wrap,
    #wp-siklane_commitment-wrap {
      /* hauteur minimale visible dans le panel */
      min-height: 360px !important;
    }

    /* textarea visual and html modes */
    #wp-siklane_desc-wrap .wp-editor-area,
    #wp-siklane_ingredients-wrap .wp-editor-area,
    #wp-siklane_usage-wrap .wp-editor-area,
    #wp-siklane_storage-wrap .wp-editor-area,
    #wp-siklane_commitment-wrap .wp-editor-area {
      min-height: 320px !important;
      max-height: 60vh !important;
      height: auto !important;
      box-sizing: border-box !important;
    }

    /* TinyMCE iframe */
    #wp-siklane_desc-wrap iframe,
    #wp-siklane_ingredients-wrap iframe,
    #wp-siklane_usage-wrap iframe,
    #wp-siklane_storage-wrap iframe,
    #wp-siklane_commitment-wrap iframe {
      min-height: 320px !important;
      height: 100% !important;
      box-sizing: border-box !important;
    }
    </style>
    <?php
}, 20 );

/**
 * Remplace l'affichage de l'accordéon front par une version stylée avec classes Bootstrap-like,
 * titres en uppercase plus gros et icône à droite indiquant le déploiement.
 *
 * Utilise <details>/<summary> (accessible) et ajoute des classes Bootstrap-ish pour faciliter le styling.
 */

/* Retirer l'affichage des tabs standard (sécuriser si hook présent) */
add_action( 'wp', function() {
    if ( function_exists( 'remove_action' ) ) {
        remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
    }
}, 9 );





/* Integrated: accordion + full-width reviews slider (safely close/reopen theme wrappers) */
add_action( 'woocommerce_after_single_product_summary', function() {
    global $post;
    if ( ! $post || 'product' !== $post->post_type ) {
        return;
    }

    // --- ACCORDION (Bootstrap collapse markup) ---
    $map = array(
        'siklane_desc'        => __( 'Description', 'siklane' ),
        'siklane_ingredients' => __( 'Ingrédients', 'siklane' ),
        'siklane_usage'       => __( 'Comment s\'en servir', 'siklane' ),
        'siklane_storage'     => __( 'Comment le conserver', 'siklane' ),
        'siklane_commitment'  => __( 'Notre engagement', 'siklane' ),
    );

    $items = array();
    foreach ( $map as $key => $title ) {
        $content = get_post_meta( $post->ID, '_' . $key, true );
        if ( ! empty( $content ) ) {
            $items[] = array( 'id' => $key, 'title' => $title, 'content' => $content );
        }
    }

    if ( ! empty( $items ) ) {
        $accordion_id = 'siklane-accordion';
        // Parent container that wraps the accordion and its title
		echo'<div class="spacer-10"></div>';
        echo '<div class="container siklane-accordion-container">';
          echo '<div class="row">';
            echo '<div class="col-12 col-lg-9 mx-lg-auto">';
              echo '<h3 class="maj_title logo_h3_content mb-3">' . esc_html__( 'En savoir plus', 'siklane' ) . '</h3>';
              echo '<div class="accordion siklane-accordion" id="' . esc_attr( $accordion_id ) . '">';
                foreach ( $items as $it ) {
                    $san_id      = esc_attr( $it['id'] );
                    $collapse_id = 'siklaneCollapse-' . $san_id;
                    $heading_id  = 'siklaneHeading-' . $san_id;

                    echo '<div class="accordion-item siklane-accordion-item" id="item-' . $san_id . '">';
                        printf(
                            '<h2 class="accordion-header" id="%1$s"><button class="accordion-button collapsed siklane-accordion-summary" type="button" data-bs-toggle="collapse" data-bs-target="#%2$s" aria-expanded="false" aria-controls="%2$s"><span class="siklane-accordion-title">%3$s</span><i class="bi bi-arrow-down-circle siklane-accordion-icon" aria-hidden="true"></i></button></h2>',
                            esc_attr( $heading_id ),
                            esc_attr( $collapse_id ),
                            esc_html( $it['title'] )
                        );
                        printf(
                            '<div id="%1$s" class="accordion-collapse collapse" aria-labelledby="%2$s" data-bs-parent="#%3$s"><div class="accordion-body siklane-accordion-body">%4$s</div></div>',
                            esc_attr( $collapse_id ),
                            esc_attr( $heading_id ),
                            esc_attr( $accordion_id ),
                            wp_kses_post( wpautop( $it['content'] ) )
                        );
                    echo '</div>';
                }
              echo '</div>'; // .accordion
            echo '</div>'; // .col
          echo '</div>'; // .row
		echo '<div class="insta-background-logo section_product mt-4" aria-hidden="true">';
			echo '<img src="' . esc_url( get_template_directory_uri() . '/images/logo-loader.svg' ) . '" alt="' . esc_attr__( 'Logo background', 'siklane' ) . '" />';
		echo '</div>';
        echo '</div>'; // .container .siklane-accordion-container
		echo '<div class="spacer-10"></div>';

    }

    // --- CLOSE theme layout wrappers so slider can be full-width ---
    // Prevent double-closing if this runs multiple times
    if ( empty( $GLOBALS['siklane_closed_content_flag'] ) ) {
        // understrap wrapper_start opened: <div class="{container}" id="content"><div class="row">...<main id="main">
        echo '</main>';                    // close <main class="site-main" id="main">
        echo '</div><!-- .row -->';        // close <div class="row">
        echo '</div><!-- .container -->';  // close <div class="{container}" id="content">
        $GLOBALS['siklane_closed_content_flag'] = true;
    }

    // --- REVIEWS SLIDER (full-width container-fluid with velvet background) ---
    $all_comments = get_comments( array(
        'post_id' => $post->ID,
        'status'  => 'approve',
        'orderby' => 'comment_date_gmt',
        'order'   => 'DESC',
    ) );

    $reviews = array();
    foreach ( $all_comments as $c ) {
        if ( empty( trim( $c->comment_content ) ) ) {
            continue;
        }
        $rating = get_comment_meta( $c->comment_ID, 'rating', true );
        if ( '' === $rating ) {
            $rating = get_comment_meta( $c->comment_ID, '_rating', true );
        }
        $reviews[] = (object) array(
            'comment' => $c,
            'rating'  => ( $rating === '' ? 0 : intval( $rating ) ),
        );
    }

    if ( ! empty( $reviews ) ) {
        $chunks      = array_chunk( $reviews, 3 );
        $carousel_id = 'siklane-review-carousel-' . (int) $post->ID;
		echo '</div>'; // close previous .wrapper (from understrap_woocommerce_wrapper_start)
        echo '<div class="container-fluid siklane-reviews-bg py-5 my-5">'; // full-width velvet bg
          echo '<div class="container">';
            echo '<div class="siklane-reviews mb-5 bg-transparent">';
              echo '<h3 class="maj_title logo_h3_content mb-5 text-white">' . esc_html__( 'Avis clients', 'siklane' ) . '</h3>';
              echo '<div id="' . esc_attr( $carousel_id ) . '" class="carousel slide" data-bs-ride="carousel" data-bs-interval="10000" data-bs-pause="hover">';
                echo '<div class="carousel-inner">';
                  foreach ( $chunks as $idx => $group ) {
                      echo '<div class="carousel-item ' . ( $idx === 0 ? 'active' : '' ) . '">';
                        echo '<div class="row gx-3">';
                          foreach ( $group as $r ) {
                              $c = $r->comment;
                              $rating = max( 0, min( 5, intval( $r->rating ) ) );

                              echo '<div class="col-12 col-md-4">';
                                echo '<div class="card siklane-review-card h-100 border-0">';
                                  echo '<div class="card-body p-3">';

                                    // étoiles
                                    echo '<div class="siklane-review-stars mb-2" aria-hidden="true">';
                                    for ( $s = 1; $s <= 5; $s++ ) {
                                        if ( $s <= $rating ) {
                                            echo '<svg class="siklane-star filled me-1" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M12 .587l3.668 7.431 8.21 1.192-5.938 5.79 1.403 8.185L12 18.896l-7.343 3.99 1.403-8.185L.122 9.21l8.21-1.192z"/></svg>';
                                        } else {
                                            echo '<svg class="siklane-star outline me-1" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.62L12 2 9.19 8.62 2 9.24l5.46 4.73L5.82 21z"/></svg>';
                                        }
                                    }
                                    echo '</div>';

                                    // commentaire en blockquote + citation
                                    $content_html = wp_kses_post( wpautop( $c->comment_content ) );
                                    echo '<blockquote class="siklane-review-quote mb-2">' . $content_html . '<footer class="siklane-review-cite mt-2"><cite>' . esc_html( get_comment_author( $c ) ) . '</cite></footer></blockquote>';

                                  echo '</div>'; // .card-body
                                echo '</div>'; // .card
                              echo '</div>'; // .col
                          }
                        echo '</div>'; // .row
                      echo '</div>'; // .carousel-item
                  }
                echo '</div>'; // .carousel-inner

                if ( count( $chunks ) > 1 ) {
                    echo '<button class="carousel-control-prev" type="button" data-bs-target="#' . esc_attr( $carousel_id ) . '" data-bs-slide="prev" aria-label="' . esc_attr__( 'Précédent', 'siklane' ) . '">';
                      echo '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
                    echo '</button>';
                    echo '<button class="carousel-control-next" type="button" data-bs-target="#' . esc_attr( $carousel_id ) . '" data-bs-slide="next" aria-label="' . esc_attr__( 'Suivant', 'siklane' ) . '">';
                      echo '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
                    echo '</button>';
                }

              echo '</div>'; // .carousel
            echo '</div>'; // .siklane-reviews
          echo '</div>'; // .container
        echo '</div>'; // .container-fluid.siklane-reviews-bg
    }

    // --- REOPEN theme layout (container / row / left-sidebar / main) ---
    // only reopen if we closed earlier
    if ( ! empty( $GLOBALS['siklane_closed_content_flag'] ) ) {
        $container = get_theme_mod( 'understrap_container_type' );
        if ( false === $container || empty( $container ) ) {
            $container = 'container';
        }
        echo '<div class="' . esc_attr( $container ) . '" id="content" tabindex="-1">';
          echo '<div class="row">';
            get_template_part( 'global-templates/left-sidebar-check' );
            echo '<main class="site-main" id="main">';
        // keep flag true (will be closed by wrapper_end later)
    }
}, 11 );




