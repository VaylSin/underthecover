<?php

get_header();

if ( have_posts() ) :
  while ( have_posts() ) : the_post();

    // URL de la vignette (fallback si absent)
    $thumb_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
    $thumb_url = $thumb_url ? $thumb_url : get_template_directory_uri() . '/assets/img/default-banner.jpg';

    // inline style sûr pour le background avec attachment fixed
    $bg_style = "background-image: url('" . esc_url( $thumb_url ) . "'); background-size: cover; background-position: center; background-attachment: fixed; background-repeat: no-repeat; height: 400px; position: relative; overflow: hidden;";
    ?>
    <!-- Bandeau full width avec overlay et titre -->
    <section class="page-banner w-100" style="<?php echo esc_attr( $bg_style ); ?>">
      <!-- overlay couvrant toute la section -->
      <div class="banner-overlay" style="position: absolute; inset: 0; background: rgba(188,51,101,0.5); z-index: 1;"></div>

      <!-- Contenu centré verticalement et aligné à gauche, contenu contraint par container-1600 -->
      <div class="container-xxl h-100 position-relative page-header-banner-content">
        <div class="row h-100">
          <div class="col-12 d-flex align-items-center justify-content-start">
            <h1 class="page-title text-white fw-bold mb-0" style="font-weight: 600;">
              <?php echo esc_html( get_the_title() ); ?>
            </h1>
          </div>
        </div>
      </div>
    </section>

    <!-- Contenu principal -->
    <div class="container-1600 py-5 position-relative" data-aos="fade-up" data-aos-duration="1000">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-10">

          <!-- bloc : contenu_pre-formulaire -->
          <?php
          $pre_form = get_field( 'contenu_pre-formulaire' );
          if ( $pre_form ) : ?>
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
			<div class="section_contact insta-background-logo">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/images/logo-loader.svg' ); ?>" alt="Logo SilkLane" >
				</div>
    </div>

    <!-- bloc : texte_post_formulaire déplacé en container-fluid > container-xxl -->
    <?php
    $post_form = get_field( 'texte_post_formulaire' );
    if ( $post_form ) : ?>
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
