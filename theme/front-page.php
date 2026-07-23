<?php
/**
 * Customizable site homepage.
 *
 * @package DocsPress
 */

if ( 'documentation' === get_theme_mod( 'docspress_homepage_layout', 'landing' ) && 'page' === get_option( 'show_on_front' ) ) {
	require get_template_directory() . '/page.php';
	return;
}

get_header();

$is_static_front = 'page' === get_option( 'show_on_front' );
$front_page      = null;
if ( $is_static_front && have_posts() ) {
	the_post();
	$front_page = get_post();
}

$hero_title       = $front_page ? get_the_title( $front_page ) : get_bloginfo( 'name' );
$hero_description = '';
if ( $front_page && has_excerpt( $front_page ) ) {
	$hero_description = get_the_excerpt( $front_page );
} else {
	$hero_description = get_bloginfo( 'description' );
}

$primary_label   = get_theme_mod( 'docspress_homepage_primary_label', __( 'Browse documentation', 'docspress' ) );
$secondary_label = get_theme_mod( 'docspress_homepage_secondary_label', __( 'Latest updates', 'docspress' ) );
$primary_url     = docspress_homepage_primary_url();
$secondary_url   = docspress_homepage_secondary_url();
?>
<main class="homepage-main" id="main-content">
	<section class="homepage-hero">
		<div class="homepage-hero-copy">
			<?php $kicker = get_theme_mod( 'docspress_homepage_kicker', __( 'Documentation, publishing, and community', 'docspress' ) ); ?>
			<?php if ( $kicker ) : ?>
				<p class="homepage-kicker" data-customize-homepage-kicker><?php echo esc_html( $kicker ); ?></p>
			<?php endif; ?>
			<h1><?php echo esc_html( $hero_title ); ?></h1>
			<?php if ( $hero_description && get_theme_mod( 'docspress_homepage_show_description', true ) ) : ?>
				<p class="homepage-description"><?php echo esc_html( $hero_description ); ?></p>
			<?php endif; ?>
			<?php if ( ( $primary_label && $primary_url ) || ( $secondary_label && $secondary_url ) ) : ?>
				<div class="homepage-actions">
					<?php if ( $primary_label && $primary_url ) : ?>
						<a class="homepage-button homepage-button-primary" href="<?php echo esc_url( $primary_url ); ?>" data-customize-homepage-primary-label><?php echo esc_html( $primary_label ); ?></a>
					<?php endif; ?>
					<?php if ( $secondary_label && $secondary_url ) : ?>
						<a class="homepage-button homepage-button-secondary" href="<?php echo esc_url( $secondary_url ); ?>" data-customize-homepage-secondary-label><?php echo esc_html( $secondary_label ); ?></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<div class="homepage-hero-mark" aria-hidden="true">
			<span><?php echo docspress_icon( 'book' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		</div>
	</section>

	<?php if ( $front_page && get_theme_mod( 'docspress_homepage_show_page_content', true ) ) : ?>
		<?php $front_content = trim( get_the_content( null, false, $front_page ) ); ?>
		<?php if ( $front_content ) : ?>
			<section class="homepage-content entry-content">
				<?php echo apply_filters( 'the_content', $front_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Filtered WordPress post content. ?>
				<?php wp_link_pages(); ?>
			</section>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( get_theme_mod( 'docspress_homepage_show_latest_posts', true ) ) : ?>
		<section class="homepage-updates" id="latest-updates">
			<header class="section-heading">
				<p class="entry-kicker"><?php esc_html_e( 'From the site', 'docspress' ); ?></p>
				<h2 data-customize-homepage-posts-title><?php echo esc_html( get_theme_mod( 'docspress_homepage_posts_title', __( 'Latest updates', 'docspress' ) ) ); ?></h2>
			</header>
			<div class="homepage-card-grid">
				<?php
				if ( $is_static_front ) {
					$latest_posts = new WP_Query(
						array(
							'post_type'           => 'post',
							'post_status'         => 'publish',
							'posts_per_page'      => min( 6, max( 1, absint( get_theme_mod( 'docspress_homepage_posts_count', 3 ) ) ) ),
							'ignore_sticky_posts' => true,
						)
					);
				} else {
					$latest_posts = $wp_query;
				}

				if ( $latest_posts->have_posts() ) :
					while ( $latest_posts->have_posts() ) :
						$latest_posts->the_post();
						get_template_part( 'template-parts/content', 'card' );
					endwhile;
				else :
					?>
					<div class="empty-state"><strong><?php esc_html_e( 'No updates yet.', 'docspress' ); ?></strong><p><?php esc_html_e( 'Publish a post and it will appear here.', 'docspress' ); ?></p></div>
					<?php
				endif;

				if ( $is_static_front ) {
					wp_reset_postdata();
				}
				?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $front_page && docspress_should_show_comments( $front_page->ID ) ) : ?>
		<div class="homepage-discussion">
			<?php comments_template(); ?>
		</div>
	<?php endif; ?>
</main>
<?php
get_footer();
