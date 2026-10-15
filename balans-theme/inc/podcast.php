<?php
/**
 * Podcast (Seriously Simple Podcasting) + EQ5-abonnement (Paid Memberships Pro).
 * Beide plugins zijn optioneel; zonder plugins toont de podcastpagina een melding.
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const BALANS_MEMBERS_META = '_eq5_members_only';

function balans_has_podcast_plugin() {
    return post_type_exists( 'podcast' );
}

/**
 * Heeft de bezoeker een actief EQ5-abonnement? (elk actief PMPro-niveau telt).
 */
function balans_is_member( $user_id = null ) {
    $is_member = function_exists( 'pmpro_hasMembershipLevel' ) && pmpro_hasMembershipLevel( null, $user_id );
    return (bool) apply_filters( 'balans_is_member', $is_member, $user_id );
}

function balans_membership_url() {
    if ( function_exists( 'pmpro_url' ) && pmpro_url( 'levels' ) ) {
        return pmpro_url( 'levels' );
    }
    return balans_url( 'contact' );
}

function balans_episode_members_only( $post_id ) {
    return (bool) get_post_meta( $post_id, BALANS_MEMBERS_META, true );
}

function balans_episode_locked( $post_id ) {
    return balans_episode_members_only( $post_id ) && ! balans_is_member();
}

/**
 * Vinkje "Alleen voor abonnees" bij afleveringen (en meditaties).
 */
function balans_members_meta_box() {
    foreach ( array( 'podcast', 'balans_meditatie' ) as $type ) {
        if ( post_type_exists( $type ) ) {
            add_meta_box( 'balans-members-only', __( 'EQ5-abonnement', 'balans' ), 'balans_members_meta_box_html', $type, 'side', 'high' );
        }
    }
}
add_action( 'add_meta_boxes', 'balans_members_meta_box' );

function balans_members_meta_box_html( $post ) {
    wp_nonce_field( 'balans_members_only', 'balans_members_only_nonce' );
    printf(
        '<label><input type="checkbox" name="balans_members_only" value="1" %s /> %s</label><p class="description">%s</p>',
        checked( balans_episode_members_only( $post->ID ), true, false ),
        esc_html__( 'Alleen voor abonnees', 'balans' ),
        esc_html__( 'Niet aangevinkt = gratis voor iedereen.', 'balans' )
    );
}

