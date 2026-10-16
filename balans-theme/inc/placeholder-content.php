<?php
/**
 * Placeholder content library.
 * Called by templates to populate example cards/pages when the owner has not yet provided real content.
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function balans_homepage_routes() {
    return array(
        array(
            'eyebrow' => __( 'Meer rust, zelf ontdekken en oefenen', 'balans' ),
            'title'   => __( 'Meditatie', 'balans' ),
            'text'    => __( 'Gratis en betaalde meditaties die je in je eigen tempo kunt beluisteren.', 'balans' ),
            'link'    => balans_url( 'meditatie' ),
        ),
        array(
            'eyebrow' => __( 'Praten over wat er bij je speelt', 'balans' ),
            'title'   => __( 'Hulp', 'balans' ),
            'text'    => __( 'Persoonlijke hulp, één op één: een afstemming, een consult of een dubbel consult.', 'balans' ),
            'link'    => balans_url( 'afspraak-voor-hulp' ),
        ),
        array(
            'eyebrow' => __( 'Een middag of avond rond één thema', 'balans' ),
            'title'   => __( 'Event', 'balans' ),
            'text'    => __( 'Laat emotionele ladingen los en ga verlicht en fris naar huis. Niemand verwacht dat je iets deelt.', 'balans' ),
            'link'    => balans_url( 'events' ),
            'upcoming' => true,
        ),
        array(
            'eyebrow' => __( 'Leren, ontdekken en jezelf ontwikkelen', 'balans' ),
            'title'   => __( 'Trainingen & workshops', 'balans' ),
            'text'    => __( 'Op vastgestelde data op onze locatie. Open inschrijving waar mogelijk.', 'balans' ),
            'link'    => balans_url( 'trainingen-workshops' ),
        ),
        array(
            'eyebrow' => __( 'Regelmatig afvoeren van verbruikte en vastgelopen emoties en energie uit je lichaam', 'balans' ),
            'title'   => __( 'Abonnementen', 'balans' ),
            'text'    => __( 'Een doorlopend ritme van energetische opschoning op afspraak.', 'balans' ),
            'link'    => balans_url( 'abonnementen' ),
        ),
    );
}

function balans_voor_jou_services() {
    return balans_homepage_routes();
}
