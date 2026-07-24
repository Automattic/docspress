<?php
/**
 * DocsPress block theme functions.
 *
 * @package DocsPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Set up the block theme.
 */
function docspress_setup() {
	load_theme_textdomain( 'docspress', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'block-template-parts' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 256,
			'width'       => 256,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support( 'editor-styles' );
	add_theme_support( 'html5', array( 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'wp-block-styles' );
	add_editor_style( 'style.css' );
}
add_action( 'after_setup_theme', 'docspress_setup' );

/**
 * Refresh WordPress's persistent theme.json cache after bundled style files
 * change. This matters for mounted Playground themes as well as upgrades.
 */
function docspress_maybe_refresh_theme_json_cache() {
	$files = array_merge(
		array( get_theme_file_path( 'theme.json' ) ),
		(array) glob( get_theme_file_path( 'styles/theme/*.json' ) )
	);
	$versions = array();

	foreach ( $files as $file ) {
		if ( is_readable( $file ) ) {
			$versions[] = $file . ':' . filemtime( $file ) . ':' . filesize( $file );
		}
	}

	$signature = md5( implode( '|', $versions ) );
	if ( get_option( 'docspress_theme_json_signature' ) === $signature ) {
		return;
	}

	if ( function_exists( 'wp_clean_theme_json_cache' ) ) {
		wp_clean_theme_json_cache();
	}
	update_option( 'docspress_theme_json_signature', $signature, false );
}
add_action( 'after_setup_theme', 'docspress_maybe_refresh_theme_json_cache', 100 );

/**
 * Check whether Post Title typography matches a retired DocsPress default.
 *
 * @param array<string,mixed> $typography Typography settings.
 * @return bool
 */
function docspress_is_legacy_post_title_typography( $typography ) {
	if ( ! is_array( $typography ) ) {
		return false;
	}

	$font_family = isset( $typography['fontFamily'] ) ? (string) $typography['fontFamily'] : '';
	$font_weight = isset( $typography['fontWeight'] ) ? (string) $typography['fontWeight'] : '';
	$legacy_locks = array(
		array( 'var:preset|font-family|ui', 'var:custom|headingWeight' ),
		array( 'var(--wp--preset--font-family--ui)', 'var(--wp--custom--heading-weight)' ),
		array( 'var:preset|font-family|inter', '700' ),
		array( 'var(--wp--preset--font-family--inter)', '700' ),
		array( 'var:preset|font-family|eb-garamond', '400' ),
		array( 'var(--wp--preset--font-family--eb-garamond)', '400' ),
		array( 'var:preset|font-family|recoleta', '400' ),
		array( 'var(--wp--preset--font-family--recoleta)', '400' ),
	);

	return in_array( array( $font_family, $font_weight ), $legacy_locks, true );
}

/**
 * Remove typography locks copied into Global Styles by older DocsPress style
 * variations so Core Post Title follows the site's Heading element settings.
 *
 * The filter is intentionally narrow: a deliberate Post Title override that
 * does not match one of the retired theme defaults is preserved.
 *
 * @param WP_Theme_JSON_Data $theme_json User-origin Global Styles data.
 * @return WP_Theme_JSON_Data
 */
function docspress_inherit_post_title_typography_from_headings( $theme_json ) {
	if ( ! $theme_json instanceof WP_Theme_JSON_Data ) {
		return $theme_json;
	}

	$data       = $theme_json->get_data();
	$typography = isset( $data['styles']['blocks']['core/post-title']['typography'] )
		? $data['styles']['blocks']['core/post-title']['typography']
		: array();

	if ( ! is_array( $typography ) ) {
		return $theme_json;
	}

	if ( ! docspress_is_legacy_post_title_typography( $typography ) ) {
		return $theme_json;
	}

	unset(
		$data['styles']['blocks']['core/post-title']['typography']['fontFamily'],
		$data['styles']['blocks']['core/post-title']['typography']['fontWeight']
	);

	if ( empty( $data['styles']['blocks']['core/post-title']['typography'] ) ) {
		unset( $data['styles']['blocks']['core/post-title']['typography'] );
	}
	if ( empty( $data['styles']['blocks']['core/post-title'] ) ) {
		unset( $data['styles']['blocks']['core/post-title'] );
	}
	if ( empty( $data['styles']['blocks'] ) ) {
		unset( $data['styles']['blocks'] );
	}

	return new WP_Theme_JSON_Data( $data, 'custom' );
}
add_filter( 'wp_theme_json_data_user', 'docspress_inherit_post_title_typography_from_headings' );

/**
 * Remove the retired Post Title typography lock from the saved Global Styles
 * post so the Site Editor canvas and REST responses inherit Headings too.
 */
function docspress_migrate_legacy_post_title_typography() {
	if (
		'1' === get_option( 'docspress_post_title_typography_migration' ) ||
		! current_user_can( 'edit_theme_options' ) ||
		! class_exists( 'WP_Theme_JSON_Resolver' )
	) {
		return;
	}

	$post_id = WP_Theme_JSON_Resolver::get_user_global_styles_post_id();
	$post    = $post_id ? get_post( $post_id ) : null;

	if ( ! $post instanceof WP_Post ) {
		update_option( 'docspress_post_title_typography_migration', '1', false );
		return;
	}

	$data       = json_decode( $post->post_content, true );
	$typography = isset( $data['styles']['blocks']['core/post-title']['typography'] )
		? $data['styles']['blocks']['core/post-title']['typography']
		: array();

	if ( docspress_is_legacy_post_title_typography( $typography ) ) {
		unset(
			$data['styles']['blocks']['core/post-title']['typography']['fontFamily'],
			$data['styles']['blocks']['core/post-title']['typography']['fontWeight']
		);

		if ( empty( $data['styles']['blocks']['core/post-title']['typography'] ) ) {
			unset( $data['styles']['blocks']['core/post-title']['typography'] );
		}
		if ( empty( $data['styles']['blocks']['core/post-title'] ) ) {
			unset( $data['styles']['blocks']['core/post-title'] );
		}
		if ( empty( $data['styles']['blocks'] ) ) {
			unset( $data['styles']['blocks'] );
		}

		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_content' => wp_slash(
					wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP )
				),
			)
		);

		if ( function_exists( 'wp_clean_theme_json_cache' ) ) {
			wp_clean_theme_json_cache();
		}
	}

	update_option( 'docspress_post_title_typography_migration', '1', false );
}
add_action( 'admin_init', 'docspress_migrate_legacy_post_title_typography' );

