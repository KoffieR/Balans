<?php
/**
 * Bewerkbare teksten: paginakop (meta box), pagina-inhoud in de editor en homepage-teksten (Customizer).
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const BALANS_HERO_FIELDS = array( 'eyebrow', 'title', 'intro' );

/**
 * Standaard paginakop per template (in de huidige taal).
 */
function balans_hero_defaults( $key ) {
    $s = balans_services( isset( $GLOBALS['balans_hero_lang'] ) ? $GLOBALS['balans_hero_lang'] : '' );
    $d = array(
        'over'           => array( __( 'Over', 'balans' ), __( 'Over ons, de oprichter en onze aanpak.', 'balans' ), __( 'Wie we zijn, hoe we werken en wat we met empathisch energiewerk bedoelen.', 'balans' ) ),
        'voor-jou'       => array( __( 'Voor jou', 'balans' ), __( 'Vijf manieren om met wat er speelt aan de slag te gaan.', 'balans' ), __( 'Kies wat op dit moment past. Je kunt altijd op een ander moment een andere route proberen.', 'balans' ) ),
        'voor-bedrijven' => array( __( 'Voor bedrijven', 'balans' ), __( 'Trainingen die ruimte scheppen in de groep.', 'balans' ), __( 'Een vastgesteld concept en programma op onze locatie. Deelnemers komen naar de trainingslocatie — training op locatie bij het bedrijf wordt niet aangeboden.', 'balans' ) ),
        'contact'        => array( __( 'Contact', 'balans' ), __( 'Heb je een vraag?', 'balans' ), __( 'Stuur een bericht en we reageren zo snel mogelijk.', 'balans' ) ),
        'afspraak'       => array( __( 'Afspraken', 'balans' ), __( 'Een afspraak inplannen.', 'balans' ), __( 'Kies de dienst, een datum en een tijdslot. We nemen daarna contact met je op om de afspraak te bevestigen.', 'balans' ) ),
        'artikelen'      => array( __( 'Artikelen & bronnen', 'balans' ), __( 'Lezen, verdiepen en reflecteren.', 'balans' ), __( 'Artikelen met inhoudelijke verdieping en betrouwbare bronnen, plus eigen blogs waarin we visie en ervaring delen.', 'balans' ) ),
        'meditatie'      => array( __( 'Meditatie', 'balans' ), __( 'Meditaties om zelf te luisteren.', 'balans' ), __( 'Gratis meditaties zonder account. Later volgen losse programma\'s en abonnementen via de shop.', 'balans' ) ),
        'offerte'        => array( __( 'Voor bedrijven', 'balans' ), __( 'Vraag een offerte aan', 'balans' ), __( 'Voor groepsaanmeldingen of een voorstel op maat van de bestaande concepten. We vragen alleen wat nodig is.', 'balans' ) ),
        'trainingen'     => array( __( 'Voor jou', 'balans' ), __( 'Trainingen & workshops', 'balans' ), __( 'Op vastgestelde data op onze locatie. Waar mogelijk kun je direct inschrijven via het winkelmandje.', 'balans' ) ),
        'podcast'        => array( __( 'Podcast', 'balans' ), __( 'Meditaties om naar te luisteren, waar je ook bent.', 'balans' ), __( 'Rustige afleveringen om even stil te staan. Een deel is gratis; met een EQ5-abonnement luister je naar alles.', 'balans' ) ),
        'events'         => array( __( 'Voor jou', 'balans' ), get_the_title(), '' ),
        'hulp'           => array( $s['hulp_eyebrow'] ?? '', $s['hulp_title'] ?? '', implode( "\n\n", (array) ( $s['hulp_intro'] ?? array() ) ) ),
        'abonnementen'   => array( $s['abo_eyebrow'] ?? '', $s['abo_title'] ?? '', $s['abo_intro'] ?? '' ),
    );
    return isset( $d[ $key ] ) ? array_combine( BALANS_HERO_FIELDS, $d[ $key ] ) : array_combine( BALANS_HERO_FIELDS, array( '', get_the_title(), '' ) );
}

/** Template-bestand → sleutel. */
function balans_hero_key_for_post( $post_id ) {
    $tpl = basename( (string) get_page_template_slug( $post_id ), '.php' );
    return str_replace( array( 'page-afspraak', 'page-' ), array( 'afspraak', '' ), $tpl );
}

