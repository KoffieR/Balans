<?php
/**
 * Template Name: Afspraak voor hulp
 *
 * @package Balans
 */

get_header();
$s = balans_services();
?>
<main id="main" class="site-main" data-testid="page-hulp">
    <?php ob_start(); ?>
            <nav class="service-jump" aria-label="<?php echo esc_attr( $s['hulp_eyebrow'] ); ?>">
                <?php foreach ( balans_get_diensten( 'hulp' ) as $key => $item ) : ?>
                    <a href="#<?php echo esc_attr( $key ); ?>" data-testid="jump-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $item['title'] ); ?></a>
                <?php endforeach; ?>
            </nav>
    <?php balans_page_hero( 'hulp', ob_get_clean() ); ?>

    <section class="section">
        <div class="container">
            <div class="service-list">
                <?php foreach ( balans_get_diensten( 'hulp' ) as $key => $item ) : ?>
                    <?php balans_service_block( $key, $item, 'hulp' ); ?>
                <?php endforeach; ?>
            </div>
            <?php balans_price_note(); ?>
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); if ( get_the_content() ) : ?>
                <div class="entry-content service-extra" style="margin-top:48px;"><?php the_content(); ?></div>
            <?php endif; endwhile; endif; ?>
        </div>
    </section>
</main>
<?php get_footer();
