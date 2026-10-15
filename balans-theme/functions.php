<?php
/**
 * Balans theme functions
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'BALANS_VERSION', '1.4.0' );
define( 'BALANS_DIR', get_template_directory() );
define( 'BALANS_URI', get_template_directory_uri() );

require_once BALANS_DIR . '/inc/theme-setup.php';
require_once BALANS_DIR . '/inc/enqueue.php';
require_once BALANS_DIR . '/inc/cpt.php';
require_once BALANS_DIR . '/inc/form-handler.php';
require_once BALANS_DIR . '/inc/appointments.php';
require_once BALANS_DIR . '/inc/woocommerce.php';
require_once BALANS_DIR . '/inc/template-helpers.php';
require_once BALANS_DIR . '/inc/placeholder-content.php';
require_once BALANS_DIR . '/inc/install-pages.php';
require_once BALANS_DIR . '/inc/i18n.php';
require_once BALANS_DIR . '/inc/podcast.php';
