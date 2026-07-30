<?php
/**
 * Documentation version taxonomy, routing, settings, and administration.
 *
 * @package DocsPressBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const DOCSPRESS_VERSION_TAXONOMY = 'docspress_version';
const DOCSPRESS_VERSION_REST_BASE = 'docspress_versions';

/**
 * Register the version taxonomy and synchronization-owned metadata.
 */
function docspress_blocks_versions_register() {
	register_taxonomy(
		DOCSPRESS_VERSION_TAXONOMY,
		array( 'page' ),
		array(
			'labels'            => array(
				'name'          => __( 'Documentation versions', 'docspress-blocks' ),
				'singular_name' => __( 'Documentation version', 'docspress-blocks' ),
			),
			'public'            => false,
			'show_ui'            => true,
			'show_admin_column'  => false,
			'show_in_quick_edit' => false,
			'show_in_rest'      => true,
			'rest_base'         => DOCSPRESS_VERSION_REST_BASE,
			'hierarchical'      => false,
			'meta_box_cb'       => false,
			'rewrite'           => false,
			'query_var'         => true,
		)
	);

	$term_meta = array(
		'docspress_version_order'               => array( 'type' => 'integer', 'default' => 0 ),
		'docspress_version_active'              => array( 'type' => 'boolean', 'default' => false ),
		'docspress_version_repository_latest'   => array( 'type' => 'boolean', 'default' => false ),
		'docspress_version_effective_latest'    => array( 'type' => 'boolean', 'default' => false ),
	);
	foreach ( $term_meta as $key => $schema ) {
		register_term_meta(
			DOCSPRESS_VERSION_TAXONOMY,
			$key,
			array(
				'type'              => $schema['type'],
				'single'            => true,
				'default'           => $schema['default'],
				'sanitize_callback' => 'integer' === $schema['type'] ? 'docspress_blocks_versions_sanitize_integer' : 'rest_sanitize_boolean',
				'auth_callback'     => static function () {
					return current_user_can( 'manage_categories' );
				},
				'show_in_rest'      => true,
			)
		);
	}

	$post_meta = array(
		'_docspress_version_id',
		'_docspress_logical_route',
		'_docspress_page_identity',
		'_docspress_source_type',
		'_docspress_source_path',
		'_docspress_github_path',
		'_docspress_github_repository',
		'_docspress_github_ref',
		'_docspress_github_server_url',
		'_docspress_docs_root',
	);
	foreach ( $post_meta as $key ) {
		register_post_meta(
			'page',
			$key,
			array(
				'type'              => 'string',
				'single'            => true,
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => static function ( $allowed, $meta_key, $post_id ) {
					return current_user_can( 'edit_post', (int) $post_id );
				},
				'show_in_rest'      => true,
			)
		);
	}
	register_post_meta(
		'page',
		'_docspress_version_container',
		array(
			'type'              => 'boolean',
			'single'            => true,
			'default'           => false,
			'sanitize_callback' => 'rest_sanitize_boolean',
			'auth_callback'     => static function ( $allowed, $meta_key, $post_id ) {
				return current_user_can( 'edit_post', (int) $post_id );
			},
			'show_in_rest'      => true,
		)
	);

	register_setting(
		'docspress_versions_repository',
		'docspress_repository_latest_version',
		array(
			'type'              => 'string',
			'default'           => '',
			'sanitize_callback' => 'sanitize_key',
			'show_in_rest'      => true,
		)
	);
	register_setting(
		'docspress_versions',
		'docspress_version_override',
		array(
			'type'              => 'string',
			'default'           => '',
			'sanitize_callback' => 'docspress_blocks_versions_sanitize_override',
			'show_in_rest'      => true,
		)
	);
	register_setting(
		'docspress_versions_repository',
		'docspress_docs_root_slug',
		array(
			'type'              => 'string',
			'default'           => 'docs',
			'sanitize_callback' => 'sanitize_title',
			'show_in_rest'      => true,
		)
	);
}
add_action( 'init', 'docspress_blocks_versions_register', 6 );

/**
 * Sanitize integer metadata while accepting WordPress's full meta callback signature.
 *
 * @param mixed $value Submitted value.
 * @return int
 */
function docspress_blocks_versions_sanitize_integer( $value ) {
	return (int) $value;
}

/**
 * Sanitize an administrator-selected version override.
 *
 * @param mixed $value Submitted value.
 * @return string
 */
