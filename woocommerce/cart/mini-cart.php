<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' ); ?>

<?php if ( WC()->cart && ! WC()->cart->is_empty() ) : ?>

    <div class="mini-cart-inner"> <!-- <-- wrapper ajouté -->
        <ul class="woocommerce-mini-cart cart_list product_list_widget p-0 <?php echo esc_attr( $args['list_class'] ); ?>">
            <?php
            do_action( 'woocommerce_before_mini_cart_contents' );

            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) : ?>
    <?php
        $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
        $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

        if ( ! $_product || ! $_product->exists() || ! isset( $cart_item['quantity'] ) ) {
            continue;
        }

        $thumbnail        = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
        $product_name     = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
        $product_price    = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
        $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
    ?>
    <li class="<?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">
        <div class="mini-cart-row d-flex justify-content-between" aria-hidden="false">
            <div class="mini-cart-thumb">
                <?php if ( $product_permalink ) : ?>
                    <a href="<?php echo esc_url( $product_permalink ); ?>"><?php echo $thumbnail; ?></a>
                <?php else : ?>
                    <?php echo $thumbnail; ?>
                <?php endif; ?>
            </div>

            <div class="mini-cart-meta d-flex flex-column align-items-end">
                <div class="product-name">
                    <?php if ( $product_permalink ) : ?>
                        <a class="text-uppercase fw-bold"href="<?php echo esc_url( $product_permalink ); ?>">
                            <?php echo wp_kses_post( $product_name ); ?>
                            <?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], '' ) . '</span>', $cart_item, $cart_item_key ); ?>

                        </a>
                    <?php else : ?>
                        <?php echo wp_kses_post( $product_name ); ?>
						<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], '' ) . '</span>', $cart_item, $cart_item_key ); ?>

                    <?php endif; ?>
                </div>

                <div class="product-price">
                    <?php
                    // afficher le coût total de la ligne (prix * quantité) formaté par WooCommerce
                    $line_total = WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] );
                    echo wp_kses_post( $line_total );
                    ?>
                </div>
            </div>
        </div>

        <?php
        // quantity / remove link (laisser ou adapter selon tes besoins)
        ?>
    </li>
<?php endforeach; ?>

            <?php
            do_action( 'woocommerce_mini_cart_contents' );
            ?>
        </ul>



    </div> <!-- /.mini-cart-inner -->

<?php else : ?>

    <p class="woocommerce-mini-cart__empty-message"><?php esc_html_e( 'No products in the cart.', 'woocommerce' ); ?></p>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