/**
 * Give the reusable discussion shell its own meaningful template-part area.
 *
 * @param array<int,array<string,mixed>> $areas Registered areas.
 * @return array<int,array<string,mixed>>
 */
function docspress_template_part_areas( $areas ) {
	$areas[] = array(
		'area'        => 'comments',
		'area_tag'    => 'section',
		'label'       => __( 'Comments', 'docspress' ),
		'description' => __( 'Reusable discussion and reply-form layouts.', 'docspress' ),
		'icon'        => 'sidebar',
	);
	return $areas;
}
add_filter( 'default_wp_template_part_areas', 'docspress_template_part_areas' );

/**
 * Install the bundled DocsPress icon as the initial editable Site Logo.
 *
 * WordPress stores the Site Logo as a Media Library attachment. Seeding the
 * bundled PNG once lets the core Site Logo block display, replace, crop, or
 * remove it normally. The marker prevents a deliberately removed logo from
 * being restored on a later request.
 */
function docspress_maybe_seed_default_site_logo() {
	if ( get_option( 'docspress_default_site_logo_seeded' ) ) {
		return;
	}

	$current_logo_id = absint( get_option( 'site_logo' ) );
	if ( ! $current_logo_id ) {
		$current_logo_id = absint( get_theme_mod( 'custom_logo' ) );
	}

	if ( $current_logo_id && wp_attachment_is_image( $current_logo_id ) ) {
		update_option( 'docspress_default_site_logo_seeded', $current_logo_id );
		return;
	}

	$existing_default = get_posts(
		array(
			'fields'         => 'ids',
			'meta_key'       => '_docspress_default_site_logo',
			'meta_value'     => '1',
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'posts_per_page' => 1,
		)
	);

	if ( $existing_default ) {
		$logo_id = absint( $existing_default[0] );
	} else {
		$source = get_theme_file_path( 'assets/images/docspress-hybrid-logo.png' );
		if ( ! is_readable( $source ) ) {
			return;
		}

		$image = file_get_contents( $source );
		if ( false === $image ) {
			return;
		}

		$upload = wp_upload_bits( 'docspress-logo.png', null, $image );
		if ( ! empty( $upload['error'] ) ) {
			return;
		}

		$filetype = wp_check_filetype( $upload['file'], null );
		$logo_id  = wp_insert_attachment(
			array(
				'guid'           => $upload['url'],
				'post_mime_type' => $filetype['type'],
				'post_status'    => 'inherit',
				'post_title'     => __( 'DocsPress logo', 'docspress' ),
			),
			$upload['file']
		);

		if ( is_wp_error( $logo_id ) ) {
			return;
		}

		require_once ABSPATH . 'wp-admin/includes/image.php';
		$metadata = wp_generate_attachment_metadata( $logo_id, $upload['file'] );
		if ( $metadata ) {
			wp_update_attachment_metadata( $logo_id, $metadata );
		}
		update_post_meta( $logo_id, '_wp_attachment_image_alt', __( 'DocsPress', 'docspress' ) );
		update_post_meta( $logo_id, '_docspress_default_site_logo', '1' );
	}

	update_option( 'site_logo', $logo_id );
	if ( ! absint( get_option( 'site_icon' ) ) ) {
		update_option( 'site_icon', $logo_id );
	}
	update_option( 'docspress_default_site_logo_seeded', $logo_id );
}
add_action( 'init', 'docspress_maybe_seed_default_site_logo', 20 );

