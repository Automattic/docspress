<?php
/**
 * Posts page template.
 *
 * @package DocsPress
 */

get_header();
$posts_page_id = absint( get_option( 'page_for_posts' ) );
?>
<main class="site-content-shell" id="main-content">
	<header class="entry-header archive-heading">
		<span class="entry-kicker"><?php esc_html_e( 'Updates', 'docspress' ); ?></span>
		<h1 class="entry-title"><?php echo esc_html( $posts_page_id ? get_the_title( $posts_page_id ) : __( 'Latest posts', 'docspress' ) ); ?></h1>
		<?php if ( get_bloginfo( 'description' ) ) : ?>
			<p class="entry-summary"><?php bloginfo( 'description' ); ?></p>
		<?php endif; ?>
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
		<div class="empty-state"><strong><?php esc_html_e( 'No posts yet.', 'docspress' ); ?></strong><p><?php esc_html_e( 'Publish your first update to get started.', 'docspress' ); ?></p></div>
	<?php endif; ?>
</main>
<?php
get_footer();
