<?php
/**
 * Template Name: Podcast
 *
 * @package Balans
 */

get_header();

$paged    = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$episodes = balans_has_podcast_plugin() ? new WP_Query( array(
    'post_type'      => 'podcast',
    'post_status'    => 'publish',
    'posts_per_page' => 10,
    'paged'          => $paged,
) ) : null;
?>

<main id="main" class="site-main" data-testid="podcast-page">

    <?php
    ob_start();
    get_template_part( 'template-parts/podcast-subscribe' );
    balans_page_hero( 'podcast', ob_get_clean(), 'podcast-hero' );
    ?>

    <section class="section">
        <div class="container">
            <?php if ( ! $episodes ) : ?>
                <?php balans_placeholder_notice( __( 'Installeer en activeer de plugin Seriously Simple Podcasting om afleveringen te tonen.', 'balans' ) ); ?>
            <?php elseif ( ! $episodes->have_posts() ) : ?>
                <?php balans_placeholder_notice( __( 'Nog geen afleveringen. Voeg ze toe via "Podcast" in het WordPress-menu.', 'balans' ) ); ?>
            <?php else : ?>
                <div class="episode-list" data-testid="episode-list">
                    <?php $i = 0; while ( $episodes->have_posts() ) : $episodes->the_post(); $i++; ?>
                        <?php get_template_part( 'template-parts/podcast-episode', null, array( 'featured' => 1 === $i && 1 === $paged ) ); ?>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
                <nav class="pagination" aria-label="<?php esc_attr_e( 'Meer afleveringen', 'balans' ); ?>">
                    <?php echo paginate_links( array( 'total' => $episodes->max_num_pages, 'current' => $paged ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                </nav>
            <?php endif; ?>
        </div>
    </section>

    <?php get_template_part( 'template-parts/podcast-membership' ); ?>

</main>

<?php
get_footer();
