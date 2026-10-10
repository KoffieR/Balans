<?php
/**
 * Default page template.
 *
 * @package Balans
 */

get_header();
?>
<main id="main" class="site-main">
    <?php while ( have_posts() ) : the_post(); ?>
        <section class="page-hero">
            <div class="container">
                <h1><?php the_title(); ?></h1>
                <?php if ( has_excerpt() ) : ?>
                    <p class="page-hero__intro"><?php echo esc_html( get_the_excerpt() ); ?></p>
                <?php endif; ?>
            </div>
        </section>
        <section class="section">
            <div class="container container--text entry-content">
                <?php the_content(); ?>
            </div>
        </section>
    <?php endwhile; ?>
</main>
<?php get_footer();
