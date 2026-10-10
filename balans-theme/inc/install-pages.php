<?php
/**
 * Page auto-install on first theme activation.
 * Creates all pages required by the Balans theme and assigns the correct page templates.
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Create pages (idempotent) and set front page.
 */
function balans_install_pages() {

    $pages = array(
        array( 'title' => 'Home',                       'slug' => 'home',                   'template' => '',                                 'is_front' => true  ),
        array( 'title' => 'Voor jou',                   'slug' => 'voor-jou',               'template' => 'page-templates/page-voor-jou.php' ),
        array( 'title' => 'Voor bedrijven',             'slug' => 'voor-bedrijven',         'template' => 'page-templates/page-voor-bedrijven.php' ),
        array( 'title' => 'Artikelen & bronnen',        'slug' => 'artikelen-bronnen',      'template' => 'page-templates/page-artikelen.php' ),
        array( 'title' => 'Over',                       'slug' => 'over',                   'template' => 'page-templates/page-over.php' ),
        array( 'title' => 'Afspraak maken',             'slug' => 'afspraak-maken',         'template' => 'page-templates/page-afspraak.php' ),
        array( 'title' => 'Heb je een vraag?',          'slug' => 'contact',                'template' => 'page-templates/page-contact.php' ),
        array( 'title' => 'Meditatie',                  'slug' => 'meditatie',              'template' => 'page-templates/page-meditatie.php' ),
        array( 'title' => 'Consult',                    'slug' => 'consult',                'template' => 'page-templates/page-consult.php' ),
        array( 'title' => 'Drukpuntmassage + consult',  'slug' => 'drukpuntmassage-consult','template' => 'page-templates/page-drukpuntmassage.php' ),
        array( 'title' => 'Abonnementen',               'slug' => 'abonnementen',           'template' => 'page-templates/page-abonnementen.php' ),
        array( 'title' => 'Trainingen & workshops',     'slug' => 'trainingen-workshops',   'template' => 'page-templates/page-trainingen.php' ),
        array( 'title' => 'Offerte aanvragen',          'slug' => 'offerte-aanvragen',      'template' => 'page-templates/page-offerte.php' ),
    );

    foreach ( $pages as $page ) {
        $existing = get_page_by_path( $page['slug'] );
        if ( ! $existing ) {
            $post_id = wp_insert_post( array(
                'post_title'   => $page['title'],
                'post_name'    => $page['slug'],
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_content' => '',
            ) );
            if ( $post_id && ! empty( $page['template'] ) ) {
                update_post_meta( $post_id, '_wp_page_template', $page['template'] );
            }
            if ( $post_id && ! empty( $page['is_front'] ) ) {
                update_option( 'show_on_front', 'page' );
                update_option( 'page_on_front', $post_id );
            }
        } else {
            if ( ! empty( $page['template'] ) && get_post_meta( $existing->ID, '_wp_page_template', true ) !== $page['template'] ) {
                update_post_meta( $existing->ID, '_wp_page_template', $page['template'] );
            }
            if ( ! empty( $page['is_front'] ) ) {
                update_option( 'show_on_front', 'page' );
                update_option( 'page_on_front', $existing->ID );
            }
        }
    }

    // Build primary menu if missing.
    $menu_name = 'Balans hoofdnavigatie';
    $menu = wp_get_nav_menu_object( $menu_name );
    if ( ! $menu ) {
        $menu_id = wp_create_nav_menu( $menu_name );
        $items = array(
            array( 'label' => __( 'Voor jou', 'balans' ),            'slug' => 'voor-jou' ),
            array( 'label' => __( 'Voor bedrijven', 'balans' ),      'slug' => 'voor-bedrijven' ),
            array( 'label' => __( 'Shop', 'balans' ),                'slug' => 'shop' ),
            array( 'label' => __( 'Artikelen & bronnen', 'balans' ), 'slug' => 'artikelen-bronnen' ),
            array( 'label' => __( 'Over', 'balans' ),                'slug' => 'over' ),
        );
        foreach ( $items as $item ) {
            $page = get_page_by_path( $item['slug'] );
            wp_update_nav_menu_item( $menu_id, 0, array(
                'menu-item-title'  => $item['label'],
                'menu-item-url'    => $page ? get_permalink( $page->ID ) : home_url( '/' . $item['slug'] . '/' ),
                'menu-item-status' => 'publish',
            ) );
        }
        $locations = get_theme_mod( 'nav_menu_locations' );
        $locations['primary'] = $menu_id;
        set_theme_mod( 'nav_menu_locations', $locations );
    }
}
add_action( 'after_switch_theme', 'balans_install_pages', 20 );
