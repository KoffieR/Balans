<?php
/**
 * Instellingenpagina "Balans → E-mailteksten": bewerkbare welkomst-, herinnerings- en reflectieteksten per taal.
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const BALANS_MAIL_OPTION = 'balans_mail_texts';
const BALANS_MAIL_LANGS  = array( 'nl' => 'Nederlands', 'en' => 'English', 'fr' => 'Français' );

function balans_mail_lang( $lang ) {
    return array_key_exists( (string) $lang, BALANS_MAIL_LANGS ) ? $lang : 'nl';
}

function balans_mail_defaults( $lang ) {
    $t = array(
        'nl' => array(
            'welcome_subject' => 'Welkom bij je EQ5-abonnement: {abonnement}',
            'welcome_hi'      => 'Lieve {naam},',
            'welcome_body'    => 'Fijn dat je hebt gekozen voor het abonnement {abonnement}. Vanaf nu word je {moment} energetisch verlicht. Je hoeft daar zelf niets voor te doen; je kunt er wel ruimte voor maken door op dat moment even rustig aan te doen, water te drinken en te voelen wat er verandert.',
            'welcome_what'    => 'Wat er gebeurt:',
            'welcome_end'     => 'Heb je vragen? Beantwoord gewoon deze e-mail of bel +31 6 23 988 488.',
            'remind_subject'  => 'Vandaag is je moment van verlichting',
            'remind_body'     => 'Vandaag vindt de verlichting van je abonnement {abonnement} plaats. Neem vandaag even de tijd voor rust: een wandeling, een kop thee, een paar minuten stilte. Zo geef je de energie de ruimte om haar werk te doen.',
            'monthly_subject' => 'Je maandelijkse rustmoment',
            'monthly_body'    => 'Vandaag is je maandelijkse rustmoment van het abonnement {abonnement}. Gun jezelf vandaag wat extra rust en aandacht: drink voldoende water, ga even naar buiten en merk op hoe je lichaam zich voelt.',
            'reflect_intro'   => 'Een vraag voor jezelf, na de verlichting:',
            'reflect_q'       => "Wat voelde je vandaag in je lichaam?\nWelke gedachte of emotie kwam vandaag naar boven?\nWaar merkte je vandaag meer rust of ruimte?\nWat wil je vandaag loslaten?\nWaar ben je vandaag dankbaar voor?",
            'reflect_tip'     => 'Schrijf je antwoord kort op in een schriftje of op je telefoon. Zo zie je na een paar weken wat er verandert.',
            'sign'            => 'Hartelijke groet, Remco Sas — EQ5',
            'unsubscribe'     => 'Geen herinneringen meer ontvangen? Klik hier.',
            'unsubscribed'    => 'Je ontvangt geen herinneringen meer. Je abonnement loopt gewoon door.',
        ),
        'en' => array(
            'welcome_subject' => 'Welcome to your EQ5 subscription: {abonnement}',
            'welcome_hi'      => 'Dear {naam},',
            'welcome_body'    => 'We are glad you chose the {abonnement} subscription. From now on you will be lightened energetically {moment}. You do not need to do anything yourself; you can make room for it by taking it easy at that moment, drinking water and feeling what changes.',
            'welcome_what'    => 'What happens:',
            'welcome_end'     => 'Any questions? Simply reply to this email or call +31 6 23 988 488.',
            'remind_subject'  => 'Today is your moment of lightening',
            'remind_body'     => 'Today the lightening of your {abonnement} subscription takes place. Take some time for rest today: a walk, a cup of tea, a few minutes of silence. This gives the energy room to do its work.',
            'monthly_subject' => 'Your monthly moment of rest',
            'monthly_body'    => 'Today is the monthly moment of rest of your {abonnement} subscription. Allow yourself some extra rest and attention today: drink enough water, step outside for a while and notice how your body feels.',
            'reflect_intro'   => 'A question for yourself, after the lightening:',
            'reflect_q'       => "What did you feel in your body today?\nWhich thought or emotion came up today?\nWhere did you notice more calm or space today?\nWhat would you like to let go of today?\nWhat are you grateful for today?",
            'reflect_tip'     => 'Write your answer down briefly in a notebook or on your phone. After a few weeks you will see what changes.',
            'sign'            => 'Warm regards, Remco Sas — EQ5',
            'unsubscribe'     => 'No longer want reminders? Click here.',
            'unsubscribed'    => 'You will no longer receive reminders. Your subscription continues as usual.',
        ),
        'fr' => array(
            'welcome_subject' => 'Bienvenue dans votre abonnement EQ5 : {abonnement}',
            'welcome_hi'      => 'Cher/Chère {naam},',
            'welcome_body'    => "Nous sommes heureux que vous ayez choisi l'abonnement {abonnement}. Désormais, vous serez allégé(e) énergétiquement {moment}. Vous n'avez rien à faire vous-même ; vous pouvez y faire de la place en prenant les choses calmement à ce moment-là, en buvant de l'eau et en ressentant ce qui change.",
            'welcome_what'    => 'Ce qui se passe :',
            'welcome_end'     => 'Des questions ? Répondez simplement à cet e-mail ou appelez le +31 6 23 988 488.',
            'remind_subject'  => "Aujourd'hui, c'est votre moment d'allègement",
            'remind_body'     => "Aujourd'hui a lieu l'allègement de votre abonnement {abonnement}. Prenez un moment de repos aujourd'hui : une promenade, une tasse de thé, quelques minutes de silence. Vous laissez ainsi à l'énergie l'espace pour faire son travail.",
            'monthly_subject' => 'Votre moment de repos mensuel',
            'monthly_body'    => "Aujourd'hui, c'est le moment de repos mensuel de votre abonnement {abonnement}. Accordez-vous un peu plus de repos et d'attention aujourd'hui : buvez suffisamment d'eau, sortez un moment et observez comment votre corps se sent.",
            'reflect_intro'   => "Une question pour vous-même, après l'allègement :",
            'reflect_q'       => "Qu'avez-vous ressenti dans votre corps aujourd'hui ?\nQuelle pensée ou émotion est remontée aujourd'hui ?\nOù avez-vous ressenti plus de calme ou d'espace aujourd'hui ?\nQue souhaitez-vous lâcher aujourd'hui ?\nDe quoi êtes-vous reconnaissant(e) aujourd'hui ?",
            'reflect_tip'     => 'Notez brièvement votre réponse dans un carnet ou sur votre téléphone. Après quelques semaines, vous verrez ce qui change.',
            'sign'            => 'Bien chaleureusement, Remco Sas — EQ5',
            'unsubscribe'     => 'Vous ne souhaitez plus recevoir de rappels ? Cliquez ici.',
            'unsubscribed'    => 'Vous ne recevrez plus de rappels. Votre abonnement continue normalement.',
        ),
    );
    return isset( $t[ $lang ] ) ? $t[ $lang ] : $t['nl'];
}

/**
 * Teksten voor een taal: opgeslagen waarden met standaardteksten als terugval.
 */