function docspress_blocks_versions_sanitize_override( $value ) {
	$slug = sanitize_key( (string) $value );
	if ( '' === $slug ) {
		return '';
	}
	$term = get_term_by( 'slug', $slug, DOCSPRESS_VERSION_TAXONOMY );
	return $term instanceof WP_Term && get_term_meta( $term->term_id, 'docspress_version_active', true ) ? $slug : '';
}

/**
 * Return configured version terms in registry order.
 *
 * @param bool $active_only Exclude inactive terms.
 * @return WP_Term[]
 */
function docspress_blocks_versions_terms( $active_only = true ) {
	if ( ! taxonomy_exists( DOCSPRESS_VERSION_TAXONOMY ) ) {
		return array();
	}

	$terms = get_terms(
		array(
			'taxonomy'   => DOCSPRESS_VERSION_TAXONOMY,
			'hide_empty' => false,
		)
	);
	if ( is_wp_error( $terms ) ) {
		return array();
	}

	$terms = array_values(
		array_filter(
			$terms,
			static function ( $term ) use ( $active_only ) {
				return ! $active_only || rest_sanitize_boolean( get_term_meta( $term->term_id, 'docspress_version_active', true ) );
			}
		)
	);
	usort(
		$terms,
		static function ( $left, $right ) {
			$order = (int) get_term_meta( $left->term_id, 'docspress_version_order', true )
				<=> (int) get_term_meta( $right->term_id, 'docspress_version_order', true );
			return 0 !== $order ? $order : strcasecmp( $left->name, $right->name );
		}
	);
	return $terms;
}

/**
 * Return the effective latest version slug.
 *
 * @return string
 */
function docspress_blocks_versions_effective_slug() {
	$terms = docspress_blocks_versions_terms();
	$active = wp_list_pluck( $terms, 'slug' );
	$override = sanitize_key( (string) get_option( 'docspress_version_override', '' ) );
	if ( $override && in_array( $override, $active, true ) ) {
		return (string) apply_filters( 'docspress_effective_latest_version', $override, $terms );
	}

	$repository = sanitize_key( (string) get_option( 'docspress_repository_latest_version', '' ) );
	if ( $repository && in_array( $repository, $active, true ) ) {
		return (string) apply_filters( 'docspress_effective_latest_version', $repository, $terms );
	}

	foreach ( $terms as $term ) {
		if ( rest_sanitize_boolean( get_term_meta( $term->term_id, 'docspress_version_repository_latest', true ) ) ) {
			return (string) apply_filters( 'docspress_effective_latest_version', $term->slug, $terms );
		}
	}

	return $terms ? (string) apply_filters( 'docspress_effective_latest_version', $terms[0]->slug, $terms ) : '';
}

/**
 * Keep the effective-latest term flag singular after settings changes.
 */
function docspress_blocks_versions_refresh_latest_flags() {
	$latest = docspress_blocks_versions_effective_slug();
	foreach ( docspress_blocks_versions_terms( false ) as $term ) {
		$expected = $latest && $term->slug === $latest && rest_sanitize_boolean( get_term_meta( $term->term_id, 'docspress_version_active', true ) );
		$current  = rest_sanitize_boolean( get_term_meta( $term->term_id, 'docspress_version_effective_latest', true ) );
		if ( $expected !== $current ) {
			update_term_meta( $term->term_id, 'docspress_version_effective_latest', $expected );
		}
	}
}
add_action( 'update_option_docspress_version_override', 'docspress_blocks_versions_after_latest_change' );
add_action( 'update_option_docspress_repository_latest_version', 'docspress_blocks_versions_after_latest_change' );
add_action( 'add_option_docspress_version_override', 'docspress_blocks_versions_after_latest_change' );
add_action( 'add_option_docspress_repository_latest_version', 'docspress_blocks_versions_after_latest_change' );

/**
 * Refresh latest state and routes after a repository or administrator change.
 */
function docspress_blocks_versions_after_latest_change() {
	docspress_blocks_versions_refresh_latest_flags();
	docspress_blocks_versions_flush_rules();
}

/**
 * Return version context for one managed Page.
 *
 * @param int $post_id Page ID.
 * @return array<string,mixed>|null
 */
