<?php
/**
 * Autodocs Publish block theme functions.
 *
 * @package Autodocs Publish
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Set up the block theme.
 */
function autodocs_publish_setup() {
	load_theme_textdomain( 'autodocs-publish', get_template_directory() . '/languages' );

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
add_action( 'after_setup_theme', 'autodocs_publish_setup' );

/**
 * Carry Site Editor data across the physical theme-directory rename.
 *
 * Theme mods and Site Editor posts are scoped to the stylesheet directory,
 * not the Theme Name header. Keep the old scope attached so rolling back the
 * rename remains possible, and never replace data already saved in the new
 * scope.
 */
function autodocs_publish_migrate_legacy_theme_slug_data() {
	$active_theme       = wp_get_theme();
	$current_stylesheet = get_stylesheet();
	$legacy_stylesheet  = 'docspress';
	$migration_key      = 'autodocs_publish_theme_slug_migration_' . sanitize_key( $current_stylesheet );

	if (
		'autodocs-publish' !== $active_theme->get( 'TextDomain' ) ||
		$legacy_stylesheet === $current_stylesheet ||
		'1' === get_option( $migration_key )
	) {
		return;
	}

	$legacy_mods  = get_option( 'theme_mods_' . $legacy_stylesheet, null );
	$current_mods = get_option( 'theme_mods_' . $current_stylesheet, null );
	if ( is_array( $legacy_mods ) ) {
		$merged_mods = array_replace( $legacy_mods, is_array( $current_mods ) ? $current_mods : array() );
		if ( null === $current_mods ) {
			add_option( 'theme_mods_' . $current_stylesheet, $merged_mods, '', false );
		} elseif ( $merged_mods !== $current_mods ) {
			update_option( 'theme_mods_' . $current_stylesheet, $merged_mods, false );
		}
	}

	if ( ! taxonomy_exists( 'wp_theme' ) ) {
		return;
	}

	$legacy_term = get_term_by( 'slug', sanitize_title( $legacy_stylesheet ), 'wp_theme' );
	$migration_failed = false;
	if ( $legacy_term instanceof WP_Term ) {
		$post_types = array( 'wp_template', 'wp_template_part', 'wp_global_styles' );
		$legacy_ids = get_posts(
			array(
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'post_status'    => 'any',
				'post_type'      => $post_types,
				'posts_per_page' => -1,
				'tax_query'      => array(
					array(
						'field'    => 'term_id',
						'taxonomy' => 'wp_theme',
						'terms'    => array( (int) $legacy_term->term_id ),
					),
				),
			)
		);

		foreach ( $legacy_ids as $legacy_id ) {
			$legacy_post = get_post( $legacy_id );
			if ( ! $legacy_post instanceof WP_Post ) {
				continue;
			}

			$canonical_query = array(
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'post_status'    => 'any',
				'post_type'      => $legacy_post->post_type,
				'posts_per_page' => 1,
				'post__not_in'   => array( (int) $legacy_id ),
				'tax_query'      => array(
					array(
						'field'    => 'slug',
						'taxonomy' => 'wp_theme',
						'terms'    => array( sanitize_title( $current_stylesheet ) ),
					),
				),
			);
			if ( 'wp_global_styles' !== $legacy_post->post_type ) {
				$canonical_query['name'] = $legacy_post->post_name;
			}

			if ( get_posts( $canonical_query ) ) {
				continue;
			}

			$result = wp_set_object_terms( (int) $legacy_id, $current_stylesheet, 'wp_theme', true );
			if ( is_wp_error( $result ) ) {
				$migration_failed = true;
			}
		}
	}

	if ( ! $migration_failed ) {
		update_option( $migration_key, '1', false );
	}
}
add_action( 'init', 'autodocs_publish_migrate_legacy_theme_slug_data', 8 );

/**
 * Copy persisted data from the pre-rename identifiers without overwriting any
 * value already written by Autodocs Publish.
 */
function autodocs_publish_migrate_legacy_brand_data() {
	if ( '1' === get_option( 'autodocs_publish_legacy_brand_migration' ) ) {
		return;
	}

	$option_map = array(
		'docspress_post_title_typography_migration' => 'autodocs_publish_post_title_typography_migration',
		'docspress_repository_latest_version'       => 'autodocs_publish_repository_latest_version',
		'docspress_version_override'                => 'autodocs_publish_version_override',
		'docspress_docs_root_slug'                  => 'autodocs_publish_docs_root_slug',
	);
	foreach ( $option_map as $legacy_key => $current_key ) {
		$legacy_value = get_option( $legacy_key, null );
		if ( null !== $legacy_value && null === get_option( $current_key, null ) ) {
			add_option( $current_key, $legacy_value, '', false );
		}
	}

	$meta_map = array(
		'_docspress_version_id'               => '_autodocs_publish_version_id',
		'_docspress_logical_route'            => '_autodocs_publish_logical_route',
		'_docspress_page_identity'            => '_autodocs_publish_page_identity',
		'_docspress_source_type'              => '_autodocs_publish_source_type',
		'_docspress_source_path'              => '_autodocs_publish_source_path',
		'_docspress_github_path'              => '_autodocs_publish_github_path',
		'_docspress_github_repository'        => '_autodocs_publish_github_repository',
		'_docspress_github_ref'               => '_autodocs_publish_github_ref',
		'_docspress_github_server_url'        => '_autodocs_publish_github_server_url',
		'_docspress_docs_root'                => '_autodocs_publish_docs_root',
		'_docspress_version_container'        => '_autodocs_publish_version_container',
		'_docspress_sidebar_id'               => '_autodocs_publish_sidebar_id',
		'_docspress_sidebar_root'             => '_autodocs_publish_sidebar_root',
		'docspress_helpful_votes'             => 'autodocs_publish_helpful_votes',
		'docspress_unhelpful_votes'           => 'autodocs_publish_unhelpful_votes',
		'docspress_feedback_enabled'          => 'autodocs_publish_feedback_enabled',
	);
	$page_ids = get_posts(
		array(
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'post_status'    => 'any',
			'post_type'      => 'page',
			'posts_per_page' => -1,
		)
	);
	foreach ( $page_ids as $page_id ) {
		foreach ( $meta_map as $legacy_key => $current_key ) {
			if ( metadata_exists( 'post', $page_id, $legacy_key ) && ! metadata_exists( 'post', $page_id, $current_key ) ) {
				update_post_meta( $page_id, $current_key, get_post_meta( $page_id, $legacy_key, true ) );
			}
		}
	}

	update_option( 'autodocs_publish_legacy_brand_migration', '1', false );
}
add_action( 'init', 'autodocs_publish_migrate_legacy_brand_data', 11 );

/**
 * Give legacy block aliases the canonical root class expected by the renamed
 * theme and companion plugin styles.
 *
 * @param string $content Rendered block HTML.
 * @param array  $block   Parsed block data.
 * @return string
 */
function autodocs_publish_add_canonical_legacy_block_class( $content, $block ) {
	$name = isset( $block['blockName'] ) ? (string) $block['blockName'] : '';
	if ( 0 !== strpos( $name, 'docspress/' ) || ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return $content;
	}

	$processor = new WP_HTML_Tag_Processor( $content );
	if ( $processor->next_tag() ) {
		$processor->add_class( 'wp-block-autodocs-publish-' . sanitize_html_class( substr( $name, strlen( 'docspress/' ) ) ) );
		return $processor->get_updated_html();
	}
	return $content;
}
add_filter( 'render_block', 'autodocs_publish_add_canonical_legacy_block_class', 10, 2 );

/**
 * Refresh WordPress's persistent theme.json cache after bundled style files
 * change. This matters for mounted Playground themes as well as upgrades.
 */
function autodocs_publish_maybe_refresh_theme_json_cache() {
	$files = array_merge(
		array( get_theme_file_path( 'theme.json' ) ),
		(array) glob( get_theme_file_path( 'styles/theme/*.json' ) ),
		(array) glob( get_theme_file_path( 'styles/color/*/*.json' ) ),
		(array) glob( get_theme_file_path( 'styles/block/*.json' ) )
	);
	$versions = array();

	foreach ( $files as $file ) {
		if ( is_readable( $file ) ) {
			$versions[] = $file . ':' . filemtime( $file ) . ':' . filesize( $file );
		}
	}

	$signature = md5( implode( '|', $versions ) );
	if ( get_option( 'autodocs_publish_theme_json_signature' ) === $signature ) {
		return;
	}

	if ( function_exists( 'wp_clean_theme_json_cache' ) ) {
		wp_clean_theme_json_cache();
	}
	update_option( 'autodocs_publish_theme_json_signature', $signature, false );
}
add_action( 'after_setup_theme', 'autodocs_publish_maybe_refresh_theme_json_cache', 100 );

/**
 * Check whether Post Title typography matches a retired Autodocs Publish default.
 *
 * @param array<string,mixed> $typography Typography settings.
 * @return bool
 */
function autodocs_publish_is_legacy_post_title_typography( $typography ) {
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
 * Remove typography locks copied into Global Styles by older Autodocs Publish style
 * variations so Core Post Title follows the site's Heading element settings.
 *
 * The filter is intentionally narrow: a deliberate Post Title override that
 * does not match one of the retired theme defaults is preserved.
 *
 * @param WP_Theme_JSON_Data $theme_json User-origin Global Styles data.
 * @return WP_Theme_JSON_Data
 */
function autodocs_publish_inherit_post_title_typography_from_headings( $theme_json ) {
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

	if ( ! autodocs_publish_is_legacy_post_title_typography( $typography ) ) {
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
add_filter( 'wp_theme_json_data_user', 'autodocs_publish_inherit_post_title_typography_from_headings' );

/**
 * Remove the retired Post Title typography lock from the saved Global Styles
 * post so the Site Editor canvas and REST responses inherit Headings too.
 */
function autodocs_publish_migrate_legacy_post_title_typography() {
	if (
		'1' === get_option( 'autodocs_publish_post_title_typography_migration' ) ||
		! current_user_can( 'edit_theme_options' ) ||
		! class_exists( 'WP_Theme_JSON_Resolver' )
	) {
		return;
	}

	$post_id = WP_Theme_JSON_Resolver::get_user_global_styles_post_id();
	$post    = $post_id ? get_post( $post_id ) : null;

	if ( ! $post instanceof WP_Post ) {
		update_option( 'autodocs_publish_post_title_typography_migration', '1', false );
		return;
	}

	$data       = json_decode( $post->post_content, true );
	$typography = isset( $data['styles']['blocks']['core/post-title']['typography'] )
		? $data['styles']['blocks']['core/post-title']['typography']
		: array();

	if ( autodocs_publish_is_legacy_post_title_typography( $typography ) ) {
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

	update_option( 'autodocs_publish_post_title_typography_migration', '1', false );
}
add_action( 'admin_init', 'autodocs_publish_migrate_legacy_post_title_typography' );

/**
 * Give the reusable discussion shell its own meaningful template-part area.
 *
 * @param array<int,array<string,mixed>> $areas Registered areas.
 * @return array<int,array<string,mixed>>
 */
function autodocs_publish_template_part_areas( $areas ) {
	$areas[] = array(
		'area'        => 'comments',
		'area_tag'    => 'section',
		'label'       => __( 'Comments', 'autodocs-publish' ),
		'description' => __( 'Reusable discussion and reply-form layouts.', 'autodocs-publish' ),
		'icon'        => 'sidebar',
	);
	return $areas;
}
add_filter( 'default_wp_template_part_areas', 'autodocs_publish_template_part_areas' );

/**
 * Install the bundled Autodocs Publish icon as the initial editable Site Logo.
 *
 * WordPress stores the Site Logo as a Media Library attachment. Seeding the
 * bundled PNG once lets the core Site Logo block display, replace, crop, or
 * remove it normally. The marker prevents a deliberately removed logo from
 * being restored on a later request.
 */
function autodocs_publish_maybe_seed_default_site_logo() {
	if ( get_option( 'autodocs_publish_default_site_logo_seeded' ) ) {
		return;
	}

	$legacy_seeded_value = get_option( 'docspress_default_site_logo_seeded', false );
	$is_legacy_upgrade   = false !== $legacy_seeded_value;
	$legacy_logo_id      = absint( $legacy_seeded_value );
	$is_legacy_default   = $legacy_logo_id
		&& '1' === (string) get_post_meta( $legacy_logo_id, '_docspress_default_site_logo', true );
	$current_logo_id     = absint( get_option( 'site_logo' ) );
	$current_icon_id     = absint( get_option( 'site_icon' ) );

	// Before the rename the seeder always wrote site_logo. A now-empty value is
	// therefore an explicit removal, even if a stale custom_logo mod remains.
	if ( ! $is_legacy_upgrade && ! $current_logo_id ) {
		$current_logo_id = absint( get_theme_mod( 'custom_logo' ) );
	}

	$replace_logo = $is_legacy_default && $current_logo_id === $legacy_logo_id;
	$replace_icon = $is_legacy_default && $current_icon_id === $legacy_logo_id;
	$seed_logo    = ! $is_legacy_upgrade && ( ! $current_logo_id || ! wp_attachment_is_image( $current_logo_id ) );
	$seed_icon    = $seed_logo && ! $current_icon_id;

	if ( ! $replace_logo && ! $replace_icon && ! $seed_logo && ! $seed_icon ) {
		update_option(
			'autodocs_publish_default_site_logo_seeded',
			$current_logo_id ? $current_logo_id : ( $current_icon_id ? $current_icon_id : 'removed' ),
			false
		);
		return;
	}

	$existing_default = get_posts(
		array(
			'fields'         => 'ids',
			'meta_key'       => '_autodocs_publish_default_site_logo',
			'meta_value'     => '1',
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'posts_per_page' => 1,
		)
	);

	if ( $existing_default ) {
		$logo_id = absint( $existing_default[0] );
	} else {
		$source = get_theme_file_path( 'assets/images/autodocs-publish-mark.png' );
		if ( ! is_readable( $source ) ) {
			return;
		}

		$image = file_get_contents( $source );
		if ( false === $image ) {
			return;
		}

		$upload = wp_upload_bits( 'autodocs-publish-logo.png', null, $image );
		if ( ! empty( $upload['error'] ) ) {
			return;
		}

		$filetype = wp_check_filetype( $upload['file'], null );
		$logo_id  = wp_insert_attachment(
			array(
				'guid'           => $upload['url'],
				'post_mime_type' => $filetype['type'],
				'post_status'    => 'inherit',
				'post_title'     => __( 'Autodocs Publish logo', 'autodocs-publish' ),
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
		update_post_meta( $logo_id, '_wp_attachment_image_alt', __( 'Autodocs Publish', 'autodocs-publish' ) );
		update_post_meta( $logo_id, '_autodocs_publish_default_site_logo', '1' );
	}

	if ( $seed_logo || $replace_logo ) {
		update_option( 'site_logo', $logo_id );
	}
	if ( $seed_icon || $replace_icon ) {
		update_option( 'site_icon', $logo_id );
	}
	update_option( 'autodocs_publish_default_site_logo_seeded', $logo_id, false );
}
add_action( 'init', 'autodocs_publish_maybe_seed_default_site_logo', 20 );

/**
 * Enqueue the shared theme runtime.
 */
function autodocs_publish_assets() {
	$theme = wp_get_theme();

	wp_enqueue_style( 'autodocs-publish-style', get_stylesheet_uri(), array(), $theme->get( 'Version' ) );
	wp_enqueue_script(
		'autodocs-publish-navigation',
		get_theme_file_uri( 'assets/js/docs.js' ),
		array(),
		$theme->get( 'Version' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'autodocs_publish_assets' );

/**
 * Load styles for Autodocs Publish controls rendered in the editor interface.
 */
function autodocs_publish_block_editor_ui_assets() {
	$theme      = wp_get_theme();
	$style_path = get_theme_file_path( 'assets/css/block-editor.css' );
	$version    = is_readable( $style_path ) ? (string) filemtime( $style_path ) : $theme->get( 'Version' );

	wp_enqueue_style(
		'autodocs-publish-block-editor',
		get_theme_file_uri( 'assets/css/block-editor.css' ),
		array(),
		$version
	);
}
add_action( 'enqueue_block_editor_assets', 'autodocs_publish_block_editor_ui_assets' );

/**
 * Return a small inline icon.
 *
 * @param string $name Icon name.
 * @return string
 */
function autodocs_publish_icon( $name ) {
	$icons = array(
		'book'     => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 4.8c2.6-.7 4.9-.2 7 1.4v13c-2.1-1.6-4.4-2.1-7-1.4v-13Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M19 4.8c-2.6-.7-4.9-.2-7 1.4v13c2.1-1.6 4.4-2.1 7-1.4v-13Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>',
		'github'   => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.58 2 12.23c0 4.51 2.87 8.34 6.84 9.69.5.1.68-.22.68-.49 0-.24-.01-1.05-.01-1.9-2.78.62-3.37-1.2-3.37-1.2-.45-1.18-1.11-1.49-1.11-1.49-.91-.64.07-.62.07-.62 1 .07 1.53 1.06 1.53 1.06.9 1.57 2.35 1.12 2.92.85.09-.66.35-1.12.64-1.37-2.22-.26-4.55-1.14-4.55-5.07 0-1.12.39-2.04 1.03-2.76-.1-.26-.45-1.3.1-2.72 0 0 .84-.28 2.75 1.05A9.36 9.36 0 0 1 12 6.92c.85 0 1.69.12 2.49.34 1.91-1.33 2.75-1.05 2.75-1.05.55 1.42.2 2.46.1 2.72.64.72 1.03 1.64 1.03 2.76 0 3.94-2.34 4.8-4.57 5.06.36.32.68.94.68 1.9 0 1.37-.01 2.47-.01 2.8 0 .27.18.59.69.49A10.25 10.25 0 0 0 22 12.23C22 6.58 17.52 2 12 2Z"/></svg>',
		'menu'     => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
		'moon'     => '<svg class="theme-icon-dark" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 15.3A8.5 8.5 0 0 1 8.7 4a8.5 8.5 0 1 0 11.3 11.3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>',
		'pencil'   => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m14.7 5.3 4 4M5 19l2.1-5.1L16.6 4.4a1.4 1.4 0 0 1 2 0l1 1a1.4 1.4 0 0 1 0 2L10.1 17 5 19Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>',
		'search'   => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="6.5" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
		'sun'      => '<svg class="theme-icon-light" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="3.7" stroke="currentColor" stroke-width="1.7"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>',
		'thumbs-up' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8.5 10.2 12.1 3c.5-1 2-.6 2 .5v4.2h4.1c1.4 0 2.4 1.3 2 2.7l-1.6 6.5a2.5 2.5 0 0 1-2.4 1.9H8.5V10.2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M4 10.2h4.5v8.6H4v-8.6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>',
		'thumbs-down' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m8.5 13.8 3.6 7.2c.5 1 2 .6 2-.5v-4.2h4.1c1.4 0 2.4-1.3 2-2.7l-1.6-6.5a2.5 2.5 0 0 0-2.4-1.9H8.5v8.6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M4 13.8h4.5V5.2H4v8.6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>',
	);

	return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}

/**
 * Resolve a reusable documentation root slug to a Page ID.
 *
 * @param string $root_slug Page path or slug.
 * @return int
 */
function autodocs_publish_get_docs_root_id( $root_slug = 'docs' ) {
	$root_slug = trim( sanitize_text_field( (string) $root_slug ), '/' );
	if ( function_exists( 'autodocs_publish_blocks_versions_find_page' ) ) {
		$context = function_exists( 'autodocs_publish_blocks_versions_page_context' )
			? autodocs_publish_blocks_versions_page_context()
			: null;
		$version = $context && ! empty( $context['version'] )
			? $context['version']
			: autodocs_publish_blocks_versions_effective_slug();
		$root = $context && ! empty( $context['root'] ) ? $context['root'] : $root_slug;
		$page = $version ? autodocs_publish_blocks_versions_find_page( $version, '', $root ) : null;
		if ( $page instanceof WP_Post ) {
			return (int) $page->ID;
		}
	}

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
 * Keep the Site Editor Design and Styles canvases focused on the complete
 * documentation template instead of the homepage or an individual Page.
 */
function autodocs_publish_site_editor_preview_context() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'site-editor' !== $screen->id ) {
		return;
	}

	$theme       = wp_get_theme();
	$script_path = get_theme_file_path( 'assets/js/site-editor-preview.js' );
	$version     = is_readable( $script_path ) ? (string) filemtime( $script_path ) : $theme->get( 'Version' );

	wp_enqueue_script(
		'autodocs-publish-site-editor-preview',
		get_theme_file_uri( 'assets/js/site-editor-preview.js' ),
		array( 'wp-compose', 'wp-element', 'wp-hooks' ),
		$version,
		true
	);
	wp_add_inline_script(
		'autodocs-publish-site-editor-preview',
		'window.autodocsPublishSiteEditorPreview = ' . wp_json_encode(
			array(
				'archivePostId' => get_stylesheet() . '//archive',
				'postId'        => get_stylesheet() . '//page',
				'postType'      => 'wp_template',
			)
		) . ';',
		'before'
	);
}
add_action( 'enqueue_block_editor_assets', 'autodocs_publish_site_editor_preview_context' );

/**
 * Give a direct visit to the bare Site Editor URL a deterministic Design
 * preview before WordPress can restore another previously visited section.
 */
function autodocs_publish_redirect_site_editor_design_preview() {
	global $pagenow;

	if (
		'site-editor.php' !== $pagenow ||
		! current_user_can( 'edit_theme_options' ) ||
		isset( $_GET['p'] ) ||
		isset( $_GET['postType'] ) ||
		isset( $_GET['postId'] )
	) {
		return;
	}

	$url = add_query_arg(
		array(
			'p'        => '/',
			'postType' => 'wp_template',
			'postId'   => get_stylesheet() . '//page',
		),
		admin_url( 'site-editor.php' )
	);

	wp_safe_redirect( $url );
	exit;
}
add_action( 'admin_init', 'autodocs_publish_redirect_site_editor_design_preview' );

/**
 * Get published Pages within a documentation tree.
 *
 * @param string $root_slug Documentation root path.
 * @param string $sort      menu_order, title, newest, or oldest.
 * @return WP_Post[]
 */
function autodocs_publish_get_docs_pages( $root_slug = 'docs', $sort = 'menu_order' ) {
	static $page_cache = array();

	$root_id = autodocs_publish_get_docs_root_id( $root_slug );
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

	if ( function_exists( 'autodocs_publish_blocks_versions_page_context' ) ) {
		$context = autodocs_publish_blocks_versions_page_context();
		$version = $context && ! empty( $context['version'] )
			? $context['version']
			: autodocs_publish_blocks_versions_effective_slug();
		if ( $version ) {
			$pages = array_values(
				array_filter(
					$pages,
					static function ( $page ) use ( $version ) {
						return $version === sanitize_key( (string) get_post_meta( $page->ID, '_autodocs_publish_version_id', true ) );
					}
				)
			);
		}
	}

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
function autodocs_publish_group_pages_by_parent( $pages ) {
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
 * Read Autodocs Publish management metadata from a Page.
 *
 * @param int $post_id Page ID.
 * @return array<string,mixed>
 */
function autodocs_publish_get_managed_metadata( $post_id = 0 ) {
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
	if ( $content && preg_match( '/<!--\s*(?:autodocs-publish|docspress):(.*?)\s*-->/s', $content, $matches ) ) {
		$parsed = json_decode( trim( $matches[1] ), true );
		if ( is_array( $parsed ) && 1 === (int) ( isset( $parsed['version'] ) ? $parsed['version'] : 0 ) ) {
			$cache[ $post_id ] = $parsed;
		}
	}
	return $cache[ $post_id ];
}

/**
 * Return synchronization-owned sidebar metadata for a Page.
 *
 * Registered post meta is the fast path. The sentinel fallback keeps Pages
 * published before the matching theme metadata registration usable.
 *
 * @param int $post_id Page ID.
 * @return array{id:string,root:bool}
 */
function autodocs_publish_get_sidebar_metadata( $post_id = 0 ) {
	static $cache = array();

	$post_id = $post_id ? absint( $post_id ) : get_queried_object_id();
	if ( ! $post_id ) {
		return array( 'id' => '', 'root' => false );
	}
	if ( isset( $cache[ $post_id ] ) ) {
		return $cache[ $post_id ];
	}

	$id = sanitize_key( (string) get_post_meta( $post_id, '_autodocs_publish_sidebar_id', true ) );
	$root = metadata_exists( 'post', $post_id, '_autodocs_publish_sidebar_root' )
		? rest_sanitize_boolean( get_post_meta( $post_id, '_autodocs_publish_sidebar_root', true ) )
		: false;
	if ( ! $id ) {
		$managed = autodocs_publish_get_managed_metadata( $post_id );
		$id      = isset( $managed['sidebarId'] ) ? sanitize_key( (string) $managed['sidebarId'] ) : '';
		$root    = $id && ! empty( $managed['sidebarRoot'] );
	}

	$cache[ $post_id ] = array( 'id' => $id, 'root' => (bool) $root );
	return $cache[ $post_id ];
}

/**
 * Resolve the current Page's contextual sidebar and its root Page.
 *
 * @param int $post_id Page ID.
 * @return array{id:string,root_id:int}|null
 */
function autodocs_publish_get_sidebar_context( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_queried_object_id();
	$current = autodocs_publish_get_sidebar_metadata( $post_id );
	if ( ! $post_id || ! $current['id'] ) {
		return null;
	}

	$candidates = array_merge( array( $post_id ), get_post_ancestors( $post_id ) );
	foreach ( $candidates as $candidate_id ) {
		$metadata = autodocs_publish_get_sidebar_metadata( $candidate_id );
		if ( $metadata['id'] === $current['id'] && $metadata['root'] ) {
			return array( 'id' => $current['id'], 'root_id' => (int) $candidate_id );
		}
	}

	return null;
}

/**
 * Keep only Pages assigned to one source-configured sidebar.
 *
 * @param WP_Post[] $pages      Documentation Pages.
 * @param string    $sidebar_id Sidebar ID.
 * @return WP_Post[]
 */
function autodocs_publish_filter_pages_by_sidebar( $pages, $sidebar_id ) {
	$sidebar_id = sanitize_key( (string) $sidebar_id );
	if ( ! $sidebar_id ) {
		return $pages;
	}

	return array_values(
		array_filter(
			$pages,
			static function ( $page ) use ( $sidebar_id ) {
				$metadata = autodocs_publish_get_sidebar_metadata( $page->ID );
				return $metadata['id'] === $sidebar_id;
			}
		)
	);
}

/**
 * Return a Page's requested initial sidebar state.
 *
 * @param int $post_id Page ID.
 * @return bool|null
 */
function autodocs_publish_get_sidebar_collapsed( $post_id = 0 ) {
	$metadata = autodocs_publish_get_managed_metadata( $post_id );
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
function autodocs_publish_render_page_tree( $pages, $parent_id = 0, $root_id = 0, $level = 1, $max_depth = 0, $grouped = null ) {
	if ( $max_depth && $level > $max_depth ) {
		return;
	}

	$grouped = null === $grouped ? autodocs_publish_group_pages_by_parent( $pages ) : $grouped;
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
		$collapsed = autodocs_publish_get_sidebar_collapsed( $page->ID );
		printf(
			'<li data-doc-title="%1$s"><a href="%2$s"%3$s%4$s><span class="nav-dot" aria-hidden="true"></span><span>%5$s</span></a>',
			esc_attr( wp_strip_all_tags( $page->post_title ) ),
			esc_url( get_permalink( $page ) ),
			$current ? ' aria-current="page"' : '',
			null === $collapsed ? '' : ' data-sidebar-collapsed="' . ( $collapsed ? 'true' : 'false' ) . '"',
			esc_html( $page->post_title )
		);
		autodocs_publish_render_page_tree( $pages, (int) $page->ID, 0, $level + 1, $max_depth, $grouped );
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
function autodocs_publish_flatten_page_tree( $pages, $root_id = 0, $parent = 0, $level = 1, $max_depth = 0, $grouped = null ) {
	if ( $max_depth && $level > $max_depth ) {
		return array();
	}

	$flat     = array();
	$grouped  = null === $grouped ? autodocs_publish_group_pages_by_parent( $pages ) : $grouped;
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
		$flat   = array_merge( $flat, autodocs_publish_flatten_page_tree( $pages, 0, (int) $page->ID, $level + 1, $max_depth, $grouped ) );
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
function autodocs_publish_prepare_content( $content, $min_level = 2, $max_level = 3 ) {
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
function autodocs_publish_post_content_anchors( $block_content ) {
	return is_page() ? autodocs_publish_prepare_content( $block_content, 2, 6 )['content'] : $block_content;
}
add_filter( 'render_block_core/post-content', 'autodocs_publish_post_content_anchors' );

/**
 * Return version taxonomy terms when the site registers them.
 *
 * @return array{terms:WP_Term[],current:int}
 */
function autodocs_publish_get_versions() {
	if ( function_exists( 'autodocs_publish_blocks_versions_terms' ) ) {
		$terms = autodocs_publish_blocks_versions_terms();
		$context = function_exists( 'autodocs_publish_blocks_versions_page_context' ) ? autodocs_publish_blocks_versions_page_context() : null;
		$current = $context && ! empty( $context['term'] ) ? (int) $context['term']->term_id : 0;
		return array( 'terms' => $terms, 'current' => $current );
	}

	if ( ! taxonomy_exists( 'autodocs_publish_version' ) ) {
		return array( 'terms' => array(), 'current' => 0 );
	}

	$terms = get_terms( array( 'taxonomy' => 'autodocs_publish_version', 'hide_empty' => true ) );
	$terms = is_wp_error( $terms ) ? array() : $terms;
	$current_terms = is_page() ? wp_get_post_terms( get_queried_object_id(), 'autodocs_publish_version' ) : array();
	$current_id = ( $current_terms && ! is_wp_error( $current_terms ) ) ? (int) $current_terms[0]->term_id : 0;
	return array( 'terms' => $terms, 'current' => $current_id );
}

/**
 * Normalize a repository-relative Markdown source path.
 *
 * @param mixed $source Untrusted source path.
 * @return string
 */
function autodocs_publish_normalize_markdown_source_path( $source ) {
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
function autodocs_publish_get_markdown_source_path( $post_id = 0 ) {
	$post_id  = $post_id ? absint( $post_id ) : get_queried_object_id();
	$metadata = autodocs_publish_get_managed_metadata( $post_id );
	$source   = isset( $metadata['source'] ) ? $metadata['source'] : '';
	if ( ! $source && $post_id ) {
		$source = get_post_meta( $post_id, '_autodocs_publish_source_path', true );
	}
	$source = apply_filters( 'autodocs_publish_markdown_source_path', $source, $post_id, $metadata );
	$source = apply_filters_deprecated(
		'docspress_markdown_source_path',
		array( $source, $post_id, $metadata ),
		'0.11.0',
		'autodocs_publish_markdown_source_path'
	);
	return autodocs_publish_normalize_markdown_source_path( $source );
}

/**
 * Build a GitHub editor URL for the current Markdown source.
 *
 * @param int    $post_id   Page ID.
 * @param string $repository Repository URL.
 * @param string $ref        Branch or tag.
 * @return string
 */
function autodocs_publish_get_github_edit_url( $post_id = 0, $repository = 'https://github.com/Automattic/autodocs-publish', $ref = 'main' ) {
	$source = autodocs_publish_get_markdown_source_path( $post_id );
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
