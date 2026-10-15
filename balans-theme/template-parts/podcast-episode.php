<?php
/**
 * Eén podcastaflevering in de lijst.
 *
 * @package Balans
 */

$episode_id = get_the_ID();
$duration   = get_post_meta( $episode_id, 'duration', true );
$members    = balans_episode_members_only( $episode_id );
$featured   = ! empty( $args['featured'] );
?>
<article class="episode<?php echo $featured ? ' episode--featured' : ''; ?>" data-testid="episode-<?php echo esc_attr( $episode_id ); ?>">
    <div class="episode__meta">
        <span class="episode-badge <?php echo $members ? 'episode-badge--members' : 'episode-badge--free'; ?>">
            <?php echo $members ? esc_html__( 'Abonnees', 'balans' ) : esc_html__( 'Gratis', 'balans' ); ?>
        </span>
        <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
        <?php if ( $duration ) : ?><span><?php echo esc_html( $duration ); ?></span><?php endif; ?>
    </div>
    <h2 class="episode__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <?php if ( has_excerpt() || $featured ) : ?>
        <p class="episode__text"><?php echo esc_html( get_the_excerpt() ); ?></p>
    <?php endif; ?>
    <?php balans_episode_player( $episode_id ); ?>
</article>
