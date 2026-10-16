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
    <?php balans_page_hero( 'voor-bedrijven' ); ?>

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
            <?php elseif ( current_user_can( 'edit_posts' ) ) : ?>
                <?php balans_placeholder_notice( __( 'Nog geen zakelijke trainingen toegevoegd. Voeg aanbod toe via "Zakelijk aanbod" in het WordPress-menu.', 'balans' ) ); ?>
                <div class="cards-grid">
                    <?php balans_card( array(
                        'eyebrow' => __( 'Voorbeeld-concept', 'balans' ),
                        'title'   => __( 'Werken met de groep', 'balans' ),
                        'text'    => __( 'Programma, aantal dagen en eventuele terugkomdag nog aan te leveren.', 'balans' ),
                        'link'    => balans_url( 'offerte-aanvragen' ),
                        'cta'     => __( 'Vraag een offerte aan', 'balans' ),
                    ) ); ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); if ( get_the_content() ) : ?>
        <section class="section section--alt">
            <div class="container entry-content entry-content--wide" data-testid="zakelijk-content">
                <?php the_content(); ?>
            </div>
        </section>
    <?php endif; endwhile; endif; ?>
</main>
<?php get_footer();
