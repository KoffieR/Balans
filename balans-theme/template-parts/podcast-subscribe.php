<?php
/**
 * Abonneerknoppen (Spotify, Apple Podcasts, RSS) + privé-feed voor abonnees.
 *
 * @package Balans
 */

$links = balans_podcast_links();
$icons = array(
    'spotify' => '<path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm4.6 14.4a.6.6 0 0 1-.9.2c-2.4-1.5-5.4-1.8-9-1a.6.6 0 1 1-.3-1.2c3.9-.9 7.3-.5 10 1.1.3.2.4.6.2.9Zm1.2-2.7a.8.8 0 0 1-1 .3c-2.7-1.7-6.9-2.2-10.2-1.2a.8.8 0 1 1-.4-1.5c3.7-1.1 8.4-.6 11.4 1.3.4.2.5.7.2 1.1Zm.1-2.8C14.7 9 9.4 8.8 6.3 9.7a.9.9 0 1 1-.5-1.8c3.5-1 9.3-.8 13 1.4a.9.9 0 0 1-.9 1.6Z"/>',
    'apple'   => '<path d="M12 2.5a7.5 7.5 0 0 0-3 14.4l.2-1.6a6 6 0 1 1 5.6 0l.2 1.6A7.5 7.5 0 0 0 12 2.5Zm0 3a4.5 4.5 0 0 0-2.4 8.3l.2-1.7a3 3 0 1 1 4.4 0l.2 1.7A4.5 4.5 0 0 0 12 5.5Zm0 4a1.6 1.6 0 1 0 0 3.2 1.6 1.6 0 0 0 0-3.2Zm-1.3 4.7-.7 6.5c0 .5.4.8.9.8h2.2c.5 0 .9-.3.9-.8l-.7-6.5a1.9 1.9 0 0 0-2.6 0Z"/>',
    'rss'     => '<path d="M5 3a1 1 0 0 0 0 2 14 14 0 0 1 14 14 1 1 0 1 0 2 0A16 16 0 0 0 5 3Zm0 5a1 1 0 0 0 0 2 9 9 0 0 1 9 9 1 1 0 1 0 2 0A11 11 0 0 0 5 8Zm1.5 7a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5Z"/>',
);
?>
<div class="podcast-subscribe" data-testid="podcast-subscribe">
    <?php if ( $links ) : ?>
        <p class="podcast-subscribe__label"><?php esc_html_e( 'Luister en abonneer via', 'balans' ); ?></p>
        <ul class="podcast-subscribe__list">
            <?php foreach ( $links as $key => $link ) : ?>
                <li>
                    <a class="podcast-btn podcast-btn--<?php echo esc_attr( $key ); ?>" href="<?php echo esc_url( $link[1] ); ?>"<?php echo 'rss' === $key ? '' : ' target="_blank" rel="noopener"'; ?> data-testid="podcast-link-<?php echo esc_attr( $key ); ?>">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><?php echo $icons[ $key ]; // phpcs:ignore WordPress.Security.EscapeOutput ?></svg>
                        <?php echo esc_html( $link[0] ); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ( balans_is_member() && balans_has_podcast_plugin() ) : ?>
        <p class="podcast-subscribe__private" data-testid="podcast-private-feed">
            <?php esc_html_e( 'Jouw privé-feed met alle afleveringen (kopieer deze link in je podcast-app):', 'balans' ); ?>
            <input type="text" readonly value="<?php echo esc_url( balans_podcast_feed_url() ); ?>" onclick="this.select()" aria-label="<?php esc_attr_e( 'Privé-feed', 'balans' ); ?>" />
        </p>
    <?php endif; ?>
</div>
