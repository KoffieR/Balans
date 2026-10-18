<?php
/**
 * Ervaringen: bezoekers laten een korte ervaring achter, jij keurt goed (publiceren) en ze verschijnen op de homepage.
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function balans_register_ervaring_cpt() {
    register_post_type( 'balans_ervaring', array(
        'labels'       => array(
            'name'          => __( 'Ervaringen', 'balans' ),
            'singular_name' => __( 'Ervaring', 'balans' ),
            'add_new_item'  => __( 'Nieuwe ervaring', 'balans' ),
            'edit_item'     => __( 'Ervaring bewerken', 'balans' ),
        ),
        'public'       => false,
        'show_ui'      => true,
        'menu_icon'    => 'dashicons-format-quote',
        'supports'     => array( 'title', 'editor' ),
    ) );
}
add_action( 'init', 'balans_register_ervaring_cpt' );

/** Keuzelijst "bij welke dienst" (titels van diensten en routes in de huidige taal). */
function balans_ervaring_diensten() {
    $titles = array();
    foreach ( array( 'hulp', 'abonnement' ) as $group ) {
        foreach ( balans_get_diensten( $group ) as $item ) {
            $titles[] = $item['title'];
        }
    }
    foreach ( balans_homepage_routes() as $route ) {
        $titles[] = $route['title'];
    }
    return array_values( array_unique( array_filter( $titles ) ) );
}

function balans_handle_ervaring() {
    check_ajax_referer( 'balans_nonce', 'nonce' );
    if ( ! empty( $_POST['website'] ) || ! get_theme_mod( 'eq5_ervaringen_form', true ) ) {
        wp_send_json_error( array( 'errors' => array( __( 'Er ging iets mis. Probeer het later opnieuw.', 'balans' ) ) ) );
    }
    $name   = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
    $text   = isset( $_POST['text'] ) ? sanitize_textarea_field( wp_unslash( $_POST['text'] ) ) : '';
    $dienst = isset( $_POST['dienst'] ) ? sanitize_text_field( wp_unslash( $_POST['dienst'] ) ) : '';
    $dienst = in_array( $dienst, balans_ervaring_diensten(), true ) ? $dienst : '';

    $errors = array();
    if ( '' === $name || mb_strlen( $name ) > 60 ) { $errors[] = __( 'Vul je naam of initialen in (max. 60 tekens).', 'balans' ); }
    if ( mb_strlen( $text ) < 10 || mb_strlen( $text ) > 600 ) { $errors[] = __( 'Schrijf je ervaring in 10 tot 600 tekens.', 'balans' ); }
    if ( $errors ) {
        wp_send_json_error( array( 'errors' => $errors ) );
    }

    $id = wp_insert_post( array(
        'post_type'    => 'balans_ervaring',
        'post_status'  => 'pending',
        'post_title'   => $name,
        'post_content' => $text,
    ) );
    if ( ! $id || is_wp_error( $id ) ) {
        wp_send_json_error( array( 'errors' => array( __( 'Er ging iets mis. Probeer het later opnieuw.', 'balans' ) ) ) );
    }
    update_post_meta( $id, '_ervaring_dienst', $dienst );
    update_post_meta( $id, '_ervaring_lang', balans_lang() );

    wp_mail(
        get_option( 'admin_email' ),
        sprintf( __( 'Nieuwe ervaring van %s wacht op goedkeuring', 'balans' ), $name ),
        sprintf( "%s\n%s\n\n%s\n\n%s\n%s", $name, $dienst, $text, __( 'Goedkeuren = publiceren:', 'balans' ), admin_url( 'post.php?post=' . $id . '&action=edit' ) )
    );
    wp_send_json_success( array( 'message' => __( 'Dank je wel voor het delen! Je ervaring verschijnt op de website zodra we hem hebben bekeken.', 'balans' ) ) );
}
add_action( 'wp_ajax_balans_ervaring', 'balans_handle_ervaring' );
add_action( 'wp_ajax_nopriv_balans_ervaring', 'balans_handle_ervaring' );