/**
 * Enqueue the shared theme runtime.
 */
function docspress_assets() {
	$theme = wp_get_theme();

	wp_enqueue_style( 'docspress-style', get_stylesheet_uri(), array(), $theme->get( 'Version' ) );
	wp_enqueue_script(
		'docspress-navigation',
		get_theme_file_uri( 'assets/js/docs.js' ),
		array(),
		$theme->get( 'Version' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'docspress_assets' );

/**
 * Return a small inline icon.
 *
 * @param string $name Icon name.
 * @return string
 */
function docspress_icon( $name ) {
	$icons = array(
		'book'     => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 4.8c2.6-.7 4.9-.2 7 1.4v13c-2.1-1.6-4.4-2.1-7-1.4v-13Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M19 4.8c-2.6-.7-4.9-.2-7 1.4v13c2.1-1.6 4.4-2.1 7-1.4v-13Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>',
		'github'   => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.58 2 12.23c0 4.51 2.87 8.34 6.84 9.69.5.1.68-.22.68-.49 0-.24-.01-1.05-.01-1.9-2.78.62-3.37-1.2-3.37-1.2-.45-1.18-1.11-1.49-1.11-1.49-.91-.64.07-.62.07-.62 1 .07 1.53 1.06 1.53 1.06.9 1.57 2.35 1.12 2.92.85.09-.66.35-1.12.64-1.37-2.22-.26-4.55-1.14-4.55-5.07 0-1.12.39-2.04 1.03-2.76-.1-.26-.45-1.3.1-2.72 0 0 .84-.28 2.75 1.05A9.36 9.36 0 0 1 12 6.92c.85 0 1.69.12 2.49.34 1.91-1.33 2.75-1.05 2.75-1.05.55 1.42.2 2.46.1 2.72.64.72 1.03 1.64 1.03 2.76 0 3.94-2.34 4.8-4.57 5.06.36.32.68.94.68 1.9 0 1.37-.01 2.47-.01 2.8 0 .27.18.59.69.49A10.25 10.25 0 0 0 22 12.23C22 6.58 17.52 2 12 2Z"/></svg>',
		'menu'     => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
		'moon'     => '<svg class="theme-icon-dark" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 15.3A8.5 8.5 0 0 1 8.7 4a8.5 8.5 0 1 0 11.3 11.3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>',
		'pencil'   => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m14.7 5.3 4 4M5 19l2.1-5.1L16.6 4.4a1.4 1.4 0 0 1 2 0l1 1a1.4 1.4 0 0 1 0 2L10.1 17 5 19Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>',
		'search'   => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="6.5" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
		'sun'      => '<svg class="theme-icon-light" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="3.7" stroke="currentColor" stroke-width="1.7"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>',
	);

	return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}

/**
 * Resolve a reusable documentation root slug to a Page ID.
 *
 * @param string $root_slug Page path or slug.
 * @return int
 */
function docspress_get_docs_root_id( $root_slug = 'docs' ) {
	$root_slug = trim( sanitize_text_field( (string) $root_slug ), '/' );
	if ( $root_slug ) {
		$page = get_page_by_path( $root_slug, OBJECT, 'page' );
		if ( $page instanceof WP_Post && 'publish' === $page->post_status ) {
			return (int) $page->ID;
		}
	}

	if ( is_page() ) {
		$current_id = get_queried_object_id();
		$ancestors  = get_post_ancestors( $current_id );
		return $ancestors ? (int) end( $ancestors ) : (int) $current_id;
	}

	return 0;
}

/**
 * Keep the Site Editor Styles canvas focused on the complete documentation
 * template instead of an individual Page's content entity.
 */
function docspress_site_editor_preview_context() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'site-editor' !== $screen->id ) {
		return;
	}

	$theme       = wp_get_theme();
	$script_path = get_theme_file_path( 'assets/js/site-editor-preview.js' );
	$version     = is_readable( $script_path ) ? (string) filemtime( $script_path ) : $theme->get( 'Version' );

	wp_enqueue_script(
		'docspress-site-editor-preview',
		get_theme_file_uri( 'assets/js/site-editor-preview.js' ),
		array( 'wp-compose', 'wp-element', 'wp-hooks' ),
		$version,
		true
	);
	wp_add_inline_script(
		'docspress-site-editor-preview',
		'window.docspressSiteEditorPreview = ' . wp_json_encode(
			array(
				'archivePostId' => get_stylesheet() . '//archive',
				'postId'        => get_stylesheet() . '//page',
				'postType'      => 'wp_template',
			)
		) . ';',
		'before'
	);
}
add_action( 'enqueue_block_editor_assets', 'docspress_site_editor_preview_context' );

