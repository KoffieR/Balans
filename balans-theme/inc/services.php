<?php
/**
 * Diensten (Abonnementen, Afspraak voor hulp): bewerkbaar via het menu "Diensten".
 * Paginakoppen staan in inc/content/services-{taal}.php; de diensten zelf zijn berichten (balans_dienst).
 *
 * @package Balans
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const BALANS_DIENSTEN_SEED = 1;

function balans_services( $lang = '' ) {
    static $cache = array();
    $lang = $lang ? $lang : balans_lang();
    if ( ! isset( $cache[ $lang ] ) ) {
        $cache[ $lang ] = require BALANS_DIR . '/inc/content/services-' . $lang . '.php';
    }
    return $cache[ $lang ];
}

function balans_register_dienst_cpt() {
    register_post_type( 'balans_dienst', array(
        'labels'       => array(
            'name'          => __( 'Diensten', 'balans' ),
            'singular_name' => __( 'Dienst', 'balans' ),
            'add_new_item'  => __( 'Nieuwe dienst', 'balans' ),
            'edit_item'     => __( 'Dienst bewerken', 'balans' ),
        ),
        'public'       => false,
        'show_ui'      => true,
        'show_in_rest' => true,
        'menu_icon'    => 'dashicons-heart',
        'supports'     => array( 'title', 'editor', 'page-attributes' ),
    ) );
}
add_action( 'init', 'balans_register_dienst_cpt' );

add_filter( 'pll_get_post_types', function ( $types ) {
    $types['balans_dienst'] = 'balans_dienst';
    return $types;
} );

function balans_dienst_fields() {
    return array(
        '_dienst_group' => __( 'Groep', 'balans' ),
        '_dienst_when'  => __( 'Tijdsaanduiding (bijv. "Iedere zondag")', 'balans' ),
        '_dienst_price' => __( 'Prijs (bijv. "€ 45 per maand")', 'balans' ),
        '_dienst_key'   => __( 'Code (voor het afspraakformulier en de link #code)', 'balans' ),
        '_dienst_level' => __( 'Paid Memberships Pro-niveau (abonnementen)', 'balans' ),
        '_dienst_cta'   => __( 'Knop', 'balans' ),
    );
}

function balans_dienst_meta_box() {
    add_meta_box( 'balans-dienst', __( 'Gegevens van de dienst', 'balans' ), 'balans_dienst_meta_box_html', 'balans_dienst', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'balans_dienst_meta_box' );

function balans_dienst_meta_box_html( $post ) {
    wp_nonce_field( 'balans_dienst', 'balans_dienst_nonce' );
    $f = balans_dienst_fields();
    $v = function ( $key ) use ( $post ) {
        return get_post_meta( $post->ID, $key, true );
    };
    echo '<table class="form-table"><tbody>';
    printf( '<tr><th>%s</th><td><select name="_dienst_group"><option value="abonnement" %s>%s</option><option value="hulp" %s>%s</option></select></td></tr>',
        esc_html( $f['_dienst_group'] ), selected( $v( '_dienst_group' ), 'abonnement', false ), esc_html__( 'Abonnementen', 'balans' ),
        selected( $v( '_dienst_group' ), 'hulp', false ), esc_html__( 'Afspraak voor hulp', 'balans' ) );
    foreach ( array( '_dienst_when', '_dienst_price', '_dienst_key' ) as $key ) {
        printf( '<tr><th><label for="%1$s">%2$s</label></th><td><input type="text" class="regular-text" id="%1$s" name="%1$s" value="%3$s" /></td></tr>', esc_attr( $key ), esc_html( $f[ $key ] ), esc_attr( $v( $key ) ) );
    }
    echo '<tr><th>' . esc_html( $f['_dienst_level'] ) . '</th><td>';
    if ( function_exists( 'pmpro_getAllLevels' ) ) {
        echo '<select name="_dienst_level"><option value="">' . esc_html__( '— geen (knop gaat naar contact) —', 'balans' ) . '</option>';
        foreach ( pmpro_getAllLevels( true ) as $level ) {
            printf( '<option value="%1$d" %2$s>%3$s</option>', (int) $level->id, selected( (int) $v( '_dienst_level' ), (int) $level->id, false ), esc_html( $level->name ) );
        }
        echo '</select><p class="description">' . esc_html__( 'Kies het niveau dat bij dit abonnement hoort. De knop wordt dan "Abonneer" en gaat direct naar afrekenen.', 'balans' ) . '</p>';
    } else {
        echo '<em>' . esc_html__( 'Activeer Paid Memberships Pro om abonnementen online te verkopen.', 'balans' ) . '</em>';
    }
    echo '</td></tr>';
    printf( '<tr><th>%s</th><td><select name="_dienst_cta"><option value="afspraak" %s>%s</option><option value="contact" %s>%s</option></select><p class="description">%s</p></td></tr>',
        esc_html( $f['_dienst_cta'] ), selected( $v( '_dienst_cta' ), 'afspraak', false ), esc_html__( 'Afspraak maken (staat ook in het afspraakformulier)', 'balans' ),
        selected( $v( '_dienst_cta' ), 'contact', false ), esc_html__( 'Contact opnemen', 'balans' ), esc_html__( 'Geldt voor "Afspraak voor hulp". Bij abonnementen met een niveau is de knop altijd "Abonneer".', 'balans' ) );
    echo '</tbody></table>';
}

function balans_save_dienst( $post_id ) {
    if ( ! isset( $_POST['balans_dienst_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['balans_dienst_nonce'] ), 'balans_dienst' ) ) {
        return;
    }
    if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    foreach ( array_keys( balans_dienst_fields() ) as $key ) {
        $value = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
        if ( '_dienst_key' === $key ) {
            $value = sanitize_title( $value ? $value : get_post_field( 'post_name', $post_id ) );
        }
        update_post_meta( $post_id, $key, $value );
    }
}
add_action( 'save_post_balans_dienst', 'balans_save_dienst' );

/**
 * Diensten van een groep in de huidige taal; valt terug op de themateksten zolang er nog geen berichten zijn.
 */
