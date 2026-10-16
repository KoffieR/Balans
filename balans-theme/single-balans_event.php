<?php
/**
 * Eén event.
 *
 * @package Balans
 */

get_header();
?>
<main id="main" class="site-main" data-testid="single-event">
    <?php while ( have_posts() ) : the_post();
        $id   = get_the_ID();
        $meta = array(
            __( 'Datum', 'balans' )   => balans_event_date( $id ),
            __( 'Tijd', 'balans' )    => balans_event_time( $id ),
            __( 'Locatie', 'balans' ) => get_post_meta( $id, '_event_location', true ),
            __( 'Thema', 'balans' )   => get_post_meta( $id, '_event_theme', true ),
            __( 'Prijs', 'balans' )   => get_post_meta( $id, '_event_price', true ),
        );
        ?>
        <section class="page-hero">
            <div class="container container--text">
                <p class="eyebrow"><a href="<?php echo esc_url( balans_url( 'events' ) ); ?>"><?php esc_html_e( 'Events', 'balans' ); ?></a></p>
                <h1><?php the_title(); ?></h1>
                <?php if ( has_excerpt() ) : ?>
                    <p class="page-hero__intro"><?php echo esc_html( get_the_excerpt() ); ?></p>
                <?php endif; ?>
            </div>
        </section>
        <section class="section">
            <div class="container container--text entry-content">
                <dl class="event-meta" data-testid="event-meta">
                    <?php foreach ( array_filter( $meta ) as $label => $value ) : ?>
                        <div><dt><?php echo esc_html( $label ); ?></dt><dd><?php echo esc_html( $value ); ?></dd></div>
                    <?php endforeach; ?>
                </dl>
                <?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'balans-hero', array( 'style' => 'border-radius:10px; margin-bottom:30px;' ) ); endif; ?>
                <?php the_content(); ?>
                <div style="margin-top:30px;">
                    <?php if ( balans_event_is_past( $id ) ) : ?>
                        <p class="event-past" data-testid="event-past"><?php esc_html_e( 'Dit event is al geweest.', 'balans' ); ?></p>
                        <?php balans_button( balans_url( 'events' ), __( 'Bekijk de komende events', 'balans' ), 'ghost', 'event-more' ); ?>
                    <?php else : ?>
                        <?php balans_button( balans_event_signup_url( $id ), __( 'Aanmelden', 'balans' ), 'primary', 'event-signup' ); ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endwhile; ?>
</main>
<?php get_footer();
