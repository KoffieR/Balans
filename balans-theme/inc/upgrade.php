<?php
/**
 * Eenmalige inhoudsupdates voor bestaande installaties (telefoonnummer weg, Familie-pakket, Maaike en Remco).
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const BALANS_CONTENT_VERSION = 3;

function balans_phone_replacements() {
    return array(
        '#\s*<li>(Telefoon|Phone|Téléphone)\s?: \+31 6 23 988 488</li>#u'   => '',
        '#, (telefoon|phone|téléphone) \+31 6 23 988 488#u'                  => '',
        '# (of bel|or call|ou appelez le) \+31 6 23 988 488#u'               => '',
        '#, \+31 6 23 988 488\.#u'                                           => '.',
        '#Vragen\? Bel \+31 6 23 988 488 of mail naar#u'                     => 'Vragen? Mail naar',
        '#Questions\? Call \+31 6 23 988 488 or email#u'                     => 'Questions? Email',
        '#Des questions \? Appelez le \+31 6 23 988 488 ou écrivez à#u'      => 'Des questions ? Écrivez à',
    );
}

function balans_remove_phone( $text ) {
    $r = balans_phone_replacements();
    return preg_replace( array_keys( $r ), array_values( $r ), $text );
}

function balans_content_upgrade() {
    if ( (int) get_option( 'balans_content_version' ) >= BALANS_CONTENT_VERSION || ! current_user_can( 'manage_options' ) ) {
        return;
    }
    // 1. Telefoonnummer uit de juridische pagina's.
    $pages = get_posts( array( 'post_type' => 'page', 'numberposts' => -1, 'lang' => '', 's' => '988 488' ) );
    foreach ( $pages as $page ) {
        $clean = balans_remove_phone( $page->post_content );
        if ( $clean !== $page->post_content ) {
            wp_update_post( array( 'ID' => $page->ID, 'post_content' => $clean ) );
        }
    }
    // 2. Opgeslagen welkomstmail-afsluiting met telefoonnummer terug naar standaard.
    $saved = (array) get_option( BALANS_MAIL_OPTION, array() );
    foreach ( $saved as $lang => $texts ) {
        if ( isset( $texts['welcome_end'] ) && false !== strpos( $texts['welcome_end'], '988' ) ) {
            unset( $saved[ $lang ]['welcome_end'] );
        }
    }
    update_option( BALANS_MAIL_OPTION, $saved, false );
    // 3. Maatwerk -> Familie-pakket en Maaike en Remco (alleen als de tekst nog niet zelf is aangepast).
    $old = array( 'maatwerk' => array( 'Maatwerk', 'Tailor-made', 'Sur mesure' ), 'dubbel-consult' => array( 'Met Remco en Maaike', 'With Remco and Maaike', 'Avec Remco et Maaike' ) );
    foreach ( get_posts( array( 'post_type' => 'balans_dienst', 'numberposts' => -1, 'lang' => '' ) ) as $post ) {
        $key = get_post_meta( $post->ID, '_dienst_key', true );
        if ( ! isset( $old[ $key ] ) ) {
            continue;
        }
        $lang = balans_has_polylang() ? pll_get_post_language( $post->ID ) : 'nl';
        $lang = in_array( $lang, array( 'nl', 'en', 'fr' ), true ) ? $lang : 'nl';
        $item = ( require BALANS_DIR . '/inc/content/services-' . $lang . '.php' )['hulp'][ $key ];
        $is_old = 'maatwerk' === $key ? in_array( $post->post_title, $old[ $key ], true ) : in_array( get_post_meta( $post->ID, '_dienst_when', true ), $old[ $key ], true );
        if ( ! $is_old ) {
            continue;
        }
        wp_update_post( array(
            'ID'           => $post->ID,
            'post_title'   => $item['title'],
            'post_content' => implode( "\n\n", array_map( function ( $p ) {
                return '<!-- wp:paragraph --><p>' . esc_html( $p ) . '</p><!-- /wp:paragraph -->';
            }, $item['text'] ) ),
        ) );
        update_post_meta( $post->ID, '_dienst_when', $item['when'] );
    }
    // 4. Vrije prijstekst van events (bijv. "€ 45") omzetten naar een getal.
    foreach ( get_posts( array( 'post_type' => 'balans_event', 'numberposts' => -1, 'lang' => '', 'post_status' => 'any' ) ) as $event ) {
        $price = get_post_meta( $event->ID, '_event_price', true );
        if ( '' !== $price && ! is_numeric( $price ) ) {
            update_post_meta( $event->ID, '_event_price', (string) (float) str_replace( ',', '.', preg_replace( '/[^0-9,.]/', '', $price ) ) );
        }
    }
    update_option( 'balans_content_version', BALANS_CONTENT_VERSION );
}
add_action( 'admin_init', 'balans_content_upgrade', 30 );
