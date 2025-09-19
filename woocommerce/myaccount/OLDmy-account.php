
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'woocommerce_before_my_account' );

$menu_items = wc_get_account_menu_items();
?>
<div class="spacer-10"></div>
<div class="container my-4">
  <div class="row">
    <aside class="col-12 col-md-3 mb-4">
      <nav class="siklane-account-nav" aria-label="<?php esc_attr_e( 'Navigation du compte', 'siklane' ); ?>">
        <?php foreach ( $menu_items as $endpoint => $label ) : ?>
          <a
            href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"
            class="siklane-account-nav-item h6 mb-0<?php echo is_wc_endpoint_url( $endpoint ) ? ' active' : ''; ?>"
            <?php echo is_wc_endpoint_url( $endpoint ) ? ' aria-current="true"' : ''; ?>
          >
            <?php echo esc_html( $label ); ?>
          </a>
        <?php endforeach; ?>
      </nav>
    </aside>

    <main class="col-12 col-md-9">
      <?php
      // Dashboard (default) ou endpoint actif
      if ( ! is_wc_endpoint_url() || is_wc_endpoint_url( 'dashboard' ) ) {
          do_action( 'woocommerce_account_dashboard' );
      } else {
          // Exécute le hook de l'endpoint actif pour obtenir le contenu correct
          foreach ( $menu_items as $endpoint => $label ) {
              if ( is_wc_endpoint_url( $endpoint ) ) {
                  do_action( 'woocommerce_account_' . $endpoint . '_endpoint' );
                  break;
              }
          }
      }
      ?>
    </main>
  </div>
</div>
<div class="spacer-10"></div>

<?php do_action( 'woocommerce_after_my_account' ); ?>
