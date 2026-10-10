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
            'link'    => home_url( '/meditatie/' ),
        ),
        array(
            'eyebrow' => __( 'Praten over wat er bij je speelt', 'balans' ),
            'title'   => __( 'Consult', 'balans' ),
            'text'    => __( 'Een gesprek om stil te staan bij jouw vraag of patroon.', 'balans' ),
            'link'    => home_url( '/consult/' ),
        ),
        array(
            'eyebrow' => __( 'Drukpuntmassage gecombineerd met een consult', 'balans' ),
            'title'   => __( 'Drukpuntmassage + consult', 'balans' ),
            'text'    => __( 'Lichaamsgericht werk gecombineerd met ruimte om te vertellen wat er speelt.', 'balans' ),
            'link'    => home_url( '/drukpuntmassage-consult/' ),
        ),
        array(
            'eyebrow' => __( 'Leren, ontdekken en jezelf ontwikkelen', 'balans' ),
            'title'   => __( 'Trainingen & workshops', 'balans' ),
            'text'    => __( 'Op vastgestelde data op onze locatie. Open inschrijving waar mogelijk.', 'balans' ),
            'link'    => home_url( '/trainingen-workshops/' ),
        ),
        array(
            'eyebrow' => __( 'Regelmatige energetische opschoning van je lichaam', 'balans' ),
            'title'   => __( 'Abonnementen', 'balans' ),
            'text'    => __( 'Een doorlopend ritme van energetische opschoning op afspraak.', 'balans' ),
            'link'    => home_url( '/abonnementen/' ),
        ),
    );
}

function balans_voor_jou_services() {
    return balans_homepage_routes();
}
