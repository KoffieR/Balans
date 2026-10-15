<?php
/**
 * Abonnementen (Paid Memberships Pro): meertalige PMPro-pagina's, welkomstmail en wekelijkse herinneringen.
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const BALANS_PMPRO_PAGES = array( 'account', 'billing', 'cancel', 'checkout', 'confirmation', 'invoice', 'levels', 'login', 'member_profile_edit' );

/* ---------------------------------------------------------------
 * 1. PMPro-pagina's per taal
 * ------------------------------------------------------------- */

function balans_pmpro_translate_pages() {
    global $pmpro_pages, $pmpro_core_pages;
    if ( ! balans_has_polylang() || empty( $pmpro_pages ) || is_admin() ) {
        return;
    }
    foreach ( array( 'pmpro_pages', 'pmpro_core_pages' ) as $var ) {
        foreach ( (array) $GLOBALS[ $var ] as $name => $id ) {
            $translated = $id ? pll_get_post( (int) $id ) : 0;
            if ( $translated ) {
                $GLOBALS[ $var ][ $name ] = $translated;
            }
        }
    }
}
add_action( 'init', 'balans_pmpro_translate_pages', 99 );
add_action( 'pll_language_defined', 'balans_pmpro_translate_pages', 99 );
add_action( 'wp', 'balans_pmpro_translate_pages', 1 );

/**
 * EN/FR-kopieën van de PMPro-pagina's aanmaken (met dezelfde shortcode/blok-inhoud) en koppelen.
 */
function balans_pmpro_install_translations() {
    if ( ! function_exists( 'pmpro_url' ) || ! balans_has_polylang() ) {
        return 0;
    }
    $names = array(
        'account'             => array( 'My subscription', 'Mon abonnement' ),
        'billing'             => array( 'Billing details', 'Informations de facturation' ),
        'cancel'              => array( 'Cancel subscription', "Résilier l'abonnement" ),
        'checkout'            => array( 'Checkout', 'Paiement' ),
        'confirmation'        => array( 'Confirmation', 'Confirmation' ),
        'invoice'             => array( 'Invoice', 'Facture' ),
        'levels'              => array( 'Subscriptions overview', 'Aperçu des abonnements' ),
        'login'               => array( 'Log in', 'Connexion' ),
        'member_profile_edit' => array( 'Edit profile', 'Modifier le profil' ),
    );
    $created = 0;
    foreach ( BALANS_PMPRO_PAGES as $name ) {
        $nl_id = (int) get_option( 'pmpro_' . $name . '_page_id' );
        $nl    = $nl_id ? get_post( $nl_id ) : null;
        if ( ! $nl ) {
            continue;
        }
        if ( ! pll_get_post_language( $nl_id ) ) {
            pll_set_post_language( $nl_id, 'nl' );
        }
        $translations = array( 'nl' => $nl_id ) + pll_get_post_translations( $nl_id );
        foreach ( array( 'en' => 0, 'fr' => 1 ) as $lang => $i ) {
            if ( ! empty( $translations[ $lang ] ) ) {
                continue;
            }
            $id = wp_insert_post( array(
                'post_type'    => 'page',
                'post_status'  => 'publish',
                'post_title'   => $names[ $name ][ $i ],
                'post_name'    => sanitize_title( $names[ $name ][ $i ] ) . '-' . $lang,
                'post_content' => $nl->post_content,
                'post_parent'  => 0,
            ) );
            if ( $id && ! is_wp_error( $id ) ) {
                pll_set_post_language( $id, $lang );
                $translations[ $lang ] = $id;
                $created++;
            }
        }
        pll_save_post_translations( $translations );
    }
    return $created;
}

function balans_pmpro_pages_notice() {
    if ( ! current_user_can( 'manage_options' ) || ! function_exists( 'pmpro_url' ) || ! balans_has_polylang() || ! get_option( 'pmpro_checkout_page_id' ) ) {
        return;
    }
    $missing = ! pll_get_post( (int) get_option( 'pmpro_checkout_page_id' ), 'en' ) || ! pll_get_post( (int) get_option( 'pmpro_checkout_page_id' ), 'fr' );
    if ( ! $missing ) {
        return;
    }
    $url = wp_nonce_url( admin_url( 'admin-post.php?action=balans_pmpro_pages' ), 'balans_pmpro_pages' );
    echo '<div class="notice notice-info"><p><strong>EQ5:</strong> ' . esc_html__( 'Maak de afreken- en accountpagina\'s van de abonnementen ook in het Engels en Frans.', 'balans' ) . '</p><p><a class="button button-primary" href="' . esc_url( $url ) . '">' . esc_html__( 'Abonnementspagina\'s vertalen', 'balans' ) . '</a></p></div>';
}
add_action( 'admin_notices', 'balans_pmpro_pages_notice' );

