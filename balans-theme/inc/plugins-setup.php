<?php
/**
 * Installatie-assistent: benodigde plugins installeren/activeren vanaf WordPress.org en alles in één klik instellen.
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function balans_plugins() {
    return array(
        'polylang'                     => array( 'Polylang', 'polylang/polylang.php', true, __( 'Meertaligheid (NL, EN, FR).', 'balans' ) ),
        'paid-memberships-pro'         => array( 'Paid Memberships Pro', 'paid-memberships-pro/paid-memberships-pro.php', true, __( 'Abonnementen, automatische incasso via Stripe en afgeschermde content.', 'balans' ), 'https://license.paidmembershipspro.com/downloads/free/paid-memberships-pro.zip' ),
        'seriously-simple-podcasting'  => array( 'Seriously Simple Podcasting', 'seriously-simple-podcasting/seriously-simple-podcasting.php', true, __( 'Podcast met publieke en privé-feeds.', 'balans' ) ),
        'woocommerce'                  => array( 'WooCommerce', 'woocommerce/woocommerce.php', true, __( 'Webshop voor meditaties, trainingen en producten.', 'balans' ) ),
        'simply-schedule-appointments' => array( 'Simply Schedule Appointments', 'simply-schedule-appointments/simply-schedule-appointments.php', false, __( 'Online agenda (gratis). Gegevens blijven op je eigen server: AVG-vriendelijk. Vervangt het afspraakformulier zodra actief.', 'balans' ) ),
    );
}

function balans_plugin_status( $file ) {
    if ( is_plugin_active( $file ) ) {
        return 'active';
    }
    return file_exists( WP_PLUGIN_DIR . '/' . $file ) ? 'installed' : 'missing';
}

function balans_missing_required_plugins() {
    if ( ! function_exists( 'is_plugin_active' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $missing = array();
    foreach ( balans_plugins() as $slug => $p ) {
        if ( $p[2] && 'active' !== balans_plugin_status( $p[1] ) ) {
            $missing[] = $slug;
        }
    }
    return $missing;
}

/**
 * Plugin installeren (indien nodig) en activeren. Geeft true of een foutmelding terug.
 */
