<?php
/**
 * Single post template.
 *
 * @package Balans
 */

get_header();
?>
<main id="main" class="site-main">
    <?php while ( have_posts() ) : the_post(); ?>
        <section class="page-hero">
            <div class="container container--text">
                <p class="eyebrow">
                    <?php
                    $type = get_post_type();
                    if ( 'balans_blog' === $type ) {
                        esc_html_e( 'Eigen blog', 'balans' );
                    } elseif ( 'balans_meditatie' === $type ) {
                        esc_html_e( 'Meditatie', 'balans' );
                    } elseif ( 'balans_training' === $type ) {
                        esc_html_e( 'Training', 'balans' );
                    } elseif ( 'balans_zakelijk' === $type ) {
                        esc_html_e( 'Zakelijke training', 'balans' );
                    } else {
                        esc_html_e( 'Artikel', 'balans' );
                    }
                    ?>
                    &middot; <?php echo esc_html( get_the_date() ); ?>
                </p>
                <h1><?php the_title(); ?></h1>
                <?php if ( has_excerpt() ) : ?>
                    <p class="page-hero__intro"><?php echo esc_html( get_the_excerpt() ); ?></p>
                <?php endif; ?>
            </div>
        </section>
        <section class="section">
            <div class="container container--text entry-content">
                <?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'balans-hero', array( 'style' => 'border-radius:10px; margin-bottom:30px;' ) ); endif; ?>
                <?php the_content(); ?>
            </div>
        </section>
    <?php endwhile; ?>
</main>
<?php get_footer();
