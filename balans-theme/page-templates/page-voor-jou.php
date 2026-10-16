<?php
/**
 * Template Name: Voor jou (overzicht)
 *
 * @package Balans
 */

get_header();
?>
<main id="main" class="site-main" data-testid="page-voor-jou">
    <section class="page-hero">
        <div class="container">
            <p class="eyebrow"><?php esc_html_e( 'Voor jou', 'balans' ); ?></p>
            <h1><?php esc_html_e( 'Vijf manieren om met wat er speelt aan de slag te gaan.', 'balans' ); ?></h1>
            <p class="page-hero__intro"><?php esc_html_e( 'Kies wat op dit moment past. Je kunt altijd op een ander moment een andere route proberen.', 'balans' ); ?></p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="voor-jou-grid">
                <?php foreach ( balans_voor_jou_services() as $svc ) :
                    $svc = balans_route_upcoming( $svc );
                    balans_card( array(
                        'badge'   => isset( $svc['badge'] ) ? $svc['badge'] : '',
                        'eyebrow' => $svc['eyebrow'],
                        'title'   => $svc['title'],
                        'text'    => $svc['text'],
                        'link'    => $svc['link'],
                        'cta'     => __( 'Meer informatie', 'balans' ),
                    ) );
                endforeach; ?>
            </div>
        </div>
    </section>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); if ( get_the_content() ) : ?>
        <section class="section section--alt">
            <div class="container container--text entry-content">
                <?php the_content(); ?>
            </div>
        </section>
    <?php endif; endwhile; endif; ?>
</main>
<?php get_footer();