function balans_install_plugin( $slug ) {
    $plugins = balans_plugins();
    if ( ! isset( $plugins[ $slug ] ) ) {
        return __( 'Onbekende plugin.', 'balans' );
    }
    $file = $plugins[ $slug ][1];
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    if ( 'missing' === balans_plugin_status( $file ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        // PMPro staat niet meer op WordPress.org en komt van de officiële PMPro-downloadserver.
        $package = isset( $plugins[ $slug ][4] ) ? $plugins[ $slug ][4] : '';
        if ( ! $package ) {
            $api = plugins_api( 'plugin_information', array( 'slug' => $slug, 'fields' => array( 'sections' => false ) ) );
            if ( is_wp_error( $api ) ) {
                return $api->get_error_message();
            }
            $package = $api->download_link;
        }
        $upgrader = new Plugin_Upgrader( new WP_Ajax_Upgrader_Skin() );
        $result   = $upgrader->install( $package );
        if ( is_wp_error( $result ) ) {
            return $result->get_error_message();
        }
        if ( ! $result ) {
            $errors = $upgrader->skin->get_errors();
            return $errors->has_errors() ? $errors->get_error_message() : __( 'Installeren lukte niet (controleer de schrijfrechten van wp-content/plugins).', 'balans' );
        }
        wp_clean_plugins_cache();
    }
    if ( ! is_plugin_active( $file ) ) {
        $activated = activate_plugin( $file, '', false, true );
        if ( is_wp_error( $activated ) ) {
            return $activated->get_error_message();
        }
    }
    return true;
}

/* ---------------------------------------------------------------
 * Alles instellen
 * ------------------------------------------------------------- */

function balans_setup_languages() {
    if ( ! function_exists( 'PLL' ) || empty( PLL()->model ) ) {
        return __( 'Polylang is niet actief.', 'balans' );
    }
    $model    = PLL()->model;
    $existing = pll_languages_list( array( 'fields' => 'slug' ) );
    $order    = 0;
    foreach ( array( 'nl' => array( 'nl_NL', 'nl' ), 'en' => array( 'en_GB', 'gb' ), 'fr' => array( 'fr_FR', 'fr' ) ) as $slug => $l ) {
        $order++;
        if ( in_array( $slug, $existing, true ) ) {
            continue;
        }
        $args = array( 'locale' => $l[0], 'slug' => $slug, 'flag' => $l[1], 'term_group' => $order );
        $res  = isset( $model->languages ) && is_object( $model->languages ) && method_exists( $model->languages, 'add' ) ? $model->languages->add( $args ) : $model->add_language( $args );
        if ( is_wp_error( $res ) ) {
            return $res->get_error_message();
        }
    }
    if ( method_exists( $model, 'clean_languages_cache' ) ) {
        $model->clean_languages_cache();
    }
    if ( method_exists( $model, 'set_language_in_mass' ) ) {
        $model->set_language_in_mass( 'nl' );
    }
    return true;
}

function balans_setup_pmpro() {
    if ( ! function_exists( 'pmpro_generatePages' ) ) {
        return __( 'Paid Memberships Pro is niet actief.', 'balans' );
    }
    pmpro_generatePages( array(
        'account'             => 'Mijn abonnement',
        'billing'             => 'Factuurgegevens',
        'cancel'              => 'Abonnement opzeggen',
        'checkout'            => 'Afrekenen',
        'confirmation'        => 'Bevestiging',
        'invoice'             => 'Factuur',
        'levels'              => 'Overzicht abonnementen',
        'login'               => 'Inloggen',
        'member_profile_edit' => 'Profiel bewerken',
    ) );
    update_option( 'pmpro_currency', 'EUR' );
    if ( balans_has_polylang() ) {
        foreach ( BALANS_PMPRO_PAGES as $name ) {
            $id = (int) get_option( 'pmpro_' . $name . '_page_id' );
            if ( $id && ! pll_get_post_language( $id ) ) {
                pll_set_post_language( $id, 'nl' );
            }
        }
        balans_pmpro_install_translations();
    }
    return true;
}

function balans_setup_woocommerce() {
    if ( ! class_exists( 'WC_Install' ) ) {
        return __( 'WooCommerce is niet actief.', 'balans' );
    }
    WC_Install::create_pages();
    update_option( 'woocommerce_currency', 'EUR' );
    update_option( 'woocommerce_currency_pos', 'left_space' );
    update_option( 'woocommerce_price_decimal_sep', ',' );
    update_option( 'woocommerce_price_thousand_sep', '.' );
    if ( ! get_option( 'woocommerce_default_country' ) || 'US:CA' === get_option( 'woocommerce_default_country' ) ) {
        update_option( 'woocommerce_default_country', 'NL' );
    }
    if ( balans_has_polylang() ) {
        foreach ( array( 'shop', 'cart', 'checkout', 'myaccount', 'terms' ) as $page ) {
            $id = (int) get_option( 'woocommerce_' . $page . '_page_id' );
            if ( $id && ! pll_get_post_language( $id ) ) {
                pll_set_post_language( $id, 'nl' );
            }
        }
    }
    return true;
}

function balans_setup_theme_content() {
    if ( function_exists( 'balans_install_pages' ) ) {
        balans_install_pages();
    }
    balans_install_extra_pages();
    $created = balans_install_translations();
    $langs   = balans_has_polylang() ? pll_languages_list( array( 'fields' => 'slug' ) ) : array();
    if ( in_array( 'en', $langs, true ) && in_array( 'fr', $langs, true ) ) {
        update_option( 'balans_translations_done', 1 );
    }
    balans_seed_diensten();
    return true;
}

function balans_setup_agenda() {
    if ( ! function_exists( 'ssa' ) || empty( ssa()->styles_settings ) ) {
        return __( 'Agenda-plugin is niet actief (optioneel).', 'balans' );
    }
    ssa()->styles_settings->update( array(
        'accent_color' => 'rgba(63, 127, 131, 1)',
        'background'   => 'rgba(245, 240, 232, 1)',
        'font'         => 'Helvetica Neue, Helvetica, Arial, sans-serif',
        'padding'      => '0px',
    ) );
    return true;
}

function balans_setup_all() {
    $steps = array(
        __( 'Talen NL / EN / FR', 'balans' )                    => 'balans_setup_languages',
        __( 'Pagina\'s, vertalingen en diensten', 'balans' )    => 'balans_setup_theme_content',
        __( 'Abonnementspagina\'s (PMPro)', 'balans' )          => 'balans_setup_pmpro',
        __( 'Webshop-pagina\'s (WooCommerce)', 'balans' )       => 'balans_setup_woocommerce',
        __( 'Agenda in huisstijl', 'balans' )                   => 'balans_setup_agenda',
    );
    $report = array();
    foreach ( $steps as $label => $fn ) {
        $report[ $label ] = call_user_func( $fn );
    }
    flush_rewrite_rules();
    update_option( 'balans_setup_done', 1 );
    return $report;
}

/* ---------------------------------------------------------------
 * Beheerpagina en melding
 * ------------------------------------------------------------- */

add_action( 'admin_menu', function () {
    add_submenu_page( 'balans', __( 'Installatie', 'balans' ), __( 'Installatie & plugins', 'balans' ), 'manage_options', 'balans-setup', 'balans_setup_page' );
}, 20 );

add_action( 'admin_notices', function () {
    if ( ! current_user_can( 'install_plugins' ) || ( isset( $_GET['page'] ) && 'balans-setup' === $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
        return;
    }
    $missing = balans_missing_required_plugins();
    if ( ! $missing && get_option( 'balans_setup_done' ) ) {
        return;
    }
    $text = $missing ? __( 'Het thema heeft een paar plugins nodig (meertaligheid, abonnementen, podcast en webshop). Installeer ze met één klik.', 'balans' ) : __( 'Alle plugins zijn actief. Maak nu met één klik de talen, pagina\'s en abonnementspagina\'s aan.', 'balans' );
    $btn  = $missing ? __( 'Benodigde plugins installeren', 'balans' ) : __( 'Alles instellen', 'balans' );
    echo '<div class="notice notice-warning"><p><strong>Balans / EQ5:</strong> ' . esc_html( $text ) . '</p><p><a data-testid="notice-setup" class="button button-primary" href="' . esc_url( admin_url( 'admin.php?page=balans-setup' ) ) . '">' . esc_html( $btn ) . '</a></p></div>';
} );

add_action( 'after_switch_theme', function () {
    delete_option( 'balans_setup_done' );
} );

add_action( 'admin_post_balans_plugins', function () {
    if ( ! current_user_can( 'install_plugins' ) || ! current_user_can( 'activate_plugins' ) ) {
        wp_die( esc_html__( 'Geen toegang.', 'balans' ) );
    }
    check_admin_referer( 'balans_plugins' );
    if ( function_exists( 'set_time_limit' ) ) {
        set_time_limit( 600 );
    }
    $slugs = isset( $_POST['plugin'] ) ? array( sanitize_key( wp_unslash( $_POST['plugin'] ) ) ) : balans_missing_required_plugins();
    if ( ! isset( $_POST['plugin'] ) && ! empty( $_POST['agenda'] ) ) {
        $slugs[] = 'simply-schedule-appointments';
    }
    $report = array();
    foreach ( $slugs as $slug ) {
        $report[ balans_plugins()[ $slug ][0] ?? $slug ] = balans_install_plugin( $slug );
    }
    set_transient( 'balans_setup_report', $report, 300 );
    wp_safe_redirect( admin_url( 'admin.php?page=balans-setup' ) );
    exit;
} );

add_action( 'admin_post_balans_setup_all', function () {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'Geen toegang.', 'balans' ) );
    }
    check_admin_referer( 'balans_setup_all' );
    set_transient( 'balans_setup_report', balans_setup_all(), 300 );
    wp_safe_redirect( admin_url( 'admin.php?page=balans-setup' ) );
    exit;
} );

