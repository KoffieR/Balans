<?php
/**
 * Form handlers (contact + offerte).
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function balans_handle_contact() {
    check_ajax_referer( 'balans_nonce', 'nonce' );

    $name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
    $email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
    $message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

    $errors = array();
    if ( empty( $name ) )                           { $errors[] = __( 'Vul je naam in.', 'balans' ); }
    if ( empty( $email ) || ! is_email( $email ) )  { $errors[] = __( 'Vul een geldig e-mailadres in.', 'balans' ); }
    if ( empty( $message ) )                        { $errors[] = __( 'Schrijf een bericht.', 'balans' ); }

    if ( ! empty( $errors ) ) {
        wp_send_json_error( array( 'errors' => $errors ) );
    }

    $admin_email = get_option( 'admin_email' );
    $subject     = sprintf( __( 'Nieuw contactbericht via de website: %s', 'balans' ), $name );
    $body        = sprintf( "Naam: %s\nE-mail: %s\n\nBericht:\n%s", $name, $email, $message );
    $headers     = array( 'Reply-To: ' . $name . ' <' . $email . '>' );

    wp_mail( $admin_email, $subject, $body, $headers );

    wp_send_json_success( array(
        'message' => __( 'Bedankt voor je bericht. We reageren zo snel mogelijk.', 'balans' ),
    ) );
}
add_action( 'wp_ajax_balans_contact', 'balans_handle_contact' );
add_action( 'wp_ajax_nopriv_balans_contact', 'balans_handle_contact' );


function balans_handle_offerte() {
    check_ajax_referer( 'balans_nonce', 'nonce' );

    $company   = isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '';
    $name      = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
    $email     = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
    $phone     = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
    $training  = isset( $_POST['training'] ) ? sanitize_text_field( wp_unslash( $_POST['training'] ) ) : '';
    $groupsize = isset( $_POST['groupsize'] ) ? sanitize_text_field( wp_unslash( $_POST['groupsize'] ) ) : '';
    $message   = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

    $errors = array();
    if ( empty( $company ) )                        { $errors[] = __( 'Vul de bedrijfsnaam in.', 'balans' ); }
    if ( empty( $name ) )                           { $errors[] = __( 'Vul de naam van de contactpersoon in.', 'balans' ); }
    if ( empty( $email ) || ! is_email( $email ) )  { $errors[] = __( 'Vul een geldig e-mailadres in.', 'balans' ); }

    if ( ! empty( $errors ) ) {
        wp_send_json_error( array( 'errors' => $errors ) );
    }

    $title = sprintf( __( 'Offerte: %1$s — %2$s', 'balans' ), $company, $training ? $training : __( 'algemene aanvraag', 'balans' ) );

    $post_id = wp_insert_post( array(
        'post_type'   => 'balans_offerte',
        'post_title'  => $title,
        'post_status' => 'publish',
        'meta_input'  => array(
            '_balans_company'   => $company,
            '_balans_name'      => $name,
            '_balans_email'     => $email,
            '_balans_phone'     => $phone,
            '_balans_training'  => $training,
            '_balans_groupsize' => $groupsize,
            '_balans_message'   => $message,
        ),
    ) );

    if ( is_wp_error( $post_id ) || ! $post_id ) {
        wp_send_json_error( array( 'errors' => array( __( 'Er ging iets mis. Probeer het later opnieuw.', 'balans' ) ) ) );
    }

    $admin_email = get_option( 'admin_email' );
    $subject     = sprintf( __( 'Nieuwe offerteaanvraag: %s', 'balans' ), $company );
    $body        = sprintf(
        "Nieuwe offerteaanvraag.\n\nBedrijf: %s\nContactpersoon: %s\nE-mail: %s\nTelefoon: %s\nTraining: %s\nGroepsgrootte: %s\n\nBericht:\n%s",
        $company, $name, $email, $phone, $training, $groupsize, $message
    );
    wp_mail( $admin_email, $subject, $body );

    wp_send_json_success( array(
        'message' => __( 'Bedankt voor je aanvraag. We nemen zo spoedig mogelijk contact op met een voorstel.', 'balans' ),
    ) );
}
add_action( 'wp_ajax_balans_offerte', 'balans_handle_offerte' );
add_action( 'wp_ajax_nopriv_balans_offerte', 'balans_handle_offerte' );