function balans_post_locale( $post_id ) {
    $lang = balans_has_polylang() ? pll_get_post_language( $post_id ) : 'nl';
    return array( 'nl' => 'nl_NL', 'en' => 'en_GB', 'fr' => 'fr_FR' )[ balans_mail_lang( $lang ) ];
}

/** Functie uitvoeren met de thema-vertalingen van een bepaalde taal (werkt ook zonder WordPress-taalpakket). */
function balans_with_locale( $locale, $fn ) {
    balans_reload_textdomain( $locale );
    $GLOBALS['balans_hero_lang'] = substr( $locale, 0, 2 );
    $result = $fn();
    unset( $GLOBALS['balans_hero_lang'] );
    balans_reload_textdomain();
    return $result;
}

/** Paginakop van een pagina: eigen tekst uit de editor, anders de standaardtekst. */
function balans_hero( $post_id, $key ) {
    $out = is_admin() ? balans_with_locale( balans_post_locale( $post_id ), function () use ( $key ) {
        return balans_hero_defaults( $key );
    } ) : balans_hero_defaults( $key );
    foreach ( BALANS_HERO_FIELDS as $f ) {
        $v = get_post_meta( $post_id, '_balans_hero_' . $f, true );
        if ( '' !== $v ) {
            $out[ $f ] = $v;
        }
    }
    return $out;
}

function balans_page_hero( $key, $after = '', $class = '' ) {
    $h = balans_hero( get_queried_object_id(), $key );
    echo '<section class="page-hero' . ( $class ? ' ' . esc_attr( $class ) : '' ) . '" data-testid="page-hero"><div class="container">';
    if ( $h['eyebrow'] ) {
        echo '<p class="eyebrow">' . esc_html( $h['eyebrow'] ) . '</p>';
    }
    echo '<h1>' . esc_html( $h['title'] ) . '</h1>';
    foreach ( array_filter( array_map( 'trim', preg_split( "/\n\s*\n/", (string) $h['intro'] ) ) ) as $p ) {
        echo '<p class="page-hero__intro">' . esc_html( $p ) . '</p>';
    }
    echo $after; // phpcs:ignore WordPress.Security.EscapeOutput
    echo '</div></section>';
}

/* Meta box "Paginakop" bij elke pagina. */
add_action( 'add_meta_boxes_page', function ( $post ) {
    add_meta_box( 'balans_hero', __( 'Paginakop (bovenaan de pagina)', 'balans' ), 'balans_hero_meta_box', 'page', 'normal', 'high' );
} );

function balans_hero_meta_box( $post ) {
    $h      = balans_hero( $post->ID, balans_hero_key_for_post( $post->ID ) );
    $labels = array( 'eyebrow' => __( 'Klein kopje erboven', 'balans' ), 'title' => __( 'Grote kop', 'balans' ), 'intro' => __( 'Introductietekst (lege regel = nieuwe alinea)', 'balans' ) );
    wp_nonce_field( 'balans_hero', 'balans_hero_nonce' );
    echo '<p class="description">' . esc_html__( 'Deze teksten staan in de gekleurde balk bovenaan de pagina. De rest van de tekst pas je hierboven in de editor aan.', 'balans' ) . '</p><table class="form-table">';
    foreach ( $labels as $f => $label ) {
        echo '<tr><th><label for="balans-hero-' . esc_attr( $f ) . '">' . esc_html( $label ) . '</label></th><td>';
        if ( 'intro' === $f ) {
            echo '<textarea class="large-text" rows="3" id="balans-hero-intro" name="balans_hero[intro]">' . esc_textarea( $h['intro'] ) . '</textarea>';
        } else {
            echo '<input class="large-text" type="text" id="balans-hero-' . esc_attr( $f ) . '" name="balans_hero[' . esc_attr( $f ) . ']" value="' . esc_attr( $h[ $f ] ) . '">';
        }
        echo '</td></tr>';
    }
    echo '</table>';
}

