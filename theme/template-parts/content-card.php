<?php
/**
 * Post or Page card used by list views.
 *
 * @package DocsPress
 */

$show_featured_image = get_theme_mod( 'docspress_show_featured_images', true ) && has_post_thumbnail();
?>
<article <?php post_class( 'result-card content-card' ); ?>>
	<?php if ( $show_featured_image ) : ?>
		<a class="content-card-thumbnail" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); ?>
		</a>
	<?php endif; ?>
	<div class="content-card-body">
		<?php if ( 'post' === get_post_type() && get_theme_mod( 'docspress_show_post_categories', true ) ) : ?>
			<div class="content-card-taxonomy"><?php the_category( ' · ' ); ?></div>
		<?php endif; ?>
		<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php if ( 'post' === get_post_type() ) : ?>
			<?php docspress_post_meta( get_the_ID(), true ); ?>
		<?php endif; ?>
		<div class="content-card-excerpt"><?php the_excerpt(); ?></div>
		<a class="content-card-link" href="<?php the_permalink(); ?>">
			<?php esc_html_e( 'Read more', 'docspress' ); ?>
			<span aria-hidden="true">→</span>
		</a>
	</div>
</article>