function balans_mail_texts( $lang ) {
    $lang  = balans_mail_lang( $lang );
    $saved = (array) get_option( BALANS_MAIL_OPTION, array() );
    $saved = isset( $saved[ $lang ] ) ? array_filter( (array) $saved[ $lang ], 'strlen' ) : array();
    return array_merge( balans_mail_defaults( $lang ), $saved );
}

function balans_mail_fill( $text, $vars ) {
    return strtr( $text, $vars );
}

/**
 * Wisselende reflectievraag: elke verzenddag een andere vraag uit de lijst.
 */
function balans_reflect_question( $t, $seed = null ) {
    $questions = array_values( array_filter( array_map( 'trim', explode( "\n", $t['reflect_q'] ) ) ) );
    if ( ! $questions ) {
        return '';
    }
    $seed = null === $seed ? (int) floor( time() / DAY_IN_SECONDS ) : (int) $seed;
    return $questions[ $seed % count( $questions ) ];
}

function balans_mail_fields() {
    return array(
        __( 'Welkomstmail', 'balans' )               => array(
            'welcome_subject' => array( __( 'Onderwerp', 'balans' ), 'text' ),
            'welcome_hi'      => array( __( 'Aanhef', 'balans' ), 'text' ),
            'welcome_body'    => array( __( 'Tekst', 'balans' ), 'textarea' ),
            'welcome_what'    => array( __( 'Kopje boven de dienstomschrijving', 'balans' ), 'text' ),
            'welcome_end'     => array( __( 'Afsluiting', 'balans' ), 'textarea' ),
        ),
        __( 'Wekelijkse herinnering', 'balans' )     => array(
            'remind_subject' => array( __( 'Onderwerp', 'balans' ), 'text' ),
            'remind_body'    => array( __( 'Tekst', 'balans' ), 'textarea' ),
        ),
        __( 'Maandelijks rustmoment (1e zaterdag)', 'balans' ) => array(
            'monthly_subject' => array( __( 'Onderwerp', 'balans' ), 'text' ),
            'monthly_body'    => array( __( 'Tekst', 'balans' ), 'textarea' ),
        ),
        __( 'Reflectievraag (in elke herinnering)', 'balans' ) => array(
            'reflect_intro' => array( __( 'Inleiding', 'balans' ), 'text' ),
            'reflect_q'     => array( __( 'Vragen — één per regel, er wordt telkens een andere gekozen', 'balans' ), 'textarea' ),
            'reflect_tip'   => array( __( 'Tip onder de vraag', 'balans' ), 'textarea' ),
        ),
        __( 'Algemeen', 'balans' )                   => array(
            'sign'         => array( __( 'Ondertekening', 'balans' ), 'text' ),
            'unsubscribe'  => array( __( 'Afmeldlink', 'balans' ), 'text' ),
            'unsubscribed' => array( __( 'Melding na afmelden', 'balans' ), 'textarea' ),
        ),
    );
}

