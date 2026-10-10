<?php
/**
 * Template Name: Over
 *
 * @package Balans
 */

get_header();
?>
<main id="main" class="site-main" data-testid="page-over">
    <section class="page-hero">
        <div class="container">
            <p class="eyebrow"><?php esc_html_e( 'Over', 'balans' ); ?></p>
            <h1><?php esc_html_e( 'Over ons, de oprichter en onze aanpak.', 'balans' ); ?></h1>
            <p class="page-hero__intro"><?php esc_html_e( 'Wie we zijn, hoe we werken en wat we met empathisch energiewerk bedoelen.', 'balans' ); ?></p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="about-grid">
                <div>
                    <h2><?php esc_html_e( 'De organisatie', 'balans' ); ?></h2>
                    <p><?php esc_html_e( 'Beschrijving van de organisatie — nog aan te leveren door de eigenaar.', 'balans' ); ?></p>

                    <h2><?php esc_html_e( 'De oprichter', 'balans' ); ?></h2>
                    <p><?php esc_html_e( 'Achtergrond, werkervaring en relevante kwalificaties — nog aan te leveren. Alleen echte informatie wordt getoond.', 'balans' ); ?></p>
                </div>
                <div>
                    <h2><?php esc_html_e( 'Onze aanpak & empathisch energiewerk', 'balans' ); ?></h2>
                    <p><?php esc_html_e( 'Een rustige, menselijke manier van kijken naar wat er speelt. Zonder beloftes, zonder jargon. We presenteren wat we doen niet als vervanging van medische zorg.', 'balans' ); ?></p>
                    <p><?php esc_html_e( 'Uitgebreide beschrijving nog aan te leveren.', 'balans' ); ?></p>

                    <h2><?php esc_html_e( 'Partners & samenwerking', 'balans' ); ?></h2>
                    <?php balans_placeholder_notice( __( 'Alleen opnemen wanneer er werkelijk relevante partners zijn. Nog aan te leveren.', 'balans' ) ); ?>
                </div>
            </div>

            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); if ( get_the_content() ) : ?>
                <div class="entry-content" style="margin-top:60px;">
                    <?php the_content(); ?>
                </div>
            <?php endif; endwhile; endif; ?>
        </div>
    </section>
</main>
<?php get_footer();
