<?php
get_header();
?>
<section class="slider_container">
<?php if( have_rows('slider') ) : ?>
<div id="main-slider" class="carousel slide carousel-fade fullscreen-slider" data-bs-ride="carousel">
  <div class="carousel-inner">
    <?php $i = 0; while( have_rows('slider') ) : the_row();
      $image = get_sub_field('image');
      $titre_slide = get_sub_field('titre_du_slide');
      $phrase = get_sub_field('phrase_daccroche');
      $lien = get_sub_field('lien_fiche_produit');
      $texte_bouton = get_sub_field('texte_bouton_lien');
      if (is_array($lien) || is_object($lien)) {
        $url = get_permalink(is_array($lien) ? $lien[0] : $lien);
      } else {
        $url = $lien;
      }
    ?>
    <div class="carousel-item<?php if($i == 0) echo ' active'; ?>" style="position:relative;">
      <?php if($image): ?>
        <img src="<?php echo esc_url($image['url']); ?>" class="d-block w-100 object-fit-cover" alt="<?php echo esc_attr($image['alt']); ?>" draggable="false">
        <div class="slider-overlay"></div>
      <?php endif; ?>
      <div class="container-xxl h-100">
        <div class="row h-100 align-items-center">
          <div class="col-md-4 offset-md-2 text-start text-white">
            <?php if($phrase): ?>
              <h5 class="fw-bold text-uppercase"><?php echo esc_html($phrase); ?></h5>
            <?php endif; ?>
            <?php if($titre_slide): ?>
              <h2 class="fw-light text-uppercase"><?php echo esc_html($titre_slide); ?></h2>
            <?php endif; ?>
            <?php if($lien && $texte_bouton): ?>
              <a href="<?php echo esc_url($url); ?>" class="btn view-all-link px-4 py-2 text-uppercase mt-3">
                <?php echo esc_html($texte_bouton); ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right ms-2" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                </svg>
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <?php $i++; endwhile; ?>
  </div>

  <!-- Flèches Bootstrap -->
  <button class="carousel-control-prev" type="button" data-bs-target="#main-slider" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Précédent</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#main-slider" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Suivant</span>
  </button>

  <!-- Dots personnalisés -->
  <div class="slider-dots d-flex justify-content-center align-items-center gap-3 position-absolute w-100" style="bottom: 32px; left:0; z-index:10;">
    <?php for($j=0; $j<$i; $j++): ?>
      <div class="slider-dot<?php echo $j === 0 ? ' active' : ''; ?>"></div>
    <?php endfor; ?>
  </div>
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
                                    <button class="btn py-2 px-4 text-uppercase font-weight-bolder">Voir le produit</button>
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
        <div class="row w-100 ms-3 align-items-center">
            <div class="col-md-5 text-start text-white parallax-content">
                <?php if($pre_titre = get_field('pre_titre')): ?>
                    <p class="text-uppercase fw-lighter"><?php echo esc_html($pre_titre); ?></p>
                <?php endif; ?>
                <?php if($titre = get_field('titre')): ?>
                    <h2 class="mb-4 text-uppercase"><?php echo esc_html($titre); ?></h2>
                <?php endif; ?>

                <?php if($texte = get_field('contenu_texte')): ?>
                    <div class="mb-4 fw-lighter"><?php echo wp_kses_post($texte); ?></div>
                <?php endif; ?>
				<a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="btn view-all-link py-2 px-4 text-uppercase">
					Aller à la boutique
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right ms-2" viewBox="0 0 16 16">
						<path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
					</svg>
				</a>
            </div>
        </div>
    </div>
