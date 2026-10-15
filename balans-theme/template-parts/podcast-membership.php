<?php
/**
 * Uitleg EQ5-abonnement onder de afleveringen.
 *
 * @package Balans
 */

if ( balans_is_member() ) {
    return;
}
?>
<section class="section section--alt" data-testid="podcast-membership">
    <div class="container">
        <div class="membership-cta">
            <div>
                <p class="eyebrow"><?php esc_html_e( 'EQ5-abonnement', 'balans' ); ?></p>
                <h2><?php esc_html_e( 'Luister naar alle afleveringen en meditaties.', 'balans' ); ?></h2>
                <ul class="membership-cta__list">
                    <li><?php esc_html_e( 'Alle podcastafleveringen, ook de afleveringen voor abonnees', 'balans' ); ?></li>
                    <li><?php esc_html_e( 'Betaalde meditaties op de website', 'balans' ); ?></li>
                    <li><?php esc_html_e( 'Een eigen privé-feed voor je podcast-app', 'balans' ); ?></li>
                    <li><?php esc_html_e( 'Maandelijks of jaarlijks, altijd opzegbaar', 'balans' ); ?></li>
                </ul>
            </div>
            <div class="membership-cta__action">
                <?php balans_button( balans_membership_url(), __( 'Bekijk het abonnement', 'balans' ), 'primary', 'membership-cta-button' ); ?>
            </div>
        </div>
    </div>
</section>
