<?php
/**
 * LLM-friendly documentation endpoints.
 *
 * @package DocsPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the public llms.txt and per-Page Markdown routes.
 */
function docspress_register_llms_rewrite_rules() {
	add_rewrite_rule( '^llms\.txt/?$', 'index.php?docspress_llms=1', 'top' );
	add_rewrite_rule( '^(.+)\.md/?$', 'index.php?docspress_markdown_path=$matches[1]', 'top' );
}
add_action( 'init', 'docspress_register_llms_rewrite_rules' );

/**
 * Make the rewrite variables available to WordPress.
 *
 * @param string[] $query_vars Public query variables.
 * @return string[]
 */
function docspress_llms_query_vars( $query_vars ) {
	$query_vars[] = 'docspress_llms';
	$query_vars[] = 'docspress_markdown_path';
	return $query_vars;
}
add_filter( 'query_vars', 'docspress_llms_query_vars' );

/**
 * Flush the routes once when the theme is activated.
 */
function docspress_flush_llms_rewrite_rules() {
	docspress_register_llms_rewrite_rules();
	flush_rewrite_rules();
	update_option( 'docspress_llms_rewrite_version', '1', false );
}
add_action( 'after_switch_theme', 'docspress_flush_llms_rewrite_rules' );

/**
 * Flush once after upgrading an already-active theme to the endpoint schema.
 */
function docspress_maybe_flush_llms_rewrite_rules() {
	$rewrite_version = '1';
	if ( $rewrite_version === get_option( 'docspress_llms_rewrite_version' ) ) {
		return;
	}

	flush_rewrite_rules( false );
	update_option( 'docspress_llms_rewrite_version', $rewrite_version, false );
}
add_action( 'init', 'docspress_maybe_flush_llms_rewrite_rules', 20 );

/**
 * Keep source metadata out of rendered HTML while retaining it in raw content.
 *
 * @param string $content Raw Page content.
 * @return string
 */
function docspress_strip_managed_source_metadata( $content ) {
	return preg_replace( '/<!--\s*docspress:.*?\s*-->\s*/s', '', (string) $content, 1 );
}
add_filter( 'the_content', 'docspress_strip_managed_source_metadata', 1 );

/**
 * Decode the original Markdown synchronized with a published Page.
 *
 * Null means the Page has no source-owned Markdown. An empty string is a valid
 * empty source file.
 *
 * @param int $post_id Page ID.
 * @return string|null
 */
function docspress_get_markdown_source_content( $post_id ) {
	$post_id = absint( $post_id );
	if ( ! $post_id || 'page' !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
		return null;
	}

	if ( ! docspress_get_markdown_source_path( $post_id ) ) {
		return null;
	}

	$metadata = docspress_get_managed_metadata( $post_id );
	if ( ! isset( $metadata['sourceContentBase64'] ) || ! is_string( $metadata['sourceContentBase64'] ) ) {
		return null;
	}

	$markdown = base64_decode( $metadata['sourceContentBase64'], true );
	if ( false === $markdown ) {
		return null;
	}

	return wp_check_invalid_utf8( $markdown, true );
}

/**
 * Return managed documentation Pages in automatic sidebar order.
 *
 * @return WP_Post[]
 */
function docspress_get_llms_pages() {
	$pages   = docspress_get_docs_pages();
	$root_id = docspress_get_docs_root_id();
	$ordered = $root_id
		? docspress_flatten_page_tree( $pages, $root_id )
		: docspress_flatten_page_tree( $pages );

	return array_values(
		array_filter(
			$ordered,
			static function ( $page ) {
				return null !== docspress_get_markdown_source_content( $page->ID );
			}
		)
	);
}

/**
 * Escape one line of user-controlled text for Markdown.
 *
 * @param mixed $value Text value.
 * @return string
 */
