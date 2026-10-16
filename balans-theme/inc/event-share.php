<?php
/**
 * Events: delen, in agenda zetten (.ics), interesselijst voor nieuwe events en testmails.
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ---------------------------------------------------------------
 * Delen (gewone links, geen trackers of externe scripts)
 * ------------------------------------------------------------- */

function balans_share_links( $post_id ) {
    $url   = get_permalink( $post_id );
    $title = get_the_title( $post_id );
    $text  = $title . ' — ' . balans_event_date( $post_id );
    return array(
        'whatsapp' => array( 'WhatsApp', 'https://wa.me/?text=' . rawurlencode( $text . ' ' . $url ) ),
        'facebook' => array( 'Facebook', 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $url ) ),
        'linkedin' => array( 'LinkedIn', 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode( $url ) ),
        'x'        => array( 'X', 'https://x.com/intent/post?text=' . rawurlencode( $text ) . '&url=' . rawurlencode( $url ) ),
        'email'    => array( __( 'E-mail', 'balans' ), 'mailto:?subject=' . rawurlencode( $title ) . '&body=' . rawurlencode( $text . "\n\n" . $url ) ),
    );
}

/* ---------------------------------------------------------------
 * In agenda zetten (.ics)
 * ------------------------------------------------------------- */

function balans_event_ics_url( $event_id ) {
    return add_query_arg( 'balans_ics', (int) $event_id, home_url( '/' ) );
}

function balans_ics_escape( $text ) {
    return str_replace( array( '\\', ';', ',', "\r\n", "\n" ), array( '\\\\', '\;', '\,', '\n', '\n' ), wp_strip_all_tags( html_entity_decode( $text, ENT_QUOTES, 'UTF-8' ) ) );
}

function balans_event_ics( $event_id ) {
    $tz    = wp_timezone();
    $date  = get_post_meta( $event_id, '_event_date', true );
    $start = get_post_meta( $event_id, '_event_start', true );
    $end   = get_post_meta( $event_id, '_event_end', true );
    $utc   = new DateTimeZone( 'UTC' );
    if ( $start ) {
        $from = new DateTime( $date . ' ' . $start, $tz );
        $to   = $end ? new DateTime( $date . ' ' . $end, $tz ) : ( clone $from )->modify( '+3 hours' );
        $when = 'DTSTART:' . $from->setTimezone( $utc )->format( 'Ymd\THis\Z' ) . "\r\nDTEND:" . $to->setTimezone( $utc )->format( 'Ymd\THis\Z' );
    } else {
        $day  = new DateTime( $date, $tz );
        $when = 'DTSTART;VALUE=DATE:' . $day->format( 'Ymd' ) . "\r\nDTEND;VALUE=DATE:" . $day->modify( '+1 day' )->format( 'Ymd' );
    }
    $lines = array(
        'BEGIN:VCALENDAR',
        'VERSION:2.0',
        'PRODID:-//EQ5//Balans//NL',
        'CALSCALE:GREGORIAN',
        'METHOD:PUBLISH',
        'BEGIN:VEVENT',
        'UID:event-' . balans_event_main_id( $event_id ) . '@' . wp_parse_url( home_url(), PHP_URL_HOST ),
        'DTSTAMP:' . gmdate( 'Ymd\THis\Z' ),
        $when,
        'SUMMARY:' . balans_ics_escape( get_the_title( $event_id ) ),
        'LOCATION:' . balans_ics_escape( get_post_meta( $event_id, '_event_location', true ) ),
        'DESCRIPTION:' . balans_ics_escape( get_the_excerpt( $event_id ) . "\n\n" . get_permalink( $event_id ) ),
        'URL:' . get_permalink( $event_id ),
        'END:VEVENT',
        'END:VCALENDAR',
    );
    return implode( "\r\n", $lines ) . "\r\n";
}

