<?php
/**
 * Singular post template.
 *
 * @package DocsPress
 */

get_header();
?>
<main class="site-content-shell singular-shell" id="main-content">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<article <?php post_class( 'docs-article post-article' ); ?>>
			<header class="entry-header">
				<?php if ( get_theme_mod( 'docspress_show_post_categories', true ) ) : ?>
					<div class="entry-taxonomy"><?php the_category( ' · ' ); ?></div>
				<?php endif; ?>
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<?php docspress_post_meta(); ?>
			</header>

			<?php if ( get_theme_mod( 'docspress_show_featured_images', true ) && has_post_thumbnail() ) : ?>
				<figure class="entry-featured-image"><?php the_post_thumbnail( 'full' ); ?></figure>
			<?php endif; ?>

			<div class="entry-content">
				<?php the_content(); ?>
				<?php wp_link_pages(); ?>
			</div>

			<?php if ( get_theme_mod( 'docspress_show_post_tags', true ) && has_tag() ) : ?>
				<footer class="entry-footer">
					<span><?php esc_html_e( 'Topics', 'docspress' ); ?></span>
					<?php the_tags( '<div class="tag-list">', '', '</div>' ); ?>
				</footer>
			<?php endif; ?>

			<?php
			the_post_navigation(
				array(
					'prev_text' => '<span>' . esc_html__( '← Previous post', 'docspress' ) . '</span><strong>%title</strong>',
					'next_text' => '<span>' . esc_html__( 'Next post →', 'docspress' ) . '</span><strong>%title</strong>',
				)
			);
			?>

			<?php if ( docspress_should_show_comments() ) : ?>
				<?php comments_template(); ?>
			<?php endif; ?>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();