add_action( 'admin_post_balans_pmpro_pages', function () {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'Geen toegang.', 'balans' ) );
    }
    check_admin_referer( 'balans_pmpro_pages' );
    balans_pmpro_install_translations();
    wp_safe_redirect( admin_url( 'edit.php?post_type=page' ) );
    exit;
} );

/* ---------------------------------------------------------------
 * 2. Welkomstmail
 * ------------------------------------------------------------- */

function balans_mail_texts( $lang ) {
    $t = array(
        'nl' => array(
            'welcome_subject' => 'Welkom bij je EQ5-abonnement: %s',
            'welcome_hi'      => 'Lieve %s,',
            'welcome_body'    => 'Fijn dat je hebt gekozen voor het abonnement %1$s. Vanaf nu word je %2$s energetisch verlicht. Je hoeft daar zelf niets voor te doen; je kunt er wel ruimte voor maken door op dat moment even rustig aan te doen, water te drinken en te voelen wat er verandert.',
            'welcome_what'    => 'Wat er gebeurt:',
            'welcome_end'     => 'Heb je vragen? Beantwoord gewoon deze e-mail of bel +31 6 23 988 488.',
            'remind_subject'  => 'Vandaag is je moment van verlichting',
            'remind_body'     => 'Vandaag vindt de verlichting van je abonnement %s plaats. Neem vandaag even de tijd voor rust: een wandeling, een kop thee, een paar minuten stilte. Zo geef je de energie de ruimte om haar werk te doen.',
            'sign'            => 'Hartelijke groet, Remco Sas — EQ5',
            'unsubscribe'     => 'Geen herinneringen meer ontvangen? Klik hier.',
            'unsubscribed'    => 'Je ontvangt geen herinneringen meer. Je abonnement loopt gewoon door.',
        ),
        'en' => array(
            'welcome_subject' => 'Welcome to your EQ5 subscription: %s',
            'welcome_hi'      => 'Dear %s,',
            'welcome_body'    => 'We are glad you chose the %1$s subscription. From now on you will be lightened energetically %2$s. You do not need to do anything yourself; you can make room for it by taking it easy at that moment, drinking water and feeling what changes.',
            'welcome_what'    => 'What happens:',
            'welcome_end'     => 'Any questions? Simply reply to this email or call +31 6 23 988 488.',
            'remind_subject'  => 'Today is your moment of lightening',
            'remind_body'     => 'Today the lightening of your %s subscription takes place. Take some time for rest today: a walk, a cup of tea, a few minutes of silence. This gives the energy room to do its work.',
            'sign'            => 'Warm regards, Remco Sas — EQ5',
            'unsubscribe'     => 'No longer want reminders? Click here.',
            'unsubscribed'    => 'You will no longer receive reminders. Your subscription continues as usual.',
        ),
        'fr' => array(
            'welcome_subject' => 'Bienvenue dans votre abonnement EQ5 : %s',
            'welcome_hi'      => 'Cher/Chère %s,',
            'welcome_body'    => "Nous sommes heureux que vous ayez choisi l'abonnement %1\$s. Désormais, vous serez allégé(e) énergétiquement %2\$s. Vous n'avez rien à faire vous-même ; vous pouvez y faire de la place en prenant les choses calmement à ce moment-là, en buvant de l'eau et en ressentant ce qui change.",
            'welcome_what'    => 'Ce qui se passe :',
            'welcome_end'     => 'Des questions ? Répondez simplement à cet e-mail ou appelez le +31 6 23 988 488.',
            'remind_subject'  => "Aujourd'hui, c'est votre moment d'allègement",
            'remind_body'     => "Aujourd'hui a lieu l'allègement de votre abonnement %s. Prenez un moment de repos aujourd'hui : une promenade, une tasse de thé, quelques minutes de silence. Vous laissez ainsi à l'énergie l'espace pour faire son travail.",
            'sign'            => 'Bien chaleureusement, Remco Sas — EQ5',
            'unsubscribe'     => 'Vous ne souhaitez plus recevoir de rappels ? Cliquez ici.',
            'unsubscribed'    => "Vous ne recevrez plus de rappels. Votre abonnement continue normalement.",
        ),
    );
    return isset( $t[ $lang ] ) ? $t[ $lang ] : $t['nl'];
}

