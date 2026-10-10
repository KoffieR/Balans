<?php
/**
 * Theme header.
 *
 * @package Balans
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Spring naar hoofdinhoud', 'balans' ); ?></a>

<header class="site-header" data-testid="site-header">
    <div class="container site-header__inner">
        <div class="site-branding">
            <?php if ( has_custom_logo() ) : the_custom_logo(); else : ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" data-testid="site-logo">
                    <?php bloginfo( 'name' ); ?>
                    <small><?php esc_html_e( 'Van vicieuze cirkel naar balans', 'balans' ); ?></small>
                </a>
            <?php endif; ?>
        </div>

        <button class="nav-toggle" aria-label="<?php esc_attr_e( 'Open menu', 'balans' ); ?>" aria-expanded="false" aria-controls="primary-navigation" data-testid="nav-toggle">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
        </button>

        <nav class="primary-nav" id="primary-navigation" aria-label="<?php esc_attr_e( 'Hoofdnavigatie', 'balans' ); ?>" data-testid="primary-nav">
            <?php
            if ( has_nav_menu( 'primary' ) ) {
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => '',
                    'depth'          => 2,
                ) );
            } else {
                echo '<ul>';
                echo '<li><a href="' . esc_url( home_url( '/voor-jou/' ) ) . '">' . esc_html__( 'Voor jou', 'balans' ) . '</a></li>';
                echo '<li><a href="' . esc_url( home_url( '/voor-bedrijven/' ) ) . '">' . esc_html__( 'Voor bedrijven', 'balans' ) . '</a></li>';
                echo '<li><a href="' . esc_url( home_url( '/shop/' ) ) . '">' . esc_html__( 'Shop', 'balans' ) . '</a></li>';
                echo '<li><a href="' . esc_url( home_url( '/artikelen-bronnen/' ) ) . '">' . esc_html__( 'Artikelen & bronnen', 'balans' ) . '</a></li>';
                echo '<li><a href="' . esc_url( home_url( '/over/' ) ) . '">' . esc_html__( 'Over', 'balans' ) . '</a></li>';
                echo '</ul>';
            }
            ?>
            <a class="balans-btn balans-btn--primary balans-btn--small" href="<?php echo esc_url( home_url( '/afspraak-maken/' ) ); ?>" data-testid="header-cta-afspraak">
                <?php esc_html_e( 'Afspraak maken', 'balans' ); ?>
            </a>
        </nav>
    </div>
</header>
