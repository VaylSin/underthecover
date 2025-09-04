<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;
global $product;

if ( post_password_required() ) {
    echo get_the_password_form();
    return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class(); ?>>

    <?php
        /**
         * Wrapper custom : .siklane-product-inner contient la galerie ET la summary
         * afin d'appliquer le layout flex uniquement à cette zone.
         */
    ?>
	<div class="siklane-product-inner my-5">
		<?php
			/**
			 * Hook : affiche la galerie (product-image.php)
			 */
			do_action( 'woocommerce_before_single_product_summary' );
		?>

		<div class="summary entry-summary col">
			<?php
				/**
				 * Hook : affiche title/price/excerpt/add-to-cart etc.
				 */
				do_action( 'woocommerce_single_product_summary' );
			?>
		</div>
	</div>

    <?php
        /**
         * Hook : other content (tabs, related, upsells)
         */
        do_action( 'woocommerce_after_single_product_summary' );
    ?>
</div>
<?php do_action( 'woocommerce_after_single_product' ); ?>
