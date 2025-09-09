<div class="cart-drawer" id="cartDrawer" aria-hidden="true" role="dialog" aria-label="<?php esc_attr_e('Panier','siklane'); ?>">
  <div class="cart-drawer-backdrop" data-cart-drawer-close></div>
  <aside class="cart-drawer-panel justify-content-between" role="document">
    <?php
    // récupération du nombre d'articles pour affichage à côté du titre
    $cart_count = ( function_exists('WC') && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
    $cart_count_label = sprintf(
      _n( '%s article', '%s articles', $cart_count, 'siklane' ),
      absint( $cart_count )
    );
    ?>
    <div class=" drawer-header d-flex justify-content-between align-items-center">
      <h3 class="text-velvet"><?php esc_html_e('Votre panier','siklane'); ?></h3>
      <span class="cart-count small text-muted"><?php echo esc_html( $cart_count_label ); ?></span>
    </div>
    <div class="drawer-content">
      <?php
      // contenu actualisé via woocommerce_mini_cart()
      // capture pour permettre update par fragments
      ob_start();

      //*Path :  woocommerce/cart/mini-cart./php pour l'overide
      woocommerce_mini_cart();
      echo ob_get_clean();
      ?>
    </div>
    <div class="drawer-actions mt-3 d-flex flex-column gap-2">
			<?php
        // Calculs / affichages personnalisés : sous-total, frais de livraison, total (articles + fdp)
        $subtotal_html = WC()->cart->get_cart_subtotal(); // HTML formaté par WooCommerce
        $shipping_total = (float) WC()->cart->get_shipping_total(); // float
        $shipping_html = wc_price( $shipping_total );

        // WC()->cart->get_subtotal() renvoie le subtotal en float (hors taxes, sans formattage)
        $subtotal_float = (float) WC()->cart->get_subtotal();
        $total_with_shipping_html = wc_price( $subtotal_float + $shipping_total );
        ?>
		<div class="woocommerce-mini-cart__totals">
            <p class="mini-cart mini-cart-subtotal d-flex align-items-center justify-content-between">
                <span class="label">Sous-total</span><br />
                <strong class="value"><?php echo wp_kses_post( $subtotal_html ); ?></strong>
            </p>
            <hr>
            <p class="mini-cart mini-cart-shipping d-flex align-items-center justify-content-between">
                <span class="label">Frais de livraison</span><br />
                <strong class="value"><?php echo wp_kses_post( $shipping_html ); ?></strong>
            </p>
            <hr>
            <p class="mini-cart mini-cart-total d-flex align-items-center justify-content-between">
                <span class="label">Total</span><br />
                <strong class="value"><?php echo wp_kses_post( $total_with_shipping_html ); ?></strong>
            </p>
        </div>

        <!-- <?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

        <p class="woocommerce-mini-cart__buttons buttons"><?php do_action( 'woocommerce_widget_shopping_cart_buttons' ); ?></p>

        <?php do_action( 'woocommerce_widget_shopping_cart_after_buttons' ); ?> -->
      <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="btn view-all-link"><?php esc_html_e('Voir le panier','siklane'); ?></a>
      <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="view-all-link"><?php esc_html_e('Commander','siklane'); ?></a>
    </div>
  </aside>
</div>
