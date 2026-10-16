<?php
/**
 * Template Name: Contact
 *
 * @package Balans
 */

get_header();
?>
<main id="main" class="site-main" data-testid="page-contact">
    <section class="page-hero">
        <div class="container">
            <p class="eyebrow"><?php esc_html_e( 'Contact', 'balans' ); ?></p>
            <h1><?php esc_html_e( 'Heb je een vraag?', 'balans' ); ?></h1>
            <p class="page-hero__intro"><?php esc_html_e( 'Stuur een bericht en we reageren zo snel mogelijk.', 'balans' ); ?></p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <?php balans_placeholder_notice( __( 'Adres en reactietermijnen nog aan te leveren door de eigenaar.', 'balans' ) ); ?>

            <form class="balans-form" data-balans-ajax="balans_contact" data-testid="form-contact" novalidate>
                <div class="form-row--inline">
                    <div class="form-row">
                        <label for="contact-name"><?php esc_html_e( 'Naam', 'balans' ); ?></label>
                        <input type="text" id="contact-name" name="name" required data-testid="contact-name" />
                    </div>
                    <div class="form-row">
                        <label for="contact-email"><?php esc_html_e( 'E-mail', 'balans' ); ?></label>
                        <input type="email" id="contact-email" name="email" required data-testid="contact-email" />
                    </div>
                </div>
                <div class="form-row">
                    <label for="contact-message"><?php esc_html_e( 'Je bericht', 'balans' ); ?></label>
                    <?php $event_id = isset( $_GET['event'] ) ? absint( $_GET['event'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification ?>
                    <textarea id="contact-message" name="message" required data-testid="contact-message"><?php echo $event_id && 'balans_event' === get_post_type( $event_id ) ? esc_textarea( sprintf( __( 'Ik meld me graag aan voor: %1$s (%2$s)', 'balans' ), get_the_title( $event_id ), balans_event_date( $event_id ) ) ) : ''; ?></textarea>
                </div>
                <button type="submit" class="balans-btn balans-btn--primary" data-testid="contact-submit"><?php esc_html_e( 'Bericht verzenden', 'balans' ); ?></button>
                <div class="balans-form-message" role="status" aria-live="polite"></div>
            </form>
        </div>
    </section>
</main>
<?php get_footer();
