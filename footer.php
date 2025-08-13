<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

</div><!-- #page -->

<footer class="footer bg-black-main pt-5 text-white">
    <div class="container-xxl">
        <div class="row gy-4">
            <!-- Colonne 1 : Présentation -->
            <div class="col-12 col-md-3 mb-4 mb-md-0">
                <?php
                $titre_footer = get_field('titre', 'option');
                $texte_footer = get_field('contenu_texte', 'option');
                if ($titre_footer): ?>
                    <h5 class="mb-3 text-uppercase"><?php echo esc_html($titre_footer); ?></h5>
                <?php endif; ?>
                <?php if ($texte_footer): ?>
                    <p class="small"><?php echo wp_strip_all_tags($texte_footer); ?></p>
                <?php endif; ?>
            </div>
            <!-- Colonne 2 : Catégories produits -->
            <div class="col-12 col-md-3 mb-4 mb-md-0">
                <h5 class="mb-3 text-uppercase">Catégories</h5>
                <ul class="list-unstyled">
                    <?php
                    $product_categories = get_terms([
                        'taxonomy' => 'product_cat',
                        'hide_empty' => false
                    ]);
                    foreach ($product_categories as $cat): ?>
                        <li>
                            <a href="<?php echo get_term_link($cat); ?>" class="text-white text-decoration-none small">
                                <?php echo esc_html($cat->name); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <!-- Colonne 3 : Pages infos -->
            <div class="col-12 col-md-3 mb-4 mb-md-0">
                <h5 class="mb-3 text-uppercase">Informations</h5>
                <ul class="list-unstyled">
                    <li><a href="/mentions-legales" class="text-white text-decoration-none small">Mentions légales</a></li>
                    <li><a href="/politique-de-confidentialite" class="text-white text-decoration-none small">Politique de confidentialité</a></li>
                    <li><a href="/conditions-de-vente" class="text-white text-decoration-none small">Conditions de vente</a></li>
                    <li><a href="/nous-contacter" class="text-white text-decoration-none small">Nous contacter</a></li>
                </ul>
            </div>
            <!-- Colonne 4 : Réseaux sociaux + Newsletter -->
            <div class="col-12 col-md-3 d-flex flex-column justify-content-between h-100">
                <div class="mb-4">
                    <h5 class="mb-3 text-uppercase">Suivez-nous</h5>
                    <div class="d-flex gap-3 align-items-center">
                        <a href="https://facebook.com" target="_blank" class="text-white fs-4"><i class="bi bi-facebook"></i></a>
                        <a href="https://instagram.com" target="_blank" class="text-white fs-4"><i class="bi bi-instagram"></i></a>
                        <a href="https://linkedin.com" target="_blank" class="text-white fs-4"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
                <div class="mt-auto">
                    <h5 class="mb-3 text-uppercase">Newsletter</h5>
                    <form method="post" action="#">
						<div class="input-group">
							<input type="email" class="form-control" placeholder="Votre email" required>
							<button class="btn btn-bloom" type="submit">S'inscrire</button>
						</div>
					</form>
                </div>
            </div>
        </div>
        <hr class="border-white opacity-25 my-4">
        <div class="row pb-4 align-items-center">
            <div class="col-md-6 text-center text-md-start small opacity-75">
                Design by <a href="https://skdigit.com" target="_blank" rel="noopener" class="text-white text-decoration-underline">Skdigit</a>
            </div>
            <div class="col-md-6 text-center text-md-end small opacity-75">
                &copy; <?php echo date('Y'); ?> Silklane. Tous droits réservés.
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>

</body>

</html>

