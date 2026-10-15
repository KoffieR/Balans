<?php
/**
 * Template Name: Afspraak voor hulp
 *
 * @package Balans
 */

get_header();
$s = balans_services();
?>
<main id="main" class="site-main" data-testid="page-hulp">
    <section class="page-hero">
        <div class="container">
            <p class="eyebrow"><?php echo esc_html( $s['hulp_eyebrow'] ); ?></p>
            <h1><?php echo esc_html( $s['hulp_title'] ); ?></h1>
            <?php foreach ( $s['hulp_intro'] as $paragraph ) : ?>
                <p class="page-hero__intro"><?php echo esc_html( $paragraph ); ?></p>
            <?php endforeach; ?>
            <nav class="service-jump" aria-label="<?php echo esc_attr( $s['hulp_eyebrow'] ); ?>">
                <?php foreach ( $s['hulp'] as $key => $item ) : ?>
                    <a href="#<?php echo esc_attr( $key ); ?>" data-testid="jump-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $item['title'] ); ?></a>
                <?php endforeach; ?>
            </nav>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="service-list">
                <?php foreach ( $s['hulp'] as $key => $item ) : ?>
                    <?php
                    $is_custom = 'maatwerk' === $key;
                    balans_service_block(
                        $key,
                        $item,
                        $is_custom ? balans_url( 'contact' ) : balans_url( 'afspraak-maken', array( 'dienst' => $key ) ),
                        $is_custom ? $s['hulp_contact'] : $s['hulp_button']
                    );
                    ?>
                <?php endforeach; ?>
            </div>
            <?php balans_price_note(); ?>
        </div>
    </section>
</main>
<?php get_footer();
