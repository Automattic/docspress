<?php
/**
 * Site header.
 *
 * @package DocsPress
 */
$show_command_search = get_theme_mod( 'docspress_show_header_search', true );
$show_color_toggle   = get_theme_mod( 'docspress_show_color_toggle', true );
$default_color_mode  = get_theme_mod( 'docspress_default_color_mode', 'light' );
$default_color_mode  = in_array( $default_color_mode, array( 'light', 'dark' ), true ) ? $default_color_mode : 'light';
$locked_color_mode   = $show_color_toggle ? '' : $default_color_mode;
$design_preset       = sanitize_key( get_theme_mod( 'docspress_design_preset', 'docspress' ) );
$is_jetpack_preset   = 'jetpack' === $design_preset;

if ( $is_jetpack_preset ) {
	$locked_color_mode = 'light';
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script>try{var f=<?php echo wp_json_encode( $locked_color_mode ); ?>;var t=f||localStorage.getItem('docspress-color-mode');if(t==='light'||t==='dark'){document.documentElement.dataset.theme=t}else if(window.matchMedia('(prefers-color-scheme: dark)').matches){document.documentElement.dataset.theme='dark'}}catch(e){}</script>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main-content"><?php esc_html_e( 'Skip to content', 'docspress' ); ?></a>
<header class="site-header">
	<div class="header-inner">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php $custom_logo_id = absint( get_theme_mod( 'custom_logo' ) ); ?>
			<span class="brand-mark<?php echo $custom_logo_id ? ' has-custom-logo' : ' has-default-logo'; ?>">
				<?php
				if ( $custom_logo_id ) {
					echo wp_get_attachment_image( $custom_logo_id, 'full', false, array( 'class' => 'brand-custom-logo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					printf(
						'<img class="brand-default-logo" src="%1$s" width="256" height="256" alt="" loading="eager" decoding="async" fetchpriority="high">',
						esc_url( get_theme_file_uri( 'assets/images/docspress-mark.svg' ) )
					);
				}
				?>
			</span>
			<span data-customize-site-title><?php bloginfo( 'name' ); ?></span>
			<?php if ( get_theme_mod( 'docspress_show_brand_suffix', true ) ) : ?>
				<span class="brand-wordpress" data-customize-brand-suffix><?php echo esc_html( get_theme_mod( 'docspress_brand_suffix', __( 'on WordPress', 'docspress' ) ) ); ?></span>
			<?php endif; ?>
		</a>

		<nav class="primary-navigation" aria-label="<?php esc_attr_e( 'Primary navigation', 'docspress' ); ?>">
			<?php if ( $is_jetpack_preset ) : ?>
				<ul class="jetpack-navigation">
					<li><a href="<?php echo esc_url( home_url( '/developer-docs/wordpress-developers/hooks-and-filters/' ) ); ?>"><?php esc_html_e( 'Hooks', 'docspress' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/#guides' ) ); ?>"><?php esc_html_e( 'Guides', 'docspress' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/#contribute' ) ); ?>"><?php esc_html_e( 'Contribute', 'docspress' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/#tools' ) ); ?>"><?php esc_html_e( 'Tools', 'docspress' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/#blog' ) ); ?>"><?php esc_html_e( 'Blog', 'docspress' ); ?></a></li>
				</ul>
			<?php else : ?>
				<?php
				$header_menu = absint( get_theme_mod( 'docspress_header_menu', 0 ) );
				if ( $header_menu || has_nav_menu( 'primary' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'menu'           => $header_menu ? $header_menu : '',
							'container'      => false,
							'depth'          => 1,
							'fallback_cb'    => false,
						)
					);
				} else {
					echo '<ul><li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Docs', 'docspress' ) . '</a></li></ul>';
				}
				?>
			<?php endif; ?>
		</nav>

		<div class="header-actions">
			<?php if ( $show_command_search && ! $is_jetpack_preset ) : ?>
				<button class="header-button search-shortcut" type="button" data-docs-search-trigger aria-haspopup="dialog" aria-controls="docspress-search-dialog" aria-expanded="false" aria-label="<?php esc_attr_e( 'Search documentation', 'docspress' ); ?>">
					<?php echo docspress_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span data-customize-search-label><?php echo esc_html( get_theme_mod( 'docspress_header_search_label', __( 'Search docs', 'docspress' ) ) ); ?></span><kbd data-search-shortcut-hint>⌘ K</kbd>
				</button>
			<?php endif; ?>
			<?php if ( $show_color_toggle && ! $is_jetpack_preset ) : ?>
				<button class="header-button" type="button" data-theme-toggle aria-label="<?php esc_attr_e( 'Switch color theme', 'docspress' ); ?>">
					<?php echo docspress_icon( 'sun' ) . docspress_icon( 'moon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			<?php endif; ?>
			<?php $github_url = get_theme_mod( 'docspress_github_url', 'https://github.com/Automattic/docspress' ); ?>
			<?php if ( $github_url && get_theme_mod( 'docspress_show_repository', true ) ) : ?>
				<a class="header-button repository-link" href="<?php echo esc_url( $github_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'View the repository on GitHub', 'docspress' ); ?>">
					<?php echo docspress_icon( 'github' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php if ( $is_jetpack_preset ) : ?><span><?php esc_html_e( 'GitHub', 'docspress' ); ?></span><?php endif; ?>
				</a>
			<?php endif; ?>
			<button class="menu-toggle" type="button" data-drawer-toggle aria-expanded="false" aria-controls="docs-sidebar" aria-label="<?php esc_attr_e( 'Open documentation menu', 'docspress' ); ?>">
				<?php echo docspress_icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
		</div>
	</div>
</header>
<?php if ( $show_command_search && ! $is_jetpack_preset ) : ?>
	<?php get_template_part( 'template-parts/search-dialog' ); ?>
<?php endif; ?>
