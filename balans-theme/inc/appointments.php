<?php
/**
 * Appointment handling: AJAX endpoint stores an appointment request as a CPT.
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function balans_handle_appointment() {
    check_ajax_referer( 'balans_nonce', 'nonce' );

    $service    = isset( $_POST['service'] ) ? sanitize_text_field( wp_unslash( $_POST['service'] ) ) : '';
    $date       = isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '';
    $time_slot  = isset( $_POST['time_slot'] ) ? sanitize_text_field( wp_unslash( $_POST['time_slot'] ) ) : '';
    $name       = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
    $email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
    $phone      = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
    $message    = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

    $errors = array();
    if ( ! array_key_exists( $service, balans_appointment_services() ) ) {
        $errors[] = __( 'Kies een geldige dienst.', 'balans' );
    }
    if ( empty( $date ) ) {
        $errors[] = __( 'Kies een datum.', 'balans' );
    }
    if ( empty( $time_slot ) ) {
        $errors[] = __( 'Kies een tijdslot.', 'balans' );
    }
    if ( empty( $name ) ) {
        $errors[] = __( 'Vul je naam in.', 'balans' );
    }
    if ( empty( $email ) || ! is_email( $email ) ) {
        $errors[] = __( 'Vul een geldig e-mailadres in.', 'balans' );
    }

    if ( ! empty( $errors ) ) {
        wp_send_json_error( array( 'errors' => $errors ) );
    }

    $title = sprintf(
        /* translators: 1: service, 2: name, 3: date, 4: time */
        __( '%1$s — %2$s — %3$s %4$s', 'balans' ),
        ucfirst( $service ),
        $name,
        $date,
        $time_slot
    );

    $post_id = wp_insert_post( array(
        'post_type'   => 'balans_afspraak',
        'post_title'  => $title,
        'post_status' => 'publish',
        'meta_input'  => array(
            '_balans_service'   => $service,
            '_balans_date'      => $date,
            '_balans_time_slot' => $time_slot,
            '_balans_name'      => $name,
            '_balans_email'     => $email,
            '_balans_phone'     => $phone,
            '_balans_message'   => $message,
            '_balans_status'    => 'nieuw',
        ),
    ) );

    if ( is_wp_error( $post_id ) || ! $post_id ) {
        wp_send_json_error( array( 'errors' => array( __( 'Er ging iets mis. Probeer het later opnieuw.', 'balans' ) ) ) );
    }

    // Notify admin.
    $admin_email = get_option( 'admin_email' );
    $subject     = sprintf( __( 'Nieuwe afspraakaanvraag: %s', 'balans' ), $title );
    $body        = sprintf(
        "Nieuwe afspraakaanvraag ontvangen.\n\nDienst: %s\nDatum: %s\nTijd: %s\nNaam: %s\nE-mail: %s\nTelefoon: %s\n\nBericht:\n%s",
        $service,
        $date,
        $time_slot,
        $name,
        $email,
        $phone,
        $message
    );
    wp_mail( $admin_email, $subject, $body );
    balans_appointment_confirmation( $email, $name, $service, $date, $time_slot, isset( $_POST['lang'] ) ? sanitize_key( wp_unslash( $_POST['lang'] ) ) : 'nl' );

    wp_send_json_success( array(
        'message' => __( 'Bedankt, je aanvraag is verzonden. We nemen zo snel mogelijk contact met je op om de afspraak te bevestigen.', 'balans' ),
    ) );
}
add_action( 'wp_ajax_balans_appointment', 'balans_handle_appointment' );

/**
 * Bevestiging naar de klant, met het telefoonnummer groot en duidelijk.
 */
function balans_appointment_confirmation( $email, $name, $service, $date, $time_slot, $lang ) {
    $lang     = balans_mail_lang( $lang );
    $t        = balans_mail_texts( $lang );
    $switched = switch_to_locale( array( 'nl' => 'nl_NL', 'en' => 'en_GB', 'fr' => 'fr_FR' )[ $lang ] );
    $dienst   = get_posts( array(
        'post_type'   => 'balans_dienst',
        'numberposts' => 1,
        'lang'        => balans_has_polylang() ? $lang : '',
        'meta_query'  => array( array( 'key' => '_dienst_key', 'value' => $service ) ), // phpcs:ignore WordPress.DB.SlowDBQuery
    ) );
    $vars     = array(
        '{naam}'   => $name,
        '{dienst}' => $dienst ? get_the_title( $dienst[0] ) : $service,
        '{datum}'  => $date ? date_i18n( 'j F Y', strtotime( $date ) ) : '',
        '{tijd}'   => $time_slot,
    );
    $phone = balans_phone();
    $html  = balans_mail_html( $lang, array(
        nl2br( esc_html( balans_mail_fill( $t['appt_body'], $vars ) ) ),
        '<span style="display:block;background:#EEF4F4;border-radius:8px;padding:18px 20px;text-align:center;">'
            . '<span style="display:block;font-size:15px;color:#3F7F83;">' . esc_html( $t['appt_phone'] ) . '</span>'
            . '<a href="tel:' . esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ) . '" style="display:block;margin-top:6px;font-size:28px;font-weight:bold;letter-spacing:1px;color:#2B2420;text-decoration:none;">' . esc_html( $phone ) . '</a></span>',
    ) );
    if ( $switched ) {
        restore_previous_locale();
    }
    return wp_mail( $email, balans_mail_fill( $t['appt_subject'], $vars ), $html, array( 'Content-Type: text/html; charset=UTF-8' ) );
}
add_action( 'wp_ajax_nopriv_balans_appointment', 'balans_handle_appointment' );

/**
 * Return available time slots for a service.
 * Placeholder slots; de eigenaar vult werkelijke beschikbaarheid in via de beheerder.
 */
function balans_get_time_slots() {
    return apply_filters( 'balans_time_slots', array(
        '09:00',
        '10:30',
        '13:00',
        '14:30',
        '16:00',
    ) );
}
