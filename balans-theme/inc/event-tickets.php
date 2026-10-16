<?php
/**
 * Event-tickets via WooCommerce (verborgen product per event, max 4 per bestelling) en herinnering een dag van tevoren.
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const BALANS_TICKETS_MAX = 4;

function balans_event_main_id( $event_id ) {
    if ( balans_has_polylang() ) {
        $main = pll_get_post( $event_id, pll_default_language() );
        return $main ? (int) $main : (int) $event_id;
    }
    return (int) $event_id;
}

function balans_event_price( $event_id ) {
    $price = (float) get_post_meta( $event_id, '_event_price', true );
    if ( $price <= 0 ) {
        return '';
    }
    return function_exists( 'wc_price' ) ? html_entity_decode( wp_strip_all_tags( wc_price( $price ) ) ) : '€ ' . number_format_i18n( $price, 2 );
}

function balans_event_product( $event_id ) {
    $id = (int) get_post_meta( $event_id, '_event_product_id', true );
    if ( ! $id || ! function_exists( 'wc_get_product' ) ) {
        return null;
    }
    $product = wc_get_product( $id );
    return $product && 'publish' === $product->get_status() ? $product : null;
}

function balans_event_sold_out( $event_id ) {
    $product = balans_event_product( $event_id );
    return $product && ! $product->is_in_stock();
}

function balans_event_tickets_left( $event_id ) {
    $product = balans_event_product( $event_id );
    if ( ! $product ) {
        return 0;
    }
    return $product->managing_stock() ? max( 0, (int) $product->get_stock_quantity() ) : BALANS_TICKETS_MAX;
}

function balans_is_ticket( $product_id ) {
    return (bool) get_post_meta( $product_id, '_balans_event_id', true );
}

/**
 * Verborgen ticketproduct aanmaken of bijwerken (één product voor alle vertalingen van een event).
 */
function balans_event_sync_product( $event_id ) {
    if ( ! class_exists( 'WC_Product_Simple' ) || 'balans_event' !== get_post_type( $event_id ) ) {
        return;
    }
    $main     = balans_event_main_id( $event_id );
    $price    = (float) get_post_meta( $event_id, '_event_price', true );
    $capacity = (int) get_post_meta( $event_id, '_event_capacity', true );
    $pid      = (int) get_post_meta( $event_id, '_event_product_id', true );
    $product  = $pid ? wc_get_product( $pid ) : null;
    if ( $price <= 0 ) {
        if ( $product ) {
            $product->set_status( 'draft' );
            $product->save();
        }
        return;
    }
    if ( ! $product ) {
        $product = new WC_Product_Simple();
        $stock   = $capacity;
    } else {
        $stock = (int) $product->get_stock_quantity() + ( $capacity - (int) $product->get_meta( '_balans_capacity' ) );
    }
    $date = get_post_meta( $event_id, '_event_date', true );
    $sw   = switch_to_locale( 'nl_NL' );
    $product->set_name( get_the_title( $main ) . ( $date ? ' — ' . date_i18n( 'j F Y', strtotime( $date ) ) : '' ) );
    if ( $sw ) {
        restore_previous_locale();
    }
    $product->set_regular_price( wc_format_decimal( $price ) );
    $product->set_virtual( true );
    $product->set_catalog_visibility( 'hidden' );
    $product->set_status( 'publish' === get_post_status( $event_id ) ? 'publish' : 'draft' );
    $product->set_manage_stock( $capacity > 0 );
    if ( $capacity > 0 ) {
        $product->set_stock_quantity( max( 0, $stock ) );
        $product->set_stock_status( $stock > 0 ? 'instock' : 'outofstock' );
    }
    $product->update_meta_data( '_balans_event_id', $main );
    $product->update_meta_data( '_balans_capacity', $capacity );
    $pid = $product->save();
    $ids = balans_has_polylang() ? pll_get_post_translations( $event_id ) : array();
    foreach ( array_unique( array_merge( array( $event_id ), array_values( $ids ) ) ) as $id ) {
        update_post_meta( $id, '_event_product_id', $pid );
    }
}

