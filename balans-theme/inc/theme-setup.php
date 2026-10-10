<?php
/**
 * Theme setup: features, menus, image sizes.
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'balans_setup' ) ) {
    function balans_setup() {
        load_theme_textdomain( 'balans', BALANS_DIR . '/languages' );

        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'automatic-feed-links' );
        add_theme_support( 'custom-logo', array(
            'height'      => 80,
            'width'       => 240,
            'flex-height' => true,
            'flex-width'  => true,
        ) );
        add_theme_support( 'html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
            'navigation-widgets',
        ) );
        add_theme_support( 'responsive-embeds' );
        add_theme_support( 'align-wide' );
        add_theme_support( 'editor-styles' );
        add_theme_support( 'woocommerce' );
        add_theme_support( 'wc-product-gallery-zoom' );
        add_theme_support( 'wc-product-gallery-lightbox' );
        add_theme_support( 'wc-product-gallery-slider' );

        add_image_size( 'balans-card', 800, 600, true );
        add_image_size( 'balans-hero', 1920, 900, true );

        register_nav_menus( array(
            'primary' => __( 'Hoofdnavigatie', 'balans' ),
            'footer'  => __( 'Footer navigatie', 'balans' ),
            'legal'   => __( 'Juridische links', 'balans' ),
        ) );
    }
}
add_action( 'after_setup_theme', 'balans_setup' );

/**
 * Content width.
 */
function balans_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'balans_content_width', 1200 );
}
add_action( 'after_setup_theme', 'balans_content_width', 0 );

/**
 * Register sidebar/widget area for the footer.
 */
function balans_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Footer', 'balans' ),
        'id'            => 'footer-1',
        'description'   => __( 'Widgets voor de footer', 'balans' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'balans_widgets_init' );
