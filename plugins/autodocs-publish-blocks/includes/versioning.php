<?php
/**
 * Documentation version taxonomy, routing, settings, and administration.
 *
 * @package AutodocsPublishBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const AUTODOCS_PUBLISH_VERSION_TAXONOMY = 'autodocs_publish_version';
const AUTODOCS_PUBLISH_VERSION_REST_BASE = 'autodocs_publish_versions';
const AUTODOCS_PUBLISH_LEGACY_VERSION_TAXONOMY = 'docspress_version';
const AUTODOCS_PUBLISH_LEGACY_VERSION_REST_BASE = 'docspress_versions';

/**
 * Return legacy-to-canonical option names used by version synchronization.
 *
 * @return array<string,string>
 */
function autodocs_publish_blocks_versions_option_map() {
	return array(
		'docspress_repository_latest_version' => 'autodocs_publish_repository_latest_version',
		'docspress_version_override'          => 'autodocs_publish_version_override',
		'docspress_docs_root_slug'            => 'autodocs_publish_docs_root_slug',
	);
}

/**
 * Return legacy-to-canonical Page metadata names.
 *
 * @return array<string,string>
 */
function autodocs_publish_blocks_versions_post_meta_map() {
	return array(
		'_docspress_version_id'        => '_autodocs_publish_version_id',
		'_docspress_logical_route'     => '_autodocs_publish_logical_route',
		'_docspress_page_identity'     => '_autodocs_publish_page_identity',
		'_docspress_source_type'       => '_autodocs_publish_source_type',
		'_docspress_source_path'       => '_autodocs_publish_source_path',
		'_docspress_github_path'       => '_autodocs_publish_github_path',
		'_docspress_github_repository' => '_autodocs_publish_github_repository',
		'_docspress_github_ref'        => '_autodocs_publish_github_ref',
		'_docspress_github_server_url' => '_autodocs_publish_github_server_url',
		'_docspress_docs_root'         => '_autodocs_publish_docs_root',
		'_docspress_version_container' => '_autodocs_publish_version_container',
		'_docspress_sidebar_id'        => '_autodocs_publish_sidebar_id',
		'_docspress_sidebar_root'      => '_autodocs_publish_sidebar_root',
	);
}

/**
 * Return legacy-to-canonical version term metadata names.
 *
 * @return array<string,string>
 */
function autodocs_publish_blocks_versions_term_meta_map() {
	return array(
		'docspress_version_order'             => 'autodocs_publish_version_order',
		'docspress_version_active'            => 'autodocs_publish_version_active',
		'docspress_version_repository_latest' => 'autodocs_publish_version_repository_latest',
		'docspress_version_effective_latest'  => 'autodocs_publish_version_effective_latest',
	);
}

/**
 * Register the version taxonomy and synchronization-owned metadata.
 */