function docspress_blocks_versions_page_context( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_queried_object_id();
	$version = sanitize_key( (string) get_post_meta( $post_id, '_docspress_version_id', true ) );
	if ( ! $post_id || ! $version ) {
		return null;
	}

	$term = get_term_by( 'slug', $version, DOCSPRESS_VERSION_TAXONOMY );
	if ( ! $term instanceof WP_Term ) {
		return null;
	}

	return array(
		'post_id'       => $post_id,
		'version'       => $version,
		'label'         => $term->name,
		'term'          => $term,
		'logical_route' => trim( (string) get_post_meta( $post_id, '_docspress_logical_route', true ), '/' ),
		'root'          => sanitize_title( (string) get_post_meta( $post_id, '_docspress_docs_root', true ) ) ?: sanitize_title( (string) get_option( 'docspress_docs_root_slug', 'docs' ) ),
		'latest'        => $version === docspress_blocks_versions_effective_slug(),
	);
}

/**
 * Find a Page in a version by its logical route.
 *
 * @param string $version Version slug.
 * @param string $logical_route Logical route.
 * @param string $root Docs root.
 * @return WP_Post|null
 */
function docspress_blocks_versions_find_page( $version, $logical_route, $root = '' ) {
	$root = $root ? sanitize_title( $root ) : sanitize_title( (string) get_option( 'docspress_docs_root_slug', 'docs' ) );
	$pages = get_posts(
		array(
			'post_type'              => 'page',
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'suppress_filters'       => false,
			'update_post_meta_cache' => true,
			'meta_query'             => array(
				'relation' => 'AND',
				array(
					'key'   => '_docspress_version_id',
					'value' => sanitize_key( $version ),
				),
				array(
					'key'   => '_docspress_logical_route',
					'value' => trim( (string) $logical_route, '/' ),
				),
				array(
					'key'   => '_docspress_docs_root',
					'value' => $root,
				),
			),
		)
	);
	return $pages ? $pages[0] : null;
}

/**
 * Return a version-aware public URL for a managed Page.
 *
 * @param int $post_id Page ID.
 * @return string
 */
function docspress_blocks_versions_page_url( $post_id ) {
	$context = docspress_blocks_versions_page_context( $post_id );
	if ( ! $context ) {
		return get_permalink( $post_id );
	}

	$segments = array( rawurlencode( $context['root'] ) );
	if ( ! $context['latest'] ) {
		$segments[] = rawurlencode( $context['version'] );
	}
	if ( $context['logical_route'] ) {
		$route_segments = array_filter( explode( '/', $context['logical_route'] ), 'strlen' );
		$segments = array_merge( $segments, array_map( 'rawurlencode', $route_segments ) );
	}
	$url = home_url( '/' . implode( '/', $segments ) . '/' );
	return (string) apply_filters( 'docspress_version_target_url', $url, $context );
}

/**
 * Filter managed Page permalinks to their public canonical route.
 *
 * @param string $link Page permalink.
 * @param int    $post_id Page ID.
 * @return string
 */
function docspress_blocks_versions_page_link( $link, $post_id ) {
	return docspress_blocks_versions_page_context( $post_id )
		? docspress_blocks_versions_page_url( $post_id )
		: $link;
}
add_filter( 'page_link', 'docspress_blocks_versions_page_link', 10, 2 );

/**
 * Convert stable internal version links to the effective public routes at render time.
 *
 * @param string $content Rendered Page content.
 * @return string
 */
function docspress_blocks_versions_content_links( $content ) {
	$context = docspress_blocks_versions_page_context();
	if ( ! $context || ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return $content;
	}

	$processor = new WP_HTML_Tag_Processor( $content );
	$versions  = wp_list_pluck( docspress_blocks_versions_terms(), 'slug' );
	$prefix    = '/' . trim( $context['root'], '/' ) . '/';
	while ( $processor->next_tag( 'a' ) ) {
		$href = (string) $processor->get_attribute( 'href' );
		if ( ! $href || 0 !== strpos( $href, $prefix ) ) {
			continue;
		}
		$parts = wp_parse_url( $href );
		$path  = isset( $parts['path'] ) ? trim( rawurldecode( $parts['path'] ), '/' ) : '';
		$route = explode( '/', $path );
		if ( count( $route ) < 2 || $route[0] !== $context['root'] || ! in_array( $route[1], $versions, true ) ) {
			continue;
		}
		$version = $route[1];
		$logical = implode( '/', array_slice( $route, 2 ) );
		$page    = docspress_blocks_versions_find_page( $version, $logical, $context['root'] );
		if ( ! $page ) {
			continue;
		}
		$url = docspress_blocks_versions_page_url( $page->ID );
		if ( ! empty( $parts['query'] ) ) {
			$url .= '?' . $parts['query'];
		}
		if ( ! empty( $parts['fragment'] ) ) {
			$url .= '#' . rawurlencode( $parts['fragment'] );
		}
		$processor->set_attribute( 'href', $url );
	}
	return $processor->get_updated_html();
}
add_filter( 'the_content', 'docspress_blocks_versions_content_links', 20 );

