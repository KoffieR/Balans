<?php
/**
 * Events: wachtlijst, nagesprek-mail, deelnemerslijst en het "Upcoming"-blok (Customizer).
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function balans_event_lang_id( $main, $lang ) {
    $id = balans_has_polylang() ? pll_get_post( $main, $lang ) : 0;
    return $id ? (int) $id : (int) $main;
}

function balans_mail_locale( $lang ) {
    return switch_to_locale( array( 'nl' => 'nl_NL', 'en' => 'en_GB', 'fr' => 'fr_FR' )[ balans_mail_lang( $lang ) ] );
}

function balans_mail_button( $url, $label ) {
    return '<a href="' . esc_url( $url ) . '" style="display:inline-block;background:#3F7F83;color:#fff;text-decoration:none;padding:12px 26px;border-radius:999px;font-family:Arial,sans-serif;font-size:15px;">' . esc_html( $label ) . '</a>';
}

/* ---------------------------------------------------------------
 * Wachtlijst
 * ------------------------------------------------------------- */

function balans_waitlist( $event_id ) {
    $list = get_post_meta( balans_event_main_id( $event_id ), '_event_waitlist', true );
    return is_array( $list ) ? $list : array();
}

function balans_handle_waitlist() {
    check_ajax_referer( 'balans_nonce', 'nonce' );
    $event = isset( $_POST['event'] ) ? absint( $_POST['event'] ) : 0;
    $name  = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
    $email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
    $lang  = balans_mail_lang( isset( $_POST['lang'] ) ? sanitize_key( wp_unslash( $_POST['lang'] ) ) : 'nl' );
    $errors = array();
    if ( 'balans_event' !== get_post_type( $event ) ) {
        $errors[] = __( 'Onbekend event.', 'balans' );
    }
    if ( '' === $name ) {
        $errors[] = __( 'Vul je naam in.', 'balans' );
    }
    if ( ! is_email( $email ) ) {
        $errors[] = __( 'Vul een geldig e-mailadres in.', 'balans' );
    }
    if ( empty( $_POST['consent'] ) ) {
        $errors[] = __( 'Geef toestemming om je gegevens voor de wachtlijst te gebruiken.', 'balans' );
    }
    if ( $errors ) {
        wp_send_json_error( array( 'errors' => $errors ) );
    }
    $main = balans_event_main_id( $event );
    $list = balans_waitlist( $main );
    $list[ strtolower( $email ) ] = array( 'name' => $name, 'lang' => $lang, 'time' => time() );
    update_post_meta( $main, '_event_waitlist', $list );
    wp_send_json_success( array( 'message' => __( 'Je staat op de wachtlijst. Komt er een plek vrij, dan krijg je meteen een e-mail.', 'balans' ) ) );
}
add_action( 'wp_ajax_balans_waitlist', 'balans_handle_waitlist' );
add_action( 'wp_ajax_nopriv_balans_waitlist', 'balans_handle_waitlist' );

/**
 * Komt een uitverkocht ticket weer op voorraad, dan krijgt iedereen op de wachtlijst tegelijk een mail.
 */
add_action( 'woocommerce_product_set_stock_status', function ( $product_id, $status ) {
    $main = (int) get_post_meta( $product_id, '_balans_event_id', true );
    if ( 'instock' !== $status || ! $main || balans_event_is_past( $main ) ) {
        return;
    }
    balans_notify_waitlist( $main );
}, 10, 2 );

function balans_notify_waitlist( $main ) {
    $list = balans_waitlist( $main );
    $sent = 0;
    foreach ( $list as $email => $entry ) {
        $sent += balans_waitlist_mail( $email, $entry, $main ) ? 1 : 0;
    }
    delete_post_meta( $main, '_event_waitlist' );
    return $sent;
}

function balans_waitlist_mail( $email, $entry, $main ) {
    $lang  = balans_mail_lang( $entry['lang'] );
    $event = balans_event_lang_id( $main, $lang );
    $t     = balans_mail_texts( $lang );
    $sw    = balans_mail_locale( $lang );
    $vars  = array( '{naam}' => $entry['name'], '{event}' => get_the_title( $event ) );
    $html  = balans_mail_html( $lang, array(
        esc_html( balans_mail_fill( $t['welcome_hi'], $vars ) ),
        nl2br( esc_html( balans_mail_fill( $t['waitlist_body'], $vars ) ) ),
        esc_html( balans_event_date( $event ) . ( balans_event_time( $event ) ? ' · ' . balans_event_time( $event ) : '' ) ),
        balans_mail_button( get_permalink( $event ), $t['waitlist_button'] ),
    ) );
    if ( $sw ) {
        restore_previous_locale();
    }
    return wp_mail( $email, balans_mail_fill( $t['waitlist_subject'], $vars ), $html, array( 'Content-Type: text/html; charset=UTF-8' ) );
}