function balans_save_members_meta( $post_id ) {
    if ( ! isset( $_POST['balans_members_only_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['balans_members_only_nonce'] ), 'balans_members_only' ) ) {
        return;
    }
    if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    if ( ! empty( $_POST['balans_members_only'] ) ) {
        update_post_meta( $post_id, BALANS_MEMBERS_META, 1 );
    } else {
        delete_post_meta( $post_id, BALANS_MEMBERS_META );
    }
}
add_action( 'save_post', 'balans_save_members_meta' );

/**
 * Abonnee-afleveringen niet in de openbare feed; wel in de privé-feed (PMPro Member RSS).
 */
function balans_filter_feed_episodes( $args, $context ) {
    if ( 'feed' !== $context ) {
        return $args;
    }
    if ( balans_has_polylang() ) {
        $lang         = isset( $_GET['lang'] ) ? sanitize_key( wp_unslash( $_GET['lang'] ) ) : 'nl'; // phpcs:ignore WordPress.Security.NonceVerification
        $args['lang'] = in_array( $lang, array( 'nl', 'en', 'fr' ), true ) ? $lang : 'nl';
    }
    if ( balans_is_member() ) {
        return $args;
    }
    $exclude = array(
        'relation' => 'OR',
        array( 'key' => BALANS_MEMBERS_META, 'compare' => 'NOT EXISTS' ),
        array( 'key' => BALANS_MEMBERS_META, 'value' => '1', 'compare' => '!=' ),
    );
    $args['meta_query'] = empty( $args['meta_query'] ) ? array( $exclude ) : array( 'relation' => 'AND', $args['meta_query'], $exclude );
    return $args;
}
add_filter( 'ssp_episode_query_args', 'balans_filter_feed_episodes', 10, 2 );

/**
 * Losse afleveringspagina: inhoud en speler afschermen voor niet-abonnees.
 */
function balans_lock_episode_content( $content ) {
    $post = get_post();
    if ( ! $post || ! in_array( $post->post_type, array( 'podcast', 'balans_meditatie' ), true ) || ! balans_episode_locked( $post->ID ) ) {
        return $content;
    }
    ob_start();
    balans_members_lock_box();
    return ( has_excerpt( $post ) ? '<p>' . esc_html( get_the_excerpt( $post ) ) . '</p>' : '' ) . ob_get_clean();
}
add_filter( 'the_content', 'balans_lock_episode_content', 999 );

function balans_members_lock_box() {
    ?>
    <div class="members-lock" data-testid="members-lock">
        <?php echo balans_svg( 'q-accent', 'members-lock__q' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
        <div>
            <p class="members-lock__title"><?php esc_html_e( 'Alleen voor abonnees', 'balans' ); ?></p>
            <p><?php esc_html_e( 'Met een EQ5-abonnement luister je naar alle afleveringen en betaalde meditaties, ook in je eigen podcast-app.', 'balans' ); ?></p>
            <?php balans_button( balans_membership_url(), __( 'Word abonnee', 'balans' ), 'primary', 'members-lock-cta' ); ?>
        </div>
    </div>
    <?php
}

/**
 * Speler of slot voor één aflevering.
 */
function balans_episode_player( $post_id ) {
    if ( balans_episode_locked( $post_id ) ) {
        balans_members_lock_box();
        return;
    }
    $audio = get_post_meta( $post_id, 'audio_file', true );
    if ( ! $audio ) {
        balans_placeholder_notice( __( 'Audio nog aan te leveren.', 'balans' ) );
        return;
    }
    echo '<div class="episode__player">' . wp_audio_shortcode( array( 'src' => esc_url( $audio ), 'preload' => 'none' ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
}

/**
 * Feed van de huidige taal (NL zonder parameter, EN/FR met ?lang=); voor abonnees met memberkey (PMPro Member RSS).
 */
function balans_podcast_feed_url() {
    $lang   = balans_lang();
    $series = function_exists( 'ssp_get_default_series_slug' ) ? ssp_get_default_series_slug() : '';
    $url    = home_url( '/feed/podcast/' . ( $series ? $series . '/' : '' ) );
    if ( 'nl' !== $lang ) {
        $url = add_query_arg( 'lang', $lang, $url );
    }
    if ( function_exists( 'pmpromrss_url' ) && balans_is_member() ) {
        $url = pmpromrss_url( $url );
    }
    return $url;
}

/**
 * Abonneerlinks (per taal in te stellen via Weergave > Customizer > EQ5 Podcast).
 */
function balans_podcast_links() {
    $lang  = balans_lang();
    $links = array(
        'spotify' => array( 'Spotify', get_theme_mod( 'eq5_podcast_spotify_' . $lang, '' ) ),
        'apple'   => array( 'Apple Podcasts', get_theme_mod( 'eq5_podcast_apple_' . $lang, '' ) ),
        'rss'     => array( 'RSS', balans_has_podcast_plugin() ? balans_podcast_feed_url() : '' ),
    );
    return array_filter( $links, function ( $link ) {
        return ! empty( $link[1] );
    } );
}

function balans_podcast_customizer( $wp_customize ) {
    $wp_customize->add_section( 'eq5_podcast', array(
        'title'       => __( 'EQ5 Podcast', 'balans' ),
        'description' => __( 'Links naar je podcast per taal. Lege velden worden niet getoond.', 'balans' ),
        'priority'    => 160,
    ) );
    foreach ( array( 'nl' => 'NL', 'en' => 'EN', 'fr' => 'FR' ) as $lang => $label ) {
        foreach ( array( 'spotify' => 'Spotify', 'apple' => 'Apple Podcasts' ) as $key => $name ) {
            $id = 'eq5_podcast_' . $key . '_' . $lang;
            $wp_customize->add_setting( $id, array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
            $wp_customize->add_control( $id, array(
                'label'   => $name . ' (' . $label . ')',
                'section' => 'eq5_podcast',
                'type'    => 'url',
            ) );
        }
    }
}
add_action( 'customize_register', 'balans_podcast_customizer' );

/**
 * Afleveringen en series vertaalbaar in Polylang.
 */
add_filter( 'pll_get_post_types', function ( $types ) {
    $types['podcast'] = 'podcast';
    return $types;
} );
add_filter( 'pll_get_taxonomies', function ( $taxonomies ) {
    $taxonomies['series'] = 'series';
    return $taxonomies;
} );

/**
 * Privé- en taalfeeds niet laten omleiden (anders verdwijnt ?memberkey= of ?lang=).
 */
function balans_keep_feed_params( $redirect_url ) {
    if ( is_feed() && ( isset( $_GET['memberkey'] ) || isset( $_GET['lang'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
        return false;
    }
    return $redirect_url;
}
add_filter( 'redirect_canonical', 'balans_keep_feed_params', 99 );
add_filter( 'pll_check_canonical_url', 'balans_keep_feed_params', 99 );