function balans_get_diensten( $group ) {
    $posts = get_posts( array(
        'post_type'      => 'balans_dienst',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => array( 'menu_order' => 'ASC', 'ID' => 'ASC' ),
        'meta_key'       => '_dienst_group', // phpcs:ignore WordPress.DB.SlowDBQuery
        'meta_value'     => $group, // phpcs:ignore WordPress.DB.SlowDBQuery
    ) );
    $items = array();
    foreach ( $posts as $post ) {
        $key           = get_post_meta( $post->ID, '_dienst_key', true );
        $items[ $key ? $key : $post->post_name ] = array(
            'title' => get_the_title( $post ),
            'when'  => get_post_meta( $post->ID, '_dienst_when', true ),
            'html'  => apply_filters( 'the_content', $post->post_content ),
            'price' => get_post_meta( $post->ID, '_dienst_price', true ),
            'level' => (int) get_post_meta( $post->ID, '_dienst_level', true ),
            'cta'   => get_post_meta( $post->ID, '_dienst_cta', true ),
        );
    }
    if ( $items ) {
        return $items;
    }
    $s = balans_services();
    foreach ( $s[ 'abonnement' === $group ? 'abonnementen' : 'hulp' ] as $key => $item ) {
        $items[ $key ] = array(
            'title' => $item['title'],
            'when'  => $item['when'],
            'html'  => wpautop( esc_html( implode( "\n\n", $item['text'] ) ) ),
            'price' => '',
            'level' => 0,
            'cta'   => 'maatwerk' === $key ? 'contact' : 'afspraak',
        );
    }
    return $items;
}

/**
 * Diensten voor het afspraakformulier (code => titel).
 */
