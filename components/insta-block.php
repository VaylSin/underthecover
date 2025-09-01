<section class="insta_flux">
    <div class="container-xxl px-lg-5rem text-center ">
        <h3 class="maj_title mb-5 logo_before d-flex justify-content-center" ><span class="logo_h3_content "><?php echo esc_html(get_field('titre_instagram', 'option')); ?></span></h3>

        <?php if ( have_rows('publication', 'options') ) : ?>
            <div class="row d-flex insta-grid">
                <?php
                while ( have_rows('publication', 'option') ) : the_row();


                    $image = get_sub_field('image_publication', 'option');
                    $link = get_sub_field('lien_publication', 'option');

                    // normaliser l'URL du lien
                    $link_url = '';
                    if ( is_array($link) || is_object($link)  ) {
                        $link_url = get_permalink( is_array($link) ? $link[0] : $link );
                    } elseif ( ! empty($link) ) {
                        $link_url = $link;
                    }

                    $image_url = '';
                    $image_alt = '';
                    if ( $image ) {
                        if ( is_array($image) ) {
                            $image_url = $image['url'] ?? '';
                            $image_alt = $image['alt'] ?? '';
                        } else {
                            $image_url = $image;
                        }
                    }
                ?>
                    <div class="col-6 col-sm-4 col-md-3">
                        <?php if ( $link_url ) : ?>
                            <a href="<?php echo esc_url( $link_url ); ?>"
                               class="d-block insta-item">
                                <?php if ( $image_url ) : ?>
                                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" class="img-fluid w-100">
                                <?php endif; ?>
                            </a>
                        <?php else : ?>
                            <div class="d-block insta-item">
                                <?php if ( $image_url ) : ?>
                                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" class="img-fluid w-100">
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                <?php
			endwhile; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Logo background en bas à droite -->
    <div class="insta-background-logo" >
        <img src="<?php echo get_template_directory_uri(); ?>/images/utc-logomark-blanc.svg" alt="Logo background" />
    </div>
</section>
