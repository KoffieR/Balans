<?php
/**
 * Template Name: Abonnementen (energetische opschoning)
 *
 * @package Balans
 */

get_header();
$s = balans_services();
?>
<main id="main" class="site-main" data-testid="page-abonnementen">
    <?php balans_page_hero( 'abonnementen' ); ?>

    <section class="section">
        <div class="container">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <?php if ( get_the_content() ) : ?><div class="entry-content service-extra"><?php the_content(); ?></div><?php endif; ?>
            <?php endwhile; endif; ?>
            <div class="service-list">
                <?php foreach ( balans_get_diensten( 'abonnement' ) as $key => $item ) : ?>
                    <?php balans_service_block( $key, $item, 'abonnement' ); ?>
                <?php endforeach; ?>
            </div>
            <?php balans_price_note(); ?>
        </div>
    </section>
</main>
<?php get_footer();
