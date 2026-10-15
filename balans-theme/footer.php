<?php
/**
 * Theme footer.
 *
 * @package Balans
 */
?>
<footer class="site-footer" data-testid="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <a class="footer-brand" href="<?php echo esc_url( balans_url() ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" data-testid="footer-logo">
                    <?php echo balans_svg( 'logo-eq5-slogan-' . balans_lang(), 'footer-brand__svg' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                </a>
                <p class="footer-text">
                    <?php esc_html_e( 'Iedereen heeft vragen in een zoektocht naar balans.', 'balans' ); ?>
                </p>
                <p class="footer-contact" data-testid="footer-contact">
                    Remco Sas &middot; <?php esc_html_e( 'Balansbrenger', 'balans' ); ?><br />
                    <a href="tel:+31623988488">+31 6 23 988 488</a><br />
                    <a href="https://www.eq5.nl">www.eq5.nl</a>
                </p>
            </div>

            <div class="footer-col">
                <h4><?php esc_html_e( 'Ontdek', 'balans' ); ?></h4>
                <ul>
                    <li><a href="<?php echo esc_url( balans_url( 'voor-jou' ) ); ?>"><?php esc_html_e( 'Voor jou', 'balans' ); ?></a></li>
                    <li><a href="<?php echo esc_url( balans_url( 'voor-bedrijven' ) ); ?>"><?php esc_html_e( 'Voor bedrijven', 'balans' ); ?></a></li>
                    <li><a href="<?php echo esc_url( balans_url( 'shop' ) ); ?>"><?php esc_html_e( 'Shop', 'balans' ); ?></a></li>
                    <li><a href="<?php echo esc_url( balans_url( 'artikelen-bronnen' ) ); ?>"><?php esc_html_e( 'Artikelen & bronnen', 'balans' ); ?></a></li>
                    <li><a href="<?php echo esc_url( balans_url( 'over' ) ); ?>"><?php esc_html_e( 'Over', 'balans' ); ?></a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4><?php esc_html_e( 'Diensten', 'balans' ); ?></h4>
                <ul>
                    <li><a href="<?php echo esc_url( balans_url( 'meditatie' ) ); ?>"><?php esc_html_e( 'Meditatie', 'balans' ); ?></a></li>
                    <li><a href="<?php echo esc_url( balans_url( 'consult' ) ); ?>"><?php esc_html_e( 'Consult', 'balans' ); ?></a></li>
                    <li><a href="<?php echo esc_url( balans_url( 'drukpuntmassage-consult' ) ); ?>"><?php esc_html_e( 'Drukpuntmassage + consult', 'balans' ); ?></a></li>
                    <li><a href="<?php echo esc_url( balans_url( 'abonnementen' ) ); ?>"><?php esc_html_e( 'Abonnementen', 'balans' ); ?></a></li>
                    <li><a href="<?php echo esc_url( balans_url( 'trainingen-workshops' ) ); ?>"><?php esc_html_e( 'Trainingen & workshops', 'balans' ); ?></a></li>
                    <li><a href="<?php echo esc_url( balans_url( 'afspraak-maken' ) ); ?>"><?php esc_html_e( 'Afspraak maken', 'balans' ); ?></a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4><?php esc_html_e( 'Contact & volg', 'balans' ); ?></h4>
                <ul>
                    <li><a href="<?php echo esc_url( balans_url( 'contact' ) ); ?>"><?php esc_html_e( 'Heb je een vraag?', 'balans' ); ?></a></li>
                    <li><a href="https://www.instagram.com/" rel="noopener" target="_blank"><?php esc_html_e( 'Instagram', 'balans' ); ?></a></li>
                    <li><small class="footer-muted"><?php esc_html_e( 'Instagram-link nog aan te vullen.', 'balans' ); ?></small></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-copy"><?php echo balans_svg( 'q-accent', 'footer-q' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Alle rechten voorbehouden.', 'balans' ); ?></div>
            <nav aria-label="<?php esc_attr_e( 'Juridische links', 'balans' ); ?>">
                <?php
                if ( has_nav_menu( 'legal' ) ) {
                    wp_nav_menu( array(
                        'theme_location' => 'legal',
                        'container'      => false,
                        'items_wrap'     => '<ul>%3$s</ul>',
                        'depth'          => 1,
                    ) );
                } else {
                    echo '<ul>';
                    echo '<li><a href="' . esc_url( balans_url( 'privacyverklaring' ) ) . '">' . esc_html__( 'Privacy', 'balans' ) . '</a></li>';
                    echo '<li><a href="' . esc_url( balans_url( 'cookies' ) ) . '">' . esc_html__( 'Cookies', 'balans' ) . '</a></li>';
                    echo '<li><a href="' . esc_url( balans_url( 'algemene-voorwaarden' ) ) . '">' . esc_html__( 'Algemene voorwaarden', 'balans' ) . '</a></li>';
                    echo '<li><a href="' . esc_url( balans_url( 'webshopvoorwaarden' ) ) . '">' . esc_html__( 'Webshopvoorwaarden', 'balans' ) . '</a></li>';
                    echo '<li><a href="' . esc_url( balans_url( 'verzenden-retour' ) ) . '">' . esc_html__( 'Verzenden & retour', 'balans' ) . '</a></li>';
                    echo '<li><a href="' . esc_url( balans_url( 'disclaimer' ) ) . '">' . esc_html__( 'Disclaimer', 'balans' ) . '</a></li>';
                    echo '</ul>';
                }
                ?>
            </nav>
        </div>


    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
