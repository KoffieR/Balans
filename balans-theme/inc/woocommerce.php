<?php
/**
 * WooCommerce integration tweaks.
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Replace default WooCommerce wrappers with our own.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

add_action( 'woocommerce_before_main_content', 'balans_wc_wrapper_start', 10 );
add_action( 'woocommerce_after_main_content',  'balans_wc_wrapper_end', 10 );

function balans_wc_wrapper_start() {
    echo '<main id="main" class="site-main balans-wc-main container"><div class="balans-wc-grid">';
}
function balans_wc_wrapper_end() {
    echo '</div></main>';
}

/**
 * Set products per page.
 */
add_filter( 'loop_shop_per_page', function() {
    return 12;
} );

/**
 * Set product columns on shop page.
 */
add_filter( 'loop_shop_columns', function() {
    return 3;
} );

/**
 * Shop hero intro — "Kristallen & (half)edelstenen".
 */
add_action( 'woocommerce_archive_description', 'balans_shop_hero', 5 );
function balans_shop_hero() {
    if ( is_shop() ) {
        echo '<section class="balans-shop-hero">';
        echo '<p class="eyebrow">' . esc_html__( 'Shop', 'balans' ) . '</p>';
        echo '<h1 class="shop-title">' . esc_html__( 'Kristallen & (half)edelstenen', 'balans' ) . '</h1>';
        echo '<p class="shop-intro">' . esc_html__( 'Energetisch bewerkte stenen, bedoeld om bij een specifiek thema of persoonlijk proces te ondersteunen. Niet bedoeld als vervanging van medische zorg.', 'balans' ) . '</p>';
        echo '</section>';
    }
}

/**
 * Universele disclaimer op elke productpagina.
 */
add_action( 'woocommerce_single_product_summary', 'balans_product_disclaimer', 45 );
function balans_product_disclaimer() {
    echo '<p class="balans-disclaimer"><small>' . esc_html__( 'Deze steen is bedoeld om te ondersteunen. Het is geen vervanging van medische zorg of professioneel advies.', 'balans' ) . '</small></p>';
}
