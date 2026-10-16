<?php
/**
 * Template Name: Over
 *
 * @package Balans
 */

get_header();
?>
<main id="main" class="site-main" data-testid="page-over">
    <?php balans_page_hero( 'over' ); ?>

    <section class="section">
        <div class="container">
            <div class="entry-content entry-content--wide" data-testid="over-content">
                <?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?>
            </div>
        </div>
    </section>
</main>
<?php get_footer();
