<?php
/**
 * Template Name: Trainingen & workshops (particulier)
 *
 * @package Balans
 */

get_header();

$query = new WP_Query( array(
    'post_type'      => 'balans_training',
    'posts_per_page' => 20,
    'orderby'        => 'date',
    'order'          => 'DESC',
) );
?>
<main id="main" class="site-main" data-testid="page-trainingen">
    <section class="page-hero">
        <div class="container">
            <p class="eyebrow"><?php esc_html_e( 'Voor jou', 'balans' ); ?></p>
            <h1><?php esc_html_e( 'Trainingen & workshops', 'balans' ); ?></h1>
            <p class="page-hero__intro"><?php esc_html_e( 'Op vastgestelde data op onze locatie. Waar mogelijk kun je direct inschrijven via het winkelmandje.', 'balans' ); ?></p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <?php if ( $query->have_posts() ) : ?>
                <div class="cards-grid">
                    <?php while ( $query->have_posts() ) : $query->the_post();
                        balans_card( array(
                            'eyebrow' => __( 'Training', 'balans' ),
                            'title'   => get_the_title(),
                            'text'    => wp_trim_words( get_the_excerpt(), 24 ),
                            'link'    => get_permalink(),
                            'image'   => get_the_post_thumbnail_url( get_the_ID(), 'balans-card' ),
                            'cta'     => __( 'Bekijk training', 'balans' ),
                        ) );
                    endwhile; wp_reset_postdata(); ?>
                </div>
            <?php else : ?>
                <?php balans_placeholder_notice( __( 'Nog geen trainingen toegevoegd. Voeg trainingen toe via "Trainingen" in het WordPress-menu. Zodra ze zijn gepubliceerd, verschijnen ze hier automatisch.', 'balans' ) ); ?>
                <div class="cards-grid">
                    <?php for ( $i = 0; $i < 3; $i++ ) :
                        balans_card( array(
                            'eyebrow' => __( 'Voorbeeld', 'balans' ),
                            'title'   => __( 'Trainingstitel', 'balans' ),
                            'text'    => __( 'Korte beschrijving van wat de deelnemer kan verwachten.', 'balans' ),
                            'link'    => '#',
                            'cta'     => __( 'Nog aan te leveren', 'balans' ),
                        ) );
                    endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php get_footer();
