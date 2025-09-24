<?php
/**
 * Template Name: Page À propos
 */

get_header();

// Image à la une pour la bannière
if ( has_post_thumbnail() ) {
    $banner_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
} else {
    $banner_url = get_template_directory_uri() . '/assets/img/default-banner.jpg';
}

// Titre et contenu principal
$page_title = get_the_title();
ob_start();
the_content();
$page_content = ob_get_clean();

// Citation
$citation = get_field('citation_entete');

// Damier repeater
$damier = get_field('mise_en_page_en_damier');
?>
<div class="about-banner" style="background-image: url('<?php echo esc_url($banner_url); ?>');">
    <div class="about-banner-overlay"></div>
    <div class="container about-banner-content justify-content-center align-items-start">
        <div class="about-banner-inner">
            <h1><?php echo esc_html($page_title); ?></h1>
            <?php if ( $page_content ) : ?>
                <div class="about-banner-desc"><?php echo $page_content; ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ( $citation ) : ?>
    <div class="about-citation py-4">
        <div class="container text-center">
            <blockquote class="mb-0 fs-4"><?php echo esc_html($citation); ?></blockquote>
        </div>
    </div>
<?php endif; ?>
<div class="spacer-10"></div>
<?php if ( $damier ) : ?>
    <div class="about-damier my-5">
        <div class="container">
            <?php foreach ($damier as $index => $row) :
                $photo = $row['image'];
                $texte = $row['contenu_texte'];
                $is_even = $index % 2 === 0;
                ?>
                <div class="row about-damier-row align-items-center mb-5 flex-md-nowrap flex-wrap">
                    <?php if ($is_even) : ?>
                        <div class="col-12 col-md-6 about-damier-photo p-0 mb-3 mb-md-0">
                            <?php if ($photo) : ?>
                                <div class="about-damier-img" style="background-image:url('<?php echo esc_url(is_array($photo) ? $photo['url'] : $photo); ?>');"></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-12 col-md-6 about-damier-texte">
                            <div class="about-damier-wysiwyg<?php echo ($index === 0 || $index === 2) ? ' no-bg' : (($index === 1) ? ' velvet-bg' : ''); ?>">
                                <div class="about-damier-inner-container">
                                    <?php echo $texte; ?>
                                </div>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="col-12 col-md-6 order-md-2 about-damier-photo p-0 mb-3 mb-md-0">
                            <?php if ($photo) : ?>
                                <div class="about-damier-img" style="background-image:url('<?php echo esc_url(is_array($photo) ? $photo['url'] : $photo); ?>');"></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-12 col-md-6 order-md-1 about-damier-texte">
                            <div class="about-damier-wysiwyg<?php echo ($index === 0 || $index === 2) ? ' no-bg' : (($index === 1) ? ' velvet-bg' : ''); ?>">
                                <div class="about-damier-inner-container">
                                    <?php echo $texte; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php get_footer(); ?>
