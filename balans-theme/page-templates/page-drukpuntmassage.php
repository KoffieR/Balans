<?php
/**
 * Template Name: Drukpuntmassage + consult
 *
 * @package Balans
 */

get_header();
?>
<main id="main" class="site-main" data-testid="page-drukpuntmassage">
    <section class="page-hero">
        <div class="container">
            <p class="eyebrow"><?php esc_html_e( 'Voor jou', 'balans' ); ?></p>
            <h1><?php esc_html_e( 'Drukpuntmassage + consult', 'balans' ); ?></h1>
            <p class="page-hero__intro"><?php esc_html_e( 'Drukpuntmassage gecombineerd met een consult: lichaamsgericht werk met ruimte om te vertellen wat er bij je speelt.', 'balans' ); ?></p>
        </div>
    </section>

    <section class="section">
        <div class="container container--text entry-content">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); the_content(); endwhile; endif; ?>

            <?php balans_placeholder_notice( __( 'Praktische informatie — duur, prijs, locatie, werkwijze — nog aan te vullen door de eigenaar.', 'balans' ) ); ?>

            <h2><?php esc_html_e( 'Wat is drukpuntmassage?', 'balans' ); ?></h2>
            <p><?php esc_html_e( 'Beschrijving nog aan te vullen. Dit is geen vervanging van medische zorg.', 'balans' ); ?></p>

            <h2><?php esc_html_e( 'Hoe verloopt een sessie?', 'balans' ); ?></h2>
            <p><?php esc_html_e( 'Nog aan te vullen.', 'balans' ); ?></p>

            <div style="margin-top:30px;">
                <?php balans_button( home_url( '/afspraak-maken/?dienst=drukpuntmassage' ), __( 'Afspraak maken', 'balans' ), 'primary', 'drukpunt-cta' ); ?>
            </div>
        </div>
    </section>
</main>
<?php get_footer();
