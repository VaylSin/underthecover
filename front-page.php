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
<section class="best_sellers container py-5">
    <h2 class="text-uppercase font-weight-bold"	>Nos Best-Sellers</h2>
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
                    <div class="card  product_item">
                        <a href="<?php the_permalink(); ?>" class="text-decoration-none text-dark">
                            <?php the_post_thumbnail('large', [
																	'class' => 'card-img-top object-fit-cover',
																	'style' => 'aspect-ratio:1/1;object-fit:cover;width:100%;'
															]); ?>
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <h3 class="card-title h6 mb-0"><?php the_title(); ?></h3>
																<?php if (isset($product) && is_object($product)) : ?>
																		<p class="price mb-0 fw-bold h6"><?php echo $product->get_price_html(); ?></p>
																<?php endif; ?>
                            </div>
														<div class=" text-center">
																<a href="<?php the_permalink(); ?>" class="text-decoration-none">
																	<button class="btn btn-bloom py-2 px-4 w-100 text-uppercase font-weight-bold">Voir le produit</button>
																</a>
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
</section>

<?php
get_footer();
?>

