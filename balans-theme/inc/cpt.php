<?php
/**
 * Custom Post Types for Balans theme.
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function balans_register_cpts() {

    // Meditaties.
    register_post_type( 'balans_meditatie', array(
        'labels' => array(
            'name'          => __( 'Meditaties', 'balans' ),
            'singular_name' => __( 'Meditatie', 'balans' ),
            'add_new_item'  => __( 'Nieuwe meditatie toevoegen', 'balans' ),
            'edit_item'     => __( 'Meditatie bewerken', 'balans' ),
            'menu_name'     => __( 'Meditaties', 'balans' ),
        ),
        'public'       => true,
        'has_archive'  => 'meditaties',
        'rewrite'      => array( 'slug' => 'meditatie' ),
        'menu_icon'    => 'dashicons-controls-play',
        'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
        'show_in_rest' => true,
    ) );

    register_taxonomy( 'meditatie_thema', 'balans_meditatie', array(
        'label'        => __( 'Meditatiethema', 'balans' ),
        'public'       => true,
        'hierarchical' => true,
        'rewrite'      => array( 'slug' => 'meditatie-thema' ),
        'show_in_rest' => true,
    ) );

    // Trainingen & workshops (particulier).
    register_post_type( 'balans_training', array(
        'labels' => array(
            'name'          => __( 'Trainingen & workshops', 'balans' ),
            'singular_name' => __( 'Training', 'balans' ),
            'add_new_item'  => __( 'Nieuwe training toevoegen', 'balans' ),
            'menu_name'     => __( 'Trainingen', 'balans' ),
        ),
        'public'       => true,
        'has_archive'  => 'trainingen',
        'rewrite'      => array( 'slug' => 'training' ),
        'menu_icon'    => 'dashicons-welcome-learn-more',
        'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
        'show_in_rest' => true,
    ) );

    // Zakelijke trainingen.
    register_post_type( 'balans_zakelijk', array(
        'labels' => array(
            'name'          => __( 'Zakelijke trainingen', 'balans' ),
            'singular_name' => __( 'Zakelijke training', 'balans' ),
            'add_new_item'  => __( 'Nieuwe zakelijke training', 'balans' ),
            'menu_name'     => __( 'Zakelijk aanbod', 'balans' ),
        ),
        'public'       => true,
        'has_archive'  => 'voor-bedrijven-aanbod',
        'rewrite'      => array( 'slug' => 'zakelijke-training' ),
        'menu_icon'    => 'dashicons-businessperson',
        'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
        'show_in_rest' => true,
    ) );

    // Eigen blogs (als aparte CPT voor nette scheiding met Artikelen).
    register_post_type( 'balans_blog', array(
        'labels' => array(
            'name'          => __( 'Eigen blogs', 'balans' ),
            'singular_name' => __( 'Blog', 'balans' ),
            'menu_name'     => __( 'Eigen blogs', 'balans' ),
        ),
        'public'       => true,
        'has_archive'  => 'eigen-blogs',
        'rewrite'      => array( 'slug' => 'blog' ),
        'menu_icon'    => 'dashicons-edit',
        'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author' ),
        'show_in_rest' => true,
    ) );

    // Appointments (opslag van ingediende afspraakverzoeken).
    register_post_type( 'balans_afspraak', array(
        'labels' => array(
            'name'          => __( 'Afspraken', 'balans' ),
            'singular_name' => __( 'Afspraak', 'balans' ),
            'menu_name'     => __( 'Afspraken', 'balans' ),
        ),
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'exclude_from_search' => true,
        'publicly_queryable'  => false,
        'menu_icon'           => 'dashicons-calendar-alt',
        'supports'            => array( 'title', 'custom-fields' ),
        'capability_type'     => 'post',
    ) );

    // Offerteaanvragen (zakelijk).
    register_post_type( 'balans_offerte', array(
        'labels' => array(
            'name'          => __( 'Offerteaanvragen', 'balans' ),
            'singular_name' => __( 'Offerteaanvraag', 'balans' ),
            'menu_name'     => __( 'Offerteaanvragen', 'balans' ),
        ),
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'exclude_from_search' => true,
        'publicly_queryable'  => false,
        'menu_icon'           => 'dashicons-email-alt',
        'supports'            => array( 'title', 'custom-fields' ),
        'capability_type'     => 'post',
    ) );
}
add_action( 'init', 'balans_register_cpts' );

/**
 * Flush rewrite rules on theme activation.
 */
function balans_activate() {
    balans_register_cpts();
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'balans_activate' );
