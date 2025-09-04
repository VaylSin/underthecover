<?php
/**
 * Single Product title
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/title.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://woocommerce.com/document/template-structure/
 * @package    WooCommerce\Templates
 * @version    1.6.4
 */

defined( 'ABSPATH' ) || exit;
global $product;
if ( ! $product ) return;

$price_html = $product->is_on_sale() && $product->get_sale_price() !== '' ? wc_price( $product->get_sale_price() ) : $product->get_price_html();

// prix initial (regular) si en promo
$regular_html = '';
if ( $product->is_on_sale() && $product->get_regular_price() !== '' ) {
    $regular_html = wc_price( $product->get_regular_price() );
}
?>
<div class="siklane-product-title-price d-flex align-items-center justify-content-between">
    <h1 class="text-uppercase fw-bold product_title entry-title mb-0"><?php echo esc_html( get_the_title() ); ?></h1>

    <div class="h2 fw-bold product-price d-flex mb-0 gap-2">
        <?php if ( $regular_html ) : ?>
            <small class="product-price-regular text-muted text-decoration-line-through d-flex fs-6 opacity-75 m-0" aria-hidden="true">
                <?php echo wp_kses_post( $regular_html ); ?>
            </small>
        <?php endif; ?>

        <span class="product-price-current d-flex">
            <?php echo wp_kses_post( $price_html ); ?>
        </span>
    </div>
</div>