/**
 * Register clean latest and explicit historical routes.
 */
function docspress_blocks_versions_rewrite_rules() {
	$root = sanitize_title( (string) get_option( 'docspress_docs_root_slug', 'docs' ) );
	if ( ! $root || ! docspress_blocks_versions_terms() ) {
		return;
	}
	$quoted = preg_quote( $root, '#' );
	$rules = array(
		'^' . $quoted . '\.md/?$'       => 'index.php?docspress_version_route=__root__&docspress_version_markdown=1',
		'^' . $quoted . '/(.+)\.md/?$'  => 'index.php?docspress_version_route=$matches[1]&docspress_version_markdown=1',
		'^' . $quoted . '/?$'           => 'index.php?docspress_version_route=__root__',
		'^' . $quoted . '/(.+?)/?$'     => 'index.php?docspress_version_route=$matches[1]',
	);
	foreach ( $rules as $regex => $query ) {
		add_rewrite_rule( $regex, $query, 'top' );
	}

	// Keep version-specific Markdown routes ahead of the theme's generic
	// `*.md` endpoint even when terms are first created after `init`.
	global $wp_rewrite;
	if ( $wp_rewrite instanceof WP_Rewrite ) {
		$wp_rewrite->extra_rules_top = array_merge(
			$rules,
			array_diff_key( $wp_rewrite->extra_rules_top, $rules )
		);
	}
}
add_action( 'init', 'docspress_blocks_versions_rewrite_rules', 7 );

/**
 * Add version routing query variables.
 *
 * @param string[] $query_vars Query vars.
 * @return string[]
 */
function docspress_blocks_versions_query_vars( $query_vars ) {
	$query_vars[] = 'docspress_version_route';
	$query_vars[] = 'docspress_version_markdown';
	return $query_vars;
}
add_filter( 'query_vars', 'docspress_blocks_versions_query_vars' );

/**
 * Resolve clean and explicit version routes to stable internal Pages.
 *
 * @param WP $wp Request object.
 */
function docspress_blocks_versions_parse_request( $wp ) {
	if ( ! array_key_exists( 'docspress_version_route', $wp->query_vars ) ) {
		return;
	}

	$route = trim( rawurldecode( (string) $wp->query_vars['docspress_version_route'] ), '/' );
	$route = '__root__' === $route ? '' : $route;
	if ( false !== strpos( $route, "\0" ) || preg_match( '#(?:^|/)\.{1,2}(?:/|$)#', $route ) ) {
		return;
	}

	$terms = docspress_blocks_versions_terms();
	$slugs = wp_list_pluck( $terms, 'slug' );
	$parts = $route ? explode( '/', $route ) : array();
	$explicit = $parts && in_array( $parts[0], $slugs, true ) ? array_shift( $parts ) : '';
	$version = $explicit ? $explicit : docspress_blocks_versions_effective_slug();
	$logical_route = implode( '/', $parts );
	if ( ! $explicit ) {
		$logical_route = $route;
	}
	$root = sanitize_title( (string) get_option( 'docspress_docs_root_slug', 'docs' ) );
	$page = $version ? docspress_blocks_versions_find_page( $version, $logical_route, $root ) : null;
	if ( ! $page ) {
		return;
	}

	unset( $wp->query_vars['name'], $wp->query_vars['pagename'] );
	$wp->query_vars['page_id'] = $page->ID;
	$GLOBALS['docspress_version_request'] = array(
		'page_id'       => $page->ID,
		'version'       => $version,
		'explicit'      => $explicit,
		'logical_route' => $logical_route,
		'root'          => $root,
		'markdown'      => ! empty( $wp->query_vars['docspress_version_markdown'] ),
	);
}
add_action( 'parse_request', 'docspress_blocks_versions_parse_request', 5 );

/**
 * Avoid core canonical redirects fighting the virtual latest route.
 *
 * @param string|false $redirect Redirect URL.
 * @return string|false
 */
function docspress_blocks_versions_disable_core_canonical( $redirect ) {
	return isset( $GLOBALS['docspress_version_request'] ) ? false : $redirect;
}
add_filter( 'redirect_canonical', 'docspress_blocks_versions_disable_core_canonical' );

/**
 * Redirect explicit latest routes and serve source Markdown.
 */
