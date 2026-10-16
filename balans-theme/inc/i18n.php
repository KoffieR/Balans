<?php
/**
 * Meertaligheid (NL / EN / FR) via Polylang.
 * Werkt ook zonder Polylang: dan is alles Nederlands.
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Vertaalde slugs per pagina (NL-slug => [en, fr]).
 */
function balans_page_map() {
    return array(
        'home'                    => array( 'Home', 'home-en', 'Accueil', 'accueil' ),
        'voor-jou'                => array( 'For you', 'for-you', 'Pour vous', 'pour-vous' ),
        'voor-bedrijven'          => array( 'For businesses', 'for-businesses', 'Pour les entreprises', 'pour-les-entreprises' ),
        'artikelen-bronnen'       => array( 'Articles & resources', 'articles-resources', 'Articles & ressources', 'articles-ressources' ),
        'over'                    => array( 'About', 'about', 'À propos', 'a-propos' ),
        'afspraak-maken'          => array( 'Book an appointment', 'book-appointment', 'Prendre rendez-vous', 'prendre-rendez-vous' ),
        'contact'                 => array( 'Do you have a question?', 'contact-en', 'Vous avez une question ?', 'contact-fr' ),
        'meditatie'               => array( 'Meditation', 'meditation', 'Méditation', 'meditation-fr' ),
        'consult'                 => array( 'Consultation', 'consultation', 'Consultation', 'consultation-fr' ),
        'drukpuntmassage-consult' => array( 'Acupressure massage + consultation', 'acupressure-consultation', 'Massage par acupression + consultation', 'acupression-consultation' ),
        'abonnementen'            => array( 'Subscriptions', 'subscriptions', 'Abonnements', 'abonnements' ),
        'trainingen-workshops'    => array( 'Trainings & workshops', 'trainings-workshops', 'Formations & ateliers', 'formations-ateliers' ),
        'offerte-aanvragen'       => array( 'Request a quote', 'request-quote', 'Demander un devis', 'demander-un-devis' ),
        'privacyverklaring'       => array( 'Privacy statement', 'privacy-statement', 'Déclaration de confidentialité', 'declaration-de-confidentialite' ),
        'cookies'                 => array( 'Cookie policy', 'cookie-policy', 'Politique en matière de cookies', 'politique-cookies' ),
        'algemene-voorwaarden'    => array( 'Terms and conditions', 'terms-and-conditions', 'Conditions générales', 'conditions-generales' ),
        'webshopvoorwaarden'      => array( 'Webshop terms', 'webshop-terms', 'Conditions de la boutique', 'conditions-boutique' ),
        'verzenden-retour'        => array( 'Shipping & returns', 'shipping-returns', 'Livraison & retours', 'livraison-retours' ),
        'disclaimer'              => array( 'Disclaimer', 'disclaimer-en', 'Avertissement', 'avertissement' ),
        'podcasts'                => array( 'Podcast', 'listen', 'Podcast', 'ecouter' ),
        'afspraak-voor-hulp'      => array( 'Appointment for help', 'appointment-for-help', 'Rendez-vous d\'aide', 'rendez-vous-aide' ),
        'events'                  => array( 'Events', 'events-en', 'Événements', 'evenements' ),
    );
}

/**
 * Juridische pagina's (NL-slug => NL-titel). Teksten staan in inc/legal/{nl,en,fr}/.
 */
function balans_legal_pages() {
    return array(
        'privacyverklaring'    => 'Privacyverklaring',
        'cookies'              => 'Cookieverklaring',
        'algemene-voorwaarden' => 'Algemene voorwaarden',
        'webshopvoorwaarden'   => 'Webshopvoorwaarden',
        'verzenden-retour'     => 'Verzenden & retour',
        'disclaimer'           => 'Disclaimer',
    );
}

function balans_legal_content( $nl_slug, $lang ) {
    foreach ( array( 'legal', 'content/pages' ) as $dir ) {
        $file = BALANS_DIR . '/inc/' . $dir . '/' . sanitize_key( $lang ) . '/' . sanitize_file_name( $nl_slug ) . '.html';
        if ( file_exists( $file ) ) {
            return file_get_contents( $file );
        }
    }
    return '';
}

const BALANS_PAGES_VERSION = 4;

/**
 * Pagina's uit nieuwere themaversies eenmalig aanmaken (bestaande pagina's worden nooit overschreven).
 */
