<?php
/**
 * Template Name: Abonnementen (energetische opschoning)
 *
 * Dit is de bestaande dienst "regelmatige energetische opschoning". NIET hetzelfde als meditatieabonnementen.
 *
 * @package Balans
 */

get_header();
?>
<main id="main" class="site-main" data-testid="page-abonnementen">
    <section class="page-hero">
        <div class="container">
            <p class="eyebrow"><?php esc_html_e( 'Voor jou', 'balans' ); ?></p>
            <h1><?php esc_html_e( 'Abonnementen — regelmatige energetische opschoning', 'balans' ); ?></h1>
            <p class="page-hero__intro"><?php esc_html_e( 'Een doorlopend ritme van energetische opschoning van je lichaam. Dit is een aparte dienst en niet hetzelfde als een meditatieabonnement.', 'balans' ); ?></p>
        </div>
    </section>

    <section class="section">
        <div class="container container--text entry-content">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); the_content(); endwhile; endif; ?>

            <?php balans_placeholder_notice( __( 'Varianten, frequenties, tarieven en voorwaarden nog aan te leveren door de eigenaar.', 'balans' ) ); ?>

            <h2><?php esc_html_e( 'Hoe werkt het?', 'balans' ); ?></h2>
            <p><?php esc_html_e( 'Nog aan te vullen. Als een variant op afspraak gaat, loopt dat via de afsprakenkalender. Varianten die rechtstreeks online afgenomen kunnen worden lopen via het winkelmandje.', 'balans' ); ?></p>

            <div style="margin-top:30px; display:flex; gap:14px; flex-wrap:wrap;">
                <?php balans_button( home_url( '/afspraak-maken/' ), __( 'Afspraak maken', 'balans' ), 'secondary', 'abo-cta-afspraak' ); ?>
                <?php balans_button( home_url( '/contact/' ), __( 'Stel een vraag', 'balans' ), 'ghost', 'abo-cta-vraag' ); ?>
            </div>
        </div>
    </section>
</main>
<?php get_footer();
