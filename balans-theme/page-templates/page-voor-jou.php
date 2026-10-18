<?php
/**
 * Template Name: Voor jou (overzicht)
 *
 * @package Balans
 */

get_header();
?>
<main id="main" class="site-main" data-testid="page-voor-jou">
    <?php balans_page_hero( 'voor-jou' ); ?>

    <section class="section">
        <div class="container">
            <div class="voor-jou-grid">
                <?php foreach ( balans_voor_jou_services() as $i => $svc ) :
                    $svc = balans_route_upcoming( $svc );
                    balans_card( array(
                        'image'   => (string) wp_get_attachment_image_url( (int) get_theme_mod( 'eq5_vj_image_' . ( $i + 1 ), 0 ), 'large' ),
                        'badge'   => isset( $svc['badge'] ) ? $svc['badge'] : '',
                        'eyebrow' => $svc['eyebrow'],
                        'title'   => $svc['title'],
                        'text'    => $svc['text'],
                        'link'    => $svc['link'],
                        'cta'     => __( 'Meer informatie', 'balans' ),
                    ) );
                endforeach; ?>
            </div>
        </div>
    </section>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); if ( get_the_content() ) : ?>
        <section class="section section--alt">
            <div class="container container--text entry-content">
                <?php the_content(); ?>
            </div>
        </section>
    <?php endif; endwhile; endif; ?>
</main>
<?php get_footer();