function docspress_blocks_versions_template_redirect() {
	if ( empty( $GLOBALS['docspress_version_request'] ) ) {
		return;
	}
	$request = $GLOBALS['docspress_version_request'];
	$latest = docspress_blocks_versions_effective_slug();

	if ( $request['explicit'] && $request['explicit'] === $latest ) {
		$target = docspress_blocks_versions_page_url( $request['page_id'] );
		if ( ! empty( $request['markdown'] ) ) {
			$target = untrailingslashit( $target ) . '.md';
		}
		wp_safe_redirect( $target, 301, 'DocsPress' );
		exit;
	}

	if ( ! empty( $request['markdown'] ) ) {
		$markdown = function_exists( 'docspress_get_markdown_source_content' )
			? docspress_get_markdown_source_content( $request['page_id'] )
			: null;
		if ( null === $markdown ) {
			$markdown = docspress_blocks_versions_source_markdown( $request['page_id'] );
		}
		if ( null === $markdown ) {
			status_header( 404 );
			$markdown = "Not found.\n";
		}
		header( 'Content-Type: text/markdown; charset=utf-8' );
		header( 'X-Content-Type-Options: nosniff' );
		echo $markdown; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Markdown is the complete response body.
		exit;
	}

}
add_action( 'template_redirect', 'docspress_blocks_versions_template_redirect', -5 );

/**
 * Decode source Markdown from a Page sentinel when the DocsPress theme is absent.
 *
 * @param int $post_id Page ID.
 * @return string|null
 */
function docspress_blocks_versions_source_markdown( $post_id ) {
	$content = (string) get_post_field( 'post_content', $post_id );
	if ( ! preg_match( '/<!--\s*docspress:(.*?)\s*-->/s', $content, $match ) ) {
		return null;
	}
	$metadata = json_decode( $match[1], true );
	if ( ! is_array( $metadata ) || ! isset( $metadata['sourceContentBase64'] ) ) {
		return null;
	}
	$markdown = base64_decode( (string) $metadata['sourceContentBase64'], true );
	return false === $markdown ? null : wp_check_invalid_utf8( $markdown, true );
}

/**
 * Flush routes after activation or a docs-root change.
 */
function docspress_blocks_versions_flush_rules() {
	docspress_blocks_versions_rewrite_rules();
	flush_rewrite_rules( false );
}
register_activation_hook( DOCSPRESS_BLOCKS_FILE, 'docspress_blocks_versions_flush_rules' );
add_action( 'update_option_docspress_docs_root_slug', 'docspress_blocks_versions_flush_rules' );
add_action( 'add_option_docspress_docs_root_slug', 'docspress_blocks_versions_flush_rules' );
add_action( 'rest_after_insert_' . DOCSPRESS_VERSION_TAXONOMY, 'docspress_blocks_versions_flush_rules' );

/**
 * Add the DocsPress version settings screen.
 */
function docspress_blocks_versions_settings_menu() {
	add_options_page(
		__( 'DocsPress versions', 'docspress-blocks' ),
		__( 'DocsPress', 'docspress-blocks' ),
		'manage_options',
		'docspress-versions',
		'docspress_blocks_versions_settings_page'
	);
}
add_action( 'admin_menu', 'docspress_blocks_versions_settings_menu' );

/**
 * Render the version settings screen.
 */
