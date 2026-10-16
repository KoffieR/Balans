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
