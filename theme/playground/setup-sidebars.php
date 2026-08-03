<?php
/**
 * Seed the dedicated DocsPress contextual-sidebar demonstration.
 *
 * @package DocsPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fixture_path = __DIR__ . '/generated-sidebars.json';
if ( ! file_exists( $fixture_path ) ) {
	wp_die( 'Missing generated sidebar fixture. Run npm run playground:sidebars.' );
}

$fixture = json_decode( file_get_contents( $fixture_path ), true );
if ( ! is_array( $fixture ) || empty( $fixture['pages'] ) || ! is_array( $fixture['pages'] ) ) {
	wp_die( 'The generated DocsPress sidebar fixture is invalid.' );
}

foreach ( array( array( 'post', 'hello-world' ), array( 'page', 'sample-page' ) ) as $starter ) {
	$post = get_page_by_path( $starter[1], OBJECT, $starter[0] );
	if ( $post ) {
		wp_delete_post( $post->ID, true );
	}
}

update_option( 'permalink_structure', '/%postname%/' );
update_option( 'blogname', 'DocsPress Contextual Sidebars' );
update_option( 'blogdescription', 'Focused route-based navigation with a simple default sidebar.' );

usort(
	$fixture['pages'],
	static function ( $left, $right ) {
		$depth = (int) $left['depth'] <=> (int) $right['depth'];
		return $depth ?: strcmp( $left['key'], $right['key'] );
	}
);

$ids_by_key = array();
foreach ( $fixture['pages'] as $page ) {
	$parent_key = isset( $page['parentKey'] ) ? (string) $page['parentKey'] : '';
	$parent_id  = $parent_key && isset( $ids_by_key[ $parent_key ] ) ? (int) $ids_by_key[ $parent_key ] : 0;
	if ( $parent_key && ! $parent_id ) {
		wp_die( esc_html( 'Sidebar fixture parent is unavailable: ' . $parent_key ) );
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
		wp_die( esc_html( 'Could not import contextual-sidebar Page: ' . $page['key'] ) );
	}

	$page_id = (int) $page_id;
	$ids_by_key[ $page['key'] ] = $page_id;
	update_post_meta( $page_id, '_docspress_sidebar_id', sanitize_key( $page['sidebarId'] ) );
	update_post_meta( $page_id, '_docspress_sidebar_root', ! empty( $page['sidebarRoot'] ) );
	update_post_meta( $page_id, '_docspress_source_path', sanitize_text_field( $page['sourcePath'] ) );
	if ( ! empty( $fixture['github'] ) ) {
		update_post_meta( $page_id, '_docspress_github_path', sanitize_text_field( $page['sourcePath'] ) );
		update_post_meta( $page_id, '_docspress_github_repository', sanitize_text_field( $fixture['github']['repository'] ?? '' ) );
		update_post_meta( $page_id, '_docspress_github_ref', sanitize_text_field( $fixture['github']['ref'] ?? 'main' ) );
		update_post_meta( $page_id, '_docspress_github_server_url', esc_url_raw( $fixture['github']['serverUrl'] ?? 'https://github.com' ) );
	}
}

flush_rewrite_rules( false );