/* ---------------------------------------------------------------
 * Nagesprek-mail (dag na het event)
 * ------------------------------------------------------------- */

function balans_event_followup_mail( $order, $main ) {
    $lang  = balans_mail_lang( $order->get_meta( '_balans_lang' ) );
    $event = balans_event_lang_id( $main, $lang );
    $t     = balans_mail_texts( $lang );
    $sw    = balans_mail_locale( $lang );
    $vars  = array( '{naam}' => $order->get_billing_first_name(), '{event}' => get_the_title( $event ) );
    $parts = array(
        esc_html( balans_mail_fill( $t['welcome_hi'], $vars ) ),
        nl2br( esc_html( balans_mail_fill( $t['followup_body'], $vars ) ) ),
        '<span style="display:block;border-left:3px solid #B8925A;padding:4px 0 4px 16px;">'
            . '<span style="display:block;font-size:13px;color:#8B6B47;text-transform:uppercase;letter-spacing:1px;">' . esc_html( $t['reflect_intro'] ) . '</span>'
            . '<span style="display:block;font-size:19px;font-style:italic;margin:6px 0;">' . esc_html( balans_mail_fill( $t['followup_question'], $vars ) ) . '</span>'
            . '<span style="display:block;font-size:14px;color:#6B5D50;">' . esc_html( $t['reflect_tip'] ) . '</span></span>',
    );
    $next = array_values( array_filter( balans_upcoming_events( 10 ), function ( $e ) use ( $main ) {
        return balans_event_main_id( $e->ID ) !== (int) $main;
    } ) );
    if ( $next ) {
        $n       = balans_event_lang_id( $next[0]->ID, $lang );
        $parts[] = '<span style="display:block;background:#EEF4F4;border-radius:8px;padding:18px 20px;">'
            . '<span style="display:block;font-size:13px;color:#3F7F83;text-transform:uppercase;letter-spacing:1px;">' . esc_html( $t['followup_next'] ) . '</span>'
            . '<span style="display:block;font-size:19px;margin:6px 0;">' . esc_html( get_the_title( $n ) ) . '</span>'
            . '<span style="display:block;font-size:14px;color:#6B5D50;margin-bottom:12px;">' . esc_html( balans_event_date( $n ) ) . '</span>'
            . balans_mail_button( get_permalink( $n ), $t['waitlist_button'] ) . '</span>';
    }
    $html = balans_mail_html( $lang, $parts );
    if ( $sw ) {
        restore_previous_locale();
    }
    return wp_mail( $order->get_billing_email(), balans_mail_fill( $t['followup_subject'], $vars ), $html, array( 'Content-Type: text/html; charset=UTF-8' ) );
}

/* ---------------------------------------------------------------
 * Deelnemerslijst (backend, printbaar)
 * ------------------------------------------------------------- */

function balans_attendees_url( $event_id ) {
    return admin_url( 'edit.php?post_type=balans_event&page=balans-attendees&event=' . balans_event_main_id( $event_id ) );
}

add_action( 'admin_menu', function () {
    add_submenu_page( 'edit.php?post_type=balans_event', __( 'Deelnemerslijst', 'balans' ), __( 'Deelnemerslijst', 'balans' ), 'edit_posts', 'balans-attendees', 'balans_attendees_page' );
} );

add_filter( 'post_row_actions', function ( $actions, $post ) {
    if ( 'balans_event' === $post->post_type ) {
        $actions['attendees'] = '<a href="' . esc_url( balans_attendees_url( $post->ID ) ) . '">' . esc_html__( 'Deelnemerslijst', 'balans' ) . '</a>';
    }
    return $actions;
}, 10, 2 );

add_action( 'post_submitbox_misc_actions', function ( $post ) {
    if ( 'balans_event' === $post->post_type && 'auto-draft' !== $post->post_status ) {
        echo '<div class="misc-pub-section"><a data-testid="attendees-button" class="button" href="' . esc_url( balans_attendees_url( $post->ID ) ) . '">' . esc_html__( 'Deelnemerslijst', 'balans' ) . '</a></div>';
    }
} );

