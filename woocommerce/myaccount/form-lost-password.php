<?php
/**
 * Lost password form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-lost-password.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.2.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_lost_password_form' );
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">

                <div class="text-center mb-4">
                    <h2 class="h3 fw-bold text-uppercase mb-3">Mot de passe oublié</h2>
                    <p class="text-muted mb-0">
                        <?php echo apply_filters( 'woocommerce_lost_password_message', esc_html__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce' ) ); ?>
                    </p>
                </div>

                <form method="post" class="woocommerce-ResetPassword lost_reset_password">

                    <div class="mb-4">
                        <label for="user_login" class="form-label fw-semibold">
                            <?php esc_html_e( 'Username or email', 'woocommerce' ); ?>
                            <span class="required text-danger ms-1" aria-hidden="true">*</span>
                            <span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span>
                        </label>
                        <input
                            class="form-control woocommerce-Input woocommerce-Input--text input-text"
                            type="text"
                            name="user_login"
                            id="user_login"
                            autocomplete="username"
                            required
                            aria-required="true"
                            placeholder="<?php esc_attr_e( 'Enter your username or email', 'woocommerce' ); ?>"
                        />
                    </div>

                    <?php do_action( 'woocommerce_lostpassword_form' ); ?>

                    <div class="d-grid gap-2 mb-4">
                        <input type="hidden" name="wc_reset_password" value="true" />
                        <button type="submit" class="btn view-all-link fw-bold py-3 text-uppercase">
                            <?php esc_html_e( 'Reset password', 'woocommerce' ); ?>
                        </button>
                    </div>

                    <div class="text-center">
                        <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="text-decoration-none">
                            <i class="bi bi-arrow-left me-2"></i>
                            <?php esc_html_e( 'Back to login', 'woocommerce' ); ?>
                        </a>
                    </div>

                    <?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>

                </form>

            </div>
        </div>
    </div>
</div>
<?php
do_action( 'woocommerce_after_lost_password_form' );