/**
 * Get published Pages within a documentation tree.
 *
 * @param string $root_slug Documentation root path.
 * @param string $sort      menu_order, title, newest, or oldest.
 * @return WP_Post[]
 */
function docspress_get_docs_pages( $root_slug = 'docs', $sort = 'menu_order' ) {
	static $page_cache = array();

	$root_id = docspress_get_docs_root_id( $root_slug );
	$sort    = in_array( $sort, array( 'menu_order', 'title', 'newest', 'oldest' ), true ) ? $sort : 'menu_order';
	$key     = $root_id . ':' . $sort;
	if ( isset( $page_cache[ $key ] ) ) {
		return $page_cache[ $key ];
	}

	$options = array(
		'menu_order' => array( 'menu_order,post_title', 'ASC' ),
		'title'      => array( 'post_title', 'ASC' ),
		'newest'     => array( 'post_date', 'DESC' ),
		'oldest'     => array( 'post_date', 'ASC' ),
	);
	$pages   = get_pages(
		array(
			'post_status' => 'publish',
			'sort_column' => $options[ $sort ][0],
			'sort_order'  => $options[ $sort ][1],
		)
	);

	if ( ! $root_id ) {
		$page_cache[ $key ] = $pages;
		return $page_cache[ $key ];
	}

	$parents = array();
	foreach ( $pages as $page ) {
		$parents[ (int) $page->ID ] = (int) $page->post_parent;
	}

	$page_cache[ $key ] = array_values(
		array_filter(
			$pages,
			static function ( $page ) use ( $root_id, $parents ) {
				$current = (int) $page->ID;
				while ( $current ) {
					if ( $current === $root_id ) {
						return true;
					}
					$current = isset( $parents[ $current ] ) ? $parents[ $current ] : 0;
				}
				return false;
			}
		)
	);

	return $page_cache[ $key ];
}