function docspress_llms_escape_text( $value ) {
	$value = wp_specialchars_decode( wp_strip_all_tags( (string) $value ), ENT_QUOTES );
	$value = preg_replace( '/\s+/u', ' ', trim( $value ) );
	return str_replace( array( '\\', '[', ']' ), array( '\\\\', '\[', '\]' ), $value );
}

/**
 * Return the public .md URL for a Page.
 *
 * @param WP_Post $page Documentation Page.
 * @return string
 */
function docspress_get_markdown_url( $page ) {
	$path = trim( get_page_uri( $page ), '/' );
	if ( ! $path ) {
		return '';
	}

	return home_url( '/' . $path . '.md' );
}

/**
 * Build the llms.txt index body.
 *
 * @return string
 */
function docspress_build_llms_txt() {
	$title   = docspress_llms_escape_text( get_bloginfo( 'name' ) );
	$summary = docspress_llms_escape_text( get_bloginfo( 'description' ) );
	$pages   = docspress_get_llms_pages();
	$lines   = array(
		'# ' . ( $title ? $title : 'Documentation' ),
		'',
		'> ' . ( $summary ? $summary : 'Documentation published from Markdown with DocsPress.' ),
		'',
		'## Documentation',
		'',
	);

	foreach ( $pages as $page ) {
		$url = docspress_get_markdown_url( $page );
		if ( ! $url ) {
			continue;
		}

		$line        = '- [' . docspress_llms_escape_text( get_the_title( $page ) ) . '](' . esc_url_raw( $url ) . ')';
		$description = docspress_llms_escape_text( get_post_field( 'post_excerpt', $page->ID ) );
		if ( $description ) {
			$line .= ': ' . $description;
		}
		$lines[] = $line;
	}

	/**
	 * Filter the generated llms.txt response.
	 *
	 * @param string    $content Generated llms.txt content.
	 * @param WP_Post[] $pages   Source-backed documentation Pages.
	 */
	return (string) apply_filters( 'docspress_llms_txt', implode( "\n", $lines ) . "\n", $pages );
}

/**
 * Send a UTF-8 text response and stop the normal theme render.
 *
 * @param string $content      Response body.
 * @param string $content_type MIME type.
 * @param int    $status       HTTP status.
 */
function docspress_send_llms_response( $content, $content_type, $status = 200 ) {
	status_header( $status );
	header( 'Content-Type: ' . $content_type . '; charset=utf-8' );
	header( 'X-Content-Type-Options: nosniff' );
	echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Source Markdown and generated plain text are the complete response body.
	exit;
}

/**
 * Resolve LLM-friendly requests before WordPress renders an HTML template.
 */
function docspress_render_llms_endpoint() {
	if ( get_query_var( 'docspress_llms' ) ) {
		docspress_send_llms_response( docspress_build_llms_txt(), 'text/plain' );
	}

	$requested_path = get_query_var( 'docspress_markdown_path' );
	if ( ! is_string( $requested_path ) || '' === $requested_path ) {
		return;
	}

	$requested_path = trim( rawurldecode( wp_unslash( $requested_path ) ), '/' );
	if ( ! $requested_path || false !== strpos( $requested_path, "\0" ) ) {
		docspress_send_llms_response( "Not found.\n", 'text/plain', 404 );
	}

	foreach ( explode( '/', $requested_path ) as $segment ) {
		if ( '' === $segment || '.' === $segment || '..' === $segment ) {
			docspress_send_llms_response( "Not found.\n", 'text/plain', 404 );
		}
	}

	$page     = get_page_by_path( $requested_path, OBJECT, 'page' );
	$markdown = $page instanceof WP_Post ? docspress_get_markdown_source_content( $page->ID ) : null;
	if ( null === $markdown ) {
		docspress_send_llms_response( "Not found.\n", 'text/plain', 404 );
	}

	docspress_send_llms_response( $markdown, 'text/markdown' );
}
add_action( 'template_redirect', 'docspress_render_llms_endpoint', 0 );
