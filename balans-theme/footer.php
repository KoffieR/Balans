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
                <p class="footer-brand"><?php bloginfo( 'name' ); ?></p>
                <p style="color:#B5A48B; margin-top:10px; max-width:28ch;">
                    <?php esc_html_e( 'Van vicieuze cirkel naar balans. Iedereen heeft vragen in een zoektocht naar balans.', 'balans' ); ?>
                </p>
                <p style="color:#B5A48B; font-size:0.85rem; margin-top:16px;">
                    <small><?php esc_html_e( 'Contactgegevens: nog aan te leveren door de eigenaar.', 'balans' ); ?></small>
                </p>
            </div>

            <div class="footer-col">
                <h4><?php esc_html_e( 'Ontdek', 'balans' ); ?></h4>
                <ul>
                    <li><a href="<?php echo esc_url( home_url( '/voor-jou/' ) ); ?>"><?php esc_html_e( 'Voor jou', 'balans' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/voor-bedrijven/' ) ); ?>"><?php esc_html_e( 'Voor bedrijven', 'balans' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'Shop', 'balans' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/artikelen-bronnen/' ) ); ?>"><?php esc_html_e( 'Artikelen & bronnen', 'balans' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/over/' ) ); ?>"><?php esc_html_e( 'Over', 'balans' ); ?></a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4><?php esc_html_e( 'Diensten', 'balans' ); ?></h4>
                <ul>
                    <li><a href="<?php echo esc_url( home_url( '/meditatie/' ) ); ?>"><?php esc_html_e( 'Meditatie', 'balans' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/consult/' ) ); ?>"><?php esc_html_e( 'Consult', 'balans' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/drukpuntmassage-consult/' ) ); ?>"><?php esc_html_e( 'Drukpuntmassage + consult', 'balans' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/abonnementen/' ) ); ?>"><?php esc_html_e( 'Abonnementen', 'balans' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/trainingen-workshops/' ) ); ?>"><?php esc_html_e( 'Trainingen & workshops', 'balans' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/afspraak-maken/' ) ); ?>"><?php esc_html_e( 'Afspraak maken', 'balans' ); ?></a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4><?php esc_html_e( 'Contact & volg', 'balans' ); ?></h4>
                <ul>
                    <li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Heb je een vraag?', 'balans' ); ?></a></li>
                    <li><a href="https://www.instagram.com/" rel="noopener" target="_blank"><?php esc_html_e( 'Instagram', 'balans' ); ?></a></li>
                    <li><small style="color:#8A7D71;"><?php esc_html_e( 'Instagram-link nog aan te vullen.', 'balans' ); ?></small></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Alle rechten voorbehouden.', 'balans' ); ?></div>
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
                    echo '<li><a href="' . esc_url( home_url( '/privacyverklaring/' ) ) . '">' . esc_html__( 'Privacy', 'balans' ) . '</a></li>';
                    echo '<li><a href="' . esc_url( home_url( '/cookies/' ) ) . '">' . esc_html__( 'Cookies', 'balans' ) . '</a></li>';
                    echo '<li><a href="' . esc_url( home_url( '/algemene-voorwaarden/' ) ) . '">' . esc_html__( 'Algemene voorwaarden', 'balans' ) . '</a></li>';
                    echo '<li><a href="' . esc_url( home_url( '/webshopvoorwaarden/' ) ) . '">' . esc_html__( 'Webshopvoorwaarden', 'balans' ) . '</a></li>';
                    echo '<li><a href="' . esc_url( home_url( '/verzenden-retour/' ) ) . '">' . esc_html__( 'Verzenden & retour', 'balans' ) . '</a></li>';
                    echo '<li><a href="' . esc_url( home_url( '/disclaimer/' ) ) . '">' . esc_html__( 'Disclaimer', 'balans' ) . '</a></li>';
                    echo '</ul>';
                }
                ?>
            </nav>
        </div>

        <p style="color:#8A7D71; font-size:0.78rem; margin-top:18px;">
            <small><?php esc_html_e( 'Juridische teksten moeten nog door de eigenaar worden aangeleverd.', 'balans' ); ?></small>
        </p>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
