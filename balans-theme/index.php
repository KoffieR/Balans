<?php
/**
 * Fallback template.
 *
 * @package Balans
 */

get_header();
?>
<main id="main" class="site-main">
    <section class="section">
        <div class="container">
            <?php if ( have_posts() ) : ?>
                <h1><?php single_post_title(); ?></h1>
                <div class="articles-grid">
                    <?php while ( have_posts() ) : the_post(); ?>
                        <article <?php post_class( 'balans-card' ); ?>>
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="balans-card__image" style="background-image:url('<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'balans-card' ) ); ?>');"></div>
                            <?php else : ?>
                                <div class="balans-card__image balans-card__image--placeholder" aria-hidden="true"></div>
                            <?php endif; ?>
                            <div class="balans-card__body">
                                <p class="eyebrow"><?php echo esc_html( get_the_date() ); ?></p>
                                <h3 class="balans-card__title"><a href="<?php the_permalink(); ?>" style="text-decoration:none;color:inherit;"><?php the_title(); ?></a></h3>
                                <p class="balans-card__text"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
                                <a class="balans-card__link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Lees meer', 'balans' ); ?><span aria-hidden="true">→</span></a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
                <?php the_posts_pagination(); ?>
            <?php else : ?>
                <h1><?php esc_html_e( 'Nog geen inhoud', 'balans' ); ?></h1>
                <p><?php esc_html_e( 'Er is nog geen inhoud op deze locatie.', 'balans' ); ?></p>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php get_footer();
