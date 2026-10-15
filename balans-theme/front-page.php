<?php
/**
 * Homepage template.
 *
 * @package Balans
 */

get_header();
?>

<main id="main" class="site-main" data-testid="homepage">

    <!-- HERO -->
    <section class="hero">
        <?php echo balans_svg( 'q-accent', 'hero__q' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
        <div class="container">
            <div class="hero__grid">
                <div class="hero__content fade-in">
                    <p class="eyebrow"><?php esc_html_e( 'Welkom', 'balans' ); ?></p>
                    <h1>
                        <?php
                        printf(
                            /* translators: %s is the italic highlight */
                            esc_html__( 'Van vicieuze cirkel %s', 'balans' ),
                            '<em>' . esc_html__( 'naar balans.', 'balans' ) . '</em>'
                        );
                        ?>
                    </h1>
                    <p class="hero__intro">
                        <?php esc_html_e( 'Iedereen heeft vragen in een zoektocht naar balans. Hier vind je rustige begeleiding — van meditaties die je zelf kunt doen tot gesprekken en lichaamsgericht werk.', 'balans' ); ?>
                    </p>
                    <div class="hero__actions">
                        <?php balans_button( balans_url( 'voor-jou' ), __( 'Ontdek wat bij je past', 'balans' ), 'primary', 'hero-cta-primary' ); ?>
                        <?php balans_button( balans_url( 'meditatie' ), __( 'Luister gratis', 'balans' ), 'secondary', 'hero-cta-secondary' ); ?>
                    </div>
                </div>
                <div class="hero__visual fade-in" data-delay="2" aria-hidden="true">
                    <p class="hero__visual-label"><?php esc_html_e( 'Beeld nog aan te leveren', 'balans' ); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- ROUTES -->
    <section class="section section--alt" data-testid="routes-section">
        <div class="container">
            <div class="section-intro">
                <p class="eyebrow"><?php esc_html_e( 'Keuzehulp', 'balans' ); ?></p>
                <h2><?php esc_html_e( 'Waar ben je naar op zoek?', 'balans' ); ?></h2>
                <p><?php esc_html_e( 'Vijf directe routes. Kies wat op dit moment het beste past.', 'balans' ); ?></p>
            </div>

            <div class="routes-grid">
                <?php $i = 0; foreach ( balans_homepage_routes() as $route ) : $i++; ?>
                    <a class="route fade-in" data-delay="<?php echo esc_attr( min( $i, 5 ) ); ?>" href="<?php echo esc_url( $route['link'] ); ?>" data-testid="route-<?php echo esc_attr( sanitize_title( $route['title'] ) ); ?>">
                        <p class="eyebrow"><?php echo esc_html( $route['eyebrow'] ); ?></p>
                        <h3><?php echo esc_html( $route['title'] ); ?></h3>
                        <p class="route__arrow" aria-hidden="true">→</p>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- VOOR BEDRIJVEN -->
    <section class="section">
        <div class="container">
            <div class="hero__grid">
                <div>
                    <p class="eyebrow"><?php esc_html_e( 'Voor bedrijven', 'balans' ); ?></p>
                    <h2><?php esc_html_e( 'Trainingen die ruimte scheppen in de groep.', 'balans' ); ?></h2>
                    <p><?php esc_html_e( 'Een vastgesteld concept en programma, op onze locatie. Op aanvraag maken we een offerte voor een groep medewerkers.', 'balans' ); ?></p>
                    <?php balans_button( balans_url( 'voor-bedrijven' ), __( 'Bekijk het zakelijke aanbod', 'balans' ), 'secondary', 'homepage-cta-zakelijk' ); ?>
                </div>
                <div>
                    <?php balans_card( array(
                        'eyebrow' => __( 'Voorbeeld-concept', 'balans' ),
                        'title'   => __( 'Werken met de groep', 'balans' ),
                        'text'    => __( 'Programmadetails nog aan te leveren door de eigenaar.', 'balans' ),
                        'link'    => balans_url( 'voor-bedrijven' ),
                        'cta'     => __( 'Meer info', 'balans' ),
                    ) ); ?>
                </div>
            </div>
        </div>
    </section>

    <!-- SHOP INTRO -->
    <section class="section section--alt" data-testid="homepage-shop">
        <div class="container">
            <div class="section-intro">
                <p class="eyebrow"><?php esc_html_e( 'Shop', 'balans' ); ?></p>
                <h2><?php esc_html_e( 'Kristallen & (half)edelstenen', 'balans' ); ?></h2>
                <p><?php esc_html_e( 'Stenen die energetisch zijn bewerkt om bij een thema of persoonlijk proces te ondersteunen.', 'balans' ); ?></p>
            </div>

            <?php if ( class_exists( 'WooCommerce' ) && ! defined( 'BALANS_PREVIEW' ) ) : ?>
                <div style="margin-top:28px;">
                    <?php echo do_shortcode( '[products limit="3" columns="3" visibility="visible"]' ); ?>
                </div>
                <div style="margin-top:28px;">
                    <?php balans_button( balans_url( 'shop' ), __( 'Bekijk de hele shop', 'balans' ), 'primary', 'homepage-shop-cta' ); ?>
                </div>
            <?php else : ?>
                <?php balans_placeholder_notice( __( 'WooCommerce is nog niet geactiveerd. Activeer de plugin om producten te tonen.', 'balans' ) ); ?>
                <?php balans_button( balans_url( 'shop' ), __( 'Naar de shop', 'balans' ), 'secondary', 'homepage-shop-cta' ); ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- AANPAK -->
    <section class="section">
        <div class="container">
            <div class="section-intro">
                <p class="eyebrow"><?php esc_html_e( 'Onze aanpak', 'balans' ); ?></p>
                <h2><?php esc_html_e( 'Empathisch energiewerk, in woorden die kloppen.', 'balans' ); ?></h2>
                <p><?php esc_html_e( 'Geen beloftes, geen jargon. Een rustige, menselijke manier van kijken naar wat er bij jou of in een groep speelt.', 'balans' ); ?></p>
            </div>
            <div style="margin-top:24px;">
                <?php balans_button( balans_url( 'over' ), __( 'Lees meer over onze aanpak', 'balans' ), 'ghost', 'homepage-aanpak-cta' ); ?>
            </div>
        </div>
    </section>

</main>

<?php
get_footer();
