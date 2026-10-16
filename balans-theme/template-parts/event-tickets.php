<?php
/**
 * Ticketformulier op de eventpagina.
 *
 * @package Balans
 */

$event   = (int) $args['event'];
$product = balans_event_product( $event );
$left    = balans_event_tickets_left( $event );
?>
<div class="event-tickets" data-testid="event-tickets">
    <?php if ( ! $product->is_in_stock() ) : ?>
        <span class="balans-btn balans-btn--primary is-disabled" aria-disabled="true" data-testid="event-sold-out"><?php esc_html_e( 'Uitverkocht', 'balans' ); ?></span>
        <form class="balans-form event-waitlist" data-balans-ajax="balans_waitlist" data-testid="form-waitlist" novalidate>
            <h3><?php esc_html_e( 'Zet me op de wachtlijst', 'balans' ); ?></h3>
            <p><?php esc_html_e( 'Komt er een plek vrij, dan krijgt iedereen op de wachtlijst tegelijk een e-mail. Wie het eerst een ticket koopt, heeft de plek.', 'balans' ); ?></p>
            <input type="hidden" name="event" value="<?php echo esc_attr( $event ); ?>" />
            <input type="hidden" name="lang" value="<?php echo esc_attr( balans_lang() ); ?>" />
            <div class="form-row--inline">
                <div class="form-row">
                    <label for="waitlist-name"><?php esc_html_e( 'Naam', 'balans' ); ?></label>
                    <input type="text" id="waitlist-name" name="name" required data-testid="waitlist-name" />
                </div>
                <div class="form-row">
                    <label for="waitlist-email"><?php esc_html_e( 'E-mail', 'balans' ); ?></label>
                    <input type="email" id="waitlist-email" name="email" required data-testid="waitlist-email" />
                </div>
            </div>
            <div class="form-row">
                <label class="event-waitlist__consent"><input type="checkbox" name="consent" value="1" required data-testid="waitlist-consent" /> <?php esc_html_e( 'Mijn gegevens worden alleen gebruikt voor deze wachtlijst en daarna verwijderd.', 'balans' ); ?></label>
            </div>
            <button type="submit" class="balans-btn balans-btn--secondary" data-testid="waitlist-submit"><?php esc_html_e( 'Op de wachtlijst', 'balans' ); ?></button>
            <div class="balans-form-message" role="status" aria-live="polite"></div>
        </form>
    <?php else : ?>
        <form class="event-tickets__form" method="get" action="<?php echo esc_url( wc_get_checkout_url() ); ?>">
            <input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />
            <label for="event-qty"><?php esc_html_e( 'Aantal tickets', 'balans' ); ?></label>
            <select id="event-qty" name="quantity" data-testid="event-qty">
                <?php for ( $i = 1; $i <= min( BALANS_TICKETS_MAX, $left ); $i++ ) : ?>
                    <option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i ); ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="balans-btn balans-btn--primary" data-testid="event-buy"><?php esc_html_e( 'Ticket kopen', 'balans' ); ?></button>
        </form>
        <p class="event-tickets__info">
            <?php echo esc_html( balans_event_price( $event ) . ' ' . __( 'per ticket', 'balans' ) ); ?>
            <?php if ( $product->managing_stock() && $left <= 10 ) : ?>
                &middot; <span data-testid="event-left"><?php echo esc_html( sprintf( 1 === $left ? __( 'Nog %d plaats beschikbaar', 'balans' ) : __( 'Nog %d plaatsen beschikbaar', 'balans' ), $left ) ); ?></span>
            <?php endif; ?>
        </p>
    <?php endif; ?>
</div>
