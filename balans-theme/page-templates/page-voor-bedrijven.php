<?php
/**
 * Template Name: Voor bedrijven
 *
 * @package Balans
 */

get_header();

$query = new WP_Query( array(
    'post_type'      => 'balans_zakelijk',
    'posts_per_page' => 20,
) );
?>
<main id="main" class="site-main" data-testid="page-zakelijk">
    <section class="page-hero">
        <div class="container">
            <p class="eyebrow"><?php esc_html_e( 'Voor bedrijven', 'balans' ); ?></p>
            <h1><?php esc_html_e( 'Trainingen die ruimte scheppen in de groep.', 'balans' ); ?></h1>
            <p class="page-hero__intro"><?php esc_html_e( 'Een vastgesteld concept en programma op onze locatie. Deelnemers komen naar de trainingslocatie — training op locatie bij het bedrijf wordt niet aangeboden.', 'balans' ); ?></p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <?php if ( $query->have_posts() ) : ?>
                <div class="cards-grid">
                    <?php while ( $query->have_posts() ) : $query->the_post();
                        balans_card( array(
                            'eyebrow' => __( 'Zakelijke training', 'balans' ),
                            'title'   => get_the_title(),
                            'text'    => wp_trim_words( get_the_excerpt(), 24 ),
                            'link'    => get_permalink(),
                            'image'   => get_the_post_thumbnail_url( get_the_ID(), 'balans-card' ),
                            'cta'     => __( 'Bekijk training', 'balans' ),
                        ) );
                    endwhile; wp_reset_postdata(); ?>
                </div>
            <?php else : ?>
                <?php balans_placeholder_notice( __( 'Nog geen zakelijke trainingen toegevoegd. Voeg aanbod toe via "Zakelijk aanbod" in het WordPress-menu.', 'balans' ) ); ?>
                <div class="cards-grid">
                    <?php balans_card( array(
                        'eyebrow' => __( 'Voorbeeld-concept', 'balans' ),
                        'title'   => __( 'Werken met de groep', 'balans' ),
                        'text'    => __( 'Programma, aantal dagen en eventuele terugkomdag nog aan te leveren.', 'balans' ),
                        'link'    => home_url( '/offerte-aanvragen/' ),
                        'cta'     => __( 'Vraag een offerte aan', 'balans' ),
                    ) ); ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="section section--alt">
        <div class="container">
            <div class="section-intro">
                <h2><?php esc_html_e( 'Twee routes', 'balans' ); ?></h2>
            </div>
            <div class="cards-grid">
                <?php balans_card( array(
                    'eyebrow' => __( 'Open inschrijving', 'balans' ),
                    'title'   => __( 'Zelf een plek boeken', 'balans' ),
                    'text'    => __( 'Als een training een vaste prijs en open inschrijving heeft, kun je direct inschrijven en afrekenen via het winkelmandje.', 'balans' ),
                    'link'    => home_url( '/voor-bedrijven-aanbod/' ),
                    'cta'     => __( 'Bekijk open trainingen', 'balans' ),
                ) ); ?>
                <?php balans_card( array(
                    'eyebrow' => __( 'Groepen', 'balans' ),
                    'title'   => __( 'Vraag een offerte aan', 'balans' ),
                    'text'    => __( 'Meerdere medewerkers aanmelden of een voorstel ontvangen? Vraag een offerte aan.', 'balans' ),
                    'link'    => home_url( '/offerte-aanvragen/' ),
                    'cta'     => __( 'Offerte aanvragen', 'balans' ),
                ) ); ?>
            </div>
        </div>
    </section>
</main>
<?php get_footer();