/**
 * Dienst (in de taal van de abonnee) die bij een PMPro-niveau hoort.
 */
function balans_dienst_for_level( $level_id, $lang ) {
    $posts = get_posts( array(
        'post_type'   => 'balans_dienst',
        'numberposts' => 1,
        'meta_key'    => '_dienst_level', // phpcs:ignore WordPress.DB.SlowDBQuery
        'meta_value'  => (int) $level_id, // phpcs:ignore WordPress.DB.SlowDBQuery
        'lang'        => '',
    ) );
    if ( ! $posts ) {
        return null;
    }
    $post = $posts[0];
    if ( balans_has_polylang() && pll_get_post( $post->ID, $lang ) ) {
        $post = get_post( pll_get_post( $post->ID, $lang ) );
    }
    return $post;
}

function balans_mail_html( $lang, $paragraphs, $user_id ) {
    $t     = balans_mail_texts( $lang );
    $style = 'font-family:Georgia,serif;font-size:16px;line-height:1.6;color:#2B2420;';
    $html  = '<div style="background:#F5F0E8;padding:32px 16px;"><div style="max-width:560px;margin:0 auto;background:#FFFBF4;border-radius:10px;padding:32px;' . $style . '">';
    $html .= '<p style="margin:0 0 20px;font-size:26px;color:#3C6E71;letter-spacing:1px;">EQ5</p>';
    foreach ( $paragraphs as $p ) {
        $html .= '<p style="margin:0 0 16px;">' . wp_kses_post( $p ) . '</p>';
    }
    $html .= '<p style="margin:24px 0 0;color:#8B6B47;font-style:italic;">' . esc_html( $t['sign'] ) . '</p>';
    $html .= '<p style="margin:28px 0 0;font-family:Arial,sans-serif;font-size:12px;color:#8A7D71;"><a style="color:#8A7D71;" href="' . esc_url( balans_reminder_unsubscribe_url( $user_id ) ) . '">' . esc_html( $t['unsubscribe'] ) . '</a></p>';
    return $html . '</div></div>';
}

function balans_send_mail( $user, $subject, $html ) {
    $headers = array( 'Content-Type: text/html; charset=UTF-8' );
    return wp_mail( $user->user_email, $subject, $html, $headers );
}

/**
 * Taal van de abonnee onthouden bij het afrekenen.
 */
add_action( 'pmpro_after_checkout', function ( $user_id ) {
    update_user_meta( $user_id, 'eq5_lang', balans_lang() );
}, 5 );

function balans_welcome_mail( $level_id, $user_id, $cancel_level = 0 ) {
    if ( ! $level_id || ! in_array( (int) $level_id, balans_dienst_level_ids(), true ) ) {
        return;
    }
    $user = get_userdata( $user_id );
    if ( ! $user ) {
        return;
    }
    $lang   = get_user_meta( $user_id, 'eq5_lang', true );
    $lang   = $lang ? $lang : balans_lang();
    $dienst = balans_dienst_for_level( $level_id, $lang );
    if ( ! $dienst ) {
        return;
    }
    $t    = balans_mail_texts( $lang );
    $when = get_post_meta( $dienst->ID, '_dienst_when', true );
    $html = balans_mail_html( $lang, array(
        esc_html( sprintf( $t['welcome_hi'], $user->first_name ? $user->first_name : $user->display_name ) ),
        esc_html( sprintf( $t['welcome_body'], $dienst->post_title, mb_strtolower( mb_substr( $when, 0, 1 ) ) . mb_substr( $when, 1 ) ) ),
        '<strong>' . esc_html( $t['welcome_what'] ) . '</strong><br>' . nl2br( esc_html( trim( wp_strip_all_tags( str_replace( '</p>', "</p>\n\n", $dienst->post_content ) ) ) ) ),
        esc_html( $t['welcome_end'] ),
    ), $user_id );
    balans_send_mail( $user, sprintf( $t['welcome_subject'], $dienst->post_title ), $html );
}
add_action( 'pmpro_after_change_membership_level', 'balans_welcome_mail', 20, 3 );

