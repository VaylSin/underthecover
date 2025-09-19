<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Template override pour login / register :
 * - n'affiche qu'un seul formulaire à la fois
 * - registration shown only on ?show_register=1 or after POST register
 */

do_action( 'woocommerce_before_customer_login_form' );

$enable_registration = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
$show_registration   = false;

if ( $enable_registration ) {
    // show register when link clicked or after registration POST (validation)
    if ( isset( $_GET['show_register'] ) || isset( $_POST['register'] ) ) {
        $show_registration = true;
    }
}

// If user already logged, you may not want to show forms at all:
if ( is_user_logged_in() ) {
    do_action( 'woocommerce_after_customer_login_form' );
    return;
}
?>
<div class="spacer-10"></div>
<div class="container my-4">
    <?php if ( $enable_registration && $show_registration ) : ?>
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 login_form_container">
                <div class="">
                    <div class="card-body">
                        <h2 class="maj_title logo_h3_content justify-content-start card-title text-uppercase mb-3"><?php esc_html_e( 'Register', 'woocommerce' ); ?></h2>

                        <form method="post" class="woocommerce-form my-5 px-0" <?php do_action( 'woocommerce_register_form_tag' ); ?>>

                            <?php do_action( 'woocommerce_register_form_start' ); ?>

                            <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
                                <p class="mt-3 form-row-wide mb-3">
                                    <label for="reg_username" class="form-label"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
                                    <input type="text" class="form-control" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required />
                                </p>
                            <?php endif; ?>

                            <p class="mt-3 form-row-wide mb-3">
                                <label for="reg_email" class="form-label"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
                                <input type="email" class="form-control" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required />
                            </p>

                            <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
                                <p class="mt-3 form-row-wide mb-3">
                                    <label for="reg_password" class="form-label"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
                                    <input type="password" class="form-control" name="password" id="reg_password" autocomplete="new-password" required />
                                </p>
                            <?php else : ?>
                                <p class="mb-3"><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'woocommerce' ); ?></p>
                            <?php endif; ?>

                            <?php do_action( 'woocommerce_register_form' ); ?>

                            <p class="form-row mt-3">
                                <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
                                <button type="submit" class="view-all-link mb-4" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
                            </p>

                            <?php do_action( 'woocommerce_register_form_end' ); ?>

                        </form>

                        <p class="mt-3 mb-0 small">
                            <a class="text-velvet" href="<?php echo esc_url( remove_query_arg( 'show_register', wc_get_page_permalink( 'myaccount' ) ) ); ?>"><?php esc_html_e( 'Retour à la connexion', 'woocommerce' ); ?></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

    <?php else : ?>
        <!-- Afficher uniquement le formulaire de connexion (par défaut) -->
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 login_form_container">
                <div class="">
                    <div class="card-body">
                        <h2 class="maj_title logo_h3_content justify-content-start card-title text-uppercase mb-3"><?php esc_html_e( 'Login', 'woocommerce' ); ?></h2>

                        <form class="woocommerce-form my-5 px-0 " method="post" novalidate>
                            <?php do_action( 'woocommerce_login_form_start' ); ?>

                            <p class=" mb-3 form-row-wide mb-3">
                                <label for="username" class="form-label"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                                <input type="text" class="form-control" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required />
                            </p>

                            <p class=" mb-3 form-row-wide mb-3">
                                <label for="password" class="form-label"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                                <input class="form-control" type="password" name="password" id="password" autocomplete="current-password" required />
                            </p>

                            <?php do_action( 'woocommerce_login_form' ); ?>

                            <div class=" mt-3">
                                <div>
                                    <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
                                    <button type="submit" class="view-all-link mb-4" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
                                </div>
                                <a class="small text-velvet text-decoration-none text-muted" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
                            </div>

                            <p class=" mb-0 small">
                                <span><?php esc_html_e( 'Pas encore de compte ?', 'woocommerce' ); ?></span>
                                <a class="ms-2 text-velvet" href="<?php echo esc_url( add_query_arg( 'show_register', '1', wc_get_page_permalink( 'myaccount' ) ) . '#customer_register' ); ?>"><?php esc_html_e( 'Créer un compte', 'woocommerce' ); ?></a>
                            </p>

                            <?php do_action( 'woocommerce_login_form_end' ); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<div class="spacer-10"></div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