/**
 * Group Pages by parent for linear-time navigation rendering.
 *
 * @param WP_Post[] $pages Documentation Pages.
 * @return array<int,WP_Post[]>
 */
function docspress_group_pages_by_parent( $pages ) {
	$grouped = array();
	foreach ( $pages as $page ) {
		$parent = (int) $page->post_parent;
		if ( ! isset( $grouped[ $parent ] ) ) {
			$grouped[ $parent ] = array();
		}
		$grouped[ $parent ][] = $page;
	}
	return $grouped;
}

/**
 * Read DocsPress management metadata from a Page.
 *
 * @param int $post_id Page ID.
 * @return array<string,mixed>
 */
function docspress_get_managed_metadata( $post_id = 0 ) {
	static $cache = array();

	$post_id = $post_id ? absint( $post_id ) : get_queried_object_id();
	if ( ! $post_id ) {
		return array();
	}
	if ( isset( $cache[ $post_id ] ) ) {
		return $cache[ $post_id ];
	}

	$content = (string) get_post_field( 'post_content', $post_id, 'raw' );
	$cache[ $post_id ] = array();
	if ( $content && preg_match( '/<!--\s*docspress:(.*?)\s*-->/s', $content, $matches ) ) {
		$parsed = json_decode( trim( $matches[1] ), true );
		if ( is_array( $parsed ) && 1 === (int) ( isset( $parsed['version'] ) ? $parsed['version'] : 0 ) ) {
			$cache[ $post_id ] = $parsed;
		}
	}
	return $cache[ $post_id ];
}

/**
 * Return a Page's requested initial sidebar state.
 *
 * @param int $post_id Page ID.
 * @return bool|null
 */
function docspress_get_sidebar_collapsed( $post_id = 0 ) {
	$metadata = docspress_get_managed_metadata( $post_id );
	return array_key_exists( 'sidebarCollapsed', $metadata ) && is_bool( $metadata['sidebarCollapsed'] )
		? $metadata['sidebarCollapsed']
		: null;
}

/**
 * Render a nested Page tree.
 *
 * @param WP_Post[] $pages      Documentation Pages.
 * @param int       $parent_id  Parent ID.
 * @param int       $root_id    Root ID.
 * @param int       $level      Current level.
 * @param int       $max_depth  Maximum level, zero for unlimited.
 * @param array     $grouped    Pages grouped by parent.
 */
function docspress_render_page_tree( $pages, $parent_id = 0, $root_id = 0, $level = 1, $max_depth = 0, $grouped = null ) {
	if ( $max_depth && $level > $max_depth ) {
		return;
	}

	$grouped = null === $grouped ? docspress_group_pages_by_parent( $pages ) : $grouped;
	$children = $root_id && 0 === $parent_id
		? array_values(
			array_filter(
				$pages,
				static function ( $page ) use ( $root_id ) {
					return (int) $page->ID === $root_id;
				}
			)
		)
		: ( isset( $grouped[ $parent_id ] ) ? $grouped[ $parent_id ] : array() );

	if ( ! $children ) {
		return;
	}

	echo '<ul>';
	foreach ( $children as $page ) {
		$current   = (int) get_queried_object_id() === (int) $page->ID;
		$collapsed = docspress_get_sidebar_collapsed( $page->ID );
		printf(
			'<li data-doc-title="%1$s"><a href="%2$s"%3$s%4$s><span class="nav-dot" aria-hidden="true"></span><span>%5$s</span></a>',
			esc_attr( wp_strip_all_tags( $page->post_title ) ),
			esc_url( get_permalink( $page ) ),
			$current ? ' aria-current="page"' : '',
			null === $collapsed ? '' : ' data-sidebar-collapsed="' . ( $collapsed ? 'true' : 'false' ) . '"',
			esc_html( $page->post_title )
		);
		docspress_render_page_tree( $pages, (int) $page->ID, 0, $level + 1, $max_depth, $grouped );
		echo '</li>';
	}
	echo '</ul>';
}

