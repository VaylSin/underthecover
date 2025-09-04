<?php
defined( 'ABSPATH' ) || exit;
$tabs = apply_filters( 'woocommerce_product_tabs', array() );
if ( empty( $tabs ) ) return;
?>
<div class="product-tabs-accordion siklane-accordion" id="siklaneProductAccordion">
    <?php $i = 0; foreach ( $tabs as $key => $tab ) : $i++; ?>
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading-<?php echo esc_attr( $key ); ?>">
                <button class="accordion-button <?php echo $i>1 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo esc_attr( $key ); ?>" aria-expanded="<?php echo $i===1 ? 'true' : 'false'; ?>" aria-controls="collapse-<?php echo esc_attr( $key ); ?>">
                    <?php echo wp_kses_post( $tab['title'] ); ?>
                </button>
            </h2>
            <div id="collapse-<?php echo esc_attr( $key ); ?>" class="accordion-collapse collapse <?php echo $i===1 ? 'show' : ''; ?>" aria-labelledby="heading-<?php echo esc_attr( $key ); ?>" data-bs-parent="#siklaneProductAccordion">
                <div class="accordion-body">
                    <?php
                        if ( is_callable( $tab['callback'] ) ) {
                            call_user_func( $tab['callback'], $key, $tab );
                        } else {
                            echo wp_kses_post( $tab['content'] ?? '' );
                        }
                    ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
