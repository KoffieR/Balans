<?php
/**
 * Archive template (for CPTs and categories).
 *
 * @package Balans
 */

get_header();
?>
<main id="main" class="site-main">
    <section class="page-hero">
        <div class="container">
            <h1><?php the_archive_title(); ?></h1>
            <?php the_archive_description( '<p class="page-hero__intro">', '</p>' ); ?>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <?php if ( have_posts() ) : ?>
                <div class="articles-grid">
                    <?php while ( have_posts() ) : the_post();
                        balans_card( array(
                            'eyebrow' => get_the_date(),
                            'title'   => get_the_title(),
                            'text'    => wp_trim_words( get_the_excerpt(), 24 ),
                            'link'    => get_permalink(),
                            'image'   => get_the_post_thumbnail_url( get_the_ID(), 'balans-card' ),
                            'cta'     => __( 'Lees meer', 'balans' ),
                        ) );
                    endwhile; ?>
                </div>
                <?php the_posts_pagination(); ?>
            <?php else : ?>
                <p><?php esc_html_e( 'Nog geen inhoud.', 'balans' ); ?></p>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php get_footer();