add_action( 'save_post_page', function ( $post_id ) {
    if ( ! isset( $_POST['balans_hero_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['balans_hero_nonce'] ) ), 'balans_hero' ) || ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    $in = isset( $_POST['balans_hero'] ) ? (array) wp_unslash( $_POST['balans_hero'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
    foreach ( BALANS_HERO_FIELDS as $f ) {
        $v = isset( $in[ $f ] ) ? ( 'intro' === $f ? sanitize_textarea_field( $in[ $f ] ) : sanitize_text_field( $in[ $f ] ) ) : '';
        update_post_meta( $post_id, '_balans_hero_' . $f, $v );
    }
} );

/* ---------------------------------------------------------------
 * Standaard pagina-inhoud (in de editor aan te passen)
 * ------------------------------------------------------------- */

function balans_block_p( $text ) {
    return '<!-- wp:paragraph --><p>' . esc_html( $text ) . '</p><!-- /wp:paragraph -->';
}

function balans_block_h( $text, $level = 2 ) {
    $attr = 2 === $level ? '' : ' {"level":' . $level . '}';
    return '<!-- wp:heading' . $attr . ' --><h' . $level . ' class="wp-block-heading">' . esc_html( $text ) . '</h' . $level . '><!-- /wp:heading -->';
}

function balans_block_button( $text, $url ) {
    return '<!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="' . esc_url( $url ) . '">' . esc_html( $text ) . '</a></div><!-- /wp:button --></div><!-- /wp:buttons -->';
}

function balans_block_columns( $columns ) {
    $html = '<!-- wp:columns --><div class="wp-block-columns">';
    foreach ( $columns as $inner ) {
        $html .= '<!-- wp:column --><div class="wp-block-column">' . implode( '', $inner ) . '</div><!-- /wp:column -->';
    }
    return $html . '</div><!-- /wp:columns -->';
}

function balans_default_body( $key ) {
    $example = __( '(Voorbeeldtekst — klik erop om hem te vervangen.)', 'balans' );
    switch ( $key ) {
        case 'over':
            return balans_block_columns( array(
                array(
                    balans_block_h( __( 'De organisatie', 'balans' ) ),
                    balans_block_p( __( 'Vertel hier in een paar zinnen wie EQ5 is, waar je voor staat en voor wie je er bent.', 'balans' ) . ' ' . $example ),
                    balans_block_h( __( 'De oprichter', 'balans' ) ),
                    balans_block_p( __( 'Vertel hier iets over jezelf: je achtergrond, je ervaring en hoe je bij dit werk bent gekomen.', 'balans' ) . ' ' . $example ),
                ),
                array(
                    balans_block_h( __( 'Onze aanpak & empathisch energiewerk', 'balans' ) ),
                    balans_block_p( __( 'Een rustige, menselijke manier van kijken naar wat er speelt. Zonder beloftes, zonder jargon. We presenteren wat we doen niet als vervanging van medische zorg.', 'balans' ) ),
                    balans_block_p( __( 'Beschrijf hier hoe een sessie verloopt en wat iemand kan verwachten.', 'balans' ) . ' ' . $example ),
                    balans_block_h( __( 'Partners & samenwerking', 'balans' ) ),
                    balans_block_p( __( 'Werk je samen met andere praktijken of organisaties? Noem ze hier, of verwijder dit kopje.', 'balans' ) . ' ' . $example ),
                ),
            ) );
        case 'contact':
            return balans_block_p( __( 'Adres: [straat en huisnummer, postcode en plaats]', 'balans' ) . ' ' . $example )
                . balans_block_p( __( 'We reageren meestal binnen twee werkdagen.', 'balans' ) );
        case 'voor-bedrijven':
            return balans_block_h( __( 'Twee routes', 'balans' ) )
                . balans_block_columns( array(
                    array(
                        balans_block_h( __( 'Zelf een plek boeken', 'balans' ), 3 ),
                        balans_block_p( __( 'Als een training een vaste prijs en open inschrijving heeft, kun je direct inschrijven en afrekenen via het winkelmandje.', 'balans' ) ),
                        balans_block_button( __( 'Bekijk open trainingen', 'balans' ), (string) get_post_type_archive_link( 'balans_zakelijk' ) ),
                    ),
                    array(
                        balans_block_h( __( 'Vraag een offerte aan', 'balans' ), 3 ),
                        balans_block_p( __( 'Meerdere medewerkers aanmelden of een voorstel ontvangen? Vraag een offerte aan.', 'balans' ) ),
                        balans_block_button( __( 'Offerte aanvragen', 'balans' ), balans_url( 'offerte-aanvragen' ) ),
                    ),
                ) );
    }
    return '';
}

/**
 * Bestaande en nieuwe pagina's eenmalig voorzien van paginakop-velden en standaardinhoud (per taal).
 */
function balans_migrate_page_texts() {
    if ( ! current_user_can( 'edit_pages' ) ) {
        return;
    }
    $pages = get_posts( array(
        'post_type'   => 'page',
        'numberposts' => -1,
        'lang'        => '',
        'post_status' => 'any',
        'meta_query'  => array( // phpcs:ignore WordPress.DB.SlowDBQuery
            array( 'key' => '_wp_page_template', 'value' => 'page-templates/', 'compare' => 'LIKE' ),
            array( 'key' => '_balans_texts_migrated', 'compare' => 'NOT EXISTS' ),
        ),
    ) );
    foreach ( $pages as $page ) {
        $key = balans_hero_key_for_post( $page->ID );
        list( $hero, $body ) = balans_with_locale( balans_post_locale( $page->ID ), function () use ( $key ) {
            return array( balans_hero_defaults( $key ), balans_default_body( $key ) );
        } );
        foreach ( $hero as $f => $v ) {
            if ( '' === (string) get_post_meta( $page->ID, '_balans_hero_' . $f, true ) && ! ( 'title' === $f && 'events' === $key ) ) {
                update_post_meta( $page->ID, '_balans_hero_' . $f, $v );
            }
        }
        if ( $body ) {
            wp_update_post( array( 'ID' => $page->ID, 'post_content' => $body . ( trim( $page->post_content ) ? "\n\n" . $page->post_content : '' ) ) );
        }
        update_post_meta( $page->ID, '_balans_texts_migrated', 1 );
    }
}
add_action( 'admin_init', 'balans_migrate_page_texts', 40 );

/* ---------------------------------------------------------------
 * Homepage-teksten (Weergave → Customizer → Homepage-teksten)
 * ------------------------------------------------------------- */

function balans_hp_fields() {
    return array(
        'hero_eyebrow'   => array( __( 'Welkomstblok: klein kopje', 'balans' ), __( 'Welkom', 'balans' ) ),
        'hero_title'     => array( __( 'Welkomstblok: grote kop', 'balans' ), sprintf( __( 'Van vicieuze cirkel %s', 'balans' ), '' ) ),
        'hero_highlight' => array( __( 'Welkomstblok: schuingedrukt deel van de kop', 'balans' ), __( 'naar balans.', 'balans' ) ),
        'hero_intro'     => array( __( 'Welkomstblok: introductie', 'balans' ), __( 'Iedereen heeft vragen in een zoektocht naar balans. Hier vind je rustige begeleiding — van meditaties die je zelf kunt doen tot gesprekken en lichaamsgericht werk.', 'balans' ), 'textarea' ),
        'hero_cta1'      => array( __( 'Welkomstblok: eerste knop', 'balans' ), __( 'Ontdek wat bij je past', 'balans' ) ),
        'hero_cta2'      => array( __( 'Welkomstblok: tweede knop', 'balans' ), __( 'Luister gratis', 'balans' ) ),
        'routes_eyebrow' => array( __( 'Keuzehulp: klein kopje', 'balans' ), __( 'Keuzehulp', 'balans' ) ),
        'routes_title'   => array( __( 'Keuzehulp: kop', 'balans' ), __( 'Waar ben je naar op zoek?', 'balans' ) ),
        'routes_intro'   => array( __( 'Keuzehulp: tekst', 'balans' ), __( 'Vijf directe routes. Kies wat op dit moment het beste past.', 'balans' ), 'textarea' ),
        'biz_eyebrow'    => array( __( 'Bedrijven: klein kopje', 'balans' ), __( 'Voor bedrijven', 'balans' ) ),
        'biz_title'      => array( __( 'Bedrijven: kop', 'balans' ), __( 'Trainingen die ruimte scheppen in de groep.', 'balans' ) ),
        'biz_text'       => array( __( 'Bedrijven: tekst', 'balans' ), __( 'Een vastgesteld concept en programma, op onze locatie. Op aanvraag maken we een offerte voor een groep medewerkers.', 'balans' ), 'textarea' ),
        'biz_button'     => array( __( 'Bedrijven: knop', 'balans' ), __( 'Bekijk het zakelijke aanbod', 'balans' ) ),
        'biz_card_title' => array( __( 'Bedrijven: kaart — titel', 'balans' ), __( 'Werken met de groep', 'balans' ) ),
        'biz_card_text'  => array( __( 'Bedrijven: kaart — tekst', 'balans' ), __( 'Een dag waarin de groep ruimte maakt voor wat er onderling speelt.', 'balans' ), 'textarea' ),
        'shop_eyebrow'   => array( __( 'Shop: klein kopje', 'balans' ), __( 'Shop', 'balans' ) ),
        'shop_title'     => array( __( 'Shop: kop', 'balans' ), __( 'Kristallen & (half)edelstenen', 'balans' ) ),
        'shop_text'      => array( __( 'Shop: tekst', 'balans' ), __( 'Stenen die energetisch zijn bewerkt om bij een thema of persoonlijk proces te ondersteunen.', 'balans' ), 'textarea' ),
        'aanpak_eyebrow' => array( __( 'Aanpak: klein kopje', 'balans' ), __( 'Onze aanpak', 'balans' ) ),
        'aanpak_title'   => array( __( 'Aanpak: kop', 'balans' ), __( 'Empathisch energiewerk, in woorden die kloppen.', 'balans' ) ),
        'aanpak_text'    => array( __( 'Aanpak: tekst', 'balans' ), __( 'Geen beloftes, geen jargon. Een rustige, menselijke manier van kijken naar wat er bij jou of in een groep speelt.', 'balans' ), 'textarea' ),
        'aanpak_button'  => array( __( 'Aanpak: knop', 'balans' ), __( 'Lees meer over onze aanpak', 'balans' ) ),
    );
}

/** Homepage-tekst in de huidige taal. */
function balans_hp( $key ) {
    $lang = balans_mail_lang( balans_lang() );
    $mod  = get_theme_mod( 'eq5_hp_' . $key . '_' . $lang, '' );
    return '' !== trim( (string) $mod ) ? $mod : trim( balans_hp_fields()[ $key ][1] );
}

add_action( 'customize_register', function ( $wp_customize ) {
    $wp_customize->add_panel( 'eq5_homepage', array(
        'title'       => __( 'Homepage-teksten', 'balans' ),
        'description' => __( 'Alle teksten van de homepage, per taal. Laat een veld leeg voor de standaardtekst.', 'balans' ),
        'priority'    => 30,
    ) );
    $wp_customize->add_section( 'eq5_hp_image', array( 'title' => __( 'Afbeelding welkomstblok', 'balans' ), 'panel' => 'eq5_homepage' ) );
    $wp_customize->add_setting( 'eq5_hp_hero_image', array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
    $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'eq5_hp_hero_image', array( 'label' => __( 'Afbeelding naast de welkomsttekst', 'balans' ), 'section' => 'eq5_hp_image', 'mime_type' => 'image' ) ) );
    foreach ( array( 'nl' => 'Nederlands', 'en' => 'English', 'fr' => 'Français' ) as $lang => $label ) {
        $wp_customize->add_section( 'eq5_hp_' . $lang, array( 'title' => $label, 'panel' => 'eq5_homepage' ) );
        $fields = balans_with_locale( array( 'nl' => 'nl_NL', 'en' => 'en_GB', 'fr' => 'fr_FR' )[ $lang ], 'balans_hp_fields' );
        foreach ( $fields as $key => $f ) {
            $id = 'eq5_hp_' . $key . '_' . $lang;
            $wp_customize->add_setting( $id, array( 'default' => '', 'sanitize_callback' => isset( $f[2] ) ? 'sanitize_textarea_field' : 'sanitize_text_field' ) );
            $wp_customize->add_control( $id, array(
                'label'       => balans_hp_fields()[ $key ][0],
                'section'     => 'eq5_hp_' . $lang,
                'type'        => isset( $f[2] ) ? 'textarea' : 'text',
                'input_attrs' => array( 'placeholder' => trim( $f[1] ) ),
            ) );
        }
    }
} );
