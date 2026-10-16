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
                    <p class="eyebrow"><?php echo esc_html( balans_hp( 'hero_eyebrow' ) ); ?></p>
                    <h1><?php echo esc_html( balans_hp( 'hero_title' ) ); ?> <?php if ( balans_hp( 'hero_highlight' ) ) : ?><em><?php echo esc_html( balans_hp( 'hero_highlight' ) ); ?></em><?php endif; ?></h1>
                    <p class="hero__intro"><?php echo esc_html( balans_hp( 'hero_intro' ) ); ?></p>
                    <div class="hero__actions">
                        <?php balans_button( balans_url( 'voor-jou' ), balans_hp( 'hero_cta1' ), 'primary', 'hero-cta-primary' ); ?>
                        <?php balans_button( balans_url( 'meditatie' ), balans_hp( 'hero_cta2' ), 'secondary', 'hero-cta-secondary' ); ?>
                    </div>
                </div>
                <?php $hero_img = (int) get_theme_mod( 'eq5_hp_hero_image', 0 ); ?>
                <div class="hero__visual fade-in<?php echo $hero_img ? ' hero__visual--image' : ''; ?>" data-delay="2" aria-hidden="true">
                    <?php if ( $hero_img ) : ?>
                        <?php echo wp_get_attachment_image( $hero_img, 'large', false, array( 'class' => 'hero__img', 'alt' => '' ) ); ?>
                    <?php elseif ( current_user_can( 'edit_theme_options' ) ) : ?>
                        <p class="hero__visual-label"><?php esc_html_e( 'Kies een afbeelding: Weergave → Customizer → Homepage-teksten (alleen jij ziet deze melding)', 'balans' ); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ROUTES -->
    <section class="section section--alt" data-testid="routes-section">
        <div class="container">
            <div class="section-intro">
                <p class="eyebrow"><?php echo esc_html( balans_hp( 'routes_eyebrow' ) ); ?></p>
                <h2><?php echo esc_html( balans_hp( 'routes_title' ) ); ?></h2>
                <p><?php echo esc_html( balans_hp( 'routes_intro' ) ); ?></p>
            </div>

            <div class="routes-grid">
                <?php $i = 0; foreach ( balans_homepage_routes() as $route ) : $i++; $route = balans_route_upcoming( $route ); ?>
                    <a class="route fade-in" data-delay="<?php echo esc_attr( min( $i, 5 ) ); ?>" href="<?php echo esc_url( $route['link'] ); ?>" data-testid="route-<?php echo esc_attr( sanitize_title( $route['title'] ) ); ?>">
                        <?php if ( ! empty( $route['badge'] ) ) : ?><span class="upcoming-badge" data-testid="upcoming-badge"><?php echo esc_html( $route['badge'] ); ?></span><?php endif; ?>
                        <p class="eyebrow"><?php echo esc_html( $route['eyebrow'] ); ?></p>
                        <h3><?php echo esc_html( $route['title'] ); ?></h3>
                        <?php if ( ! empty( $route['badge'] ) ) : ?><p class="route__text" data-testid="upcoming-text"><?php echo esc_html( $route['text'] ); ?></p><?php endif; ?>
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
                    <p class="eyebrow"><?php echo esc_html( balans_hp( 'biz_eyebrow' ) ); ?></p>
                    <h2><?php echo esc_html( balans_hp( 'biz_title' ) ); ?></h2>
                    <p><?php echo esc_html( balans_hp( 'biz_text' ) ); ?></p>
                    <?php balans_button( balans_url( 'voor-bedrijven' ), balans_hp( 'biz_button' ), 'secondary', 'homepage-cta-zakelijk' ); ?>
                </div>
                <div>
                    <?php balans_card( array(
                        'eyebrow' => balans_hp( 'biz_eyebrow' ),
                        'title'   => balans_hp( 'biz_card_title' ),
                        'text'    => balans_hp( 'biz_card_text' ),
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
                <p class="eyebrow"><?php echo esc_html( balans_hp( 'shop_eyebrow' ) ); ?></p>
                <h2><?php echo esc_html( balans_hp( 'shop_title' ) ); ?></h2>
                <p><?php echo esc_html( balans_hp( 'shop_text' ) ); ?></p>
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
                <p class="eyebrow"><?php echo esc_html( balans_hp( 'aanpak_eyebrow' ) ); ?></p>
                <h2><?php echo esc_html( balans_hp( 'aanpak_title' ) ); ?></h2>
                <p><?php echo esc_html( balans_hp( 'aanpak_text' ) ); ?></p>
            </div>
            <div style="margin-top:24px;">
                <?php balans_button( balans_url( 'over' ), balans_hp( 'aanpak_button' ), 'ghost', 'homepage-aanpak-cta' ); ?>
            </div>
        </div>
    </section>

</main>

<?php
get_footer();
