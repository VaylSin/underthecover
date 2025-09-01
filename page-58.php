<?php

get_header();

if ( have_posts() ) :
  while ( have_posts() ) : the_post();

    // URL de la vignette
    $thumb_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
    $thumb_url = $thumb_url ? $thumb_url : get_template_directory_uri() . '/assets/img/default-banner.jpg';
    // ACF fields
    $pre_form = get_field( 'contenu_pre-formulaire' );
    $post_form = get_field( 'texte_post_formulaire' );
    ?>
    <!-- Bandeau full width -->
    <section class="page-banner w-100" style="background-image: url('<?php echo esc_url( $thumb_url ); ?>'); background-size: cover; background-position: center; height: 400px;"></section>

    <!-- Contenu principal -->
    <div class="container-1600 py-5">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-10">

          <!-- bloc : contenu_pre-formulaire -->
          <?php if ( $pre_form ) : ?>
            <section class="mb-4">
              <div class="card border-0">
                <div class="card-body pre_form">
                  <?php echo wp_kses_post( $pre_form ); ?>
                </div>
              </div>
            </section>
          <?php endif; ?>

          <!-- bloc : formulaire (Contact Form 7) -->
          <section class="mb-4">
            <div class="contact-form">
              <?php echo do_shortcode('[contact-form-7 id="4906df5" title="Formulaire de contact principal"]'); ?>
            </div>
          </section>

        </div>
      </div>
    </div>

    <!-- bloc : texte_post_formulaire déplacé en container-fluid > container-xxl -->
    <?php if ( $post_form ) : ?>
      <div class="container-fluid bg-velvet">
        <div class="container-xxl py-5">
          <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
              <section class="mb-4">
                <div class="p-4 rounded text-white">
                  <?php echo wp_kses_post( $post_form ); ?>
                </div>
              </section>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
<?php get_template_part('components/insta-block');?>

  <?php
  endwhile;
endif;

get_footer();
?>
