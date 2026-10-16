<?php
/**
 * Template Name: Meditatie (overzicht)
 *
 * Faseplan:
 *  Fase 1 — gratis meditaties direct afspeelbaar zonder registratie (deze template).
 *  Fase 2 — betaalde losse programma's via WooCommerce (categorie 'meditatie').
 *  Fase 3 — abonnementen (vereist WooCommerce Subscriptions + Memberships, afhankelijk van later besluit).
 *
 * @package Balans
 */

get_header();

// Placeholder free meditations list — vervang via custom post type 'balans_meditatie' zodra beschikbaar.
$placeholder_free = array(
    array(
        'title'    => __( 'Adem — 5 minuten rust', 'balans' ),
        'duration' => '5:00',
        'text'     => __( 'Een korte adembegeleiding om tussendoor even stil te staan.', 'balans' ),
        'audio'    => '', // vul audio-URL in via de beheerder (CPT: meditatie).
    ),
    array(
        'title'    => __( 'Grond je dag', 'balans' ),
        'duration' => '10:00',
        'text'     => __( 'Een rustige meditatie om je dag mee te beginnen.', 'balans' ),
        'audio'    => '',
    ),
    array(
        'title'    => __( 'Loslaten voor de nacht', 'balans' ),
        'duration' => '12:00',
        'text'     => __( 'Een zachte afsluiting om los te laten wat er was.', 'balans' ),
        'audio'    => '',
    ),
);

$meditatie_query = new WP_Query( array(
    'post_type'      => 'balans_meditatie',
    'posts_per_page' => 20,
    'orderby'        => 'date',
    'order'          => 'DESC',
) );
?>
<main id="main" class="site-main" data-testid="page-meditatie">
    <?php balans_page_hero( 'meditatie' ); ?>

    <section class="section">
        <div class="container">
            <h2><?php esc_html_e( 'Gratis meditaties', 'balans' ); ?></h2>

            <?php if ( $meditatie_query->have_posts() ) : ?>
                <div class="meditation-list">
                    <?php while ( $meditatie_query->have_posts() ) : $meditatie_query->the_post();
                        $audio_url = get_post_meta( get_the_ID(), '_balans_audio_url', true );
                        $duration  = get_post_meta( get_the_ID(), '_balans_duration', true );
                        ?>
                        <article class="meditation" data-testid="meditation-item">
                            <div class="meditation__header">
                                <div>
                                    <span class="badge-free"><?php esc_html_e( 'Gratis', 'balans' ); ?></span>
                                    <h3 class="meditation__title"><?php the_title(); ?></h3>
                                </div>
                                <?php if ( $duration ) : ?><span class="meditation__meta"><?php echo esc_html( $duration ); ?></span><?php endif; ?>
                            </div>
                            <p class="meditation__text"><?php echo esc_html( get_the_excerpt() ); ?></p>
                            <?php if ( $audio_url ) : ?>
                                <audio controls preload="none" src="<?php echo esc_url( $audio_url ); ?>" data-testid="meditation-audio">
                                    <?php esc_html_e( 'Je browser ondersteunt geen audio.', 'balans' ); ?>
                                </audio>
                            <?php else : ?>
                                <?php balans_placeholder_notice( __( 'Audiobestand nog aan te leveren. Upload de MP3 bij deze meditatie in de beheeromgeving.', 'balans' ) ); ?>
                            <?php endif; ?>
                        </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            <?php elseif ( current_user_can( 'edit_posts' ) ) : ?>
                <?php balans_placeholder_notice( __( 'Nog geen meditaties toegevoegd. Voeg ze toe onder "Meditaties" in het WordPress-menu. Hieronder staan voorbeeld-blokken ter illustratie van de layout.', 'balans' ) ); ?>
                <div class="meditation-list">
                    <?php foreach ( $placeholder_free as $m ) : ?>
                        <article class="meditation" data-testid="meditation-placeholder">
                            <div class="meditation__header">
                                <div>
                                    <span class="badge-free"><?php esc_html_e( 'Gratis', 'balans' ); ?></span>
                                    <h3 class="meditation__title"><?php echo esc_html( $m['title'] ); ?></h3>
                                </div>
                                <span class="meditation__meta"><?php echo esc_html( $m['duration'] ); ?></span>
                            </div>
                            <p class="meditation__text"><?php echo esc_html( $m['text'] ); ?></p>
                            <audio controls preload="none">
                                <?php esc_html_e( 'Audio nog aan te leveren.', 'balans' ); ?>
                            </audio>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="section section--alt">
        <div class="container">
            <div class="section-intro">
                <h2><?php esc_html_e( 'Binnenkort: betaalde programma\'s en abonnementen', 'balans' ); ?></h2>
                <p><?php esc_html_e( 'Losse meditatieprogramma\'s en abonnementen worden gefaseerd toegevoegd via de shop. Je rekent dan af via het gewone winkelmandje en krijgt na betaling toegang.', 'balans' ); ?></p>
            </div>
        </div>
    </section>
</main>
<?php get_footer();
