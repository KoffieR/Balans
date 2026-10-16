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
            'welcome_end'     => 'Heb je vragen? Beantwoord gewoon deze e-mail.',
            'remind_subject'  => 'Vandaag is je moment van verlichting',
            'remind_body'     => 'Vandaag vindt de verlichting van je abonnement {abonnement} plaats. Neem vandaag even de tijd voor rust: een wandeling, een kop thee, een paar minuten stilte. Zo geef je de energie de ruimte om haar werk te doen.',
            'monthly_subject' => 'Je maandelijkse rustmoment',
            'monthly_body'    => 'Vandaag is je maandelijkse rustmoment van het abonnement {abonnement}. Gun jezelf vandaag wat extra rust en aandacht: drink voldoende water, ga even naar buiten en merk op hoe je lichaam zich voelt.',
            'reflect_intro'   => 'Een vraag voor jezelf, na de verlichting:',
            'reflect_q'       => "Wat voelde je vandaag in je lichaam?\nWelke gedachte of emotie kwam vandaag naar boven?\nWaar merkte je vandaag meer rust of ruimte?\nWat wil je vandaag loslaten?\nWaar ben je vandaag dankbaar voor?",
            'reflect_tip'     => 'Schrijf je antwoord kort op in een schriftje of op je telefoon. Zo zie je na een paar weken wat er verandert.',
            'appt_subject'    => 'Je afspraakaanvraag bij EQ5: {dienst}',
            'appt_body'       => "Lieve {naam},\n\nBedankt voor je aanvraag voor {dienst} op {datum} om {tijd}. We nemen zo snel mogelijk contact met je op om de afspraak te bevestigen.",
            'appt_phone'      => 'Op het afgesproken tijdstip bel je naar:',
            'event_subject'   => 'Morgen: {event}',
            'event_body'      => 'Morgen is het zover: {event}. Fijn dat je erbij bent!',
            'event_prep'      => 'Doe vandaag en morgen rustig aan. Drink voldoende water, eet licht en kom op tijd, zodat je ontspannen binnen kunt komen. Je hoeft niets voor te bereiden en niets te delen: je bent welkom zoals je bent.',
            'event_map'       => 'Bekijk de locatie op de kaart',
            'waitlist_subject' => 'Er is een plek vrijgekomen: {event}',
            'waitlist_body'    => 'Goed nieuws: er is een plek vrijgekomen voor {event}. Je stond op de wachtlijst, dus je hoort het als eerste. Wie het eerst een ticket koopt, heeft de plek.',
            'waitlist_button'  => 'Bekijk het event',
            'followup_subject' => 'Dank je wel voor {event}',
            'followup_body'    => 'Dank je wel dat je erbij was bij {event}. Er is veel in beweging gekomen; geef jezelf de komende dagen de tijd en ruimte om dat te laten landen.',
            'followup_question'=> 'Wat voel je nu anders dan vóór het event?',
            'followup_next'    => 'Het volgende event',
            'ics_link'         => 'Zet dit event in je agenda',
            'interest_subject' => 'Nieuw event: {event}',
            'interest_body'    => 'Je gaf aan dat je graag wilt horen wanneer er een nieuw event is. Het is zover:',
            'interest_unsub'   => 'Geen berichten meer over nieuwe events? Klik hier.',
            'interest_unsubscribed' => 'Je bent afgemeld en krijgt geen berichten meer over nieuwe events.',
            'sign'            => 'Hartelijke groet, Remco Sas — EQ5',
            'unsubscribe'     => 'Geen herinneringen meer ontvangen? Klik hier.',
            'unsubscribed'    => 'Je ontvangt geen herinneringen meer. Je abonnement loopt gewoon door.',
        ),
        'en' => array(
            'welcome_subject' => 'Welcome to your EQ5 subscription: {abonnement}',
            'welcome_hi'      => 'Dear {naam},',
            'welcome_body'    => 'We are glad you chose the {abonnement} subscription. From now on you will be lightened energetically {moment}. You do not need to do anything yourself; you can make room for it by taking it easy at that moment, drinking water and feeling what changes.',
            'welcome_what'    => 'What happens:',
            'welcome_end'     => 'Any questions? Simply reply to this email.',
            'remind_subject'  => 'Today is your moment of lightening',
            'remind_body'     => 'Today the lightening of your {abonnement} subscription takes place. Take some time for rest today: a walk, a cup of tea, a few minutes of silence. This gives the energy room to do its work.',
            'monthly_subject' => 'Your monthly moment of rest',
            'monthly_body'    => 'Today is the monthly moment of rest of your {abonnement} subscription. Allow yourself some extra rest and attention today: drink enough water, step outside for a while and notice how your body feels.',
            'reflect_intro'   => 'A question for yourself, after the lightening:',
            'reflect_q'       => "What did you feel in your body today?\nWhich thought or emotion came up today?\nWhere did you notice more calm or space today?\nWhat would you like to let go of today?\nWhat are you grateful for today?",
            'reflect_tip'     => 'Write your answer down briefly in a notebook or on your phone. After a few weeks you will see what changes.',
            'appt_subject'    => 'Your appointment request with EQ5: {dienst}',
            'appt_body'       => "Dear {naam},\n\nThank you for your request for {dienst} on {datum} at {tijd}. We will contact you as soon as possible to confirm the appointment.",
            'appt_phone'      => 'At the agreed time, please call:',
            'event_subject'   => 'Tomorrow: {event}',
            'event_body'      => 'Tomorrow is the day: {event}. We are glad you are joining us!',
            'event_prep'      => 'Take it easy today and tomorrow. Drink enough water, eat lightly and arrive on time, so that you can come in relaxed. You do not need to prepare anything or share anything: you are welcome just as you are.',
            'event_map'       => 'View the location on the map',
            'waitlist_subject' => 'A place has become available: {event}',
            'waitlist_body'    => 'Good news: a place has become available for {event}. You were on the waiting list, so you are the first to know. Whoever buys a ticket first gets the place.',
            'waitlist_button'  => 'View the event',
            'followup_subject' => 'Thank you for {event}',
            'followup_body'    => 'Thank you for joining {event}. A lot has been set in motion; give yourself time and space in the coming days to let it settle.',
            'followup_question'=> 'What do you feel differently now compared to before the event?',
            'followup_next'    => 'The next event',
            'ics_link'         => 'Add this event to your calendar',
            'interest_subject' => 'New event: {event}',
            'interest_body'    => 'You told us you would like to hear about new events. Here it is:',
            'interest_unsub'   => 'No longer want news about new events? Click here.',
            'interest_unsubscribed' => 'You have been unsubscribed and will no longer receive news about new events.',
            'sign'            => 'Warm regards, Remco Sas — EQ5',
            'unsubscribe'     => 'No longer want reminders? Click here.',
            'unsubscribed'    => 'You will no longer receive reminders. Your subscription continues as usual.',
        ),
        'fr' => array(
            'welcome_subject' => 'Bienvenue dans votre abonnement EQ5 : {abonnement}',
            'welcome_hi'      => 'Cher/Chère {naam},',
            'welcome_body'    => "Nous sommes heureux que vous ayez choisi l'abonnement {abonnement}. Désormais, vous serez allégé(e) énergétiquement {moment}. Vous n'avez rien à faire vous-même ; vous pouvez y faire de la place en prenant les choses calmement à ce moment-là, en buvant de l'eau et en ressentant ce qui change.",
            'welcome_what'    => 'Ce qui se passe :',
            'welcome_end'     => 'Des questions ? Répondez simplement à cet e-mail.',
            'remind_subject'  => "Aujourd'hui, c'est votre moment d'allègement",
            'remind_body'     => "Aujourd'hui a lieu l'allègement de votre abonnement {abonnement}. Prenez un moment de repos aujourd'hui : une promenade, une tasse de thé, quelques minutes de silence. Vous laissez ainsi à l'énergie l'espace pour faire son travail.",
            'monthly_subject' => 'Votre moment de repos mensuel',
            'monthly_body'    => "Aujourd'hui, c'est le moment de repos mensuel de votre abonnement {abonnement}. Accordez-vous un peu plus de repos et d'attention aujourd'hui : buvez suffisamment d'eau, sortez un moment et observez comment votre corps se sent.",
            'reflect_intro'   => "Une question pour vous-même, après l'allègement :",
            'reflect_q'       => "Qu'avez-vous ressenti dans votre corps aujourd'hui ?\nQuelle pensée ou émotion est remontée aujourd'hui ?\nOù avez-vous ressenti plus de calme ou d'espace aujourd'hui ?\nQue souhaitez-vous lâcher aujourd'hui ?\nDe quoi êtes-vous reconnaissant(e) aujourd'hui ?",
            'reflect_tip'     => 'Notez brièvement votre réponse dans un carnet ou sur votre téléphone. Après quelques semaines, vous verrez ce qui change.',
            'appt_subject'    => 'Votre demande de rendez-vous chez EQ5 : {dienst}',
            'appt_body'       => "Cher/Chère {naam},\n\nMerci pour votre demande de {dienst} le {datum} à {tijd}. Nous vous contacterons dès que possible pour confirmer le rendez-vous.",
            'appt_phone'      => "À l'heure convenue, appelez le :",
            'event_subject'   => 'Demain : {event}',
            'event_body'      => "C'est demain : {event}. Nous sommes heureux de vous accueillir !",
            'event_prep'      => "Prenez les choses calmement aujourd'hui et demain. Buvez suffisamment d'eau, mangez léger et arrivez à l'heure, afin d'entrer détendu(e). Vous n'avez rien à préparer ni à partager : vous êtes le/la bienvenu(e) tel(le) que vous êtes.",
            'event_map'       => 'Voir le lieu sur la carte',
            'waitlist_subject' => 'Une place s\'est libérée : {event}',
            'waitlist_body'    => "Bonne nouvelle : une place s'est libérée pour {event}. Vous étiez sur la liste d'attente, vous êtes donc les premiers informés. La première personne qui achète un billet obtient la place.",
            'waitlist_button'  => "Voir l'événement",
            'followup_subject' => 'Merci pour {event}',
            'followup_body'    => "Merci d'avoir participé à {event}. Beaucoup de choses se sont mises en mouvement ; accordez-vous du temps et de l'espace dans les jours à venir pour les laisser s'installer.",
            'followup_question'=> "Que ressentez-vous différemment maintenant par rapport à avant l'événement ?",
            'followup_next'    => 'Le prochain événement',
            'ics_link'         => 'Ajouter cet événement à votre agenda',
            'interest_subject' => 'Nouvel événement : {event}',
            'interest_body'    => "Vous nous avez indiqué vouloir être informé(e) des nouveaux événements. Le voici :",
            'interest_unsub'   => 'Vous ne souhaitez plus recevoir de nouvelles sur les événements ? Cliquez ici.',
            'interest_unsubscribed' => 'Vous êtes désinscrit(e) et ne recevrez plus de nouvelles sur les événements.',
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

function balans_phone() {
    return (string) get_option( 'balans_phone', '+31 6 23 988 488' );
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
        __( 'Afspraakbevestiging (naar de klant)', 'balans' ) => array(
            'appt_subject' => array( __( 'Onderwerp', 'balans' ), 'text' ),
            'appt_body'    => array( __( 'Tekst — plaatshouders: {naam}, {dienst}, {datum}, {tijd}', 'balans' ), 'textarea' ),
            'appt_phone'   => array( __( 'Zin boven het telefoonnummer', 'balans' ), 'text' ),
        ),
        __( 'Event-herinnering (dag van tevoren)', 'balans' ) => array(
            'event_subject' => array( __( 'Onderwerp — plaatshouder: {event}', 'balans' ), 'text' ),
            'event_body'    => array( __( 'Tekst — plaatshouders: {naam}, {event}', 'balans' ), 'textarea' ),
            'event_prep'    => array( __( 'Voorbereiding', 'balans' ), 'textarea' ),
            'event_map'     => array( __( 'Tekst van de kaartlink (OpenStreetMap)', 'balans' ), 'text' ),
        ),
        __( 'Wachtlijst: plek vrijgekomen', 'balans' ) => array(
            'waitlist_subject' => array( __( 'Onderwerp — plaatshouder: {event}', 'balans' ), 'text' ),
            'waitlist_body'    => array( __( 'Tekst — plaatshouders: {naam}, {event}', 'balans' ), 'textarea' ),
            'waitlist_button'  => array( __( 'Tekst op de knop naar het event', 'balans' ), 'text' ),
        ),
        __( 'Nagesprek (dag na het event)', 'balans' ) => array(
            'followup_subject'  => array( __( 'Onderwerp — plaatshouder: {event}', 'balans' ), 'text' ),
            'followup_body'     => array( __( 'Tekst — plaatshouders: {naam}, {event}', 'balans' ), 'textarea' ),
            'followup_question' => array( __( 'Reflectievraag', 'balans' ), 'text' ),
            'followup_next'     => array( __( 'Kopje boven het volgende event', 'balans' ), 'text' ),
        ),
        __( 'Nieuw event (interesselijst)', 'balans' ) => array(
            'interest_subject'      => array( __( 'Onderwerp — plaatshouder: {event}', 'balans' ), 'text' ),
            'interest_body'         => array( __( 'Tekst boven het event', 'balans' ), 'textarea' ),
            'interest_unsub'        => array( __( 'Afmeldlink', 'balans' ), 'text' ),
            'interest_unsubscribed' => array( __( 'Melding na afmelden', 'balans' ), 'textarea' ),
        ),
        __( 'Agenda-link', 'balans' ) => array(
            'ics_link' => array( __( 'Tekst van de agenda-link (ticketmail en herinnering)', 'balans' ), 'text' ),
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
    if ( isset( $_POST['balans_phone'] ) ) {
        update_option( 'balans_phone', sanitize_text_field( wp_unslash( $_POST['balans_phone'] ) ) );
    }
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
    echo '<h2>' . esc_html__( 'Telefoonnummer', 'balans' ) . '</h2><table class="form-table" role="presentation"><tr><th scope="row"><label for="balans-phone">' . esc_html__( 'Telefoonnummer in de afspraakbevestiging (geldt voor alle talen)', 'balans' ) . '</label></th><td><input data-testid="balans-phone" class="regular-text" type="text" id="balans-phone" name="balans_phone" value="' . esc_attr( balans_phone() ) . '"><p class="description">' . esc_html__( 'Dit nummer staat alleen in de bevestigingsmail van een afspraak, niet op de website.', 'balans' ) . '</p></td></tr></table>';
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
    echo '</form>';
    balans_test_mail_form( $lang );
    echo '</div>';
}
