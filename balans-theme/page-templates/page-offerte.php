<?php
/**
 * Template Name: Offerte aanvragen (zakelijk)
 *
 * @package Balans
 */

get_header();
?>
<main id="main" class="site-main" data-testid="page-offerte">
    <?php balans_page_hero( 'offerte' ); ?>

    <section class="section">
        <div class="container">
            <form class="balans-form" data-balans-ajax="balans_offerte" data-testid="form-offerte" novalidate>
                <div class="form-row--inline">
                    <div class="form-row">
                        <label for="offerte-company"><?php esc_html_e( 'Bedrijfsnaam', 'balans' ); ?></label>
                        <input type="text" id="offerte-company" name="company" required data-testid="offerte-company" />
                    </div>
                    <div class="form-row">
                        <label for="offerte-name"><?php esc_html_e( 'Contactpersoon', 'balans' ); ?></label>
                        <input type="text" id="offerte-name" name="name" required data-testid="offerte-name" />
                    </div>
                </div>
                <div class="form-row--inline">
                    <div class="form-row">
                        <label for="offerte-email"><?php esc_html_e( 'E-mail', 'balans' ); ?></label>
                        <input type="email" id="offerte-email" name="email" required data-testid="offerte-email" />
                    </div>
                    <div class="form-row">
                        <label for="offerte-phone"><?php esc_html_e( 'Telefoon (optioneel)', 'balans' ); ?></label>
                        <input type="tel" id="offerte-phone" name="phone" data-testid="offerte-phone" />
                    </div>
                </div>
                <div class="form-row--inline">
                    <div class="form-row">
                        <label for="offerte-training"><?php esc_html_e( 'Gewenste training', 'balans' ); ?></label>
                        <input type="text" id="offerte-training" name="training" placeholder="<?php esc_attr_e( 'Bijv. Werken met de groep', 'balans' ); ?>" data-testid="offerte-training" />
                    </div>
                    <div class="form-row">
                        <label for="offerte-groupsize"><?php esc_html_e( 'Groepsgrootte (optioneel)', 'balans' ); ?></label>
                        <input type="text" id="offerte-groupsize" name="groupsize" data-testid="offerte-groupsize" />
                    </div>
                </div>
                <div class="form-row">
                    <label for="offerte-message"><?php esc_html_e( 'Vraag of aanvullende informatie', 'balans' ); ?></label>
                    <textarea id="offerte-message" name="message" data-testid="offerte-message"></textarea>
                </div>
                <button type="submit" class="balans-btn balans-btn--primary" data-testid="offerte-submit"><?php esc_html_e( 'Offerte aanvragen', 'balans' ); ?></button>
                <div class="balans-form-message" role="status" aria-live="polite"></div>
            </form>
        </div>
    </section>
</main>
<?php get_footer();
