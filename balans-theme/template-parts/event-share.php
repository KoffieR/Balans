<?php
/**
 * Delen en in agenda zetten.
 *
 * @package Balans
 */

$event = (int) $args['event'];
?>
<div class="event-share" data-testid="event-share">
    <a class="balans-btn balans-btn--ghost balans-btn--small" href="<?php echo esc_url( balans_event_ics_url( $event ) ); ?>" data-testid="event-ics"><?php esc_html_e( 'In mijn agenda zetten', 'balans' ); ?></a>
    <p class="event-share__label"><?php esc_html_e( 'Deel dit event', 'balans' ); ?></p>
    <ul class="event-share__list">
        <?php foreach ( balans_share_links( $event ) as $key => $link ) : ?>
            <li><a href="<?php echo esc_url( $link[1] ); ?>" target="_blank" rel="noopener nofollow" data-testid="share-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $link[0] ); ?></a></li>
        <?php endforeach; ?>
        <li><button type="button" class="event-share__copy" data-copy="<?php echo esc_url( get_permalink( $event ) ); ?>" data-done="<?php esc_attr_e( 'Gekopieerd!', 'balans' ); ?>" data-testid="share-copy"><?php esc_html_e( 'Link kopiëren', 'balans' ); ?></button></li>
    </ul>
</div>