/**
 * Flatten the Page tree in the same order used by navigation.
 *
 * @param WP_Post[] $pages      Documentation Pages.
 * @param int       $root_id    Root ID.
 * @param int       $parent     Parent ID.
 * @param int       $level      Current level.
 * @param int       $max_depth  Maximum level, zero for unlimited.
 * @param array     $grouped    Pages grouped by parent.
 * @return WP_Post[]
 */
function docspress_flatten_page_tree( $pages, $root_id = 0, $parent = 0, $level = 1, $max_depth = 0, $grouped = null ) {
	if ( $max_depth && $level > $max_depth ) {
		return array();
	}

	$flat     = array();
	$grouped  = null === $grouped ? docspress_group_pages_by_parent( $pages ) : $grouped;
	$children = $root_id && 0 === $parent
		? array_values(
			array_filter(
				$pages,
				static function ( $page ) use ( $root_id ) {
					return (int) $page->ID === $root_id;
				}
			)
		)
		: ( isset( $grouped[ $parent ] ) ? $grouped[ $parent ] : array() );

	foreach ( $children as $page ) {
		$flat[] = $page;
		$flat   = array_merge( $flat, docspress_flatten_page_tree( $pages, 0, (int) $page->ID, $level + 1, $max_depth, $grouped ) );
	}
	return $flat;
}

/**
 * Add stable IDs to headings and build a table of contents.
 *
 * @param string $content   Rendered content.
 * @param int    $min_level Lowest heading level.
 * @param int    $max_level Highest heading level.
 * @return array{content:string,toc:array<int,array{level:int,id:string,title:string}>}
 */
function docspress_prepare_content( $content, $min_level = 2, $max_level = 3 ) {
	$toc       = array();
	$used      = array();
	$min_level = min( 6, max( 1, absint( $min_level ) ) );
	$max_level = min( 6, max( $min_level, absint( $max_level ) ) );

	$content = preg_replace_callback(
		'/<h([1-6])([^>]*)>(.*?)<\/h\1>/is',
		static function ( $matches ) use ( &$toc, &$used, $min_level, $max_level ) {
			$level = (int) $matches[1];
			if ( $level < $min_level || $level > $max_level ) {
				return $matches[0];
			}

			$attributes = $matches[2];
			$inner      = $matches[3];
			$title      = trim( wp_strip_all_tags( $inner ) );
			if ( '' === $title ) {
				return $matches[0];
			}

			$id = preg_match( '/\sid=(["\'])(.*?)\1/i', $attributes, $id_match )
				? sanitize_title( $id_match[2] )
				: sanitize_title( $title );
			$id = $id ? $id : 'section';
			$base = $id;
			$index = 2;
			while ( isset( $used[ $id ] ) ) {
				$id = $base . '-' . $index++;
			}
			$used[ $id ] = true;

			if ( ! preg_match( '/\sid=(["\'])(.*?)\1/i', $attributes ) ) {
				$attributes .= ' id="' . esc_attr( $id ) . '"';
			}
			$toc[] = array(
				'level' => $level,
				'id'    => $id,
				'title' => $title,
			);
			return '<h' . $level . $attributes . '>' . $inner . '</h' . $level . '>';
		},
		(string) $content
	);

	return array(
		'content' => $content,
		'toc'     => $toc,
	);
}

/**
 * Add heading anchors to the Post Content block used by documentation pages.
 *
 * @param string $block_content Rendered block.
 * @return string
 */