function balans_attendees_page() {
    $main = isset( $_GET['event'] ) ? absint( $_GET['event'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
    echo '<div class="wrap balans-attendees">';
    if ( 'balans_event' !== get_post_type( $main ) ) {
        echo '<h1>' . esc_html__( 'Deelnemerslijst', 'balans' ) . '</h1><p>' . esc_html__( 'Kies een event:', 'balans' ) . '</p><ul>';
        foreach ( get_posts( array( 'post_type' => 'balans_event', 'numberposts' => 50, 'lang' => balans_has_polylang() ? pll_default_language() : '', 'meta_key' => '_event_date', 'orderby' => 'meta_value', 'order' => 'DESC' ) ) as $e ) { // phpcs:ignore WordPress.DB.SlowDBQuery
            echo '<li><a href="' . esc_url( balans_attendees_url( $e->ID ) ) . '">' . esc_html( get_the_title( $e ) . ' — ' . balans_event_date( $e->ID ) ) . '</a></li>';
        }
        echo '</ul>';
        $interest = balans_interest_list();
        echo '<h2>' . esc_html__( 'Interesselijst (bericht bij elk nieuw event)', 'balans' ) . ' (' . count( $interest ) . ')</h2>';
        if ( $interest ) {
            echo '<table class="widefat striped" data-testid="interest-table"><tbody>';
            foreach ( $interest as $email => $entry ) {
                echo '<tr><td>' . esc_html( $entry['name'] ) . '</td><td>' . esc_html( $email ) . '</td><td>' . esc_html( strtoupper( $entry['lang'] ) ) . '</td><td>' . esc_html( wp_date( 'j-n-Y', $entry['time'] ) ) . '</td></tr>';
            }
            echo '</tbody></table>';
        }
        echo '</div>';
        return;
    }
    $pid   = (int) get_post_meta( $main, '_event_product_id', true );
    $rows  = $pid ? balans_event_orders( $pid ) : array();
    $total = array_sum( wp_list_pluck( $rows, 1 ) );
    echo '<style>@media print{#adminmenumain,#wpadminbar,#wpfooter,.notice,.balans-noprint{display:none!important}#wpcontent{margin:0!important;padding:0!important}.balans-attendees table{font-size:12pt}}.balans-attendees td.check{width:40px}</style>';
    echo '<h1>' . esc_html( get_the_title( $main ) ) . '</h1>';
    echo '<p><strong>' . esc_html( balans_event_date( $main ) ) . '</strong> · ' . esc_html( balans_event_time( $main ) ) . ' · ' . esc_html( get_post_meta( $main, '_event_location', true ) ) . '</p>';
    /* translators: 1: verkochte tickets, 2: maximum */
    echo '<p data-testid="attendees-total">' . esc_html( sprintf( __( '%1$d tickets verkocht (maximaal %2$d plaatsen)', 'balans' ), $total, (int) get_post_meta( $main, '_event_capacity', true ) ) ) . '</p>';
    echo '<p class="balans-noprint"><button data-testid="attendees-print" class="button button-primary" onclick="window.print()">' . esc_html__( 'Printen', 'balans' ) . '</button> <a class="button" href="' . esc_url( get_edit_post_link( $main ) ) . '">' . esc_html__( 'Terug naar het event', 'balans' ) . '</a></p>';
    echo '<table class="widefat striped" data-testid="attendees-table"><thead><tr><th class="check">✓</th><th>' . esc_html__( 'Naam', 'balans' ) . '</th><th>' . esc_html__( 'E-mail', 'balans' ) . '</th><th>' . esc_html__( 'Tickets', 'balans' ) . '</th><th>' . esc_html__( 'Bestelling', 'balans' ) . '</th></tr></thead><tbody>';
    if ( ! $rows ) {
        echo '<tr><td colspan="5">' . esc_html__( 'Nog geen deelnemers.', 'balans' ) . '</td></tr>';
    }
    usort( $rows, function ( $a, $b ) {
        return strcasecmp( $a[0]->get_billing_last_name(), $b[0]->get_billing_last_name() );
    } );
    foreach ( $rows as $row ) {
        list( $order, $qty ) = $row;
        printf( '<tr><td class="check">☐</td><td>%1$s</td><td>%2$s</td><td>%3$d</td><td><a href="%4$s">#%5$s</a></td></tr>', esc_html( $order->get_formatted_billing_full_name() ), esc_html( $order->get_billing_email() ), (int) $qty, esc_url( $order->get_edit_order_url() ), esc_html( $order->get_order_number() ) );
    }
    echo '</tbody></table>';
    $wait = balans_waitlist( $main );
    if ( $wait ) {
        echo '<h2>' . esc_html__( 'Wachtlijst', 'balans' ) . ' (' . count( $wait ) . ')</h2><table class="widefat striped"><tbody>';
        foreach ( $wait as $email => $entry ) {
            echo '<tr><td>' . esc_html( $entry['name'] ) . '</td><td>' . esc_html( $email ) . '</td><td>' . esc_html( strtoupper( $entry['lang'] ) ) . '</td><td>' . esc_html( wp_date( 'j-n-Y H:i', $entry['time'] ) ) . '</td></tr>';
        }
        echo '</tbody></table>';
    }
    echo '</div>';
}

/* ---------------------------------------------------------------
 * "Upcoming" bij de dienst Event (Weergave → Customizer → Homepage: Upcoming)
 * ------------------------------------------------------------- */

function balans_upcoming_defaults( $lang ) {
    $d = array(
        'nl' => array( 'Upcoming', 'Binnenkort: middagen en avonden rond thema\'s als Familie en vrienden, Geld en Gezondheid. Houd deze website in de gaten!' ),
        'en' => array( 'Upcoming', 'Coming soon: afternoons and evenings around themes such as Family and friends, Money and Health. Keep an eye on this website!' ),
        'fr' => array( 'Upcoming', 'Bientôt : des après-midi et des soirées autour de thèmes comme Famille et amis, Argent et Santé. Restez à l\'écoute !' ),
    );
    return $d[ balans_mail_lang( $lang ) ];
}

function balans_upcoming() {
    $lang = balans_mail_lang( balans_lang() );
    $def  = balans_upcoming_defaults( $lang );
    if ( ! get_theme_mod( 'eq5_upcoming_show', true ) ) {
        return null;
    }
    $data = array(
        'badge' => get_theme_mod( 'eq5_upcoming_badge_' . $lang, $def[0] ),
        'text'  => get_theme_mod( 'eq5_upcoming_text_' . $lang, $def[1] ),
        'next'  => null,
    );
    if ( get_theme_mod( 'eq5_upcoming_next', true ) ) {
        $next         = balans_upcoming_events( 1 );
        $data['next'] = $next ? $next[0] : null;
    }
    return $data;
}

/**
 * Dienst "Event" aanvullen met het Upcoming-label en de tekst of het eerstvolgende event.
 */
function balans_route_upcoming( $route ) {
    if ( empty( $route['upcoming'] ) ) {
        return $route;
    }
    $u = balans_upcoming();
    if ( ! $u ) {
        return $route;
    }
    $route['badge'] = $u['badge'];
    if ( $u['next'] ) {
        $route['eyebrow'] = balans_event_date( $u['next']->ID );
        $route['text']    = get_the_title( $u['next'] );
        $route['link']    = get_permalink( $u['next'] );
    } else {
        $route['text'] = $u['text'];
    }
    return $route;
}

add_action( 'customize_register', function ( $wp_customize ) {
    $wp_customize->add_section( 'eq5_upcoming', array(
        'title'       => __( 'Homepage: Upcoming', 'balans' ),
        'description' => __( 'Het label en de tekst bij de dienst "Event" op de homepage en de pagina Voor jou. Per taal aan te passen.', 'balans' ),
        'priority'    => 31,
    ) );
    $wp_customize->add_setting( 'eq5_upcoming_show', array( 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean' ) );
    $wp_customize->add_control( 'eq5_upcoming_show', array( 'label' => __( 'Upcoming-label en tekst tonen', 'balans' ), 'section' => 'eq5_upcoming', 'type' => 'checkbox' ) );
    $wp_customize->add_setting( 'eq5_upcoming_next', array( 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean' ) );
    $wp_customize->add_control( 'eq5_upcoming_next', array( 'label' => __( 'Zodra er een event gepland is: het eerstvolgende event tonen in plaats van de tekst', 'balans' ), 'section' => 'eq5_upcoming', 'type' => 'checkbox' ) );
    foreach ( array( 'nl' => 'NL', 'en' => 'EN', 'fr' => 'FR' ) as $lang => $label ) {
        $def = balans_upcoming_defaults( $lang );
        $wp_customize->add_setting( 'eq5_upcoming_badge_' . $lang, array( 'default' => $def[0], 'sanitize_callback' => 'sanitize_text_field' ) );
        $wp_customize->add_control( 'eq5_upcoming_badge_' . $lang, array( 'label' => __( 'Label', 'balans' ) . ' (' . $label . ')', 'section' => 'eq5_upcoming', 'type' => 'text' ) );
        $wp_customize->add_setting( 'eq5_upcoming_text_' . $lang, array( 'default' => $def[1], 'sanitize_callback' => 'sanitize_textarea_field' ) );
        $wp_customize->add_control( 'eq5_upcoming_text_' . $lang, array( 'label' => __( 'Tekst', 'balans' ) . ' (' . $label . ')', 'section' => 'eq5_upcoming', 'type' => 'textarea' ) );
    }
} );
