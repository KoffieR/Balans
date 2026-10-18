<?php
/**
 * Homepage: goedgekeurde ervaringen + formulier "Deel je ervaring".
 *
 * @package Balans
 */

$show_list = get_theme_mod( 'eq5_ervaringen_show', true );
$show_form = get_theme_mod( 'eq5_ervaringen_form', true );
$items     = $show_list ? balans_get_ervaringen() : array();
if ( ! $items && ! $show_form ) {
    return;
}
?>
<section class="section section--alt" data-testid="homepage-ervaringen">
    <div class="container">
        <div class="section-intro">
            <p class="eyebrow"><?php echo esc_html( balans_hp( 'erv_eyebrow' ) ); ?></p>
            <h2><?php echo esc_html( balans_hp( 'erv_title' ) ); ?></h2>
        </div>

        <?php if ( $items ) : ?>
            <div class="ervaringen-grid" data-testid="ervaringen-list">
                <?php foreach ( $items as $i => $item ) : $dienst = get_post_meta( $item->ID, '_ervaring_dienst', true ); ?>
                    <figure class="ervaring fade-in" data-delay="<?php echo esc_attr( min( $i + 1, 5 ) ); ?>" data-testid="ervaring-<?php echo esc_attr( $item->ID ); ?>">
                        <?php echo balans_svg( 'q-accent', 'ervaring__q' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                        <blockquote class="ervaring__text"><?php echo wp_kses_post( wpautop( esc_html( $item->post_content ) ) ); ?></blockquote>
                        <figcaption class="ervaring__by">
                            <strong><?php echo esc_html( get_the_title( $item ) ); ?></strong>
                            <?php if ( $dienst ) : ?><span><?php echo esc_html( $dienst ); ?></span><?php endif; ?>
                        </figcaption>
                    </figure>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ( $show_form ) : ?>
            <div class="ervaring-form-wrap">
                <h3><?php echo esc_html( balans_hp( 'erv_form_title' ) ); ?></h3>
                <p><?php echo esc_html( balans_hp( 'erv_form_intro' ) ); ?></p>
                <form class="balans-form" data-balans-ajax="balans_ervaring" data-testid="form-ervaring" novalidate>
                    <div class="form-row--inline">
                        <div class="form-row">
                            <label for="ervaring-name"><?php esc_html_e( 'Naam of initialen', 'balans' ); ?></label>
                            <input type="text" id="ervaring-name" name="name" maxlength="60" required data-testid="ervaring-name" />
                        </div>
                        <div class="form-row">
                            <label for="ervaring-dienst"><?php esc_html_e( 'Bij welke dienst (optioneel)', 'balans' ); ?></label>
                            <select id="ervaring-dienst" name="dienst" data-testid="ervaring-dienst">
                                <option value="">—</option>
                                <?php foreach ( balans_ervaring_diensten() as $title ) : ?>
                                    <option value="<?php echo esc_attr( $title ); ?>"><?php echo esc_html( $title ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="ervaring-text"><?php esc_html_e( 'Je ervaring', 'balans' ); ?></label>
                        <textarea id="ervaring-text" name="text" maxlength="600" required data-testid="ervaring-text"></textarea>
                    </div>
                    <div class="form-row form-row--hp" aria-hidden="true">
                        <label for="ervaring-website">Website</label>
                        <input type="text" id="ervaring-website" name="website" tabindex="-1" autocomplete="off" />
                    </div>
                    <button type="submit" class="balans-btn balans-btn--primary" data-testid="ervaring-submit"><?php esc_html_e( 'Ervaring versturen', 'balans' ); ?></button>
                    <div class="balans-form-message" role="status" aria-live="polite" data-testid="ervaring-message"></div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</section>
