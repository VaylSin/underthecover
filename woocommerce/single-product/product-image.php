<?php
defined( 'ABSPATH' ) || exit;
global $product;
if ( ! $product ) return;

$post_thumbnail_id = $product->get_image_id();
$gallery_ids = $product->get_gallery_image_ids();
$all_ids = array();
if ( $post_thumbnail_id ) $all_ids[] = $post_thumbnail_id;
if ( is_array( $gallery_ids ) ) {
    foreach ( $gallery_ids as $id ) {
        if ( $id && $id !== $post_thumbnail_id ) $all_ids[] = $id;
    }
}
if ( empty( $all_ids ) ) $all_ids[] = false;
?>
<div class="woocommerce-product-gallery siklane-swiper-gallery">

    <div class="siklane-swiper-container siklane-swiper-thumbs swiper" aria-hidden="true">
        <div class="swiper-wrapper">
            <?php foreach ( $all_ids as $index => $aid ) :
                if ( ! $aid ) : ?>
                    <div class="swiper-slide" data-index="<?php echo esc_attr( $index ); ?>" data-siklane-index="<?php echo esc_attr( $index ); ?>">
                        <img data-siklane-index="<?php echo esc_attr( $index ); ?>" src="<?php echo esc_url( wc_placeholder_img_src( 'woocommerce_gallery_thumbnail' ) ); ?>" alt="" />
                    </div>
                <?php else :
                    $thumb = wp_get_attachment_image_url( $aid, 'woocommerce_gallery_thumbnail' ); ?>
                    <div class="swiper-slide" data-index="<?php echo esc_attr( $index ); ?>" data-siklane-index="<?php echo esc_attr( $index ); ?>">
                        <img data-siklane-index="<?php echo esc_attr( $index ); ?>" src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( get_post_meta( $aid, '_wp_attachment_image_alt', true ) ?: '' ); ?>" />
                    </div>
                <?php endif;
            endforeach; ?>
        </div>
    </div>

    <div class="siklane-swiper-container siklane-swiper-main swiper">
        <div class="swiper-wrapper">
            <?php foreach ( $all_ids as $index => $aid ) :
                if ( ! $aid ) : ?>
                    <div class="swiper-slide" data-index="<?php echo esc_attr( $index ); ?>" data-siklane-index="<?php echo esc_attr( $index ); ?>">
                        <img data-siklane-index="<?php echo esc_attr( $index ); ?>" src="<?php echo esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ); ?>" alt="<?php esc_attr_e( 'Placeholder', 'woocommerce' ); ?>" />
                    </div>
                <?php else : ?>
                    <div class="swiper-slide" data-index="<?php echo esc_attr( $index ); ?>" data-siklane-index="<?php echo esc_attr( $index ); ?>">
                        <?php
                        echo wp_get_attachment_image( $aid, 'siklane_square', false, array(
                          'class' => 'wp-post-image',
                          'data-large' => wp_get_attachment_url( $aid ),
                          'data-siklane-index' => $index,
                          'loading' => 'eager'
                        ) );
                        ?>
                    </div>
                <?php endif;
            endforeach; ?>
        </div>

        <button class="siklane-slide-prev" aria-label="Précédent"></button>
        <button class="siklane-slide-next" aria-label="Suivant"></button>
    </div>

</div>
