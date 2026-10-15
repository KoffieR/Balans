<?php
/**
 * Template helpers.
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Render a simple card.
 */
function balans_card( $args ) {
    $defaults = array(
        'eyebrow'  => '',
        'title'    => '',
        'text'     => '',
        'link'     => '',
        'cta'      => __( 'Lees meer', 'balans' ),
        'image'    => '', // URL or empty for CSS placeholder.
        'tone'     => '', // optional CSS modifier.
    );
    $args = wp_parse_args( $args, $defaults );
    ?>
    <article class="balans-card <?php echo esc_attr( $args['tone'] ); ?>" data-testid="balans-card">
        <?php if ( ! empty( $args['image'] ) ) : ?>
            <div class="balans-card__image" style="background-image:url('<?php echo esc_url( $args['image'] ); ?>');" role="img" aria-label="<?php echo esc_attr( $args['title'] ); ?>"></div>
        <?php else : ?>
            <div class="balans-card__image balans-card__image--placeholder" aria-hidden="true"></div>
        <?php endif; ?>
        <div class="balans-card__body">
            <?php if ( $args['eyebrow'] ) : ?><p class="eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></p><?php endif; ?>
            <h3 class="balans-card__title"><?php echo esc_html( $args['title'] ); ?></h3>
            <p class="balans-card__text"><?php echo esc_html( $args['text'] ); ?></p>
            <?php if ( $args['link'] ) : ?>
                <a class="balans-card__link" href="<?php echo esc_url( $args['link'] ); ?>">
                    <?php echo esc_html( $args['cta'] ); ?>
                    <span aria-hidden="true">→</span>
                </a>
            <?php endif; ?>
        </div>
    </article>
    <?php
}

/**
 * Render a "placeholder" notice for sections where content still needs to come from the owner.
 */
function balans_placeholder_notice( $text = '' ) {
    if ( empty( $text ) ) {
        $text = __( 'Nog aan te leveren door de eigenaar. Dit is een tijdelijke aanduiding.', 'balans' );
    }
    echo '<p class="balans-placeholder-notice"><small>' . esc_html( $text ) . '</small></p>';
}

/**
 * Button helper.
 */
function balans_button( $url, $label, $style = 'primary', $testid = '' ) {
    $classes = 'balans-btn balans-btn--' . sanitize_html_class( $style );
    printf(
        '<a href="%s" class="%s"%s>%s<span aria-hidden="true"> →</span></a>',
        esc_url( $url ),
        esc_attr( $classes ),
        $testid ? ' data-testid="' . esc_attr( $testid ) . '"' : '',
        esc_html( $label )
    );
}

/**
 * Inline SVG uit assets/images (kleur via CSS currentColor).
 */
function balans_svg( $name, $class = '' ) {
    $file = BALANS_DIR . '/assets/images/' . sanitize_file_name( $name ) . '.svg';
    if ( ! file_exists( $file ) ) {
        return '';
    }
    $svg = file_get_contents( $file );
    if ( $class ) {
        $svg = preg_replace( '/^<svg /', '<svg class="' . esc_attr( $class ) . '" ', $svg );
    }
    return $svg;
}

/**
 * Q-favicon zolang er geen Site-pictogram in de Customizer is ingesteld.
 */
function balans_favicon() {
    if ( has_site_icon() ) {
        return;
    }
    $img = BALANS_URI . '/assets/images/';
    echo '<link rel="icon" href="' . esc_url( $img . 'favicon.svg' ) . '" type="image/svg+xml" />' . "\n";
    echo '<link rel="icon" href="' . esc_url( $img . 'favicon-32.png' ) . '" sizes="32x32" type="image/png" />' . "\n";
    echo '<link rel="apple-touch-icon" href="' . esc_url( $img . 'apple-touch-icon.png' ) . '" />' . "\n";
}
add_action( 'wp_head', 'balans_favicon', 5 );
