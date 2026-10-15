<?php
/**
 * Template Name: Consult
 *
 * @package Balans
 */

get_header();
?>
<main id="main" class="site-main" data-testid="page-consult">
    <section class="page-hero">
        <div class="container">
            <p class="eyebrow"><?php esc_html_e( 'Voor jou', 'balans' ); ?></p>
            <h1><?php esc_html_e( 'Consult', 'balans' ); ?></h1>
            <p class="page-hero__intro"><?php esc_html_e( 'Een gesprek om stil te staan bij wat er speelt en zicht te krijgen op een volgende stap.', 'balans' ); ?></p>
        </div>
    </section>

    <section class="section">
        <div class="container container--text entry-content">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); the_content(); endwhile; endif; ?>

            <?php balans_placeholder_notice( __( 'Praktische informatie — duur, prijs, locatie, werkwijze — nog aan te vullen door de eigenaar.', 'balans' ) ); ?>

            <h2><?php esc_html_e( 'Voor wie', 'balans' ); ?></h2>
            <p><?php esc_html_e( 'Nog aan te vullen.', 'balans' ); ?></p>

            <h2><?php esc_html_e( 'Wat je kunt verwachten', 'balans' ); ?></h2>
            <p><?php esc_html_e( 'Nog aan te vullen.', 'balans' ); ?></p>

            <div style="margin-top:30px;">
                <?php balans_button( balans_url( 'afspraak-maken', array( 'dienst' => 'consult' ) ), __( 'Afspraak maken', 'balans' ), 'primary', 'consult-cta' ); ?>
            </div>
        </div>
    </section>
</main>
<?php get_footer();