</section>
<div class="spacer-10"></div>
<section class="boutique_categories py-5">
    <div class="container-xxl">
        <h3 class="text-center mb-5">Nos produits par catégorie</h3>
        <div id="categories-carousel" class="carousel slide categories-carousel" data-bs-ride="false" data-bs-interval="false">
            <div class="carousel-inner">
                <div class="carousel-row">
                    <?php
                    $product_categories = get_terms(array(
                        'taxonomy'   => 'product_cat',
                        'hide_empty' => true
                    ));

                    if (!empty($product_categories) && !is_wp_error($product_categories)) {
                        foreach ($product_categories as $index => $category) {
                            $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                            $image = wp_get_attachment_url($thumbnail_id);

                            if (!$image) {
                                $image = wc_placeholder_img_src();
                            }
                            ?>
                            <div class="category-item">
                                <a href="<?php echo get_term_link($category); ?>" class="category-link">
                                    <div class="category-image" style="background-image: url('<?php echo esc_url($image); ?>')">
                                        <div class="category-overlay">
                                            <h4 class="category-name"><?php echo esc_html($category->name); ?></h4>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="carousel-controls">
                <button class="carousel-control-prev" type="button" data-bs-target="#categories-carousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Précédent</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#categories-carousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Suivant</span>
                </button>
            </div>
        </div>
    </div>
</section>
<div class="spacer-10"></div>
<section class="charte_rse container-fluid" style="background-image: url('<?php echo esc_url(get_field('image_rse')['url'] ?? ''); ?>');">
    <div class="container-xxl d-flex justify-content-center align-items-center" style="min-height: 500px;">
        <div class="text-center col-md-8 mx-auto text-white">
            <?php if($texte_accroche = get_field('phrase_daccroche_bloc_charte')): ?>
                <h2 class="mb-4 text-uppercase fw-bold"><?php echo $texte_accroche; ?></h2>
            <?php endif;
            if($texte = get_field('contenu_texte_charte')): ?>
                <?php echo wp_kses_post($texte); ?>
            <?php endif;
            $lien = get_field('lien_page_charte');
            $texte_bouton = get_field('texte_cta_charte');
            if($lien && $texte_bouton):

                if (is_array($lien) || is_object($lien)) {
                    $url = get_permalink(is_array($lien) ? $lien[0] : $lien);
                } else {
                    $url = $lien;
                }?>
                <a href="<?php echo esc_url($url); ?>" class="btn view-all-link px-4 py-2 text-uppercase mt-3">
                    <?php echo esc_html($texte_bouton); ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right ms-2" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
<div class="spacer-10"></div>
<section class="best_advice col-md-8 mx-auto py-5">
    <div class="row align-items-center">
        <?php
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => 1,
            'meta_key'       => '_wc_average_rating',
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
            'meta_query'     => array(
                array(
                    'key'     => '_wc_average_rating',
                    'value'   => 0,
                    'compare' => '>',
                    'type'    => 'NUMERIC'
                )
            )
        );
        $query = new WP_Query($args);
        if ($query->have_posts()) :
            while ($query->have_posts()) : $query->the_post();
                global $product;
                $comments = get_comments(array(
                    'post_id' => get_the_ID(),
                    'number'  => 1,
                    'status'  => 'approve',
                    'orderby' => 'comment_date',
                    'order'   => 'DESC',
                ));
                ?>
				<div class="col-12">
					<h2 class="text-center mb-4">Le mieux noté</h2>
				</div>
                <div class="col-md-6">
                    <div class="product-image-container" >
                        <?php echo get_the_post_thumbnail(get_the_ID(), 'large', ['style' => 'width:100%; height:100%; object-fit:cover;']); ?>
                    </div>
                </div>
                <div class="col-md-6 d-flex flex-column justify-content-center align-items-center">
                    <div class="w-100 px-4">
                        <h3 class="mb-3 text-uppercase fw-bold"><?php the_title(); ?></h3>
                        <div class="mb-3">
                            <?php echo silklane_get_star_rating_html($product->get_average_rating(), $product->get_rating_count()); ?>
                            <span class="rating-count small text-muted">(<?php echo $product->get_rating_count(); ?> avis)</span>
                            <div class="rating-literal  mt-2">
                                <?php
                                $note = number_format((float)$product->get_average_rating(), 2, ',', ' ');
                                $nb_avis = $product->get_rating_count();
                                echo "<h3>Note de {$note} / 5</h3>
								<p>par {$nb_avis} client" . ($nb_avis > 1 ? "s" : "") . "</p>";
                                ?>
                            </div>
                        </div>
                        <?php if (!empty($comments)) :
                            $comment = $comments[0]; ?>
                            <blockquote class="blockquote">
                                <p class="mb-2">
                                    &laquo;&nbsp;
                                    <?php
                                    $avis = trim($comment->comment_content);
                                    $avis = mb_strtoupper(mb_substr($avis, 0, 1)) . mb_substr($avis, 1);
                                    echo esc_html($avis);
                                    ?>
                                    &nbsp;&raquo;
                                </p>
                                <footer class="blockquote-footer">
                                    <?php echo esc_html($comment->comment_author); ?>
                                    <span class="text-muted">le <?php echo date_i18n('d/m/Y', strtotime($comment->comment_date)); ?></span>
                                </footer>
                            </blockquote>
                        <?php else : ?>
                            <p class="text-muted">Aucun avis pour ce produit.</p>
                        <?php endif; ?>
                        <a href="<?php the_permalink(); ?>" class="btn view-all-link mt-4 px-4 py-2 text-uppercase">
                            Voir le produit
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right ms-2" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                <?php
            endwhile;
            wp_reset_postdata();
        else :
            echo '<div class="col-12 text-center text-muted">Aucun produit avec avis trouvé.</div>';
        endif;
        ?>
    </div>
</section>
<div class="spacer-10"></div>
<?php
get_footer();
?>

