<?php
/**
 * Enqueue assets.
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function balans_enqueue_assets() {
    // Google Fonts — Fraunces (warm serif) + Instrument Sans (modern humanist sans).
    wp_enqueue_style(
        'balans-fonts',
        'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Instrument+Sans:wght@400;500;600&display=swap',
        array(),
        null
    );

    // Theme stylesheet header (required by WP) + main stylesheet.
    wp_enqueue_style( 'balans-root', get_stylesheet_uri(), array(), BALANS_VERSION );
    wp_enqueue_style(
        'balans-main',
        BALANS_URI . '/assets/css/main.css',
        array( 'balans-root' ),
        BALANS_VERSION
    );

    wp_enqueue_script(
        'balans-main',
        BALANS_URI . '/assets/js/main.js',
        array(),
        BALANS_VERSION,
        true
    );

    wp_localize_script( 'balans-main', 'BalansData', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'balans_nonce' ),
    ) );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'balans_enqueue_assets' );
