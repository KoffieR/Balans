<?php
/**
 * Interesselijst: bericht bij elk nieuw event.
 *
 * @package Balans
 */
?>
<form class="balans-form event-waitlist event-interest" data-balans-ajax="balans_interest" data-testid="form-interest" novalidate>
    <h3><?php esc_html_e( 'Houd me op de hoogte', 'balans' ); ?></h3>
    <p><?php esc_html_e( 'Laat je e-mailadres achter, dan krijg je bericht zodra er een nieuw event is. Afmelden kan altijd met één klik.', 'balans' ); ?></p>
    <input type="hidden" name="lang" value="<?php echo esc_attr( balans_lang() ); ?>" />
    <div class="form-row--inline">
        <div class="form-row">
            <label for="interest-name"><?php esc_html_e( 'Naam', 'balans' ); ?></label>
            <input type="text" id="interest-name" name="name" required data-testid="interest-name" />
        </div>
        <div class="form-row">
            <label for="interest-email"><?php esc_html_e( 'E-mail', 'balans' ); ?></label>
            <input type="email" id="interest-email" name="email" required data-testid="interest-email" />
        </div>
    </div>
    <div class="form-row">
        <label class="event-waitlist__consent"><input type="checkbox" name="consent" value="1" required data-testid="interest-consent" /> <?php esc_html_e( 'Ik wil een e-mail ontvangen over nieuwe events. Mijn gegevens worden alleen hiervoor gebruikt.', 'balans' ); ?></label>
    </div>
    <button type="submit" class="balans-btn balans-btn--secondary" data-testid="interest-submit"><?php esc_html_e( 'Houd me op de hoogte', 'balans' ); ?></button>
    <div class="balans-form-message" role="status" aria-live="polite"></div>
</form>