function balans_appointment_services() {
    $list = array();
    foreach ( balans_get_diensten( 'hulp' ) as $key => $item ) {
        if ( 'contact' !== $item['cta'] ) {
            $list[ $key ] = $item['title'];
        }
    }
    return $list;
}

/**
 * PMPro-niveaus die aan een energetisch abonnement gekoppeld zijn (tellen niet als EQ5-podcastabonnement).
 */
function balans_dienst_level_ids() {
    global $wpdb;
    return array_map( 'intval', $wpdb->get_col( "SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_dienst_level' AND meta_value <> ''" ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
}

function balans_user_has_level( $level_id ) {
    return $level_id && is_user_logged_in() && function_exists( 'pmpro_hasMembershipLevel' ) && pmpro_hasMembershipLevel( $level_id );
}

function balans_service_button( $key, $item, $group ) {
    $s = balans_services();
    if ( 'abonnement' === $group && $item['level'] && function_exists( 'pmpro_url' ) ) {
        if ( balans_user_has_level( $item['level'] ) ) {
            balans_button( pmpro_url( 'account' ), $s['abo_active'], 'secondary', 'service-cta-' . $key );
            return;
        }
        balans_button( add_query_arg( 'pmpro_level', $item['level'], pmpro_url( 'checkout' ) ), $s['abo_subscribe'], 'primary', 'service-cta-' . $key );
        return;
    }
    if ( 'abonnement' === $group ) {
        balans_button( balans_url( 'contact' ), $s['abo_button'], 'primary', 'service-cta-' . $key );
    } elseif ( 'contact' === $item['cta'] ) {
        balans_button( balans_url( 'contact' ), $s['hulp_contact'], 'primary', 'service-cta-' . $key );
    } else {
        balans_button( balans_url( 'afspraak-maken', array( 'dienst' => $key ) ), $s['hulp_button'], 'primary', 'service-cta-' . $key );
    }
}

function balans_service_block( $key, $item, $group ) {
    $s = balans_services();
    ?>
    <article class="service-block" id="<?php echo esc_attr( $key ); ?>" data-testid="service-<?php echo esc_attr( $key ); ?>">
        <header class="service-block__head">
            <h2 class="service-block__title"><?php echo esc_html( $item['title'] ); ?></h2>
            <?php if ( $item['when'] ) : ?><p class="service-block__when"><?php echo esc_html( $item['when'] ); ?></p><?php endif; ?>
        </header>
        <div class="service-block__body"><?php echo wp_kses_post( $item['html'] ); ?></div>
        <footer class="service-block__foot">
            <p class="service-block__price" data-testid="price-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $s['price_label'] . ': ' . ( $item['price'] ? $item['price'] : $s['price_value'] ) ); ?></p>
            <?php balans_service_button( $key, $item, $group ); ?>
        </footer>
    </article>
    <?php
}

function balans_price_note() {
    $custom = trim( (string) get_theme_mod( 'eq5_price_note_' . balans_lang(), '' ) );
    $paras  = $custom ? preg_split( "/\R{2,}/", $custom ) : balans_services()['price_note'];
    ?>
    <aside class="price-note" data-testid="price-note">
        <?php echo balans_svg( 'q-accent', 'price-note__q' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
        <div>
            <?php foreach ( $paras as $paragraph ) : ?>
                <p><?php echo esc_html( trim( $paragraph ) ); ?></p>
            <?php endforeach; ?>
        </div>
    </aside>
    <?php
}

function balans_price_note_customizer( $wp_customize ) {
    $wp_customize->add_section( 'eq5_prices', array(
        'title'       => __( 'EQ5 Prijsnotitie', 'balans' ),
        'description' => __( 'De notitie onder de diensten, per taal. Leeg = standaardtekst. Witregel = nieuwe alinea.', 'balans' ),
        'priority'    => 161,
    ) );
    foreach ( array( 'nl' => 'NL', 'en' => 'EN', 'fr' => 'FR' ) as $lang => $label ) {
        $id = 'eq5_price_note_' . $lang;
        $wp_customize->add_setting( $id, array( 'default' => '', 'sanitize_callback' => 'sanitize_textarea_field' ) );
        $wp_customize->add_control( $id, array( 'label' => __( 'Prijsnotitie', 'balans' ) . ' (' . $label . ')', 'section' => 'eq5_prices', 'type' => 'textarea' ) );
    }
}
add_action( 'customize_register', 'balans_price_note_customizer' );

/**
 * Huidige teksten eenmalig als Diensten-berichten aanmaken (NL, en EN/FR gekoppeld als Polylang actief is).
 */
function balans_seed_diensten() {
    if ( (int) get_option( 'balans_diensten_seed' ) >= BALANS_DIENSTEN_SEED || ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $langs = balans_has_polylang() ? pll_languages_list( array( 'fields' => 'slug' ) ) : array();
    if ( balans_has_polylang() && ! ( in_array( 'nl', $langs, true ) && in_array( 'en', $langs, true ) && in_array( 'fr', $langs, true ) ) ) {
        return;
    }
    $todo  = balans_has_polylang() ? array( 'nl', 'en', 'fr' ) : array( 'nl' );
    $ids   = array();
    foreach ( $todo as $lang ) {
        $data  = require BALANS_DIR . '/inc/content/services-' . $lang . '.php';
        $order = 0;
        foreach ( array( 'abonnement' => 'abonnementen', 'hulp' => 'hulp' ) as $group => $list ) {
            foreach ( $data[ $list ] as $key => $item ) {
                $order += 10;
                $id = wp_insert_post( array(
                    'post_type'    => 'balans_dienst',
                    'post_status'  => 'publish',
                    'post_title'   => $item['title'],
                    'post_name'    => $key . ( 'nl' === $lang ? '' : '-' . $lang ),
                    'post_content' => implode( "\n\n", array_map( function ( $p ) {
                        return '<!-- wp:paragraph --><p>' . esc_html( $p ) . '</p><!-- /wp:paragraph -->';
                    }, $item['text'] ) ),
                    'menu_order'   => $order,
                ) );
                if ( ! $id || is_wp_error( $id ) ) {
                    continue;
                }
                update_post_meta( $id, '_dienst_group', $group );
                update_post_meta( $id, '_dienst_when', $item['when'] );
                update_post_meta( $id, '_dienst_price', '' );
                update_post_meta( $id, '_dienst_key', $key );
                update_post_meta( $id, '_dienst_cta', 'maatwerk' === $key ? 'contact' : 'afspraak' );
                if ( balans_has_polylang() ) {
                    pll_set_post_language( $id, $lang );
                }
                $ids[ $key ][ $lang ] = $id;
            }
        }
    }
    if ( balans_has_polylang() ) {
        foreach ( $ids as $translations ) {
            pll_save_post_translations( $translations );
        }
    }
    update_option( 'balans_diensten_seed', BALANS_DIENSTEN_SEED );
}
add_action( 'admin_init', 'balans_seed_diensten' );

/**
 * Prijs en niveau bij het opslaan doorzetten naar de vertalingen, zodat je die maar één keer hoeft in te vullen.
 */
function balans_sync_dienst_translations( $post_id ) {
    if ( ! balans_has_polylang() || ! isset( $_POST['balans_dienst_nonce'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
        return;
    }
    foreach ( pll_get_post_translations( $post_id ) as $id ) {
        if ( (int) $id === (int) $post_id ) {
            continue;
        }
        foreach ( array( '_dienst_group', '_dienst_key', '_dienst_level', '_dienst_cta' ) as $key ) {
            update_post_meta( $id, $key, get_post_meta( $post_id, $key, true ) );
        }
        if ( ! get_post_meta( $id, '_dienst_price', true ) ) {
            update_post_meta( $id, '_dienst_price', get_post_meta( $post_id, '_dienst_price', true ) );
        }
    }
}
add_action( 'save_post_balans_dienst', 'balans_sync_dienst_translations', 20 );
