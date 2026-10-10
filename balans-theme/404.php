<?php
/**
 * 404 template.
 *
 * @package Balans
 */

get_header();
?>
<main id="main" class="site-main">
    <section class="section">
        <div class="container container--text" style="text-align:center;">
            <p class="eyebrow"><?php esc_html_e( '404', 'balans' ); ?></p>
            <h1><?php esc_html_e( 'Deze pagina kunnen we niet vinden.', 'balans' ); ?></h1>
            <p><?php esc_html_e( 'Misschien is de link verouderd. Ga terug naar de homepage of ontdek wat bij je past.', 'balans' ); ?></p>
            <div style="display:inline-flex; gap:12px; margin-top:20px; flex-wrap:wrap; justify-content:center;">
                <?php balans_button( home_url( '/' ), __( 'Naar de homepage', 'balans' ), 'primary' ); ?>
                <?php balans_button( home_url( '/voor-jou/' ), __( 'Voor jou', 'balans' ), 'secondary' ); ?>
            </div>
        </div>
    </section>
</main>
<?php get_footer();
