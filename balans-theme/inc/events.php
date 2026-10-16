<?php
/**
 * Events: georganiseerde middagen/avonden rond een thema, elk event een eigen pagina.
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const BALANS_EVENT_SYNC = array( '_event_date', '_event_start', '_event_end', '_event_price', '_event_url' );

add_action( 'init', function () {
    register_post_type( 'balans_event', array(
        'labels'       => array(
            'name'          => __( 'Events', 'balans' ),
            'singular_name' => __( 'Event', 'balans' ),
            'add_new_item'  => __( 'Nieuw event', 'balans' ),
            'edit_item'     => __( 'Event bewerken', 'balans' ),
            'menu_name'     => __( 'Events', 'balans' ),
        ),
        'public'       => true,
        'has_archive'  => false,
        'rewrite'      => array( 'slug' => 'event' ),
        'menu_icon'    => 'dashicons-groups',
        'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'show_in_rest' => true,
    ) );
} );

function balans_event_fields() {
    return array(
        '_event_date'     => array( __( 'Datum', 'balans' ), 'date' ),
        '_event_start'    => array( __( 'Begintijd', 'balans' ), 'time' ),
        '_event_end'      => array( __( 'Eindtijd', 'balans' ), 'time' ),
        '_event_location' => array( __( 'Locatie', 'balans' ), 'text' ),
        '_event_theme'    => array( __( 'Thema', 'balans' ), 'text' ),
        '_event_price'    => array( __( 'Prijs', 'balans' ), 'text' ),
        '_event_url'      => array( __( 'Aanmeldlink (optioneel, bijv. webshop-product; leeg = contactformulier)', 'balans' ), 'url' ),
    );
}

add_action( 'add_meta_boxes', function () {
    add_meta_box( 'balans_event', __( 'Gegevens van het event', 'balans' ), 'balans_event_meta_box', 'balans_event', 'normal', 'high' );
} );

function balans_event_meta_box( $post ) {
    wp_nonce_field( 'balans_event', 'balans_event_nonce' );
    echo '<table class="form-table">';
    foreach ( balans_event_fields() as $key => $f ) {
        printf(
            '<tr><th><label for="%1$s">%2$s</label></th><td><input type="%3$s" class="regular-text" id="%1$s" name="%1$s" value="%4$s" /></td></tr>',
            esc_attr( $key ),
            esc_html( $f[0] ),
            esc_attr( $f[1] ),
            esc_attr( get_post_meta( $post->ID, $key, true ) )
        );
    }
    echo '</table>';
}

add_action( 'save_post_balans_event', function ( $post_id ) {
    if ( ! isset( $_POST['balans_event_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['balans_event_nonce'] ) ), 'balans_event' ) || ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    foreach ( balans_event_fields() as $key => $f ) {
        $value = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
        update_post_meta( $post_id, $key, 'url' === $f[1] ? esc_url_raw( $value ) : sanitize_text_field( $value ) );
    }
    if ( balans_has_polylang() ) {
        foreach ( pll_get_post_translations( $post_id ) as $id ) {
            if ( (int) $id === (int) $post_id ) {
                continue;
            }
            foreach ( BALANS_EVENT_SYNC as $key ) {
                update_post_meta( $id, $key, get_post_meta( $post_id, $key, true ) );
            }
        }
    }
} );

function balans_upcoming_events( $limit = 20 ) {
    return get_posts( array(
        'post_type'   => 'balans_event',
        'numberposts' => $limit,
        'meta_key'    => '_event_date', // phpcs:ignore WordPress.DB.SlowDBQuery
        'orderby'     => 'meta_value',
        'order'       => 'ASC',
        'meta_query'  => array( array( 'key' => '_event_date', 'value' => wp_date( 'Y-m-d' ), 'compare' => '>=', 'type' => 'DATE' ) ), // phpcs:ignore WordPress.DB.SlowDBQuery
    ) );
}

function balans_event_date( $post_id ) {
    $date = get_post_meta( $post_id, '_event_date', true );
    return $date ? date_i18n( 'l j F Y', strtotime( $date ) ) : '';
}

function balans_event_time( $post_id ) {
    $start = get_post_meta( $post_id, '_event_start', true );
    $end   = get_post_meta( $post_id, '_event_end', true );
    return trim( $start . ( $end ? ' – ' . $end : '' ) );
}

function balans_event_signup_url( $post_id ) {
    $url = get_post_meta( $post_id, '_event_url', true );
    return $url ? $url : balans_url( 'contact', array( 'event' => $post_id ) );
}

function balans_event_is_past( $post_id ) {
    $date = get_post_meta( $post_id, '_event_date', true );
    return $date && $date < wp_date( 'Y-m-d' );
}

/**
 * Oude losse pagina's Consult en Drukpuntmassage + consult doorsturen naar de Hulp-pagina.
 */
add_action( 'template_redirect', function () {
    foreach ( array( 'page-templates/page-consult.php' => '#consult', 'page-templates/page-drukpuntmassage.php' => '#drukpuntmassage' ) as $template => $anchor ) {
        if ( is_page_template( $template ) ) {
            wp_safe_redirect( balans_url( 'afspraak-voor-hulp' ) . $anchor, 301 );
            exit;
        }
    }
} );