/* ---------------------------------------------------------------
 * 3. Herinneringen op zondag (A), woensdag (B) en vrijdag (C)
 * ------------------------------------------------------------- */

function balans_reminder_days() {
    return array( 0 => 'wekelijks-a', 3 => 'wekelijks-b', 5 => 'wekelijks-c' ); // 0 = zondag.
}

function balans_schedule_reminders() {
    if ( ! wp_next_scheduled( 'balans_daily_reminders' ) ) {
        $first = new DateTime( 'tomorrow 08:00', wp_timezone() );
        wp_schedule_event( $first->getTimestamp(), 'daily', 'balans_daily_reminders' );
    }
}
add_action( 'init', 'balans_schedule_reminders' );
add_action( 'switch_theme', function () {
    wp_clear_scheduled_hook( 'balans_daily_reminders' );
} );

function balans_level_for_key( $key ) {
    $posts = get_posts( array(
        'post_type'   => 'balans_dienst',
        'numberposts' => 1,
        'lang'        => '',
        'meta_query'  => array( array( 'key' => '_dienst_key', 'value' => $key ) ), // phpcs:ignore WordPress.DB.SlowDBQuery
    ) );
    return $posts ? (int) get_post_meta( $posts[0]->ID, '_dienst_level', true ) : 0;
}

function balans_send_reminders( $weekday = null ) {
    global $wpdb;
    $weekday = null === $weekday ? (int) wp_date( 'w' ) : (int) $weekday;
    $days    = balans_reminder_days();
    if ( ! isset( $days[ $weekday ] ) || empty( $wpdb->pmpro_memberships_users ) ) {
        return 0;
    }
    $level = balans_level_for_key( $days[ $weekday ] );
    if ( ! $level ) {
        return 0;
    }
    $user_ids = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT user_id FROM {$wpdb->pmpro_memberships_users} WHERE membership_id = %d AND status = 'active'", $level ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    $sent     = 0;
    foreach ( $user_ids as $user_id ) {
        $user = get_userdata( $user_id );
        if ( ! $user || get_user_meta( $user_id, 'eq5_no_reminders', true ) ) {
            continue;
        }
        $lang   = get_user_meta( $user_id, 'eq5_lang', true );
        $lang   = $lang ? $lang : 'nl';
        $t      = balans_mail_texts( $lang );
        $dienst = balans_dienst_for_level( $level, $lang );
        $name   = $dienst ? $dienst->post_title : '';
        $html   = balans_mail_html( $lang, array(
            esc_html( sprintf( $t['welcome_hi'], $user->first_name ? $user->first_name : $user->display_name ) ),
            esc_html( sprintf( $t['remind_body'], $name ) ),
        ), $user_id );
        $sent  += balans_send_mail( $user, $t['remind_subject'], $html ) ? 1 : 0;
    }
    return $sent;
}
add_action( 'balans_daily_reminders', 'balans_send_reminders' );

function balans_reminder_token( $user_id ) {
    return hash_hmac( 'sha256', 'eq5-reminders-' . $user_id, wp_salt( 'auth' ) );
}

function balans_reminder_unsubscribe_url( $user_id ) {
    return add_query_arg( array( 'eq5_unsub' => $user_id, 'k' => balans_reminder_token( $user_id ) ), home_url( '/' ) );
}

add_action( 'template_redirect', function () {
    if ( empty( $_GET['eq5_unsub'] ) || empty( $_GET['k'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
        return;
    }
    $user_id = absint( $_GET['eq5_unsub'] ); // phpcs:ignore WordPress.Security.NonceVerification
    if ( ! hash_equals( balans_reminder_token( $user_id ), sanitize_text_field( wp_unslash( $_GET['k'] ) ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
        return;
    }
    update_user_meta( $user_id, 'eq5_no_reminders', 1 );
    $lang = get_user_meta( $user_id, 'eq5_lang', true );
    wp_die( esc_html( balans_mail_texts( $lang ? $lang : 'nl' )['unsubscribed'] ), 'EQ5', array( 'response' => 200 ) );
} );