function balans_install_extra_pages() {
    if ( (int) get_option( 'balans_pages_version' ) >= BALANS_PAGES_VERSION || ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $pages = balans_legal_pages() + array( 'podcasts' => 'Podcast', 'afspraak-voor-hulp' => 'Afspraak voor hulp', 'events' => 'Events' );
    $templates = array(
        'events'             => 'page-templates/page-events.php',
        'podcasts'           => 'page-templates/page-podcast.php',
        'afspraak-voor-hulp' => 'page-templates/page-hulp.php',
    );
    foreach ( $pages as $slug => $title ) {
        $page = get_page_by_path( $slug );
        if ( ! $page ) {
            $id = wp_insert_post( array(
                'post_title'   => $title,
                'post_name'    => $slug,
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_content' => balans_legal_content( $slug, 'nl' ),
            ) );
            if ( $id && ! is_wp_error( $id ) && isset( $templates[ $slug ] ) ) {
                update_post_meta( $id, '_wp_page_template', $templates[ $slug ] );
            }
            if ( $id && ! is_wp_error( $id ) && balans_has_polylang() && function_exists( 'pll_set_post_language' ) ) {
                pll_set_post_language( $id, 'nl' );
            }
            $page = $id ? get_post( $id ) : null;
        }
        if ( $page && 'privacyverklaring' === $slug ) {
            update_option( 'wp_page_for_privacy_policy', $page->ID );
        }
        if ( $page && 'webshopvoorwaarden' === $slug && ! get_option( 'woocommerce_terms_page_id' ) ) {
            update_option( 'woocommerce_terms_page_id', $page->ID );
        }
    }

    $langs = balans_has_polylang() ? pll_languages_list( array( 'fields' => 'slug' ) ) : array();
    if ( balans_has_polylang() && ! ( in_array( 'en', $langs, true ) && in_array( 'fr', $langs, true ) ) ) {
        return; // Opnieuw proberen zodra de talen zijn toegevoegd.
    }
    if ( balans_has_polylang() ) {
        balans_install_translations();
    }
    update_option( 'balans_pages_version', BALANS_PAGES_VERSION );
}
add_action( 'admin_init', 'balans_install_extra_pages' );

function balans_has_polylang() {
    return function_exists( 'pll_current_language' ) && function_exists( 'pll_get_post' );
}

/**
 * Huidige taalcode: nl, en of fr.
 */
function balans_lang() {
    $lang = balans_has_polylang() ? pll_current_language( 'slug' ) : '';
    return in_array( $lang, array( 'nl', 'en', 'fr' ), true ) ? $lang : 'nl';
}

/**
 * Taalbewuste link naar een thema-pagina op basis van de Nederlandse slug.
 * Voorbeeld: balans_url( 'afspraak-maken', array( 'dienst' => 'consult' ) ).
 */
function balans_url( $slug = '', $args = array() ) {
    if ( '' === $slug ) {
        $url = balans_has_polylang() && function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' );
        return $args ? add_query_arg( $args, $url ) : $url;
    }

    $url  = '';
    $page = get_page_by_path( $slug );
    if ( $page && balans_has_polylang() ) {
        $translated = pll_get_post( $page->ID );
        if ( $translated ) {
            $url = get_permalink( $translated );
        }
    }
    if ( ! $url && 'shop' === $slug && function_exists( 'wc_get_page_permalink' ) ) {
        $url = wc_get_page_permalink( 'shop' );
    }
    if ( ! $url ) {
        $url = $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
    }
    return $args ? add_query_arg( $args, $url ) : $url;
}

/**
 * Taalkeuze NL · EN · FR (alleen als Polylang actief is).
 */
function balans_language_switcher() {
    if ( ! function_exists( 'pll_the_languages' ) ) {
        return;
    }
    $langs = pll_the_languages( array( 'raw' => 1, 'hide_if_empty' => 0, 'hide_if_no_translation' => 0 ) );
    if ( empty( $langs ) || count( $langs ) < 2 ) {
        return;
    }
    echo '<ul class="lang-switch" aria-label="' . esc_attr__( 'Kies een taal', 'balans' ) . '" data-testid="language-switcher">';
    foreach ( $langs as $lang ) {
        printf(
            '<li><a href="%1$s" hreflang="%2$s" lang="%2$s"%3$s data-testid="lang-%4$s">%5$s</a></li>',
            esc_url( $lang['url'] ),
            esc_attr( $lang['locale'] ? str_replace( '_', '-', $lang['locale'] ) : $lang['slug'] ),
            $lang['current_lang'] ? ' class="is-current" aria-current="true"' : '',
            esc_attr( $lang['slug'] ),
            esc_html( strtoupper( $lang['slug'] ) )
        );
    }
    echo '</ul>';
}

/**
 * Themavertalingen opnieuw laden zodra Polylang de taal van de bezoeker kent.
 */
function balans_reload_textdomain() {
    unload_textdomain( 'balans', true );
    load_textdomain( 'balans', BALANS_DIR . '/languages/' . determine_locale() . '.mo' );
}
add_action( 'pll_language_defined', 'balans_reload_textdomain' );
add_action( 'change_locale', 'balans_reload_textdomain' );

/**
 * Maak eigen berichttypes en taxonomie vertaalbaar in Polylang.
 */
function balans_pll_post_types( $types, $is_settings ) {
    foreach ( array( 'balans_meditatie', 'balans_training', 'balans_zakelijk', 'balans_blog', 'balans_event' ) as $type ) {
        $types[ $type ] = $type;
    }
    return $types;
}
add_filter( 'pll_get_post_types', 'balans_pll_post_types', 10, 2 );

function balans_pll_taxonomies( $taxonomies, $is_settings ) {
    $taxonomies['meditatie_thema'] = 'meditatie_thema';
    return $taxonomies;
}
add_filter( 'pll_get_taxonomies', 'balans_pll_taxonomies', 10, 2 );

/**
 * EN/FR-pagina's aanmaken en koppelen aan de NL-pagina's (idempotent).
 */
function balans_install_translations() {
    if ( ! balans_has_polylang() || ! function_exists( 'pll_set_post_language' ) ) {
        return 0;
    }
    $available = pll_languages_list( array( 'fields' => 'slug' ) );
    $created   = 0;

    foreach ( balans_page_map() as $nl_slug => $t ) {
        $nl = get_page_by_path( $nl_slug );
        if ( ! $nl ) {
            continue;
        }
        if ( in_array( 'nl', $available, true ) && ! pll_get_post_language( $nl->ID ) ) {
            pll_set_post_language( $nl->ID, 'nl' );
        }
        $translations = array( 'nl' => $nl->ID ) + pll_get_post_translations( $nl->ID );
        $template     = get_post_meta( $nl->ID, '_wp_page_template', true );

        foreach ( array( 'en' => array( $t[0], $t[1] ), 'fr' => array( $t[2], $t[3] ) ) as $lang => $info ) {
            if ( ! in_array( $lang, $available, true ) || ! empty( $translations[ $lang ] ) ) {
                continue;
            }
            $existing = get_page_by_path( $info[1] );
            $id = $existing ? $existing->ID : wp_insert_post( array(
                'post_title'   => $info[0],
                'post_name'    => $info[1],
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_content' => balans_legal_content( $nl_slug, $lang ),
            ) );
            if ( ! $id || is_wp_error( $id ) ) {
                continue;
            }
            if ( $template ) {
                update_post_meta( $id, '_wp_page_template', $template );
            }
            pll_set_post_language( $id, $lang );
            $translations[ $lang ] = $id;
            $created++;
        }
        pll_save_post_translations( $translations );
    }

    // Browsertaal herkennen bij het eerste bezoek.
    $options = get_option( 'polylang' );
    if ( is_array( $options ) ) {
        $options['browser']       = 1;
        $options['redirect_lang'] = 1;
        update_option( 'polylang', $options );
    }
    if ( function_exists( 'PLL' ) && isset( PLL()->model ) && method_exists( PLL()->model, 'clean_languages_cache' ) ) {
        PLL()->model->clean_languages_cache();
    }
    flush_rewrite_rules();
    return $created;
}

/**
 * Beheermelding met knop om de vertaalde pagina's aan te maken.
 */
function balans_translations_notice() {
    if ( ! current_user_can( 'manage_options' ) || ! balans_has_polylang() || get_option( 'balans_translations_done' ) ) {
        return;
    }
    $url = wp_nonce_url( admin_url( 'admin-post.php?action=balans_install_translations' ), 'balans_install_translations' );
    echo '<div class="notice notice-info"><p><strong>EQ5 / Balans:</strong> ';
    esc_html_e( 'Polylang is actief. Voeg eerst de talen Nederlands, English en Français toe (Talen → Talen) en maak daarna met één klik de Engelse en Franse pagina\'s aan.', 'balans' );
    echo '</p><p><a class="button button-primary" href="' . esc_url( $url ) . '" data-testid="install-translations">';
    esc_html_e( 'Engelse en Franse pagina\'s aanmaken', 'balans' );
    echo '</a></p></div>';
}
add_action( 'admin_notices', 'balans_translations_notice' );

function balans_handle_install_translations() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'Geen toegang.', 'balans' ) );
    }
    check_admin_referer( 'balans_install_translations' );
    $created = balans_install_translations();
    $langs   = balans_has_polylang() ? pll_languages_list( array( 'fields' => 'slug' ) ) : array();
    if ( in_array( 'en', $langs, true ) && in_array( 'fr', $langs, true ) ) {
        update_option( 'balans_translations_done', 1 );
    }
    wp_safe_redirect( add_query_arg( 'balans_created', (int) $created, admin_url( 'edit.php?post_type=page' ) ) );
    exit;
}
add_action( 'admin_post_balans_install_translations', 'balans_handle_install_translations' );