add_action( 'transition_post_status', function ( $new, $old, $post ) {
    if ( 'balans_event' === $post->post_type && $new !== $old && ! isset( $_POST['balans_event_nonce'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
        balans_event_sync_product( $post->ID );
    }
}, 10, 3 );

/* ---------------------------------------------------------------
 * Winkelwagen: max 4 tickets, direct door naar afrekenen
 * ------------------------------------------------------------- */

function balans_ticket_qty_in_cart( $product_id ) {
    $qty = 0;
    if ( function_exists( 'WC' ) && WC()->cart ) {
        foreach ( WC()->cart->get_cart() as $item ) {
            if ( (int) $item['product_id'] === (int) $product_id ) {
                $qty += (int) $item['quantity'];
            }
        }
    }
    return $qty;
}

function balans_ticket_limit_notice() {
    /* translators: %d: maximum aantal tickets */
    wc_add_notice( sprintf( __( 'Je kunt maximaal %d tickets per bestelling kopen.', 'balans' ), BALANS_TICKETS_MAX ), 'error' );
}

add_filter( 'woocommerce_add_to_cart_validation', function ( $valid, $product_id, $quantity ) {
    if ( $valid && balans_is_ticket( $product_id ) && balans_ticket_qty_in_cart( $product_id ) + $quantity > BALANS_TICKETS_MAX ) {
        balans_ticket_limit_notice();
        return false;
    }
    return $valid;
}, 10, 3 );

add_filter( 'woocommerce_update_cart_validation', function ( $valid, $key, $values, $quantity ) {
    if ( $valid && balans_is_ticket( $values['product_id'] ) && $quantity > BALANS_TICKETS_MAX ) {
        balans_ticket_limit_notice();
        return false;
    }
    return $valid;
}, 10, 4 );

/* Vangnet: ook bij toevoegen via andere wegen nooit meer dan 4 tickets in de winkelwagen. */
function balans_clamp_ticket_cart() {
    if ( ! WC()->cart ) {
        return;
    }
    foreach ( WC()->cart->get_cart() as $key => $item ) {
        if ( balans_is_ticket( $item['product_id'] ) && $item['quantity'] > BALANS_TICKETS_MAX ) {
            WC()->cart->set_quantity( $key, BALANS_TICKETS_MAX, true );
            balans_ticket_limit_notice();
        }
    }
}
add_action( 'woocommerce_add_to_cart', 'balans_clamp_ticket_cart', 20 );
add_action( 'woocommerce_check_cart_items', 'balans_clamp_ticket_cart' );

add_filter( 'woocommerce_quantity_input_args', function ( $args, $product ) {
    if ( $product && balans_is_ticket( $product->get_id() ) ) {
        $args['max_value'] = min( BALANS_TICKETS_MAX, $args['max_value'] > 0 ? $args['max_value'] : BALANS_TICKETS_MAX );
    }
    return $args;
}, 10, 2 );

add_filter( 'woocommerce_store_api_product_quantity_maximum', function ( $max, $product ) {
    return balans_is_ticket( $product->get_id() ) ? min( $max, BALANS_TICKETS_MAX ) : $max;
}, 10, 2 );

add_filter( 'woocommerce_add_to_cart_redirect', function ( $url, $product ) {
    return $product && balans_is_ticket( $product->get_id() ) ? wc_get_checkout_url() : $url;
}, 10, 2 );

/* Taal van de koper onthouden voor de herinnering. */
add_action( 'woocommerce_checkout_create_order', function ( $order ) {
    $order->update_meta_data( '_balans_lang', balans_lang() );
} );
add_action( 'woocommerce_store_api_checkout_update_order_meta', function ( $order ) {
    if ( ! $order->get_meta( '_balans_lang' ) ) {
        $order->update_meta_data( '_balans_lang', balans_lang() );
    }
} );

/* ---------------------------------------------------------------
 * Herinnering een dag van tevoren
 * ------------------------------------------------------------- */

function balans_send_event_reminders( $timestamp = null ) {
    if ( ! function_exists( 'wc_get_orders' ) ) {
        return 0;
    }
    $timestamp = $timestamp ? (int) $timestamp : time();
    $tomorrow  = wp_date( 'Y-m-d', $timestamp + DAY_IN_SECONDS );
    $products  = array();
    foreach ( get_posts( array( 'post_type' => 'balans_event', 'numberposts' => -1, 'lang' => '', 'meta_key' => '_event_date', 'meta_value' => $tomorrow ) ) as $event ) { // phpcs:ignore WordPress.DB.SlowDBQuery
        $pid = (int) get_post_meta( $event->ID, '_event_product_id', true );
        if ( $pid ) {
            $products[ $pid ] = balans_event_main_id( $event->ID );
        }
    }
    if ( ! $products ) {
        return 0;
    }
    $sent = 0;
    foreach ( wc_get_orders( array( 'status' => array( 'processing', 'completed' ), 'limit' => -1, 'date_created' => '>' . ( $timestamp - YEAR_IN_SECONDS ) ) ) as $order ) {
        foreach ( $order->get_items() as $item ) {
            $pid = (int) $item->get_product_id();
            if ( ! isset( $products[ $pid ] ) || $order->get_meta( '_balans_reminded_' . $pid ) ) {
                continue;
            }
            if ( balans_event_reminder_mail( $order, $products[ $pid ], (int) $item->get_quantity() ) ) {
                $order->update_meta_data( '_balans_reminded_' . $pid, 1 );
                $order->save();
                $sent++;
            }
        }
    }
    return $sent;
}
add_action( 'balans_daily_reminders', 'balans_send_event_reminders' );

function balans_event_reminder_mail( $order, $main_event, $qty ) {
    $lang  = balans_mail_lang( $order->get_meta( '_balans_lang' ) );
    $event = balans_has_polylang() && pll_get_post( $main_event, $lang ) ? pll_get_post( $main_event, $lang ) : $main_event;
    $t     = balans_mail_texts( $lang );
    $sw    = switch_to_locale( array( 'nl' => 'nl_NL', 'en' => 'en_GB', 'fr' => 'fr_FR' )[ $lang ] );
    $vars  = array(
        '{naam}'  => $order->get_billing_first_name(),
        '{event}' => get_the_title( $event ),
    );
    $where = get_post_meta( $event, '_event_location', true );
    $rows  = array_filter( array(
        __( 'Datum', 'balans' )   => esc_html( balans_event_date( $event ) ),
        __( 'Tijd', 'balans' )    => esc_html( balans_event_time( $event ) ),
        __( 'Locatie', 'balans' ) => $where ? esc_html( $where ) . '<br><a style="color:#3F7F83;" href="' . esc_url( 'https://www.openstreetmap.org/search?query=' . rawurlencode( $where ) ) . '">' . esc_html( $t['event_map'] ) . '</a>' : '',
        __( 'Tickets', 'balans' ) => (string) $qty,
    ) );
    $info = '';
    foreach ( $rows as $label => $value ) {
        $info .= '<span style="display:block;margin:0 0 8px;"><span style="display:block;font-size:12px;letter-spacing:1px;text-transform:uppercase;color:#8A7D71;">' . esc_html( $label ) . '</span>' . $value . '</span>';
    }
    $html = balans_mail_html( $lang, array(
        esc_html( balans_mail_fill( $t['welcome_hi'], $vars ) ),
        nl2br( esc_html( balans_mail_fill( $t['event_body'], $vars ) ) ),
        '<span style="display:block;background:#EEF4F4;border-radius:8px;padding:18px 20px;">' . $info . '</span>',
        nl2br( esc_html( balans_mail_fill( $t['event_prep'], $vars ) ) ),
    ) );
    if ( $sw ) {
        restore_previous_locale();
    }
    return wp_mail( $order->get_billing_email(), balans_mail_fill( $t['event_subject'], $vars ), $html, array( 'Content-Type: text/html; charset=UTF-8' ) );
}
