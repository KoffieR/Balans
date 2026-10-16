<?php
/**
 * Template Name: Artikelen & bronnen
 *
 * Toont twee groepen:
 *  1) Artikelen & bronnen — WordPress posts (categorie "artikelen" of alle posts).
 *  2) Eigen blogs — CPT 'balans_blog'.
 *
 * @package Balans
 */

get_header();

$articles = new WP_Query( array(
    'post_type'      => 'post',
    'posts_per_page' => 6,
) );
$blogs = new WP_Query( array(
    'post_type'      => 'balans_blog',
    'posts_per_page' => 6,
) );
?>
<main id="main" class="site-main" data-testid="page-artikelen">
    <?php balans_page_hero( 'artikelen' ); ?>

    <section class="section">
        <div class="container">
            <h2><?php esc_html_e( 'Artikelen & bronnen', 'balans' ); ?></h2>
            <?php if ( $articles->have_posts() ) : ?>
                <div class="articles-grid">
                    <?php while ( $articles->have_posts() ) : $articles->the_post();
                        balans_card( array(
                            'eyebrow' => get_the_date(),
                            'title'   => get_the_title(),
                            'text'    => wp_trim_words( get_the_excerpt(), 22 ),
                            'link'    => get_permalink(),
                            'image'   => get_the_post_thumbnail_url( get_the_ID(), 'balans-card' ),
                            'cta'     => __( 'Lees artikel', 'balans' ),
                        ) );
                    endwhile; wp_reset_postdata(); ?>
                </div>
            <?php else : ?>
                <?php balans_placeholder_notice( __( 'Nog geen artikelen gepubliceerd. Voeg artikelen toe via "Berichten" in het WordPress-menu.', 'balans' ) ); ?>
            <?php endif; ?>
        </div>
    </section>

    <section class="section section--alt">
        <div class="container">
            <h2><?php esc_html_e( 'Eigen blogs', 'balans' ); ?></h2>
            <?php if ( $blogs->have_posts() ) : ?>
                <div class="articles-grid">
                    <?php while ( $blogs->have_posts() ) : $blogs->the_post();
                        balans_card( array(
                            'eyebrow' => get_the_date(),
                            'title'   => get_the_title(),
                            'text'    => wp_trim_words( get_the_excerpt(), 22 ),
                            'link'    => get_permalink(),
                            'image'   => get_the_post_thumbnail_url( get_the_ID(), 'balans-card' ),
                            'cta'     => __( 'Lees blog', 'balans' ),
                        ) );
                    endwhile; wp_reset_postdata(); ?>
                </div>
            <?php else : ?>
                <?php balans_placeholder_notice( __( 'Nog geen eigen blogs. Voeg ze toe via "Eigen blogs" in het WordPress-menu.', 'balans' ) ); ?>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php get_footer();
