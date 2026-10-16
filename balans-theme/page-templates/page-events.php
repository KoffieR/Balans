<?php
/**
 * Template Name: Events
 *
 * Bovenaan de (bewerkbare) pagina-inhoud met uitleg en thema's, daaronder de komende events.
 *
 * @package Balans
 */

get_header();
$events = balans_upcoming_events();
?>
<main id="main" class="site-main" data-testid="page-events">
    <section class="page-hero">
        <div class="container">
            <p class="eyebrow"><?php esc_html_e( 'Voor jou', 'balans' ); ?></p>
            <h1><?php the_title(); ?></h1>
        </div>
    </section>

    <section class="section">
        <div class="container container--text entry-content" data-testid="events-intro">
            <?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?>
        </div>
    </section>

    <section class="section section--alt" data-testid="events-upcoming">
        <div class="container">
            <h2><?php esc_html_e( 'Komende events', 'balans' ); ?></h2>
            <?php if ( $events ) : ?>
                <div class="cards-grid">
                    <?php foreach ( $events as $event ) :
                        $where = get_post_meta( $event->ID, '_event_location', true );
                        balans_card( array(
                            'eyebrow' => balans_event_date( $event->ID ) . ( $where ? ' · ' . $where : '' ) . ( balans_event_sold_out( $event->ID ) ? ' · ' . __( 'Uitverkocht', 'balans' ) : '' ),
                            'title'   => get_the_title( $event ),
                            'text'    => wp_trim_words( get_the_excerpt( $event ), 24 ),
                            'link'    => get_permalink( $event ),
                            'image'   => get_the_post_thumbnail_url( $event, 'balans-card' ),
                            'cta'     => __( 'Bekijk event', 'balans' ),
                        ) );
                    endforeach; ?>
                </div>
            <?php else : ?>
                <?php $u = balans_upcoming(); ?>
                <?php if ( $u ) : ?><span class="upcoming-badge"><?php echo esc_html( $u['badge'] ); ?></span><?php endif; ?>
                <p data-testid="events-empty"><?php echo esc_html( $u ? $u['text'] : __( 'Er staan op dit moment geen events gepland. Wil je op de hoogte blijven? Laat het ons weten.', 'balans' ) ); ?></p>
            <?php endif; ?>
            <?php get_template_part( 'template-parts/event-interest' ); ?>
        </div>
    </section>
</main>
<?php get_footer();
