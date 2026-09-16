<?php
/**
 * Historical-version notice block.
 *
 * @package AutodocsPublishBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the notice on historical version Pages only.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content Inner content.
 * @param WP_Block $block Block instance.
 * @return string
 */
function autodocs_publish_blocks_render_version_notice( $attributes, $content = '', $block = null ) {
	$post_id = $block instanceof WP_Block && ! empty( $block->context['postId'] )
		? absint( $block->context['postId'] )
		: get_queried_object_id();
	$context = autodocs_publish_blocks_versions_page_context( $post_id );
	$latest  = autodocs_publish_blocks_versions_effective_slug();
	if ( ! $context || ! $latest || $context['version'] === $latest ) {
		return '';
	}

	$latest_term = get_term_by( 'slug', $latest, AUTODOCS_PUBLISH_VERSION_TAXONOMY );
	if ( ! $latest_term instanceof WP_Term ) {
		return '';
	}
	$counterpart = autodocs_publish_blocks_versions_find_page( $latest, $context['logical_route'], $context['root'] );
	$missing     = ! $counterpart;
	if ( ! $counterpart ) {
		$counterpart = autodocs_publish_blocks_versions_find_page( $latest, '', $context['root'] );
	}
	if ( ! $counterpart ) {
		return '';
	}

	$template = isset( $attributes['message'] )
		? sanitize_text_field( $attributes['message'] )
		: __( 'You are viewing {current}. The latest version is {latest}.', 'autodocs-publish-blocks' );
	$message = strtr(
		$template,
		array(
			'{current}' => wp_strip_all_tags( $context['label'] ),
			'{latest}'  => wp_strip_all_tags( $latest_term->name ),
		)
	);
	$message = (string) apply_filters( 'docspress_version_notice_message', $message, $context, $latest_term );
	$message = (string) apply_filters( 'autodocs_publish_version_notice_message', $message, $context, $latest_term );
	$link_label = isset( $attributes['latestLinkLabel'] ) ? sanitize_text_field( $attributes['latestLinkLabel'] ) : __( 'Switch to latest', 'autodocs-publish-blocks' );
	if ( $missing ) {
		$link_label .= ' — ' . __( 'this page is unavailable', 'autodocs-publish-blocks' );
	}
	$dismissible = ! empty( $attributes['dismissible'] );
	$show_icon   = ! isset( $attributes['showIcon'] ) || (bool) $attributes['showIcon'];
	$wrapper_attributes = array(
		'class' => 'autodocs-publish-version-notice',
		'role'  => 'status',
	);
	if ( $dismissible ) {
		$wrapper_attributes['data-autodocs-publish-version-notice'] = $context['version'] . ':' . $latest;
	}
	$wrapper = get_block_wrapper_attributes( $wrapper_attributes );
	$icon = $show_icon
		? '<span class="autodocs-publish-version-notice__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 11v5m0-8h.01"/></svg></span>'
		: '';
	$dismiss = $dismissible
		? '<button type="button" class="autodocs-publish-version-notice__dismiss" data-autodocs-publish-version-dismiss><span aria-hidden="true">×</span><span class="screen-reader-text">' . esc_html__( 'Dismiss version notice', 'autodocs-publish-blocks' ) . '</span></button>'
		: '';

	return sprintf(
		'<aside %1$s>%2$s<div class="autodocs-publish-version-notice__content"><p>%3$s</p><a href="%4$s">%5$s <span aria-hidden="true">→</span></a></div>%6$s</aside>',
		$wrapper,
		$icon,
		esc_html( $message ),
		esc_url( autodocs_publish_blocks_versions_page_url( $counterpart->ID ) ),
		esc_html( $link_label ),
		$dismiss
	);
}

/**
 * Register the Version Notice block.
 */
function autodocs_publish_blocks_register_version_notice() {
	$block_url = AUTODOCS_PUBLISH_BLOCKS_URL . 'blocks/version-notice/';
	wp_register_script(
		'autodocs-publish-version-notice-editor',
		$block_url . 'editor.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n', 'autodocs-publish-blocks-editor-shared' ),
		AUTODOCS_PUBLISH_BLOCKS_VERSION,
		true
	);
	wp_register_style( 'autodocs-publish-version-notice', $block_url . 'style.css', array(), AUTODOCS_PUBLISH_BLOCKS_VERSION );
	wp_register_style( 'autodocs-publish-version-notice-editor', $block_url . 'editor.css', array( 'wp-edit-blocks', 'autodocs-publish-version-notice' ), AUTODOCS_PUBLISH_BLOCKS_VERSION );

	autodocs_publish_blocks_register_block_type(
		'autodocs-publish/version-notice',
		array(
			'api_version'     => 3,
			'editor_script'   => 'autodocs-publish-version-notice-editor',
			'view_script'     => 'autodocs-publish-blocks-view',
			'style'           => 'autodocs-publish-version-notice',
			'editor_style'    => 'autodocs-publish-version-notice-editor',
			'render_callback' => 'autodocs_publish_blocks_render_version_notice',
			'uses_context'    => array( 'postId' ),
			'attributes'      => array(
				'message'          => array( 'type' => 'string', 'default' => 'You are viewing {current}. The latest version is {latest}.' ),
				'latestLinkLabel'  => array( 'type' => 'string', 'default' => 'Switch to latest' ),
				'showIcon'         => array( 'type' => 'boolean', 'default' => true ),
				'dismissible'      => array( 'type' => 'boolean', 'default' => false ),
			),
			'supports'        => autodocs_publish_blocks_design_supports( array( 'wide', 'full' ) ),
		)
	);
}
add_action( 'init', 'autodocs_publish_blocks_register_version_notice', 10 );