/* Beheer: dienst-kolom, meta box en teller voor ervaringen die wachten. */
add_filter( 'manage_balans_ervaring_posts_columns', function ( $cols ) {
    return array_slice( $cols, 0, 2, true ) + array( 'ervaring_dienst' => __( 'Dienst', 'balans' ), 'ervaring_text' => __( 'Ervaring', 'balans' ) ) + $cols;
} );
add_action( 'manage_balans_ervaring_posts_custom_column', function ( $col, $post_id ) {
    if ( 'ervaring_dienst' === $col ) {
        echo esc_html( get_post_meta( $post_id, '_ervaring_dienst', true ) );
    } elseif ( 'ervaring_text' === $col ) {
        echo esc_html( wp_trim_words( get_post_field( 'post_content', $post_id ), 20 ) );
    }
}, 10, 2 );

add_action( 'add_meta_boxes', function () {
    add_meta_box( 'balans-ervaring', __( 'Bij welke dienst', 'balans' ), function ( $post ) {
        wp_nonce_field( 'balans_ervaring', 'balans_ervaring_nonce' );
        printf( '<input type="text" class="widefat" name="_ervaring_dienst" value="%s" /><p class="description">%s</p>',
            esc_attr( get_post_meta( $post->ID, '_ervaring_dienst', true ) ),
            esc_html__( 'Klik op "Publiceren" om de ervaring goed te keuren en op de homepage te tonen.', 'balans' ) );
    }, 'balans_ervaring', 'side' );
} );
add_action( 'save_post_balans_ervaring', function ( $post_id ) {
    if ( isset( $_POST['balans_ervaring_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['balans_ervaring_nonce'] ), 'balans_ervaring' ) && current_user_can( 'edit_post', $post_id ) ) {
        update_post_meta( $post_id, '_ervaring_dienst', isset( $_POST['_ervaring_dienst'] ) ? sanitize_text_field( wp_unslash( $_POST['_ervaring_dienst'] ) ) : '' );
    }
} );

add_action( 'admin_menu', function () {
    global $menu;
    $count = (int) wp_count_posts( 'balans_ervaring' )->pending;
    foreach ( (array) $menu as $i => $item ) {
        if ( $count && 'edit.php?post_type=balans_ervaring' === $item[2] ) {
            $menu[ $i ][0] .= ' <span class="awaiting-mod"><span class="pending-count">' . $count . '</span></span>'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
        }
    }
}, 99 );

add_action( 'customize_register', function ( $wp_customize ) {
    $wp_customize->add_section( 'eq5_ervaringen', array(
        'title'       => __( 'Ervaringen', 'balans' ),
        'description' => __( 'Goedgekeurde ervaringen (Ervaringen → Publiceren) en het formulier onderaan de homepage. De koppen pas je per taal aan bij de taal-secties.', 'balans' ),
        'panel'       => 'eq5_homepage',
    ) );
    $wp_customize->add_setting( 'eq5_ervaringen_show', array( 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean' ) );
    $wp_customize->add_control( 'eq5_ervaringen_show', array( 'label' => __( 'Ervaringen op de homepage tonen', 'balans' ), 'section' => 'eq5_ervaringen', 'type' => 'checkbox' ) );
    $wp_customize->add_setting( 'eq5_ervaringen_count', array( 'default' => 3, 'sanitize_callback' => 'absint' ) );
    $wp_customize->add_control( 'eq5_ervaringen_count', array( 'label' => __( 'Aantal ervaringen (nieuwste eerst)', 'balans' ), 'section' => 'eq5_ervaringen', 'type' => 'number', 'input_attrs' => array( 'min' => 1, 'max' => 24 ) ) );
    $wp_customize->add_setting( 'eq5_ervaringen_form', array( 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean' ) );
    $wp_customize->add_control( 'eq5_ervaringen_form', array( 'label' => __( 'Formulier "Deel je ervaring" tonen', 'balans' ), 'section' => 'eq5_ervaringen', 'type' => 'checkbox' ) );
} );

function balans_get_ervaringen() {
    return get_posts( array(
        'post_type'      => 'balans_ervaring',
        'post_status'    => 'publish',
        'posts_per_page' => max( 1, (int) get_theme_mod( 'eq5_ervaringen_count', 3 ) ),
    ) );
}
