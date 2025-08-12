<?php
get_header();
?>
<section class="slider_container">
<?php if( have_rows('slider') ) : ?>
	<!-- data-bs-interval="5000"-->
<div id="main-slider" class="carousel slide carousel-fade fullscreen-slider" data-bs-ride="carousel" >
  <div class="carousel-inner">
    <?php $i = 0; while( have_rows('slider') ) : the_row();
      $image = get_sub_field('image');
	  $titre_slide = get_sub_field('titre_du_slide');
      $phrase = get_sub_field('phrase_daccroche');
      $lien = get_sub_field('lien_fiche_produit');
      $texte_bouton = get_sub_field('texte_bouton_lien');
    ?>
    <div class="carousel-item<?php if($i == 0) echo ' active'; ?>">
      <?php if($image): ?>
        <img src="<?php echo esc_url($image['url']); ?>" class="d-block w-100 object-fit-cover" alt="<?php echo esc_attr($image['alt']); ?>">
      <?php endif; ?>
      <div class="carousel-caption d-flex flex-column justify-content-center align-items-start top-50 start-50 translate-middle" style="transform: translate(-50%, -50%);">
        <?php if($titre_slide): ?>
          <h2 class="text-start"><?php echo esc_html($titre_slide); ?></h2>
        <?php endif; ?>
        <?php if($phrase): ?>
          <p class="text-start"><?php echo esc_html($phrase); ?></p>
        <?php endif; ?>
        <?php
        // Si c'est un champ URL, $lien est déjà une string
        // Si c'est un Post Object, il faut récupérer le permalien
        if ($lien && $texte_bouton) {
            // Si $lien est un tableau ou un objet (Post Object)
            if (is_array($lien) || is_object($lien)) {
                $url = get_permalink(is_array($lien) ? $lien[0] : $lien);
            } else {
                $url = $lien;
            }
            ?>
            <a href="<?php echo esc_url($url); ?>" class="btn btn-bloom py-2 px-4">
                <?php echo esc_html($texte_bouton); ?>
            </a>
        <?php } ?>
      </div>
    </div>
    <?php $i++; endwhile; ?>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#main-slider" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
    <span class="visually-hidden">Précédent</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#main-slider" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
    <span class="visually-hidden">Suivant</span>
  </button>
</div>
<?php endif; ?>
</section>
<div class="spacer-10"></div>
<section class="best_sellers container-xxl">
	<h3 class="text-center mb-4">Nos Best Sellers</h3>
    <div class="row g-4">
        <?php
        // Query pour les 3 meilleures ventes
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => 3,
            'meta_key'       => 'total_sales',
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
        );
        $best_sellers = new WP_Query($args);
        if ($best_sellers->have_posts()) :
            while ($best_sellers->have_posts()) : $best_sellers->the_post();
                global $product;
                ?>
                <div class="col-12 col-md-4">
                    <div class="card product_item">
                        <a href="<?php the_permalink(); ?>" class="text-decoration-none text-dark">
                            <div class="img-container overflow-hidden position-relative">
                                <?php the_post_thumbnail('large', [
                                    'class' => 'card-img-top object-fit-cover',
                                    'style' => 'aspect-ratio:1/1;object-fit:cover;width:100%;'
                                ]); ?>

                                <!-- Bouton qui apparaîtra au survol -->
                                <div class="btn-overlay">
                                    <button class="btn btn-bloom py-2 px-4 text-uppercase font-weight-bold">Voir le produit</button>
                                </div>
                            </div>

                            <div class="card-body d-flex flex-column align-items-center gap-3">
                                <h3 class="card-title h5 mb-0"><?php the_title(); ?></h3>

                                <!-- Description courte -->
                                <?php if (isset($product) && is_object($product) && method_exists($product, 'get_short_description')) : ?>
                                    <p class="card-text mb-0 text-center small">
                                        <?php
                                        $short_description = $product->get_short_description();
                                        echo wp_trim_words($short_description, 15, '...');
                                        ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (isset($product) && is_object($product)) : ?>
                                    <p class="price mb-0"><?php echo $product->get_price_html(); ?></p>

                                    <!-- Ajout des étoiles et du nombre d'avis -->
                                    <div class="product-rating d-flex align-items-center gap-2">
                                        <?php echo silklane_get_star_rating_html($product->get_average_rating(), $product->get_rating_count()); ?>
                                        <span class="rating-count small text-muted">(<?php echo $product->get_rating_count(); ?>)</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                </div>
                <?php
            endwhile;
            wp_reset_postdata();
        endif;
        ?>
    </div>

    <!-- Lien "Voir tous les produits" -->
    <div class="text-center mt-5">
        <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="view-all-link">
            tous les produits
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
            </svg>
        </a>
    </div>
</section>
<div class="spacer-10"></div>

<section class="parallax-banner selection_moment container-fluid" style="background-image: url('<?php echo esc_url(get_field('infographie')['url'] ?? ''); ?>');">
    <div class="overlay"></div>
    <div class="container-xxl position-relative d-flex align-items-center" >
        <div class="row w-100 ms-5 align-items-center">
            <div class="col-md-4 text-start text-white parallax-content">
                <?php if($titre = get_field('titre')): ?>
                    <h2 class="mb-4 text-uppercase"><?php echo esc_html($titre); ?></h2>
                <?php endif; ?>

                <?php if($texte = get_field('contenu_texte')): ?>
                    <div class="mb-4"><?php echo wp_kses_post($texte); ?></div>
                <?php endif; ?>

                <?php
                $lien_produit = get_field('lien_produit');
                $texte_bouton = get_field('texte_bouton') ?: 'Découvrir';

                if($lien_produit):
                    $url = is_array($lien_produit) ? get_permalink($lien_produit[0]) : get_permalink($lien_produit);
                ?>
                    <a href="<?php echo esc_url($url); ?>" class="btn w-100 btn-bloom py-2 px-4 text-uppercase">
                        <?php echo esc_html($texte_bouton); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php
get_footer();
?>