function docspress_post_content_anchors( $block_content ) {
	return is_page() ? docspress_prepare_content( $block_content, 2, 6 )['content'] : $block_content;
}
add_filter( 'render_block_core/post-content', 'docspress_post_content_anchors' );

/**
 * Return version taxonomy terms when the site registers them.
 *
 * @return array{terms:WP_Term[],current:int}
 */
function docspress_get_versions() {
	if ( ! taxonomy_exists( 'docspress_version' ) ) {
		return array( 'terms' => array(), 'current' => 0 );
	}

	$terms = get_terms( array( 'taxonomy' => 'docspress_version', 'hide_empty' => true ) );
	$terms = is_wp_error( $terms ) ? array() : $terms;
	$current_terms = is_page() ? wp_get_post_terms( get_queried_object_id(), 'docspress_version' ) : array();
	$current_id = ( $current_terms && ! is_wp_error( $current_terms ) ) ? (int) $current_terms[0]->term_id : 0;
	return array( 'terms' => $terms, 'current' => $current_id );
}

/**
 * Normalize a repository-relative Markdown source path.
 *
 * @param mixed $source Untrusted source path.
 * @return string
 */
function docspress_normalize_markdown_source_path( $source ) {
	if ( ! is_string( $source ) ) {
		return '';
	}

	$source = trim( str_replace( '\\', '/', wp_strip_all_tags( $source ) ) );
	if ( '' === $source || '/' === $source[0] || false !== strpos( $source, ':' ) || false !== strpos( $source, "\0" ) ) {
		return '';
	}
	foreach ( explode( '/', $source ) as $segment ) {
		if ( '' === $segment || '.' === $segment || '..' === $segment ) {
			return '';
		}
	}
	return preg_match( '/\.(?:md|markdown|mdx)$/i', $source ) ? $source : '';
}

/**
 * Read the exact synchronized Markdown source path.
 *
 * @param int $post_id Page ID.
 * @return string
 */
function docspress_get_markdown_source_path( $post_id = 0 ) {
	$post_id  = $post_id ? absint( $post_id ) : get_queried_object_id();
	$metadata = docspress_get_managed_metadata( $post_id );
	$source   = isset( $metadata['source'] ) ? $metadata['source'] : '';
	if ( ! $source && $post_id ) {
		$source = get_post_meta( $post_id, '_docspress_source_path', true );
	}
	$source = apply_filters( 'docspress_markdown_source_path', $source, $post_id, $metadata );
	return docspress_normalize_markdown_source_path( $source );
}

/**
 * Build a GitHub editor URL for the current Markdown source.
 *
 * @param int    $post_id   Page ID.
 * @param string $repository Repository URL.
 * @param string $ref        Branch or tag.
 * @return string
 */
function docspress_get_github_edit_url( $post_id = 0, $repository = 'https://github.com/Automattic/docspress', $ref = 'main' ) {
	$source = docspress_get_markdown_source_path( $post_id );
	if ( ! $source ) {
		return '';
	}

	$repository = preg_replace( '/\.git$/i', '', untrailingslashit( esc_url_raw( $repository ) ) );
	$parts      = wp_parse_url( $repository );
	if (
		! $repository ||
		! is_array( $parts ) ||
		empty( $parts['scheme'] ) ||
		empty( $parts['host'] ) ||
		! in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true ) ||
		isset( $parts['user'] ) ||
		isset( $parts['pass'] ) ||
		isset( $parts['query'] ) ||
		isset( $parts['fragment'] )
	) {
		return '';
	}

	$ref = trim( (string) $ref );
	if ( ! preg_match( '#^[A-Za-z0-9._/-]+$#', $ref ) || false !== strpos( $ref, '..' ) ) {
		$ref = 'main';
	}
	$source = implode( '/', array_map( 'rawurlencode', explode( '/', $source ) ) );
	return $repository . '/edit/' . rawurlencode( $ref ) . '/' . $source;
}

require get_theme_file_path( 'inc/blocks.php' );
require get_theme_file_path( 'inc/llms.php' );
require get_theme_file_path( 'inc/performance.php' );