function balans_setup_report() {
    $report = get_transient( 'balans_setup_report' );
    if ( ! $report ) {
        return;
    }
    delete_transient( 'balans_setup_report' );
    echo '<div class="notice notice-info" data-testid="setup-report"><ul>';
    foreach ( $report as $label => $res ) {
        printf( '<li>%1$s <strong>%2$s</strong>%3$s</li>', true === $res ? '&#10003;' : '&#10007;', esc_html( $label ), true === $res ? '' : ': ' . esc_html( $res ) );
    }
    echo '</ul></div>';
}

function balans_setup_page() {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    $labels = array( 'active' => __( 'Actief', 'balans' ), 'installed' => __( 'Geïnstalleerd, niet actief', 'balans' ), 'missing' => __( 'Niet geïnstalleerd', 'balans' ) );
    $missing = balans_missing_required_plugins();
    echo '<div class="wrap"><h1>' . esc_html__( 'Installatie & plugins', 'balans' ) . '</h1>';
    balans_setup_report();
    echo '<h2>' . esc_html__( 'Stap 1 — Plugins', 'balans' ) . '</h2><table class="widefat striped" style="max-width:900px"><thead><tr><th>Plugin</th><th>' . esc_html__( 'Waarvoor', 'balans' ) . '</th><th>Status</th><th></th></tr></thead><tbody>';
    foreach ( balans_plugins() as $slug => $p ) {
        $status = balans_plugin_status( $p[1] );
        echo '<tr data-testid="plugin-row-' . esc_attr( $slug ) . '"><td><strong>' . esc_html( $p[0] ) . '</strong><br><small>' . ( $p[2] ? esc_html__( 'Verplicht', 'balans' ) : esc_html__( 'Optioneel', 'balans' ) ) . '</small></td><td>' . esc_html( $p[3] ) . '</td><td>' . esc_html( $labels[ $status ] ) . '</td><td>';
        if ( 'active' !== $status ) {
            echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
            wp_nonce_field( 'balans_plugins' );
            echo '<input type="hidden" name="action" value="balans_plugins"><input type="hidden" name="plugin" value="' . esc_attr( $slug ) . '"><button data-testid="install-' . esc_attr( $slug ) . '" class="button">' . ( 'missing' === $status ? esc_html__( 'Installeren & activeren', 'balans' ) : esc_html__( 'Activeren', 'balans' ) ) . '</button></form>';
        }
        echo '</td></tr>';
    }
    echo '</tbody></table>';
    if ( $missing || 'active' !== balans_plugin_status( balans_plugins()['simply-schedule-appointments'][1] ) ) {
        echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="margin-top:16px">';
        wp_nonce_field( 'balans_plugins' );
        echo '<input type="hidden" name="action" value="balans_plugins">';
        echo '<p><label><input data-testid="install-agenda" type="checkbox" name="agenda" value="1" checked> ' . esc_html__( 'Ook de AVG-vriendelijke agenda (Simply Schedule Appointments) installeren', 'balans' ) . '</label></p>';
        echo '<p><button data-testid="install-all" class="button button-primary button-hero">' . esc_html__( 'Alle plugins installeren & activeren', 'balans' ) . '</button></p>';
        echo '<p class="description">' . esc_html__( 'Dit kan een minuut duren. De plugins worden van WordPress.org (PMPro van de officiële PMPro-site) gedownload en blijven daarna gewoon updaten.', 'balans' ) . '</p></form>';
    }
    echo '<h2>' . esc_html__( 'Stap 2 — Alles instellen', 'balans' ) . '</h2><p>' . esc_html__( 'Maakt de talen Nederlands, English en Français aan, alle pagina\'s met vertalingen, de diensten, de afreken- en accountpagina\'s van de abonnementen (in 3 talen) en de webshop-pagina\'s. Veilig om vaker te doen: bestaande pagina\'s blijven staan.', 'balans' ) . '</p>';
    echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
    wp_nonce_field( 'balans_setup_all' );
    echo '<input type="hidden" name="action" value="balans_setup_all"><button data-testid="setup-all" class="button button-primary button-hero"' . disabled( (bool) $missing, true, false ) . '>' . esc_html__( 'Alles instellen', 'balans' ) . '</button></form>';
    echo '<h2>' . esc_html__( 'Stap 3 — Zelf nog doen', 'balans' ) . '</h2><ol>';
    foreach ( array(
        __( 'Paid Memberships Pro → Instellingen → Betalingen: kies Stripe en koppel je Stripe-account.', 'balans' ),
        __( 'Maak per abonnement een PMPro-niveau (Lidmaatschappen → Niveaus) en koppel het bij Diensten → [abonnement].', 'balans' ),
        __( 'WooCommerce → Instellingen → Betalingen: zet Stripe of Mollie aan voor de webshop.', 'balans' ),
        __( 'Podcast → Instellingen: vul titel, omslagafbeelding en categorie van je podcast in.', 'balans' ),
        __( 'Agenda (indien geïnstalleerd): Appointments → Appointment Types, stel je beschikbaarheid en diensten in.', 'balans' ),
    ) as $todo ) {
        echo '<li>' . esc_html( $todo ) . '</li>';
    }
    echo '</ol></div>';
}
