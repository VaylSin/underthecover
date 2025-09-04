<?php
/**
 * Partial : affiche les specs en grille 2x2.
 * Attend un tableau $items fourni par le hook (chaque item = ['icon'=>..., 'label'=>...])
 */
if ( empty( $items ) || ! is_array( $items ) ) {
    return;
}

// prendre les 4 premiers et compléter si nécessaire pour garder la grille 2x2
$grid = array_slice( $items, 0, 4 );
while ( count( $grid ) < 4 ) {
    $grid[] = array( 'icon' => '', 'label' => '' );
}

// helper local pour l'icone (gère array image, ID, url ou SVG)
$render_icon = function( $icon ) {
    if ( empty( $icon ) ) return '';
    if ( is_array( $icon ) && ! empty( $icon['url'] ) ) {
        return '<span class="spec-icon"><img src="' . esc_url( $icon['url'] ) . '" alt="" /></span>';
    }
    if ( is_numeric( $icon ) ) {
        return '<span class="spec-icon">' . wp_get_attachment_image( intval( $icon ), array(36,36), false, array( 'class' => 'spec-icon-img' ) ) . '</span>';
    }
    if ( is_string( $icon ) && filter_var( $icon, FILTER_VALIDATE_URL ) ) {
        return '<span class="spec-icon"><img src="' . esc_url( $icon ) . '" alt="" /></span>';
    }
    // raw markup (ex: inline SVG)
    return '<span class="spec-icon">' . $icon . '</span>';
};
?>
<div class="product-specs-grid my-4" aria-hidden="false">
    <ul class="specs-grid list-unstyled mb-0">
        <?php foreach ( $grid as $it ) : ?>
            <li class="spec-item d-flex align-items-center">
                <?php
                    echo wp_kses_post( $render_icon( $it['icon'] ?? '' ) );
                ?>
                <span class="spec-label ms-3"><?php echo esc_html( $it['label'] ?? '' ); ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
