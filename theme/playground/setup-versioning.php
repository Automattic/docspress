<?php
/**
 * Seed the dedicated DocsPress API versioning demonstration.
 *
 * @package DocsPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fixture_path = __DIR__ . '/generated-versioning.json';
if ( ! file_exists( $fixture_path ) ) {
	wp_die( 'Missing generated versioning fixture. Run npm run playground:versioning.' );
}

$fixture = json_decode( file_get_contents( $fixture_path ), true );
if ( ! is_array( $fixture ) || empty( $fixture['pages'] ) || empty( $fixture['terms'] ) ) {
	wp_die( 'The generated DocsPress versioning fixture is invalid.' );
}

foreach ( array( array( 'post', 'hello-world' ), array( 'page', 'sample-page' ) ) as $starter ) {
	$post = get_page_by_path( $starter[1], OBJECT, $starter[0] );
	if ( $post ) {
		wp_delete_post( $post->ID, true );
	}
}

$term_ids = array();
foreach ( $fixture['terms'] as $version ) {
	$term = get_term_by( 'slug', sanitize_key( $version['id'] ), DOCSPRESS_VERSION_TAXONOMY );
	if ( ! $term ) {
		$created = wp_insert_term(
			sanitize_text_field( $version['label'] ),
			DOCSPRESS_VERSION_TAXONOMY,
			array( 'slug' => sanitize_key( $version['id'] ) )
		);
		if ( is_wp_error( $created ) ) {
			wp_die( esc_html( $created->get_error_message() ) );
		}
		$term = get_term( $created['term_id'], DOCSPRESS_VERSION_TAXONOMY );
	}
	$term_ids[ $version['id'] ] = (int) $term->term_id;
	update_term_meta( $term->term_id, 'docspress_version_order', (int) $version['order'] );
	update_term_meta( $term->term_id, 'docspress_version_active', ! empty( $version['active'] ) );
	update_term_meta( $term->term_id, 'docspress_version_repository_latest', ! empty( $version['repositoryLatest'] ) );
	update_term_meta( $term->term_id, 'docspress_version_effective_latest', ! empty( $version['effectiveLatest'] ) );
}

update_option( 'docspress_repository_latest_version', sanitize_key( $fixture['repositoryLatest'] ) );
update_option( 'docspress_version_override', '' );
update_option( 'docspress_docs_root_slug', sanitize_title( $fixture['rootSlug'] ) );
update_option( 'permalink_structure', '/%postname%/' );
update_option( 'blogname', 'DocsPress Versioning Example' );
update_option( 'blogdescription', 'Root, directory, and filename-suffix API documentation versions.' );

usort(
	$fixture['pages'],
	static function ( $left, $right ) {
		$depth = (int) $left['depth'] <=> (int) $right['depth'];
		return $depth ?: strcmp( $left['key'], $right['key'] );
	}
);

$ids_by_key = array();
foreach ( $fixture['pages'] as $page ) {
	$parent_key = (string) ( $page['parentKey'] ?? '' );
	$parent_id  = $parent_key ? (int) ( $ids_by_key[ $parent_key ] ?? 0 ) : 0;
	if ( $parent_key && ! $parent_id ) {
		wp_die( esc_html( 'Versioning fixture parent is unavailable: ' . $parent_key ) );
	}
	$existing = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'any',
			'name'           => sanitize_title( $page['slug'] ),
			'post_parent'    => $parent_id,
			'posts_per_page' => 1,
		)
	);
	$post_data = array(
		'post_title'     => sanitize_text_field( $page['title'] ),
		'post_name'      => sanitize_title( $page['slug'] ),
		'post_content'   => wp_slash( $page['content'] ),
		'post_parent'    => $parent_id,
		'menu_order'     => (int) $page['menuOrder'],
		'post_status'    => 'publish',
		'post_type'      => 'page',
		'comment_status' => 'closed',
	);
	if ( $existing ) {
		$post_data['ID'] = $existing[0]->ID;
		$page_id = wp_update_post( $post_data );
	} else {
		$page_id = wp_insert_post( $post_data );
	}
	if ( is_wp_error( $page_id ) || ! $page_id ) {
		wp_die( esc_html( 'Could not import versioned Page: ' . $page['key'] ) );
	}
	$page_id = (int) $page_id;
	$ids_by_key[ $page['key'] ] = $page_id;

	update_post_meta( $page_id, '_docspress_version_container', ! empty( $page['versionContainer'] ) );
	if ( ! empty( $page['version'] ) ) {
		wp_set_object_terms( $page_id, array( $term_ids[ $page['version'] ] ), DOCSPRESS_VERSION_TAXONOMY, false );
		update_post_meta( $page_id, '_docspress_version_id', sanitize_key( $page['version'] ) );
		update_post_meta( $page_id, '_docspress_logical_route', sanitize_text_field( $page['logicalRoute'] ) );
		update_post_meta( $page_id, '_docspress_page_identity', sanitize_text_field( $page['stableIdentity'] ) );
		update_post_meta( $page_id, '_docspress_source_type', sanitize_key( $page['sourceType'] ) );
		update_post_meta( $page_id, '_docspress_source_path', sanitize_text_field( $page['sourcePath'] ) );
		update_post_meta( $page_id, '_docspress_docs_root', sanitize_title( $page['docsRoot'] ) );
		if ( preg_match( '/\.(?:md|markdown|mdx)$/i', (string) $page['sourcePath'] ) && ! empty( $fixture['github'] ) ) {
			update_post_meta( $page_id, '_docspress_github_path', sanitize_text_field( $page['sourcePath'] ) );
			update_post_meta( $page_id, '_docspress_github_repository', sanitize_text_field( $fixture['github']['repository'] ?? '' ) );
			update_post_meta( $page_id, '_docspress_github_ref', sanitize_text_field( $fixture['github']['ref'] ?? 'main' ) );
			update_post_meta( $page_id, '_docspress_github_server_url', esc_url_raw( $fixture['github']['serverUrl'] ?? 'https://github.com' ) );
		}
	}
}

docspress_blocks_versions_rewrite_rules();
flush_rewrite_rules( false );
