<?php
/**
 * Simple product add to cart (override)
 *
 * Version : adaptée pour Bootstrap 5
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product->is_purchasable() ) {
    return;
}

echo wc_get_stock_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

if ( $product->is_in_stock() ) : ?>

    <?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

    <form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
        <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

        <?php
        $min_qty = apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product );
        $max_qty = apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product );
        $input_value = isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity();

        // prix unitaire pour calcul total JS
        $unit_price = (float) $product->get_price();
        $initial_total = wc_price( $unit_price * (int) $input_value );
        ?>

        <?php do_action( 'woocommerce_before_add_to_cart_quantity' ); ?>

        <div class="mb-2 d-flex justify-content-center">
            <label class="form-label visually-hidden" for="quantity_<?php echo esc_attr( $product->get_id() ); ?>"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></label>

            <div class="d-flex increment_container align-items-center gap-2 input-group input-group-lg w-100">
                <span class=" btn-decrement" aria-label="<?php esc_attr_e( 'Decrease quantity', 'woocommerce' ); ?>"><i class="bi bi-dash-circle"></i></span>

                <input
                    type="number"
                    id="quantity_<?php echo esc_attr( $product->get_id() ); ?>"
                    class="border-0 p-0 form-control text-center"
                    step="1"
                    min="<?php echo esc_attr( $min_qty ); ?>"
                    max="<?php echo esc_attr( $max_qty ); ?>"
                    name="quantity"
                    value="<?php echo esc_attr( $input_value ); ?>"
                    aria-label="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>"
                    pattern="[0-9]*"
                    inputmode="numeric"
                />

                <span class=" btn-increment" aria-label="<?php esc_attr_e( 'Increase quantity', 'woocommerce' ); ?>"><i class="bi bi-plus-circle-fill"></i></span>
            </div>
        </div>

        <?php do_action( 'woocommerce_after_add_to_cart_quantity' ); ?>

        <button
            type="submit"
            name="add-to-cart"
            value="<?php echo esc_attr( $product->get_id() ); ?>"
            class="view-all-link w-100 d-flex align-items-center justify-content-center"
            style="gap:.5rem;"
        >
            <span class="siklane-add-label"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></span>
            <span aria-hidden="true">—</span>
            <span class="siklane-total-price"><?php echo wp_kses_post( $initial_total ); ?></span>
        </button>

        <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
    </form>

    <?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

    <script type="text/javascript">
    (function () {
        // valeurs injectées côté PHP
        var productId = <?php echo json_encode( (int) $product->get_id() ); ?>;
        var unitPrice = parseFloat(<?php echo json_encode( (float) $unit_price ); ?>) || 0;
        var minQty = parseInt(<?php echo json_encode( (int) $min_qty ); ?>, 10) || 1;
        var maxQty = parseInt(<?php echo json_encode( (int) $max_qty ); ?>, 10) || 9999;
        var currency = <?php echo json_encode( html_entity_decode( get_woocommerce_currency_symbol(), ENT_QUOTES ) ); ?>;

        // récupère directement l'input via son id (robuste même si script n'est pas dans le form)
        var qtyInput = document.getElementById('quantity_' + productId);
        if (!qtyInput) {
            console.warn('siklane qty: input not found for product', productId);
            return;
        }
        var form = qtyInput.closest('form');
        if (!form) {
            console.warn('siklane qty: form not found for product', productId);
            return;
        }

        var btnMinus = form.querySelector('.btn-decrement');
        var btnPlus  = form.querySelector('.btn-increment');
        var totalEl  = form.querySelector('.siklane-total-price');

        function formatPrice(n) {
            if (typeof Intl === 'object' && typeof Intl.NumberFormat === 'function') {
                try {
                    return new Intl.NumberFormat(navigator.language || 'fr-FR', { style: 'currency', currency: '<?php echo esc_js( get_woocommerce_currency() ); ?>' }).format(n);
                } catch (e) {}
            }
            return currency + n.toFixed(2);
        }

        function sanitizeQty(v) {
            v = parseInt(v, 10);
            if (isNaN(v) || v < minQty) v = minQty;
            if (typeof maxQty === 'number' && maxQty > 0 && v > maxQty) v = maxQty;
            return v;
        }

        function updateTotalFromInput() {
            var q = sanitizeQty(qtyInput.value);
            // MAJ valeur sans retriggering
            if (String(qtyInput.value) !== String(q)) qtyInput.value = q;
            var total = (parseFloat(unitPrice) || 0) * q;
            if (totalEl) totalEl.textContent = formatPrice(total);
        }

        // boutons +/- : protéger contre focus submit et faire update
        if (btnMinus) btnMinus.addEventListener('click', function (e) {
            e.preventDefault();
            var v = sanitizeQty((parseInt(qtyInput.value || minQty, 10) - 1));
            qtyInput.value = v;
            updateTotalFromInput();
        });
        if (btnPlus) btnPlus.addEventListener('click', function (e) {
            e.preventDefault();
            var v = sanitizeQty((parseInt(qtyInput.value || minQty, 10) + 1));
            qtyInput.value = v;
            updateTotalFromInput();
        });

        // écoute changes manuels (input / paste)
        qtyInput.addEventListener('input', function () {
            // throttle léger pour éviter spam
            if (qtyInput._siklane_timer) clearTimeout(qtyInput._siklane_timer);
            qtyInput._siklane_timer = setTimeout(function () {
                updateTotalFromInput();
                qtyInput._siklane_timer = null;
            }, 60);
        });

        // initial update
        setTimeout(updateTotalFromInput, 20);

        // si d'autres scripts (variations) modifient unitPrice, exposer setter via event custom
        document.addEventListener('siklane:variation-price-updated', function (e) {
            if (e && e.detail && typeof e.detail.unitPrice !== 'undefined') {
                unitPrice = parseFloat(e.detail.unitPrice) || unitPrice;
                updateTotalFromInput();
            }
        }, false);

    })();
    </script>
<?php endif;
