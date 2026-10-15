<?php
/**
 * Dienstenteksten (Abonnementen, Afspraak voor hulp) per taal uit inc/content/services-{taal}.php.
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function balans_services() {
    static $cache = array();
    $lang = balans_lang();
    if ( ! isset( $cache[ $lang ] ) ) {
        $cache[ $lang ] = require BALANS_DIR . '/inc/content/services-' . $lang . '.php';
    }
    return $cache[ $lang ];
}

/**
 * Diensten die via het afspraakformulier geboekt kunnen worden (sleutel => titel).
 */
function balans_appointment_services() {
    $list = array();
    foreach ( balans_services()['hulp'] as $key => $item ) {
        if ( 'maatwerk' !== $key ) {
            $list[ $key ] = $item['title'];
        }
    }
    return $list;
}

/**
 * Eén dienstblok met tijdsaanduiding, tekst, prijs en knop.
 */
function balans_service_block( $key, $item, $button_url, $button_label ) {
    $s = balans_services();
    ?>
    <article class="service-block" id="<?php echo esc_attr( $key ); ?>" data-testid="service-<?php echo esc_attr( $key ); ?>">
        <header class="service-block__head">
            <h2 class="service-block__title"><?php echo esc_html( $item['title'] ); ?></h2>
            <p class="service-block__when"><?php echo esc_html( $item['when'] ); ?></p>
        </header>
        <div class="service-block__body">
            <?php foreach ( $item['text'] as $paragraph ) : ?>
                <p><?php echo esc_html( $paragraph ); ?></p>
            <?php endforeach; ?>
        </div>
        <footer class="service-block__foot">
            <p class="service-block__price" data-testid="price-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $s['price_label'] . ': ' . $s['price_value'] ); ?></p>
            <?php balans_button( $button_url, $button_label, 'primary', 'service-cta-' . $key ); ?>
        </footer>
    </article>
    <?php
}

function balans_price_note() {
    ?>
    <aside class="price-note" data-testid="price-note">
        <?php echo balans_svg( 'q-accent', 'price-note__q' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
        <div>
            <?php foreach ( balans_services()['price_note'] as $paragraph ) : ?>
                <p><?php echo esc_html( $paragraph ); ?></p>
            <?php endforeach; ?>
        </div>
    </aside>
    <?php
}
