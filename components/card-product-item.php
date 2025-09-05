<?php
// $args is provided by get_template_part(..., $args)
// fallbacks
$product      = isset( $args['product'] ) ? $args['product'] : ( isset( $product ) ? $product : null );
$post_id      = isset( $args['post_id'] ) ? (int) $args['post_id'] : get_the_ID();
$permalink    = isset( $args['permalink'] ) ? esc_url( $args['permalink'] ) : get_permalink( $post_id );
$thumbnail    = isset( $args['thumbnail'] ) ? $args['thumbnail'] : get_the_post_thumbnail( $post_id, 'large', array(
                    'class' => 'card-img-top object-fit-cover',
                    'style' => 'aspect-ratio:1/1;object-fit:cover;width:100%;'
                ) );
$price_html   = isset( $args['price_html'] ) ? $args['price_html'] : ( $product ? $product->get_price_html() : '' );
$avg_rating   = isset( $args['avg_rating'] ) ? $args['avg_rating'] : ( $product ? $product->get_average_rating() : 0 );
$rating_count = isset( $args['rating_count'] ) ? $args['rating_count'] : ( $product ? $product->get_rating_count() : 0 );
?>
<div class="card product_item">
    <a href="<?php echo $permalink; ?>" class="text-decoration-none text-dark">
        <div class="img-container overflow-hidden position-relative">
            <?php echo $thumbnail; ?>

            <div class="btn-overlay">
                <span class="logo-bg-overlay">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/images/utc-logomark-blanc.svg' ); ?>" alt="Logo UTC" />
                </span>
                <button class="btn py-2 px-4 text-uppercase font-weight-bolder">
                    Voir le produit
                </button>
            </div>
        </div>

        <div class="card-body card_infos d-flex flex-column gap-2 ">
            <h3 class="card-title h6 mb-0"><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>

            <?php if ( $product && is_object( $product ) ) : ?>
                <div class="d-flex align-items-center justify-content-between w-100">
                    <p class="price mb-0"><?php echo wp_kses_post( $price_html ); ?></p>

                    <div class="product-rating">
                        <?php
                        // reuse theme helper if exists
                        if ( function_exists( 'silklane_get_star_rating_html' ) ) {
                            echo silklane_get_star_rating_html( $avg_rating, $rating_count );
                        } else {
                            // simple fallback: stars based on avg_rating
                            $stars = intval( round( $avg_rating ) );
                            for ( $i = 1; $i <= 5; $i++ ) {
                                if ( $i <= $stars ) {
                                    echo '<svg class="bi-star-fill me-1" width="14" height="14" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.283.95l-2.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/></svg>';
                                } else {
                                    echo '<svg class="bi-star me-1" width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" aria-hidden="true"><path d="M2.866 14.85c-.078.444.36.791.746.593L8 13.187l4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.523-3.356c.329-.32.158-.888-.283-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.119l-4.898.696c-.441.062-.612.63-.283.95l3.523 3.356-.83 4.73z"/></svg>';
                                }
                            }
                        }
                        ?>
                        <span class="rating-count small text-muted">(<?php echo (int) $rating_count; ?>)</span>
                    </div>
                </div>

                <form method="post" class="add-to-cart-form mt-2">
                    <input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product ? $product->get_id() : $post_id ); ?>">
                    <button type="submit" class="view-all-link d-flex justify-content-center align-items-center w-100">
                        ajouter au panier&nbsp;&nbsp;<i class="bi bi-bag-heart"></i>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </a>
</div>