function docspress_blocks_versions_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$terms = docspress_blocks_versions_terms();
	$repository = sanitize_key( (string) get_option( 'docspress_repository_latest_version', '' ) );
	$override = sanitize_key( (string) get_option( 'docspress_version_override', '' ) );
	$effective = docspress_blocks_versions_effective_slug();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'DocsPress versions', 'docspress-blocks' ); ?></h1>
		<p><?php esc_html_e( 'The repository supplies the default API version. Choose an override to change the public latest version immediately without moving Pages.', 'docspress-blocks' ); ?></p>
		<table class="widefat striped" style="max-width:760px;margin:20px 0">
			<tbody>
				<tr><th><?php esc_html_e( 'Repository default', 'docspress-blocks' ); ?></th><td><code><?php echo esc_html( $repository ?: '—' ); ?></code></td></tr>
				<tr><th><?php esc_html_e( 'Effective latest', 'docspress-blocks' ); ?></th><td><strong><?php echo esc_html( $effective ?: '—' ); ?></strong></td></tr>
			</tbody>
		</table>
		<form method="post" action="options.php">
			<?php settings_fields( 'docspress_versions' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="docspress-version-override"><?php esc_html_e( 'Latest-version override', 'docspress-blocks' ); ?></label></th>
					<td>
						<select id="docspress-version-override" name="docspress_version_override">
							<option value=""><?php esc_html_e( 'Use repository default', 'docspress-blocks' ); ?></option>
							<?php foreach ( $terms as $term ) : ?>
								<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $override, $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Determine whether the site has synchronized Pages with GitHub source paths.
 *
 * @return bool
 */
function docspress_blocks_versions_has_github_paths() {
	static $has_paths = null;
	if ( null !== $has_paths ) {
		return $has_paths;
	}
	$ids = get_posts(
		array(
			'post_type'              => 'page',
			'post_status'            => 'any',
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'meta_key'               => '_docspress_github_path',
			'meta_compare'           => 'EXISTS',
			'no_found_rows'          => true,
			'suppress_filters'       => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);
	$has_paths = ! empty( $ids );
	return $has_paths;
}

/**
 * Add DocsPress management columns to the Pages screen.
 *
 * @param array<string,string> $columns Columns.
 * @return array<string,string>
 */
function docspress_blocks_versions_page_columns( $columns ) {
	$columns['docspress_version'] = __( 'Docs version', 'docspress-blocks' );
	if ( docspress_blocks_versions_has_github_paths() ) {
		$columns['docspress_github_path'] = __( 'GitHub path', 'docspress-blocks' );
	}
	return $columns;
}
add_filter( 'manage_pages_columns', 'docspress_blocks_versions_page_columns' );

/**
 * Resolve a managed Page's exact GitHub source.
 *
 * @param int $post_id Page ID.
 * @return array{path:string,url:string}|null
 */
function docspress_blocks_versions_github_source( $post_id ) {
	$source = array(
		'path'       => (string) get_post_meta( $post_id, '_docspress_github_path', true ),
		'repository' => (string) get_post_meta( $post_id, '_docspress_github_repository', true ),
		'ref'        => (string) get_post_meta( $post_id, '_docspress_github_ref', true ),
		'server_url' => (string) get_post_meta( $post_id, '_docspress_github_server_url', true ),
	);
	/**
	 * Filter the GitHub source metadata used by the Pages column.
	 *
	 * @param array{path:string,repository:string,ref:string,server_url:string} $source Source metadata.
	 * @param int                                                               $post_id Page ID.
	 */
	$source = apply_filters( 'docspress_github_source', $source, $post_id );
	if ( ! is_array( $source ) ) {
		return null;
	}

	$path = isset( $source['path'] ) ? trim( str_replace( '\\', '/', wp_strip_all_tags( (string) $source['path'] ) ) ) : '';
	if ( ! preg_match( '/\.(?:md|markdown|mdx)$/i', $path ) || '/' === substr( $path, 0, 1 ) || false !== strpos( $path, ':' ) || false !== strpos( $path, "\0" ) ) {
		return null;
	}
	foreach ( explode( '/', $path ) as $segment ) {
		if ( '' === $segment || '.' === $segment || '..' === $segment ) {
			return null;
		}
	}

	$repository = isset( $source['repository'] ) ? trim( (string) $source['repository'] ) : '';
	if ( ! preg_match( '#^[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+$#', $repository ) ) {
		return null;
	}
	$ref = isset( $source['ref'] ) ? trim( (string) $source['ref'] ) : 'main';
	if ( ! preg_match( '#^[A-Za-z0-9._/-]+$#', $ref ) || false !== strpos( $ref, '..' ) ) {
		return null;
	}
	$server_url = isset( $source['server_url'] ) ? untrailingslashit( esc_url_raw( (string) $source['server_url'] ) ) : '';
	$parts      = wp_parse_url( $server_url );
	if (
		! $server_url ||
		! is_array( $parts ) ||
		empty( $parts['scheme'] ) ||
		empty( $parts['host'] ) ||
		! in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true ) ||
		isset( $parts['user'] ) ||
		isset( $parts['pass'] ) ||
		isset( $parts['query'] ) ||
		isset( $parts['fragment'] )
	) {
		return null;
	}

	$url = $server_url . '/' . $repository . '/blob/' . rawurlencode( $ref ) . '/' . implode( '/', array_map( 'rawurlencode', explode( '/', $path ) ) );
	/**
	 * Filter the final GitHub source URL for a managed Page.
	 *
	 * @param string $url     GitHub source URL.
	 * @param string $path    Repository-relative source path.
	 * @param int    $post_id Page ID.
	 */
	$url = (string) apply_filters( 'docspress_github_source_url', $url, $path, $post_id );
	if ( ! $url ) {
		return null;
	}
	return array(
		'path' => $path,
		'url'  => $url,
	);
}

/**
 * Render the DocsPress Page columns.
 *
 * @param string $column Column name.
 * @param int    $post_id Page ID.
 */
function docspress_blocks_versions_page_column( $column, $post_id ) {
	if ( 'docspress_github_path' === $column ) {
		$source = docspress_blocks_versions_github_source( $post_id );
		if ( ! $source ) {
			echo '<span aria-hidden="true">—</span>';
			return;
		}
		printf(
			'<a href="%1$s" target="_blank" rel="noopener noreferrer" aria-label="%2$s"><code>%3$s</code><span class="screen-reader-text"> %4$s</span></a>',
			esc_url( $source['url'] ),
			esc_attr( sprintf( __( 'Open %s on GitHub in a new tab', 'docspress-blocks' ), $source['path'] ) ),
			esc_html( $source['path'] ),
			esc_html__( '(opens in a new tab)', 'docspress-blocks' )
		);
		return;
	}
	if ( 'docspress_version' !== $column ) {
		return;
	}
	$context = docspress_blocks_versions_page_context( $post_id );
	if ( ! $context ) {
		if ( get_post_meta( $post_id, '_docspress_version_container', true ) ) {
			echo '<span class="post-state">' . esc_html__( 'All versions', 'docspress-blocks' ) . '</span>';
			return;
		}
		echo '<span aria-hidden="true">—</span>';
		return;
	}
	$url = add_query_arg(
		array(
			'post_type'               => 'page',
			'docspress_docs_version'  => $context['version'],
		),
		admin_url( 'edit.php' )
	);
	printf(
		'<a href="%1$s"><strong>%2$s</strong></a>%3$s',
		esc_url( $url ),
		esc_html( $context['label'] ),
		$context['latest'] ? ' <span class="post-state">' . esc_html__( 'Latest', 'docspress-blocks' ) . '</span>' : ''
	);
	if ( ! rest_sanitize_boolean( get_term_meta( $context['term']->term_id, 'docspress_version_active', true ) ) ) {
		echo ' <span class="post-state">' . esc_html__( 'Inactive', 'docspress-blocks' ) . '</span>';
	}
}
add_action( 'manage_pages_custom_column', 'docspress_blocks_versions_page_column', 10, 2 );

/**
 * Make the version column sortable.
 *
 * @param array<string,string> $columns Sortable columns.
 * @return array<string,string>
 */
function docspress_blocks_versions_sortable_columns( $columns ) {
	$columns['docspress_version'] = 'docspress_version';
	return $columns;
}
add_filter( 'manage_edit-page_sortable_columns', 'docspress_blocks_versions_sortable_columns' );

/**
 * Render the version filter above the Pages table.
 */
function docspress_blocks_versions_admin_filter() {
	global $typenow;
	if ( 'page' !== $typenow ) {
		return;
	}
	$current = isset( $_GET['docspress_docs_version'] ) ? sanitize_key( wp_unslash( $_GET['docspress_docs_version'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$terms   = docspress_blocks_versions_terms( false );
	$all_count = array_sum( wp_list_pluck( $terms, 'count' ) );
	$latest_slug = docspress_blocks_versions_effective_slug();
	$latest_term = $latest_slug ? get_term_by( 'slug', $latest_slug, DOCSPRESS_VERSION_TAXONOMY ) : null;
	?>
	<label class="screen-reader-text" for="docspress-docs-version-filter"><?php esc_html_e( 'Filter Pages by documentation version', 'docspress-blocks' ); ?></label>
	<select name="docspress_docs_version" id="docspress-docs-version-filter">
		<option value=""><?php echo esc_html( sprintf( __( 'All versions (%d)', 'docspress-blocks' ), $all_count ) ); ?></option>
		<option value="latest" <?php selected( $current, 'latest' ); ?>><?php echo esc_html( sprintf( __( 'Latest (%d)', 'docspress-blocks' ), $latest_term instanceof WP_Term ? $latest_term->count : 0 ) ); ?></option>
		<?php foreach ( $terms as $term ) : ?>
			<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $current, $term->slug ); ?>><?php echo esc_html( sprintf( '%1$s (%2$d)%3$s', $term->name, $term->count, rest_sanitize_boolean( get_term_meta( $term->term_id, 'docspress_version_active', true ) ) ? '' : __( ' — inactive', 'docspress-blocks' ) ) ); ?></option>
		<?php endforeach; ?>
	</select>
	<?php
}
add_action( 'restrict_manage_posts', 'docspress_blocks_versions_admin_filter' );

/**
 * Apply Page-list version filtering.
 *
 * @param WP_Query $query Query.
 */
function docspress_blocks_versions_admin_query( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || 'page' !== $query->get( 'post_type' ) ) {
		return;
	}
	$selected = isset( $_GET['docspress_docs_version'] ) ? sanitize_key( wp_unslash( $_GET['docspress_docs_version'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( 'latest' === $selected ) {
		$selected = docspress_blocks_versions_effective_slug();
	}
	if ( $selected ) {
		$query->set(
			'tax_query',
			array(
				array(
					'taxonomy' => DOCSPRESS_VERSION_TAXONOMY,
					'field'    => 'slug',
					'terms'    => array( $selected ),
				),
			)
		);
	}
}
add_action( 'pre_get_posts', 'docspress_blocks_versions_admin_query' );

/**
 * Sort the custom version column by configured term order.
 *
 * @param array<string,string> $clauses SQL clauses.
 * @param WP_Query             $query Query.
 * @return array<string,string>
 */
function docspress_blocks_versions_admin_sort( $clauses, $query ) {
	global $wpdb;
	if ( ! is_admin() || ! $query->is_main_query() || 'docspress_version' !== $query->get( 'orderby' ) ) {
		return $clauses;
	}
	$clauses['join'] .= " LEFT JOIN {$wpdb->term_relationships} dpv_tr ON ({$wpdb->posts}.ID = dpv_tr.object_id)";
	$clauses['join'] .= " LEFT JOIN {$wpdb->term_taxonomy} dpv_tt ON (dpv_tr.term_taxonomy_id = dpv_tt.term_taxonomy_id AND dpv_tt.taxonomy = '" . esc_sql( DOCSPRESS_VERSION_TAXONOMY ) . "')";
	$clauses['join'] .= " LEFT JOIN {$wpdb->termmeta} dpv_tm ON (dpv_tt.term_id = dpv_tm.term_id AND dpv_tm.meta_key = 'docspress_version_order')";
	$direction = 'DESC' === strtoupper( (string) $query->get( 'order' ) ) ? 'DESC' : 'ASC';
	$clauses['orderby'] = "COALESCE(MIN(CAST(dpv_tm.meta_value AS UNSIGNED)), 999999) {$direction}, {$wpdb->posts}.post_title ASC";
	$clauses['groupby'] = "{$wpdb->posts}.ID";
	return $clauses;
}
add_filter( 'posts_clauses', 'docspress_blocks_versions_admin_sort', 10, 2 );

/**
 * Add latest/version state beside Page titles.
 *
 * @param string[] $states States.
 * @param WP_Post  $post Page.
 * @return string[]
 */
function docspress_blocks_versions_post_states( $states, $post ) {
	$context = 'page' === $post->post_type ? docspress_blocks_versions_page_context( $post->ID ) : null;
	if ( $context ) {
		$states[] = $context['latest']
			? sprintf( __( 'Docs %s · Latest', 'docspress-blocks' ), $context['label'] )
			: sprintf( __( 'Docs %s', 'docspress-blocks' ), $context['label'] );
	}
	return $states;
}
add_filter( 'display_post_states', 'docspress_blocks_versions_post_states', 10, 2 );

/**
 * Keep managed version assignment read-only in the Page editor.
 */
function docspress_blocks_versions_remove_metabox() {
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $post_id && get_post_meta( $post_id, '_docspress_version_id', true ) ) {
		remove_meta_box( 'tagsdiv-' . DOCSPRESS_VERSION_TAXONOMY, 'page', 'side' );
	}
}
add_action( 'add_meta_boxes_page', 'docspress_blocks_versions_remove_metabox' );

/**
 * Exclude the stable internal version container from public XML sitemaps.
 *
 * @param array  $args Query arguments.
 * @param string $post_type Post type.
 * @return array
 */
function docspress_blocks_versions_sitemap_args( $args, $post_type ) {
	if ( 'page' !== $post_type ) {
		return $args;
	}
	$args['meta_query'] = array(
		'relation' => 'OR',
		array(
			'key'     => '_docspress_version_container',
			'compare' => 'NOT EXISTS',
		),
		array(
			'key'     => '_docspress_version_container',
			'value'   => '1',
			'compare' => '!=',
		),
	);
	return $args;
}
add_filter( 'wp_sitemaps_posts_query_args', 'docspress_blocks_versions_sitemap_args', 10, 2 );
