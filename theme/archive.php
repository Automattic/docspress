<?php
/**
 * Archive template.
 *
 * @package DocsPress
 */

get_header();
?>
<main class="site-content-shell" id="main-content">
	<header class="entry-header archive-heading">
		<span class="entry-kicker"><?php esc_html_e( 'Archive', 'docspress' ); ?></span>
		<?php the_archive_title( '<h1 class="entry-title">', '</h1>' ); ?>
		<?php the_archive_description( '<div class="entry-summary">', '</div>' ); ?>
	</header>
	<?php if ( have_posts() ) : ?>
		<div class="result-list">
			<?php
			while ( have_posts() ) {
				the_post();
				get_template_part( 'template-parts/content', 'card' );
			}
			?>
		</div>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<div class="empty-state"><strong><?php esc_html_e( 'Nothing here yet.', 'docspress' ); ?></strong></div>
	<?php endif; ?>
</main>
<?php
get_footer();