function autodocs_publish_blocks_versions_register() {
	// Keep the pre-rename REST contract writable during a rolling Action update.
	register_taxonomy(
		AUTODOCS_PUBLISH_LEGACY_VERSION_TAXONOMY,
		array( 'page' ),
		array(
			'public'       => false,
			'show_ui'      => false,
			'show_in_rest' => true,
			'rest_base'    => AUTODOCS_PUBLISH_LEGACY_VERSION_REST_BASE,
			'rewrite'      => false,
			'query_var'    => false,
		)
	);

	register_taxonomy(
		AUTODOCS_PUBLISH_VERSION_TAXONOMY,
		array( 'page' ),
		array(
			'labels'            => array(
				'name'          => __( 'Documentation versions', 'autodocs-publish-blocks' ),
				'singular_name' => __( 'Documentation version', 'autodocs-publish-blocks' ),
			),
			'public'            => false,
			'show_ui'            => true,
			'show_admin_column'  => false,
			'show_in_quick_edit' => false,
			'show_in_rest'      => true,
			'rest_base'         => AUTODOCS_PUBLISH_VERSION_REST_BASE,
			'hierarchical'      => false,
			'meta_box_cb'       => false,
			'rewrite'           => false,
			'query_var'         => true,
		)
	);

	$term_meta = array(
		'autodocs_publish_version_order'               => array( 'type' => 'integer', 'default' => 0 ),
		'autodocs_publish_version_active'              => array( 'type' => 'boolean', 'default' => false ),
		'autodocs_publish_version_repository_latest'   => array( 'type' => 'boolean', 'default' => false ),
		'autodocs_publish_version_effective_latest'    => array( 'type' => 'boolean', 'default' => false ),
	);
	foreach ( $term_meta as $key => $schema ) {
		register_term_meta(
			AUTODOCS_PUBLISH_VERSION_TAXONOMY,
			$key,
			array(
				'type'              => $schema['type'],
				'single'            => true,
				'default'           => $schema['default'],
				'sanitize_callback' => 'integer' === $schema['type'] ? 'autodocs_publish_blocks_versions_sanitize_integer' : 'rest_sanitize_boolean',
				'auth_callback'     => static function () {
					return current_user_can( 'manage_categories' );
				},
				'show_in_rest'      => true,
			)
		);
	}
	foreach ( autodocs_publish_blocks_versions_term_meta_map() as $legacy_key => $current_key ) {
		$schema = $term_meta[ $current_key ];
		register_term_meta(
			AUTODOCS_PUBLISH_LEGACY_VERSION_TAXONOMY,
			$legacy_key,
			array(
				'type'              => $schema['type'],
				'single'            => true,
				'default'           => $schema['default'],
				'sanitize_callback' => 'integer' === $schema['type'] ? 'autodocs_publish_blocks_versions_sanitize_integer' : 'rest_sanitize_boolean',
				'auth_callback'     => static function () {
					return current_user_can( 'manage_categories' );
				},
				'show_in_rest'      => true,
			)
		);
	}

	$post_meta = array(
		'_autodocs_publish_version_id',
		'_autodocs_publish_logical_route',
		'_autodocs_publish_page_identity',
		'_autodocs_publish_source_type',
		'_autodocs_publish_source_path',
		'_autodocs_publish_github_path',
		'_autodocs_publish_github_repository',
		'_autodocs_publish_github_ref',
		'_autodocs_publish_github_server_url',
		'_autodocs_publish_docs_root',
		'_autodocs_publish_sidebar_id',
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
	foreach ( array( '_autodocs_publish_version_container', '_autodocs_publish_sidebar_root' ) as $key ) {
		register_post_meta(
			'page',
			$key,
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
	}
	foreach ( autodocs_publish_blocks_versions_post_meta_map() as $legacy_key => $current_key ) {
		$is_boolean = in_array( $current_key, array( '_autodocs_publish_version_container', '_autodocs_publish_sidebar_root' ), true );
		register_post_meta(
			'page',
			$legacy_key,
			array(
				'type'              => $is_boolean ? 'boolean' : 'string',
				'single'            => true,
				'default'           => $is_boolean ? false : '',
				'sanitize_callback' => $is_boolean ? 'rest_sanitize_boolean' : 'sanitize_text_field',
				'auth_callback'     => static function ( $allowed, $meta_key, $post_id ) {
					return current_user_can( 'edit_post', (int) $post_id );
				},
				'show_in_rest'      => true,
			)
		);
	}

	// Run before register_setting() installs default-value filters, so a missing
	// option remains distinguishable from an explicitly saved empty value.
	autodocs_publish_blocks_versions_sync_compatibility_options();

	register_setting(
		'autodocs_publish_versions_repository',
		'autodocs_publish_repository_latest_version',
		array(
			'type'              => 'string',
			'default'           => '',
			'sanitize_callback' => 'sanitize_key',
			'show_in_rest'      => true,
		)
	);
	register_setting(
		'autodocs_publish_versions',
		'autodocs_publish_version_override',
		array(
			'type'              => 'string',
			'default'           => '',
			'sanitize_callback' => 'autodocs_publish_blocks_versions_sanitize_override',
			'show_in_rest'      => true,
		)
	);
	register_setting(
		'autodocs_publish_versions_repository',
		'autodocs_publish_docs_root_slug',
		array(
			'type'              => 'string',
			'default'           => 'docs',
			'sanitize_callback' => 'sanitize_title',
			'show_in_rest'      => true,
		)
	);

	// Keep the old REST settings shape writable during a rolling Action update.
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
			'sanitize_callback' => 'autodocs_publish_blocks_versions_sanitize_override',
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
add_action( 'init', 'autodocs_publish_blocks_versions_register', 6 );

/**
 * Return the corresponding key from a legacy-to-canonical map.
 *
 * @param string               $key Key to resolve.
 * @param array<string,string> $map Legacy-to-canonical map.
 * @return string
 */
function autodocs_publish_blocks_versions_mirrored_key( $key, $map ) {
	if ( isset( $map[ $key ] ) ) {
		return $map[ $key ];
	}

	$legacy_key = array_search( $key, $map, true );
	return false === $legacy_key ? '' : $legacy_key;
}

/**
 * Populate both REST settings shapes, treating canonical values as authoritative.
 */
function autodocs_publish_blocks_versions_sync_compatibility_options() {
	foreach ( autodocs_publish_blocks_versions_option_map() as $legacy_key => $current_key ) {
		$current_value = get_option( $current_key, null );
		$legacy_value  = get_option( $legacy_key, null );

		if ( null !== $current_value ) {
			if ( $legacy_value !== $current_value ) {
				update_option( $legacy_key, $current_value, false );
			}
		} elseif ( null !== $legacy_value ) {
			add_option( $current_key, $legacy_value, '', false );
		}
	}
}

/**
 * Mirror one version setting between the legacy and canonical option names.
 *
 * @param string $option Changed option name.
 * @param mixed  $value  New option value.
 */
function autodocs_publish_blocks_versions_mirror_option( $option, $value ) {
	static $mirroring = false;
	if ( $mirroring ) {
		return;
	}

	$target = autodocs_publish_blocks_versions_mirrored_key( $option, autodocs_publish_blocks_versions_option_map() );
	if ( ! $target || get_option( $target, null ) === $value ) {
		return;
	}

	$mirroring = true;
	update_option( $target, $value, false );
	$mirroring = false;
}

/**
 * Mirror a newly added version setting.
 *
 * @param string $option Option name.
 * @param mixed  $value  Option value.
 */
function autodocs_publish_blocks_versions_mirror_added_option( $option, $value ) {
	autodocs_publish_blocks_versions_mirror_option( $option, $value );
}

/**
 * Mirror an updated version setting.
 *
 * @param mixed  $old_value Previous value.
 * @param mixed  $value     New value.
 * @param string $option    Option name.
 */
function autodocs_publish_blocks_versions_mirror_updated_option( $old_value, $value, $option ) {
	autodocs_publish_blocks_versions_mirror_option( $option, $value );
}

foreach ( array_merge( array_keys( autodocs_publish_blocks_versions_option_map() ), array_values( autodocs_publish_blocks_versions_option_map() ) ) as $autodocs_publish_blocks_version_option ) {
	add_action( 'add_option_' . $autodocs_publish_blocks_version_option, 'autodocs_publish_blocks_versions_mirror_added_option', 10, 2 );
	add_action( 'update_option_' . $autodocs_publish_blocks_version_option, 'autodocs_publish_blocks_versions_mirror_updated_option', 10, 3 );
}
unset( $autodocs_publish_blocks_version_option );

/**
 * Ensure a matching term exists in the other version taxonomy.
 *
 * @param int    $term_id  Source term ID.
 * @param string $taxonomy Source taxonomy.
 * @return WP_Term|WP_Error
 */
function autodocs_publish_blocks_versions_ensure_counterpart_term( $term_id, $taxonomy ) {
	$target_taxonomy = AUTODOCS_PUBLISH_VERSION_TAXONOMY === $taxonomy
		? AUTODOCS_PUBLISH_LEGACY_VERSION_TAXONOMY
		: AUTODOCS_PUBLISH_VERSION_TAXONOMY;
	$source_term = get_term( (int) $term_id, $taxonomy );
	if ( ! $source_term instanceof WP_Term ) {
		return new WP_Error( 'autodocs_publish_invalid_version_term', __( 'The source documentation version term could not be read.', 'autodocs-publish-blocks' ) );
	}

	$target_term = get_term_by( 'slug', $source_term->slug, $target_taxonomy );
	if ( ! $target_term ) {
		$inserted = wp_insert_term(
			$source_term->name,
			$target_taxonomy,
			array(
				'description' => $source_term->description,
				'slug'        => $source_term->slug,
			)
		);
		if ( is_wp_error( $inserted ) ) {
			return $inserted;
		}
		$target_term = get_term( (int) $inserted['term_id'], $target_taxonomy );
	} elseif ( $target_term->name !== $source_term->name || $target_term->description !== $source_term->description ) {
		$updated = wp_update_term(
			$target_term->term_id,
			$target_taxonomy,
			array(
				'description' => $source_term->description,
				'name'        => $source_term->name,
			)
		);
		if ( is_wp_error( $updated ) ) {
			return $updated;
		}
		$target_term = get_term( (int) $updated['term_id'], $target_taxonomy );
	}

	return $target_term instanceof WP_Term
		? $target_term
		: new WP_Error( 'autodocs_publish_invalid_version_term', __( 'The matching documentation version term could not be read.', 'autodocs-publish-blocks' ) );
}

/**
 * Mirror version term creation and edits across both REST taxonomies.
 *
 * @param int $term_id Term ID.
 */
function autodocs_publish_blocks_versions_mirror_term( $term_id ) {
	static $mirroring = false;
	if ( $mirroring ) {
		return;
	}

	$term = get_term( (int) $term_id );
	if ( ! $term instanceof WP_Term || ! in_array( $term->taxonomy, array( AUTODOCS_PUBLISH_VERSION_TAXONOMY, AUTODOCS_PUBLISH_LEGACY_VERSION_TAXONOMY ), true ) ) {
		return;
	}

	$mirroring = true;
	autodocs_publish_blocks_versions_ensure_counterpart_term( $term->term_id, $term->taxonomy );
	$mirroring = false;
}
add_action( 'created_' . AUTODOCS_PUBLISH_VERSION_TAXONOMY, 'autodocs_publish_blocks_versions_mirror_term' );
add_action( 'edited_' . AUTODOCS_PUBLISH_VERSION_TAXONOMY, 'autodocs_publish_blocks_versions_mirror_term' );
add_action( 'created_' . AUTODOCS_PUBLISH_LEGACY_VERSION_TAXONOMY, 'autodocs_publish_blocks_versions_mirror_term' );
add_action( 'edited_' . AUTODOCS_PUBLISH_LEGACY_VERSION_TAXONOMY, 'autodocs_publish_blocks_versions_mirror_term' );

/**
 * Mirror version term metadata across the two taxonomy records.
 *
 * @param int    $meta_id  Metadata row ID.
 * @param int    $term_id  Term ID.
 * @param string $meta_key Metadata key.
 * @param mixed  $value    Metadata value.
 */
function autodocs_publish_blocks_versions_mirror_term_meta( $meta_id, $term_id, $meta_key, $value ) {
	static $mirroring = false;
	if ( $mirroring ) {
		return;
	}

	$map        = autodocs_publish_blocks_versions_term_meta_map();
	$target_key = autodocs_publish_blocks_versions_mirrored_key( $meta_key, $map );
	if ( ! $target_key ) {
		return;
	}

	$source_taxonomy = isset( $map[ $meta_key ] ) ? AUTODOCS_PUBLISH_LEGACY_VERSION_TAXONOMY : AUTODOCS_PUBLISH_VERSION_TAXONOMY;
	$source_term     = get_term( (int) $term_id, $source_taxonomy );
	if ( ! $source_term instanceof WP_Term ) {
		return;
	}

	$mirroring = true;
	$target_term = autodocs_publish_blocks_versions_ensure_counterpart_term( $source_term->term_id, $source_taxonomy );
	if ( ! is_wp_error( $target_term ) ) {
		update_term_meta( $target_term->term_id, $target_key, $value );
	}
	$mirroring = false;
}
add_action( 'added_term_meta', 'autodocs_publish_blocks_versions_mirror_term_meta', 10, 4 );
add_action( 'updated_term_meta', 'autodocs_publish_blocks_versions_mirror_term_meta', 10, 4 );

/**
 * Remove matching term metadata when either compatibility key is removed.
 *
 * @param int    $meta_ids  Deleted metadata row ID.
 * @param int    $term_id   Term ID.
 * @param string $meta_key  Metadata key.
 */
function autodocs_publish_blocks_versions_delete_mirrored_term_meta( $meta_ids, $term_id, $meta_key ) {
	static $mirroring = false;
	if ( $mirroring ) {
		return;
	}

	$map        = autodocs_publish_blocks_versions_term_meta_map();
	$target_key = autodocs_publish_blocks_versions_mirrored_key( $meta_key, $map );
	if ( ! $target_key ) {
		return;
	}

	$source_taxonomy = isset( $map[ $meta_key ] ) ? AUTODOCS_PUBLISH_LEGACY_VERSION_TAXONOMY : AUTODOCS_PUBLISH_VERSION_TAXONOMY;
	$source_term     = get_term( (int) $term_id, $source_taxonomy );
	if ( ! $source_term instanceof WP_Term ) {
		return;
	}
	$target_taxonomy = AUTODOCS_PUBLISH_VERSION_TAXONOMY === $source_taxonomy ? AUTODOCS_PUBLISH_LEGACY_VERSION_TAXONOMY : AUTODOCS_PUBLISH_VERSION_TAXONOMY;
	$target_term     = get_term_by( 'slug', $source_term->slug, $target_taxonomy );
	if ( ! $target_term ) {
		return;
	}

	$mirroring = true;
	delete_term_meta( $target_term->term_id, $target_key );
	$mirroring = false;
}
add_action( 'deleted_term_meta', 'autodocs_publish_blocks_versions_delete_mirrored_term_meta', 10, 3 );

/**
 * Mirror a Page's complete version assignment to the other taxonomy.
 *
 * @param int    $object_id Page ID.
 * @param mixed  $terms     Assigned terms (unused).
 * @param int[]  $tt_ids    Assigned term-taxonomy IDs (unused).
 * @param string $taxonomy  Source taxonomy.
 */
function autodocs_publish_blocks_versions_mirror_object_terms( $object_id, $terms, $tt_ids, $taxonomy ) {
	static $mirroring = false;
	if ( $mirroring || ! in_array( $taxonomy, array( AUTODOCS_PUBLISH_VERSION_TAXONOMY, AUTODOCS_PUBLISH_LEGACY_VERSION_TAXONOMY ), true ) ) {
		return;
	}

	$source_terms = wp_get_object_terms( (int) $object_id, $taxonomy );
	if ( is_wp_error( $source_terms ) ) {
		return;
	}

	$target_taxonomy = AUTODOCS_PUBLISH_VERSION_TAXONOMY === $taxonomy ? AUTODOCS_PUBLISH_LEGACY_VERSION_TAXONOMY : AUTODOCS_PUBLISH_VERSION_TAXONOMY;
	$target_ids      = array();
	$mirroring       = true;
	foreach ( $source_terms as $source_term ) {
		$target_term = autodocs_publish_blocks_versions_ensure_counterpart_term( $source_term->term_id, $taxonomy );
		if ( ! is_wp_error( $target_term ) ) {
			$target_ids[] = (int) $target_term->term_id;
		}
	}
	wp_set_object_terms( (int) $object_id, $target_ids, $target_taxonomy, false );
	$mirroring = false;
}
add_action( 'set_object_terms', 'autodocs_publish_blocks_versions_mirror_object_terms', 10, 4 );

/**
 * Mirror Page metadata written by either Action generation.
 *
 * @param int    $meta_id  Metadata row ID.
 * @param int    $post_id  Page ID.
 * @param string $meta_key Metadata key.
 * @param mixed  $value    Metadata value.
 */
function autodocs_publish_blocks_versions_mirror_post_meta( $meta_id, $post_id, $meta_key, $value ) {
	static $mirroring = false;
	if ( $mirroring || 'page' !== get_post_type( $post_id ) ) {
		return;
	}

	$target_key = autodocs_publish_blocks_versions_mirrored_key( $meta_key, autodocs_publish_blocks_versions_post_meta_map() );
	if ( ! $target_key || ( metadata_exists( 'post', $post_id, $target_key ) && get_post_meta( $post_id, $target_key, true ) === $value ) ) {
		return;
	}

	$mirroring = true;
	update_post_meta( $post_id, $target_key, $value );
	$mirroring = false;
}
add_action( 'added_post_meta', 'autodocs_publish_blocks_versions_mirror_post_meta', 10, 4 );
add_action( 'updated_post_meta', 'autodocs_publish_blocks_versions_mirror_post_meta', 10, 4 );

/**
 * Delete the compatibility Page metadata twin when one side is deleted.
 *
 * @param int    $meta_ids Deleted metadata row ID.
 * @param int    $post_id  Page ID.
 * @param string $meta_key Metadata key.
 */
function autodocs_publish_blocks_versions_delete_mirrored_post_meta( $meta_ids, $post_id, $meta_key ) {
	static $mirroring = false;
	if ( $mirroring || 'page' !== get_post_type( $post_id ) ) {
		return;
	}

	$target_key = autodocs_publish_blocks_versions_mirrored_key( $meta_key, autodocs_publish_blocks_versions_post_meta_map() );
	if ( ! $target_key || ! metadata_exists( 'post', $post_id, $target_key ) ) {
		return;
	}

	$mirroring = true;
	delete_post_meta( $post_id, $target_key );
	$mirroring = false;
}
add_action( 'deleted_post_meta', 'autodocs_publish_blocks_versions_delete_mirrored_post_meta', 10, 3 );

/**
 * Move version terms and synchronization metadata from the legacy identifiers.
 * Existing Autodocs Publish values always win, making the migration idempotent.
 */
function autodocs_publish_blocks_migrate_legacy_version_data() {
	if ( '2' === get_option( 'autodocs_publish_blocks_legacy_version_migration' ) ) {
		return;
	}

	$migration_complete = true;
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
		foreach ( autodocs_publish_blocks_versions_post_meta_map() as $legacy_key => $current_key ) {
			$legacy_exists  = metadata_exists( 'post', $page_id, $legacy_key );
			$current_exists = metadata_exists( 'post', $page_id, $current_key );
			if ( $current_exists ) {
				$current_value = get_post_meta( $page_id, $current_key, true );
				if ( ! $legacy_exists || get_post_meta( $page_id, $legacy_key, true ) !== $current_value ) {
					update_post_meta( $page_id, $legacy_key, $current_value );
					if ( ! metadata_exists( 'post', $page_id, $legacy_key ) || get_post_meta( $page_id, $legacy_key, true ) !== $current_value ) {
						$migration_complete = false;
					}
				}
			} elseif ( $legacy_exists ) {
				$legacy_value = get_post_meta( $page_id, $legacy_key, true );
				update_post_meta( $page_id, $current_key, $legacy_value );
				if ( ! metadata_exists( 'post', $page_id, $current_key ) || get_post_meta( $page_id, $current_key, true ) !== $legacy_value ) {
					$migration_complete = false;
				}
			}
		}
	}

	$legacy_terms = get_terms(
		array(
			'hide_empty' => false,
			'taxonomy'   => AUTODOCS_PUBLISH_LEGACY_VERSION_TAXONOMY,
		)
	);
	if ( is_wp_error( $legacy_terms ) ) {
		$migration_complete = false;
	} else {
		foreach ( $legacy_terms as $legacy_term ) {
			$current_term = get_term_by( 'slug', $legacy_term->slug, AUTODOCS_PUBLISH_VERSION_TAXONOMY );
			if ( ! $current_term ) {
				$inserted = wp_insert_term(
					$legacy_term->name,
					AUTODOCS_PUBLISH_VERSION_TAXONOMY,
					array(
						'description' => $legacy_term->description,
						'slug'        => $legacy_term->slug,
					)
				);
				if ( is_wp_error( $inserted ) ) {
					$migration_complete = false;
					continue;
				}
				$current_term = get_term( (int) $inserted['term_id'], AUTODOCS_PUBLISH_VERSION_TAXONOMY );
			}
			if ( ! $current_term instanceof WP_Term ) {
				$migration_complete = false;
				continue;
			}
			foreach ( autodocs_publish_blocks_versions_term_meta_map() as $legacy_key => $current_key ) {
				$legacy_exists  = metadata_exists( 'term', $legacy_term->term_id, $legacy_key );
				$current_exists = metadata_exists( 'term', $current_term->term_id, $current_key );
				if ( $current_exists ) {
					$current_value = get_term_meta( $current_term->term_id, $current_key, true );
					if ( ! $legacy_exists || get_term_meta( $legacy_term->term_id, $legacy_key, true ) !== $current_value ) {
						update_term_meta( $legacy_term->term_id, $legacy_key, $current_value );
						if ( ! metadata_exists( 'term', $legacy_term->term_id, $legacy_key ) || get_term_meta( $legacy_term->term_id, $legacy_key, true ) !== $current_value ) {
							$migration_complete = false;
						}
					}
				} elseif ( $legacy_exists ) {
					$legacy_value = get_term_meta( $legacy_term->term_id, $legacy_key, true );
					update_term_meta( $current_term->term_id, $current_key, $legacy_value );
					if ( ! metadata_exists( 'term', $current_term->term_id, $current_key ) || get_term_meta( $current_term->term_id, $current_key, true ) !== $legacy_value ) {
						$migration_complete = false;
					}
				}
			}
			$object_ids = get_objects_in_term( $legacy_term->term_id, AUTODOCS_PUBLISH_LEGACY_VERSION_TAXONOMY );
			if ( is_wp_error( $object_ids ) ) {
				$migration_complete = false;
			} else {
				foreach ( $object_ids as $object_id ) {
					$assigned = wp_set_object_terms( (int) $object_id, (int) $current_term->term_id, AUTODOCS_PUBLISH_VERSION_TAXONOMY, true );
					if ( is_wp_error( $assigned ) ) {
						$migration_complete = false;
					}
				}
			}
		}
	}

	if ( $migration_complete ) {
		update_option( 'autodocs_publish_blocks_legacy_version_migration', '2', false );
	}
}
add_action( 'init', 'autodocs_publish_blocks_migrate_legacy_version_data', 7 );

/**
 * Sanitize integer metadata while accepting WordPress's full meta callback signature.
 *
 * @param mixed $value Submitted value.
 * @return int
 */
function autodocs_publish_blocks_versions_sanitize_integer( $value ) {
	return (int) $value;
}

/**
 * Sanitize an administrator-selected version override.
 *
 * @param mixed $value Submitted value.
 * @return string
 */
function autodocs_publish_blocks_versions_sanitize_override( $value ) {
	$slug = sanitize_key( (string) $value );
	if ( '' === $slug ) {
		return '';
	}
	$term = get_term_by( 'slug', $slug, AUTODOCS_PUBLISH_VERSION_TAXONOMY );
	return $term instanceof WP_Term && get_term_meta( $term->term_id, 'autodocs_publish_version_active', true ) ? $slug : '';
}

/**
 * Return configured version terms in registry order.
 *
 * @param bool $active_only Exclude inactive terms.
 * @return WP_Term[]
 */
function autodocs_publish_blocks_versions_terms( $active_only = true ) {
	if ( ! taxonomy_exists( AUTODOCS_PUBLISH_VERSION_TAXONOMY ) ) {
		return array();
	}

	$terms = get_terms(
		array(
			'taxonomy'   => AUTODOCS_PUBLISH_VERSION_TAXONOMY,
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
				return ! $active_only || rest_sanitize_boolean( get_term_meta( $term->term_id, 'autodocs_publish_version_active', true ) );
			}
		)
	);
	usort(
		$terms,
		static function ( $left, $right ) {
			$order = (int) get_term_meta( $left->term_id, 'autodocs_publish_version_order', true )
				<=> (int) get_term_meta( $right->term_id, 'autodocs_publish_version_order', true );
			return 0 !== $order ? $order : strcasecmp( $left->name, $right->name );
		}
	);
	return $terms;
}

/**
 * Apply both names of the effective-latest-version filter during the rename.
 *
 * @param string    $slug  Effective version slug.
 * @param WP_Term[] $terms Available version terms.
 * @return string
 */
function autodocs_publish_blocks_versions_filter_effective_slug( $slug, $terms ) {
	$slug = (string) apply_filters( 'docspress_effective_latest_version', $slug, $terms );
	return (string) apply_filters( 'autodocs_publish_effective_latest_version', $slug, $terms );
}

/**
 * Return the effective latest version slug.
 *
 * @return string
 */
function autodocs_publish_blocks_versions_effective_slug() {
	$terms = autodocs_publish_blocks_versions_terms();
	$active = wp_list_pluck( $terms, 'slug' );
	$override = sanitize_key( (string) get_option( 'autodocs_publish_version_override', '' ) );
	if ( $override && in_array( $override, $active, true ) ) {
		return autodocs_publish_blocks_versions_filter_effective_slug( $override, $terms );
	}

	$repository = sanitize_key( (string) get_option( 'autodocs_publish_repository_latest_version', '' ) );
	if ( $repository && in_array( $repository, $active, true ) ) {
		return autodocs_publish_blocks_versions_filter_effective_slug( $repository, $terms );
	}

	foreach ( $terms as $term ) {
		if ( rest_sanitize_boolean( get_term_meta( $term->term_id, 'autodocs_publish_version_repository_latest', true ) ) ) {
			return autodocs_publish_blocks_versions_filter_effective_slug( $term->slug, $terms );
		}
	}

	return $terms ? autodocs_publish_blocks_versions_filter_effective_slug( $terms[0]->slug, $terms ) : '';
}

/**
 * Keep the effective-latest term flag singular after settings changes.
 */
function autodocs_publish_blocks_versions_refresh_latest_flags() {
	$latest = autodocs_publish_blocks_versions_effective_slug();
	foreach ( autodocs_publish_blocks_versions_terms( false ) as $term ) {
		$expected = $latest && $term->slug === $latest && rest_sanitize_boolean( get_term_meta( $term->term_id, 'autodocs_publish_version_active', true ) );
		$current  = rest_sanitize_boolean( get_term_meta( $term->term_id, 'autodocs_publish_version_effective_latest', true ) );
		if ( $expected !== $current ) {
			update_term_meta( $term->term_id, 'autodocs_publish_version_effective_latest', $expected );
		}
	}
}
add_action( 'update_option_autodocs_publish_version_override', 'autodocs_publish_blocks_versions_after_latest_change' );
add_action( 'update_option_autodocs_publish_repository_latest_version', 'autodocs_publish_blocks_versions_after_latest_change' );
add_action( 'add_option_autodocs_publish_version_override', 'autodocs_publish_blocks_versions_after_latest_change' );
add_action( 'add_option_autodocs_publish_repository_latest_version', 'autodocs_publish_blocks_versions_after_latest_change' );

/**
 * Refresh latest state and routes after a repository or administrator change.
 */
function autodocs_publish_blocks_versions_after_latest_change() {
	autodocs_publish_blocks_versions_refresh_latest_flags();
	autodocs_publish_blocks_versions_flush_rules();
}

/**
 * Return version context for one managed Page.
 *
 * @param int $post_id Page ID.
 * @return array<string,mixed>|null
 */
function autodocs_publish_blocks_versions_page_context( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_queried_object_id();
	$version = sanitize_key( (string) get_post_meta( $post_id, '_autodocs_publish_version_id', true ) );
	if ( ! $post_id || ! $version ) {
		return null;
	}

	$term = get_term_by( 'slug', $version, AUTODOCS_PUBLISH_VERSION_TAXONOMY );
	if ( ! $term instanceof WP_Term ) {
		return null;
	}

	return array(
		'post_id'       => $post_id,
		'version'       => $version,
		'label'         => $term->name,
		'term'          => $term,
		'logical_route' => trim( (string) get_post_meta( $post_id, '_autodocs_publish_logical_route', true ), '/' ),
		'root'          => sanitize_title( (string) get_post_meta( $post_id, '_autodocs_publish_docs_root', true ) ) ?: sanitize_title( (string) get_option( 'autodocs_publish_docs_root_slug', 'docs' ) ),
		'latest'        => $version === autodocs_publish_blocks_versions_effective_slug(),
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
function autodocs_publish_blocks_versions_find_page( $version, $logical_route, $root = '' ) {
	$root = $root ? sanitize_title( $root ) : sanitize_title( (string) get_option( 'autodocs_publish_docs_root_slug', 'docs' ) );
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
					'key'   => '_autodocs_publish_version_id',
					'value' => sanitize_key( $version ),
				),
				array(
					'key'   => '_autodocs_publish_logical_route',
					'value' => trim( (string) $logical_route, '/' ),
				),
				array(
					'key'   => '_autodocs_publish_docs_root',
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
function autodocs_publish_blocks_versions_page_url( $post_id ) {
	$context = autodocs_publish_blocks_versions_page_context( $post_id );
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
	$url = (string) apply_filters( 'docspress_version_target_url', $url, $context );
	return (string) apply_filters( 'autodocs_publish_version_target_url', $url, $context );
}

/**
 * Filter managed Page permalinks to their public canonical route.
 *
 * @param string $link Page permalink.
 * @param int    $post_id Page ID.
 * @return string
 */
function autodocs_publish_blocks_versions_page_link( $link, $post_id ) {
	return autodocs_publish_blocks_versions_page_context( $post_id )
		? autodocs_publish_blocks_versions_page_url( $post_id )
		: $link;
}
add_filter( 'page_link', 'autodocs_publish_blocks_versions_page_link', 10, 2 );

/**
 * Convert stable internal version links to the effective public routes at render time.
 *
 * @param string $content Rendered Page content.
 * @return string
 */
function autodocs_publish_blocks_versions_content_links( $content ) {
	$context = autodocs_publish_blocks_versions_page_context();
	if ( ! $context || ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return $content;
	}

	$processor = new WP_HTML_Tag_Processor( $content );
	$versions  = wp_list_pluck( autodocs_publish_blocks_versions_terms(), 'slug' );
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
		$page    = autodocs_publish_blocks_versions_find_page( $version, $logical, $context['root'] );
		if ( ! $page ) {
			continue;
		}
		$url = autodocs_publish_blocks_versions_page_url( $page->ID );
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
add_filter( 'the_content', 'autodocs_publish_blocks_versions_content_links', 20 );

/**
 * Register clean latest and explicit historical routes.
 */
function autodocs_publish_blocks_versions_rewrite_rules() {
	$root = sanitize_title( (string) get_option( 'autodocs_publish_docs_root_slug', 'docs' ) );
	if ( ! $root || ! autodocs_publish_blocks_versions_terms() ) {
		return;
	}
	$quoted = preg_quote( $root, '#' );
	$rules = array(
		'^' . $quoted . '\.md/?$'       => 'index.php?autodocs_publish_version_route=__root__&autodocs_publish_version_markdown=1',
		'^' . $quoted . '/(.+)\.md/?$'  => 'index.php?autodocs_publish_version_route=$matches[1]&autodocs_publish_version_markdown=1',
		'^' . $quoted . '/?$'           => 'index.php?autodocs_publish_version_route=__root__',
		'^' . $quoted . '/(.+?)/?$'     => 'index.php?autodocs_publish_version_route=$matches[1]',
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
add_action( 'init', 'autodocs_publish_blocks_versions_rewrite_rules', 7 );

/**
 * Add version routing query variables.
 *
 * @param string[] $query_vars Query vars.
 * @return string[]
 */
function autodocs_publish_blocks_versions_query_vars( $query_vars ) {
	$query_vars[] = 'autodocs_publish_version_route';
	$query_vars[] = 'autodocs_publish_version_markdown';
	return $query_vars;
}
add_filter( 'query_vars', 'autodocs_publish_blocks_versions_query_vars' );

/**
 * Resolve clean and explicit version routes to stable internal Pages.
 *
 * @param WP $wp Request object.
 */
function autodocs_publish_blocks_versions_parse_request( $wp ) {
	if ( ! array_key_exists( 'autodocs_publish_version_route', $wp->query_vars ) ) {
		return;
	}

	$route = trim( rawurldecode( (string) $wp->query_vars['autodocs_publish_version_route'] ), '/' );
	$route = '__root__' === $route ? '' : $route;
	if ( false !== strpos( $route, "\0" ) || preg_match( '#(?:^|/)\.{1,2}(?:/|$)#', $route ) ) {
		return;
	}

	$terms = autodocs_publish_blocks_versions_terms();
	$slugs = wp_list_pluck( $terms, 'slug' );
	$parts = $route ? explode( '/', $route ) : array();
	$explicit = $parts && in_array( $parts[0], $slugs, true ) ? array_shift( $parts ) : '';
	$version = $explicit ? $explicit : autodocs_publish_blocks_versions_effective_slug();
	$logical_route = implode( '/', $parts );
	if ( ! $explicit ) {
		$logical_route = $route;
	}
	$root = sanitize_title( (string) get_option( 'autodocs_publish_docs_root_slug', 'docs' ) );
	$page = $version ? autodocs_publish_blocks_versions_find_page( $version, $logical_route, $root ) : null;
	if ( ! $page ) {
		return;
	}

	unset( $wp->query_vars['name'], $wp->query_vars['pagename'] );
	$wp->query_vars['page_id'] = $page->ID;
	$GLOBALS['autodocs_publish_version_request'] = array(
		'page_id'       => $page->ID,
		'version'       => $version,
		'explicit'      => $explicit,
		'logical_route' => $logical_route,
		'root'          => $root,
		'markdown'      => ! empty( $wp->query_vars['autodocs_publish_version_markdown'] ),
	);
}
add_action( 'parse_request', 'autodocs_publish_blocks_versions_parse_request', 5 );

/**
 * Avoid core canonical redirects fighting the virtual latest route.
 *
 * @param string|false $redirect Redirect URL.
 * @return string|false
 */
function autodocs_publish_blocks_versions_disable_core_canonical( $redirect ) {
	return isset( $GLOBALS['autodocs_publish_version_request'] ) ? false : $redirect;
}
add_filter( 'redirect_canonical', 'autodocs_publish_blocks_versions_disable_core_canonical' );

/**
 * Redirect explicit latest routes and serve source Markdown.
 */
function autodocs_publish_blocks_versions_template_redirect() {
	if ( empty( $GLOBALS['autodocs_publish_version_request'] ) ) {
		return;
	}
	$request = $GLOBALS['autodocs_publish_version_request'];
	$latest = autodocs_publish_blocks_versions_effective_slug();

	if ( $request['explicit'] && $request['explicit'] === $latest ) {
		$target = autodocs_publish_blocks_versions_page_url( $request['page_id'] );
		if ( ! empty( $request['markdown'] ) ) {
			$target = untrailingslashit( $target ) . '.md';
		}
		wp_safe_redirect( $target, 301, 'Autodocs Publish' );
		exit;
	}

	if ( ! empty( $request['markdown'] ) ) {
		$markdown = function_exists( 'autodocs_publish_get_markdown_source_content' )
			? autodocs_publish_get_markdown_source_content( $request['page_id'] )
			: null;
		if ( null === $markdown ) {
			$markdown = autodocs_publish_blocks_versions_source_markdown( $request['page_id'] );
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
add_action( 'template_redirect', 'autodocs_publish_blocks_versions_template_redirect', -5 );

/**
 * Decode source Markdown from a Page sentinel when the Autodocs Publish theme is absent.
 *
 * @param int $post_id Page ID.
 * @return string|null
 */
function autodocs_publish_blocks_versions_source_markdown( $post_id ) {
	$content = (string) get_post_field( 'post_content', $post_id );
	if ( ! preg_match( '/<!--\s*(?:autodocs-publish|docspress):(.*?)\s*-->/s', $content, $match ) ) {
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
function autodocs_publish_blocks_versions_flush_rules() {
	autodocs_publish_blocks_versions_rewrite_rules();
	flush_rewrite_rules( false );
}
register_activation_hook( AUTODOCS_PUBLISH_BLOCKS_FILE, 'autodocs_publish_blocks_versions_flush_rules' );
add_action( 'update_option_autodocs_publish_docs_root_slug', 'autodocs_publish_blocks_versions_flush_rules' );
add_action( 'add_option_autodocs_publish_docs_root_slug', 'autodocs_publish_blocks_versions_flush_rules' );
add_action( 'rest_after_insert_' . AUTODOCS_PUBLISH_VERSION_TAXONOMY, 'autodocs_publish_blocks_versions_flush_rules' );

/**
 * Add the Autodocs Publish version settings screen.
 */
function autodocs_publish_blocks_versions_settings_menu() {
	add_options_page(
		__( 'Autodocs Publish versions', 'autodocs-publish-blocks' ),
		__( 'Autodocs Publish', 'autodocs-publish-blocks' ),
		'manage_options',
		'autodocs-publish-versions',
		'autodocs_publish_blocks_versions_settings_page'
	);
}
add_action( 'admin_menu', 'autodocs_publish_blocks_versions_settings_menu' );

/**
 * Render the version settings screen.
 */
function autodocs_publish_blocks_versions_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$terms = autodocs_publish_blocks_versions_terms();
	$repository = sanitize_key( (string) get_option( 'autodocs_publish_repository_latest_version', '' ) );
	$override = sanitize_key( (string) get_option( 'autodocs_publish_version_override', '' ) );
	$effective = autodocs_publish_blocks_versions_effective_slug();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Autodocs Publish versions', 'autodocs-publish-blocks' ); ?></h1>
		<p><?php esc_html_e( 'The repository supplies the default API version. Choose an override to change the public latest version immediately without moving Pages.', 'autodocs-publish-blocks' ); ?></p>
		<table class="widefat striped" style="max-width:760px;margin:20px 0">
			<tbody>
				<tr><th><?php esc_html_e( 'Repository default', 'autodocs-publish-blocks' ); ?></th><td><code><?php echo esc_html( $repository ?: '—' ); ?></code></td></tr>
				<tr><th><?php esc_html_e( 'Effective latest', 'autodocs-publish-blocks' ); ?></th><td><strong><?php echo esc_html( $effective ?: '—' ); ?></strong></td></tr>
			</tbody>
		</table>
		<form method="post" action="options.php">
			<?php settings_fields( 'autodocs_publish_versions' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="autodocs-publish-version-override"><?php esc_html_e( 'Latest-version override', 'autodocs-publish-blocks' ); ?></label></th>
					<td>
						<select id="autodocs-publish-version-override" name="autodocs_publish_version_override">
							<option value=""><?php esc_html_e( 'Use repository default', 'autodocs-publish-blocks' ); ?></option>
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
function autodocs_publish_blocks_versions_has_github_paths() {
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
			'meta_key'               => '_autodocs_publish_github_path',
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
 * Add Autodocs Publish management columns to the Pages screen.
 *
 * @param array<string,string> $columns Columns.
 * @return array<string,string>
 */
function autodocs_publish_blocks_versions_page_columns( $columns ) {
	$columns['autodocs_publish_version'] = __( 'Docs version', 'autodocs-publish-blocks' );
	if ( autodocs_publish_blocks_versions_has_github_paths() ) {
		$columns['autodocs_publish_github_path'] = __( 'GitHub path', 'autodocs-publish-blocks' );
	}
	return $columns;
}
add_filter( 'manage_pages_columns', 'autodocs_publish_blocks_versions_page_columns' );

/**
 * Resolve a managed Page's exact GitHub source.
 *
 * @param int $post_id Page ID.
 * @return array{path:string,url:string}|null
 */
function autodocs_publish_blocks_versions_github_source( $post_id ) {
	$source = array(
		'path'       => (string) get_post_meta( $post_id, '_autodocs_publish_github_path', true ),
		'repository' => (string) get_post_meta( $post_id, '_autodocs_publish_github_repository', true ),
		'ref'        => (string) get_post_meta( $post_id, '_autodocs_publish_github_ref', true ),
		'server_url' => (string) get_post_meta( $post_id, '_autodocs_publish_github_server_url', true ),
	);
	/**
	 * Filter the GitHub source metadata used by the Pages column.
	 *
	 * @param array{path:string,repository:string,ref:string,server_url:string} $source Source metadata.
	 * @param int                                                               $post_id Page ID.
	 */
	$source = apply_filters( 'docspress_github_source', $source, $post_id );
	$source = apply_filters( 'autodocs_publish_github_source', $source, $post_id );
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
	$url = (string) apply_filters( 'autodocs_publish_github_source_url', $url, $path, $post_id );
	if ( ! $url ) {
		return null;
	}
	return array(
		'path' => $path,
		'url'  => $url,
	);
}

/**
 * Render the Autodocs Publish Page columns.
 *
 * @param string $column Column name.
 * @param int    $post_id Page ID.
 */
function autodocs_publish_blocks_versions_page_column( $column, $post_id ) {
	if ( 'autodocs_publish_github_path' === $column ) {
		$source = autodocs_publish_blocks_versions_github_source( $post_id );
		if ( ! $source ) {
			echo '<span aria-hidden="true">—</span>';
			return;
		}
		printf(
			'<a href="%1$s" target="_blank" rel="noopener noreferrer" aria-label="%2$s"><code>%3$s</code><span class="screen-reader-text"> %4$s</span></a>',
			esc_url( $source['url'] ),
			esc_attr( sprintf( __( 'Open %s on GitHub in a new tab', 'autodocs-publish-blocks' ), $source['path'] ) ),
			esc_html( $source['path'] ),
			esc_html__( '(opens in a new tab)', 'autodocs-publish-blocks' )
		);
		return;
	}
	if ( 'autodocs_publish_version' !== $column ) {
		return;
	}
	$context = autodocs_publish_blocks_versions_page_context( $post_id );
	if ( ! $context ) {
		if ( get_post_meta( $post_id, '_autodocs_publish_version_container', true ) ) {
			echo '<span class="post-state">' . esc_html__( 'All versions', 'autodocs-publish-blocks' ) . '</span>';
			return;
		}
		echo '<span aria-hidden="true">—</span>';
		return;
	}
	$url = add_query_arg(
		array(
			'post_type'               => 'page',
			'autodocs_publish_docs_version'  => $context['version'],
		),
		admin_url( 'edit.php' )
	);
	printf(
		'<a href="%1$s"><strong>%2$s</strong></a>%3$s',
		esc_url( $url ),
		esc_html( $context['label'] ),
		$context['latest'] ? ' <span class="post-state">' . esc_html__( 'Latest', 'autodocs-publish-blocks' ) . '</span>' : ''
	);
	if ( ! rest_sanitize_boolean( get_term_meta( $context['term']->term_id, 'autodocs_publish_version_active', true ) ) ) {
		echo ' <span class="post-state">' . esc_html__( 'Inactive', 'autodocs-publish-blocks' ) . '</span>';
	}
}
add_action( 'manage_pages_custom_column', 'autodocs_publish_blocks_versions_page_column', 10, 2 );

/**
 * Make the version column sortable.
 *
 * @param array<string,string> $columns Sortable columns.
 * @return array<string,string>
 */
function autodocs_publish_blocks_versions_sortable_columns( $columns ) {
	$columns['autodocs_publish_version'] = 'autodocs_publish_version';
	return $columns;
}
add_filter( 'manage_edit-page_sortable_columns', 'autodocs_publish_blocks_versions_sortable_columns' );

/**
 * Render the version filter above the Pages table.
 */
function autodocs_publish_blocks_versions_admin_filter() {
	global $typenow;
	if ( 'page' !== $typenow ) {
		return;
	}
	$current = isset( $_GET['autodocs_publish_docs_version'] ) ? sanitize_key( wp_unslash( $_GET['autodocs_publish_docs_version'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$terms   = autodocs_publish_blocks_versions_terms( false );
	$all_count = array_sum( wp_list_pluck( $terms, 'count' ) );
	$latest_slug = autodocs_publish_blocks_versions_effective_slug();
	$latest_term = $latest_slug ? get_term_by( 'slug', $latest_slug, AUTODOCS_PUBLISH_VERSION_TAXONOMY ) : null;
	?>
	<label class="screen-reader-text" for="autodocs-publish-docs-version-filter"><?php esc_html_e( 'Filter Pages by documentation version', 'autodocs-publish-blocks' ); ?></label>
	<select name="autodocs_publish_docs_version" id="autodocs-publish-docs-version-filter">
		<option value=""><?php echo esc_html( sprintf( __( 'All versions (%d)', 'autodocs-publish-blocks' ), $all_count ) ); ?></option>
		<option value="latest" <?php selected( $current, 'latest' ); ?>><?php echo esc_html( sprintf( __( 'Latest (%d)', 'autodocs-publish-blocks' ), $latest_term instanceof WP_Term ? $latest_term->count : 0 ) ); ?></option>
		<?php foreach ( $terms as $term ) : ?>
			<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $current, $term->slug ); ?>><?php echo esc_html( sprintf( '%1$s (%2$d)%3$s', $term->name, $term->count, rest_sanitize_boolean( get_term_meta( $term->term_id, 'autodocs_publish_version_active', true ) ) ? '' : __( ' — inactive', 'autodocs-publish-blocks' ) ) ); ?></option>
		<?php endforeach; ?>
	</select>
	<?php
}
add_action( 'restrict_manage_posts', 'autodocs_publish_blocks_versions_admin_filter' );

/**
 * Apply Page-list version filtering.
 *
 * @param WP_Query $query Query.
 */
function autodocs_publish_blocks_versions_admin_query( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || 'page' !== $query->get( 'post_type' ) ) {
		return;
	}
	$selected = isset( $_GET['autodocs_publish_docs_version'] ) ? sanitize_key( wp_unslash( $_GET['autodocs_publish_docs_version'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( 'latest' === $selected ) {
		$selected = autodocs_publish_blocks_versions_effective_slug();
	}
	if ( $selected ) {
		$query->set(
			'tax_query',
			array(
				array(
					'taxonomy' => AUTODOCS_PUBLISH_VERSION_TAXONOMY,
					'field'    => 'slug',
					'terms'    => array( $selected ),
				),
			)
		);
	}
}
add_action( 'pre_get_posts', 'autodocs_publish_blocks_versions_admin_query' );

/**
 * Sort the custom version column by configured term order.
 *
 * @param array<string,string> $clauses SQL clauses.
 * @param WP_Query             $query Query.
 * @return array<string,string>
 */
function autodocs_publish_blocks_versions_admin_sort( $clauses, $query ) {
	global $wpdb;
	if ( ! is_admin() || ! $query->is_main_query() || 'autodocs_publish_version' !== $query->get( 'orderby' ) ) {
		return $clauses;
	}
	$clauses['join'] .= " LEFT JOIN {$wpdb->term_relationships} dpv_tr ON ({$wpdb->posts}.ID = dpv_tr.object_id)";
	$clauses['join'] .= " LEFT JOIN {$wpdb->term_taxonomy} dpv_tt ON (dpv_tr.term_taxonomy_id = dpv_tt.term_taxonomy_id AND dpv_tt.taxonomy = '" . esc_sql( AUTODOCS_PUBLISH_VERSION_TAXONOMY ) . "')";
	$clauses['join'] .= " LEFT JOIN {$wpdb->termmeta} dpv_tm ON (dpv_tt.term_id = dpv_tm.term_id AND dpv_tm.meta_key = 'autodocs_publish_version_order')";
	$direction = 'DESC' === strtoupper( (string) $query->get( 'order' ) ) ? 'DESC' : 'ASC';
	$clauses['orderby'] = "COALESCE(MIN(CAST(dpv_tm.meta_value AS UNSIGNED)), 999999) {$direction}, {$wpdb->posts}.post_title ASC";
	$clauses['groupby'] = "{$wpdb->posts}.ID";
	return $clauses;
}
add_filter( 'posts_clauses', 'autodocs_publish_blocks_versions_admin_sort', 10, 2 );

/**
 * Add latest/version state beside Page titles.
 *
 * @param string[] $states States.
 * @param WP_Post  $post Page.
 * @return string[]
 */
function autodocs_publish_blocks_versions_post_states( $states, $post ) {
	$context = 'page' === $post->post_type ? autodocs_publish_blocks_versions_page_context( $post->ID ) : null;
	if ( $context ) {
		$states[] = $context['latest']
			? sprintf( __( 'Docs %s · Latest', 'autodocs-publish-blocks' ), $context['label'] )
			: sprintf( __( 'Docs %s', 'autodocs-publish-blocks' ), $context['label'] );
	}
	return $states;
}
add_filter( 'display_post_states', 'autodocs_publish_blocks_versions_post_states', 10, 2 );

/**
 * Keep managed version assignment read-only in the Page editor.
 */
function autodocs_publish_blocks_versions_remove_metabox() {
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $post_id && get_post_meta( $post_id, '_autodocs_publish_version_id', true ) ) {
		remove_meta_box( 'tagsdiv-' . AUTODOCS_PUBLISH_VERSION_TAXONOMY, 'page', 'side' );
	}
}
add_action( 'add_meta_boxes_page', 'autodocs_publish_blocks_versions_remove_metabox' );

/**
 * Exclude the stable internal version container from public XML sitemaps.
 *
 * @param array  $args Query arguments.
 * @param string $post_type Post type.
 * @return array
 */
function autodocs_publish_blocks_versions_sitemap_args( $args, $post_type ) {
	if ( 'page' !== $post_type ) {
		return $args;
	}
	$args['meta_query'] = array(
		'relation' => 'OR',
		array(
			'key'     => '_autodocs_publish_version_container',
			'compare' => 'NOT EXISTS',
		),
		array(
			'key'     => '_autodocs_publish_version_container',
			'value'   => '1',
			'compare' => '!=',
		),
	);
	return $args;
}
add_filter( 'wp_sitemaps_posts_query_args', 'autodocs_publish_blocks_versions_sitemap_args', 10, 2 );