add_action( 'admin_menu', function () {
    add_menu_page( 'Balans', 'Balans', 'manage_options', 'balans', 'balans_mail_settings_page', 'dashicons-heart', 59 );
    add_submenu_page( 'balans', __( 'E-mailteksten', 'balans' ), __( 'E-mailteksten', 'balans' ), 'manage_options', 'balans', 'balans_mail_settings_page' );
} );

add_action( 'admin_post_balans_mail_texts', function () {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'Geen toegang.', 'balans' ) );
    }
    check_admin_referer( 'balans_mail_texts' );
    $lang  = balans_mail_lang( isset( $_POST['lang'] ) ? sanitize_key( $_POST['lang'] ) : '' );
    $saved = (array) get_option( BALANS_MAIL_OPTION, array() );
    if ( isset( $_POST['reset'] ) ) {
        unset( $saved[ $lang ] );
    } else {
        $input = isset( $_POST['t'] ) ? (array) wp_unslash( $_POST['t'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
        $saved[ $lang ] = array();
        foreach ( balans_mail_fields() as $fields ) {
            foreach ( $fields as $key => $field ) {
                $value                  = isset( $input[ $key ] ) ? (string) $input[ $key ] : '';
                $saved[ $lang ][ $key ] = 'textarea' === $field[1] ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
            }
        }
    }
    update_option( BALANS_MAIL_OPTION, $saved, false );
    wp_safe_redirect( admin_url( 'admin.php?page=balans&lang=' . $lang . '&saved=1' ) );
    exit;
} );

function balans_mail_settings_page() {
    $lang = balans_mail_lang( isset( $_GET['lang'] ) ? sanitize_key( $_GET['lang'] ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification
    $t    = balans_mail_texts( $lang );
    echo '<div class="wrap"><h1>' . esc_html__( 'E-mailteksten voor abonnees', 'balans' ) . '</h1>';
    if ( isset( $_GET['saved'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Opgeslagen.', 'balans' ) . '</p></div>';
    }
    echo '<p>' . esc_html__( 'Bruikbare plaatshouders: {naam} (voornaam abonnee), {abonnement} (naam van het abonnement), {moment} (bijv. "elke zondag", alleen in de welkomstmail). Laat een veld leeg om de standaardtekst te gebruiken.', 'balans' ) . '</p>';
    echo '<nav class="nav-tab-wrapper">';
    foreach ( BALANS_MAIL_LANGS as $code => $label ) {
        printf( '<a data-testid="mail-tab-%1$s" class="nav-tab%2$s" href="%3$s">%4$s</a>', esc_attr( $code ), $code === $lang ? ' nav-tab-active' : '', esc_url( admin_url( 'admin.php?page=balans&lang=' . $code ) ), esc_html( $label ) );
    }
    echo '</nav><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
    wp_nonce_field( 'balans_mail_texts' );
    echo '<input type="hidden" name="action" value="balans_mail_texts"><input type="hidden" name="lang" value="' . esc_attr( $lang ) . '">';
    foreach ( balans_mail_fields() as $section => $fields ) {
        echo '<h2>' . esc_html( $section ) . '</h2><table class="form-table" role="presentation">';
        foreach ( $fields as $key => $field ) {
            $id = 'balans-mail-' . $key;
            echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $field[0] ) . '</label></th><td>';
            if ( 'textarea' === $field[1] ) {
                echo '<textarea data-testid="' . esc_attr( $id ) . '" class="large-text" rows="' . ( 'reflect_q' === $key ? 7 : 4 ) . '" id="' . esc_attr( $id ) . '" name="t[' . esc_attr( $key ) . ']">' . esc_textarea( $t[ $key ] ) . '</textarea>';
            } else {
                echo '<input data-testid="' . esc_attr( $id ) . '" class="large-text" type="text" id="' . esc_attr( $id ) . '" name="t[' . esc_attr( $key ) . ']" value="' . esc_attr( $t[ $key ] ) . '">';
            }
            echo '</td></tr>';
        }
        echo '</table>';
    }
    echo '<p class="submit"><button data-testid="mail-save" type="submit" class="button button-primary">' . esc_html__( 'Opslaan', 'balans' ) . '</button> ';
    echo '<button data-testid="mail-reset" type="submit" name="reset" value="1" class="button" onclick="return confirm(\'' . esc_js( __( 'Standaardteksten voor deze taal terugzetten?', 'balans' ) ) . '\');">' . esc_html__( 'Standaardteksten terugzetten', 'balans' ) . '</button></p>';
    echo '</form></div>';
}
