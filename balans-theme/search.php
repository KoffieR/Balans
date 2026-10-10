<?php
/**
 * Search results template.
 *
 * @package Balans
 */

get_header();
?>
<main id="main" class="site-main">
    <section class="page-hero">
        <div class="container">
            <h1><?php printf( esc_html__( 'Zoekresultaten voor: %s', 'balans' ), '<em>' . esc_html( get_search_query() ) . '</em>' ); ?></h1>
        </div>
    </section>
    <section class="section">
        <div class="container">
            <?php if ( have_posts() ) : ?>
                <div class="articles-grid">
                    <?php while ( have_posts() ) : the_post();
                        balans_card( array(
                            'eyebrow' => get_post_type(),
                            'title'   => get_the_title(),
                            'text'    => wp_trim_words( get_the_excerpt(), 24 ),
                            'link'    => get_permalink(),
                            'image'   => get_the_post_thumbnail_url( get_the_ID(), 'balans-card' ),
                            'cta'     => __( 'Bekijk', 'balans' ),
                        ) );
                    endwhile; ?>
                </div>
                <?php the_posts_pagination(); ?>
            <?php else : ?>
                <p><?php esc_html_e( 'Geen resultaten gevonden.', 'balans' ); ?></p>
                <?php get_search_form(); ?>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php get_footer();
