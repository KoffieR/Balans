<?php
/**
 * Template Name: Afspraak maken
 *
 * @package Balans
 */

get_header();
$slots = balans_get_time_slots();
?>
<main id="main" class="site-main" data-testid="page-afspraak">
    <section class="page-hero">
        <div class="container">
            <p class="eyebrow"><?php esc_html_e( 'Afspraken', 'balans' ); ?></p>
            <h1><?php esc_html_e( 'Een afspraak inplannen.', 'balans' ); ?></h1>
            <p class="page-hero__intro"><?php esc_html_e( 'Kies de dienst, een datum en een tijdslot. We nemen daarna contact met je op om de afspraak te bevestigen.', 'balans' ); ?></p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <?php if ( shortcode_exists( 'ssa_booking' ) ) : ?>
            <div class="balans-agenda" data-testid="agenda-booking"><?php echo do_shortcode( '[ssa_booking]' ); ?></div>
            <?php else : ?>
            <?php balans_placeholder_notice( __( 'Beschikbare tijdslots zijn tijdelijke voorbeelden. De werkelijke beschikbaarheid wordt later door de eigenaar ingericht.', 'balans' ) ); ?>

            <form class="balans-form" data-balans-ajax="balans_appointment" data-testid="form-afspraak" novalidate>

                <div class="form-row">
                    <label for="afspraak-service"><?php esc_html_e( 'Dienst', 'balans' ); ?></label>
                    <select id="afspraak-service" name="service" required data-testid="afspraak-service">
                        <option value=""><?php esc_html_e( 'Kies een dienst…', 'balans' ); ?></option>
                        <?php $chosen = isset( $_GET['dienst'] ) ? sanitize_key( wp_unslash( $_GET['dienst'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification ?>
                        <?php foreach ( balans_appointment_services() as $key => $label ) : ?>
                            <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $chosen, $key ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <label for="afspraak-date"><?php esc_html_e( 'Datum', 'balans' ); ?></label>
                    <input type="date" id="afspraak-date" name="date" min="<?php echo esc_attr( date_i18n( 'Y-m-d' ) ); ?>" required data-testid="afspraak-date" />
                </div>

                <div class="form-row">
                    <label><?php esc_html_e( 'Tijdslot', 'balans' ); ?></label>
                    <div class="time-slots" data-time-slots data-testid="afspraak-timeslots">
                        <?php foreach ( $slots as $slot ) : ?>
                            <button type="button" class="time-slot" data-value="<?php echo esc_attr( $slot ); ?>"><?php echo esc_html( $slot ); ?></button>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="time_slot" required />
                </div>

                <div class="form-row--inline">
                    <div class="form-row">
                        <label for="afspraak-name"><?php esc_html_e( 'Naam', 'balans' ); ?></label>
                        <input type="text" id="afspraak-name" name="name" required data-testid="afspraak-name" />
                    </div>
                    <div class="form-row">
                        <label for="afspraak-email"><?php esc_html_e( 'E-mail', 'balans' ); ?></label>
                        <input type="email" id="afspraak-email" name="email" required data-testid="afspraak-email" />
                    </div>
                </div>

                <div class="form-row">
                    <label for="afspraak-phone"><?php esc_html_e( 'Telefoon (optioneel)', 'balans' ); ?></label>
                    <input type="tel" id="afspraak-phone" name="phone" data-testid="afspraak-phone" />
                </div>

                <div class="form-row">
                    <label for="afspraak-message"><?php esc_html_e( 'Toelichting (optioneel)', 'balans' ); ?></label>
                    <textarea id="afspraak-message" name="message" data-testid="afspraak-message"></textarea>
                </div>

                <button type="submit" class="balans-btn balans-btn--primary" data-testid="afspraak-submit"><?php esc_html_e( 'Afspraak aanvragen', 'balans' ); ?></button>
                <div class="balans-form-message" role="status" aria-live="polite"></div>

                <p style="font-size:0.85rem; color:var(--balans-muted); margin-top:4px;">
                    <small><?php esc_html_e( 'Afspraken zijn alleen bedoeld voor consult en drukpuntmassage + consult. Shop, meditaties en vaste trainingen lopen via het winkelmandje.', 'balans' ); ?></small>
                </p>
            </form>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php get_footer();