add_action( 'template_redirect', function () {
    $id = isset( $_GET['balans_ics'] ) ? absint( $_GET['balans_ics'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
    if ( ! $id ) {
        return;
    }
    if ( 'balans_event' !== get_post_type( $id ) || 'publish' !== get_post_status( $id ) || ! get_post_meta( $id, '_event_date', true ) ) {
        status_header( 404 );
        exit;
    }
    nocache_headers();
    header( 'Content-Type: text/calendar; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( get_post_field( 'post_name', $id ) ) . '.ics"' );
    echo balans_event_ics( $id ); // phpcs:ignore WordPress.Security.EscapeOutput
    exit;
} );

/* Link in de bestelmail van WooCommerce (ticketmail). */
add_action( 'woocommerce_email_after_order_table', function ( $order, $sent_to_admin, $plain_text ) {
    if ( $sent_to_admin ) {
        return;
    }
    $lang = balans_mail_lang( $order->get_meta( '_balans_lang' ) );
    $t    = balans_mail_texts( $lang );
    foreach ( $order->get_items() as $item ) {
        $main = (int) get_post_meta( $item->get_product_id(), '_balans_event_id', true );
        if ( ! $main ) {
            continue;
        }
        $event = balans_event_lang_id( $main, $lang );
        $label = $t['ics_link'] . ': ' . get_the_title( $event );
        if ( $plain_text ) {
            echo "\n" . esc_html( $label ) . "\n" . esc_url_raw( balans_event_ics_url( $event ) ) . "\n";
        } else {
            echo '<p style="margin:16px 0;"><a href="' . esc_url( balans_event_ics_url( $event ) ) . '" style="color:#3F7F83;font-weight:bold;">' . esc_html( $label ) . '</a></p>';
        }
    }
}, 10, 3 );

/* ---------------------------------------------------------------
 * Interesselijst: bericht bij elk nieuw event
 * ------------------------------------------------------------- */

function balans_interest_list() {
    $list = get_option( 'balans_interest', array() );
    return is_array( $list ) ? $list : array();
}

function balans_interest_token( $email ) {
    return substr( wp_hash( 'interest|' . strtolower( $email ) ), 0, 20 );
}

function balans_interest_unsub_url( $email ) {
    return add_query_arg( array( 'eq5_interest_unsub' => rawurlencode( $email ), 'k' => balans_interest_token( $email ) ), home_url( '/' ) );
}

function balans_handle_interest() {
    check_ajax_referer( 'balans_nonce', 'nonce' );
    $name  = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
    $email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
    $lang  = balans_mail_lang( isset( $_POST['lang'] ) ? sanitize_key( wp_unslash( $_POST['lang'] ) ) : 'nl' );
    $errors = array();
    if ( '' === $name ) {
        $errors[] = __( 'Vul je naam in.', 'balans' );
    }
    if ( ! is_email( $email ) ) {
        $errors[] = __( 'Vul een geldig e-mailadres in.', 'balans' );
    }
    if ( empty( $_POST['consent'] ) ) {
        $errors[] = __( 'Geef toestemming om je een e-mail te sturen over nieuwe events.', 'balans' );
    }
    if ( $errors ) {
        wp_send_json_error( array( 'errors' => $errors ) );
    }
    $list = balans_interest_list();
    $list[ strtolower( $email ) ] = array( 'name' => $name, 'lang' => $lang, 'time' => time() );
    update_option( 'balans_interest', $list, false );
    wp_send_json_success( array( 'message' => __( 'Dank je wel! Je krijgt een e-mail zodra er een nieuw event is.', 'balans' ) ) );
}
add_action( 'wp_ajax_balans_interest', 'balans_handle_interest' );
add_action( 'wp_ajax_nopriv_balans_interest', 'balans_handle_interest' );

/* Eerste keer publiceren: na 15 minuten mailen, zodat er tijd is om de vertalingen toe te voegen. */
add_action( 'transition_post_status', function ( $new, $old, $post ) {
    if ( 'balans_event' !== $post->post_type || 'publish' !== $new || 'publish' === $old ) {
        return;
    }
    $main = balans_event_main_id( $post->ID );
    if ( get_post_meta( $main, '_event_announced', true ) || balans_event_is_past( $post->ID ) ) {
        return;
    }
    update_post_meta( $main, '_event_announced', time() );
    wp_schedule_single_event( time() + 15 * MINUTE_IN_SECONDS, 'balans_announce_event', array( $main ) );
}, 20, 3 );

function balans_announce_event( $main ) {
    $sent = 0;
    if ( 'publish' !== get_post_status( $main ) || balans_event_is_past( $main ) ) {
        return 0;
    }
    foreach ( balans_interest_list() as $email => $entry ) {
        $sent += balans_interest_mail( $email, $entry, $main ) ? 1 : 0;
    }
    return $sent;
}
add_action( 'balans_announce_event', 'balans_announce_event' );

function balans_interest_mail( $email, $entry, $main ) {
    $lang  = balans_mail_lang( $entry['lang'] );
    $event = balans_event_lang_id( $main, $lang );
    $t     = balans_mail_texts( $lang );
    $sw    = balans_mail_locale( $lang );
    $vars  = array( '{naam}' => $entry['name'], '{event}' => get_the_title( $event ) );
    $parts = array(
        esc_html( balans_mail_fill( $t['welcome_hi'], $vars ) ),
        nl2br( esc_html( balans_mail_fill( $t['interest_body'], $vars ) ) ),
        '<strong>' . esc_html( get_the_title( $event ) ) . '</strong><br>' . esc_html( balans_event_date( $event ) . ( get_post_meta( $event, '_event_location', true ) ? ' · ' . get_post_meta( $event, '_event_location', true ) : '' ) ),
        balans_mail_button( get_permalink( $event ), $t['waitlist_button'] ),
    );
    $html = balans_mail_html( $lang, $parts );
    $html = str_replace( '</div></div>', '<p style="margin:28px 0 0;font-family:Arial,sans-serif;font-size:12px;"><a style="color:#8A7D71;" href="' . esc_url( balans_interest_unsub_url( $email ) ) . '">' . esc_html( $t['interest_unsub'] ) . '</a></p></div></div>', $html );
    if ( $sw ) {
        restore_previous_locale();
    }
    return wp_mail( $email, balans_mail_fill( $t['interest_subject'], $vars ), $html, array( 'Content-Type: text/html; charset=UTF-8' ) );
}

add_action( 'template_redirect', function () {
    if ( empty( $_GET['eq5_interest_unsub'] ) || empty( $_GET['k'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
        return;
    }
    $email = strtolower( sanitize_email( wp_unslash( $_GET['eq5_interest_unsub'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
    $list  = balans_interest_list();
    $lang  = isset( $list[ $email ] ) ? $list[ $email ]['lang'] : 'nl';
    if ( ! hash_equals( balans_interest_token( $email ), sanitize_text_field( wp_unslash( $_GET['k'] ) ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
        wp_die( esc_html__( 'Deze afmeldlink is ongeldig.', 'balans' ), 'EQ5', array( 'response' => 400 ) );
    }
    unset( $list[ $email ] );
    update_option( 'balans_interest', $list, false );
    wp_die( esc_html( balans_mail_texts( $lang )['interest_unsubscribed'] ), 'EQ5', array( 'response' => 200 ) );
} );

/* ---------------------------------------------------------------
 * Testmail (Balans → E-mailteksten)
 * ------------------------------------------------------------- */

function balans_test_mail_types() {
    return array(
        'welcome'   => __( 'Welkomstmail abonnement', 'balans' ),
        'remind'    => __( 'Wekelijkse herinnering', 'balans' ),
        'monthly'   => __( 'Maandelijks rustmoment', 'balans' ),
        'appt'      => __( 'Afspraakbevestiging', 'balans' ),
        'event'     => __( 'Event-herinnering (dag van tevoren)', 'balans' ),
        'waitlist'  => __( 'Wachtlijst: plek vrijgekomen', 'balans' ),
        'followup'  => __( 'Nagesprek (dag na het event)', 'balans' ),
        'interest'  => __( 'Nieuw event (interesselijst)', 'balans' ),
    );
}

function balans_test_mail_form( $lang ) {
    $sent = isset( $_GET['test'] ) ? sanitize_key( $_GET['test'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
    $msg  = array( 'ok' => array( 'success', __( 'Testmail verstuurd.', 'balans' ) ), 'fail' => array( 'error', __( 'Versturen is mislukt. Controleer het e-mailadres en de mailinstellingen van de server.', 'balans' ) ), 'noevent' => array( 'warning', __( 'Maak eerst een event aan om deze testmail te kunnen versturen.', 'balans' ) ), 'nodienst' => array( 'warning', __( 'Er is nog geen abonnement (Diensten) in deze taal.', 'balans' ) ) );
    echo '<hr><h2 id="testmail">' . esc_html__( 'Testmail versturen', 'balans' ) . '</h2>';
    if ( isset( $msg[ $sent ] ) ) {
        echo '<div class="notice notice-' . esc_attr( $msg[ $sent ][0] ) . '"><p>' . esc_html( $msg[ $sent ][1] ) . '</p></div>';
    }
    echo '<p>' . esc_html__( 'Verstuurt een voorbeeld met de teksten van het gekozen taal-tabblad. Voor de event-mails wordt het eerstvolgende (of laatste) event gebruikt.', 'balans' ) . '</p>';
    echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
    wp_nonce_field( 'balans_mail_test' );
    echo '<input type="hidden" name="action" value="balans_mail_test"><input type="hidden" name="lang" value="' . esc_attr( $lang ) . '">';
    echo '<table class="form-table" role="presentation"><tr><th><label for="balans-test-type">' . esc_html__( 'Welke mail', 'balans' ) . '</label></th><td><select data-testid="test-type" id="balans-test-type" name="type">';
    foreach ( balans_test_mail_types() as $key => $label ) {
        echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</option>';
    }
    echo '</select></td></tr><tr><th><label for="balans-test-to">' . esc_html__( 'Naar e-mailadres', 'balans' ) . '</label></th><td><input data-testid="test-to" required type="email" class="regular-text" id="balans-test-to" name="to" value=""></td></tr></table>';
    echo '<p><button data-testid="test-send" class="button">' . esc_html__( 'Testmail versturen', 'balans' ) . '</button></p></form>';
}

function balans_send_test_mail( $type, $to, $lang ) {
    $force = function ( $args ) use ( $to ) {
        $args['to']      = $to;
        $args['subject'] = '[TEST] ' . $args['subject'];
        return $args;
    };
    add_filter( 'wp_mail', $force, 999 );
    $user  = wp_get_current_user();
    $event = balans_upcoming_events( 1 );
    $event = $event ? $event[0] : get_posts( array( 'post_type' => 'balans_event', 'numberposts' => 1, 'lang' => '' ) );
    $event = is_array( $event ) ? ( $event ? $event[0] : null ) : $event;
    $order = new class( $to, $lang ) {
        private $to;
        private $lang;
        public function __construct( $to, $lang ) {
            $this->to   = $to;
            $this->lang = $lang;
        }
        public function get_meta( $key ) {
            return '_balans_lang' === $key ? $this->lang : '';
        }
        public function get_billing_first_name() {
            return 'Voorbeeld';
        }
        public function get_billing_email() {
            return $this->to;
        }
    };
    $result = 'fail';
    if ( in_array( $type, array( 'event', 'waitlist', 'followup', 'interest' ), true ) && ! $event ) {
        $result = 'noevent';
    } elseif ( in_array( $type, array( 'welcome', 'remind', 'monthly' ), true ) ) {
        $dienst = get_posts( array( 'post_type' => 'balans_dienst', 'numberposts' => 1, 'lang' => balans_has_polylang() ? $lang : '', 'meta_key' => '_dienst_group', 'meta_value' => 'abonnement' ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
        if ( ! $dienst ) {
            $result = 'nodienst';
        } else {
            $ok     = 'welcome' === $type ? balans_send_welcome( $user, $lang, $dienst[0] ) : balans_reminder_mail( $user, $lang, $type, $dienst[0]->post_title );
            $result = $ok ? 'ok' : 'fail';
        }
    } else {
        $main  = $event ? balans_event_main_id( $event->ID ) : 0;
        $entry = array( 'name' => 'Voorbeeld', 'lang' => $lang, 'time' => time() );
        switch ( $type ) {
            case 'appt':
                $ok = balans_appointment_confirmation( $to, 'Voorbeeld', 'consult', wp_date( 'Y-m-d', time() + WEEK_IN_SECONDS ), '10:00', $lang );
                break;
            case 'event':
                $ok = balans_event_reminder_mail( $order, $main, 2 );
                break;
            case 'waitlist':
                $ok = balans_waitlist_mail( $to, $entry, $main );
                break;
            case 'followup':
                $ok = balans_event_followup_mail( $order, $main );
                break;
            default:
                $ok = balans_interest_mail( $to, $entry, $main );
        }
        $result = $ok ? 'ok' : 'fail';
    }
    remove_filter( 'wp_mail', $force, 999 );
    return $result;
}

add_action( 'admin_post_balans_mail_test', function () {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'Geen toegang.', 'balans' ) );
    }
    check_admin_referer( 'balans_mail_test' );
    $lang = balans_mail_lang( isset( $_POST['lang'] ) ? sanitize_key( $_POST['lang'] ) : 'nl' );
    $to   = isset( $_POST['to'] ) ? sanitize_email( wp_unslash( $_POST['to'] ) ) : '';
    $type = isset( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : '';
    $res  = is_email( $to ) && isset( balans_test_mail_types()[ $type ] ) ? balans_send_test_mail( $type, $to, $lang ) : 'fail';
    wp_safe_redirect( admin_url( 'admin.php?page=balans&lang=' . $lang . '&test=' . $res . '#testmail' ) );
    exit;
} );
