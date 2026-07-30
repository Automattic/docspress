<?php
/**
 * Historical-version notice block.
 *
 * @package DocsPressBlocks
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
function docspress_blocks_render_version_notice( $attributes, $content = '', $block = null ) {
	$post_id = $block instanceof WP_Block && ! empty( $block->context['postId'] )
		? absint( $block->context['postId'] )
		: get_queried_object_id();
	$context = docspress_blocks_versions_page_context( $post_id );
	$latest  = docspress_blocks_versions_effective_slug();
	if ( ! $context || ! $latest || $context['version'] === $latest ) {
		return '';
	}

	$latest_term = get_term_by( 'slug', $latest, DOCSPRESS_VERSION_TAXONOMY );
	if ( ! $latest_term instanceof WP_Term ) {
		return '';
	}
	$counterpart = docspress_blocks_versions_find_page( $latest, $context['logical_route'], $context['root'] );
	$missing     = ! $counterpart;
	if ( ! $counterpart ) {
		$counterpart = docspress_blocks_versions_find_page( $latest, '', $context['root'] );
	}
	if ( ! $counterpart ) {
		return '';
	}

	$template = isset( $attributes['message'] )
		? sanitize_text_field( $attributes['message'] )
		: __( 'You are viewing {current}. The latest version is {latest}.', 'docspress-blocks' );
	$message = strtr(
		$template,
		array(
			'{current}' => wp_strip_all_tags( $context['label'] ),
			'{latest}'  => wp_strip_all_tags( $latest_term->name ),
		)
	);
	$message = (string) apply_filters( 'docspress_version_notice_message', $message, $context, $latest_term );
	$link_label = isset( $attributes['latestLinkLabel'] ) ? sanitize_text_field( $attributes['latestLinkLabel'] ) : __( 'Switch to latest', 'docspress-blocks' );
	if ( $missing ) {
		$link_label .= ' — ' . __( 'this page is unavailable', 'docspress-blocks' );
	}
	$dismissible = ! empty( $attributes['dismissible'] );
	$show_icon   = ! isset( $attributes['showIcon'] ) || (bool) $attributes['showIcon'];
	$wrapper_attributes = array(
		'class' => 'docspress-version-notice',
		'role'  => 'status',
	);
	if ( $dismissible ) {
		$wrapper_attributes['data-docspress-version-notice'] = $context['version'] . ':' . $latest;
	}
	$wrapper = get_block_wrapper_attributes( $wrapper_attributes );
	$icon = $show_icon
		? '<span class="docspress-version-notice__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 11v5m0-8h.01"/></svg></span>'
		: '';
	$dismiss = $dismissible
		? '<button type="button" class="docspress-version-notice__dismiss" data-docspress-version-dismiss><span aria-hidden="true">×</span><span class="screen-reader-text">' . esc_html__( 'Dismiss version notice', 'docspress-blocks' ) . '</span></button>'
		: '';

	return sprintf(
		'<aside %1$s>%2$s<div class="docspress-version-notice__content"><p>%3$s</p><a href="%4$s">%5$s <span aria-hidden="true">→</span></a></div>%6$s</aside>',
		$wrapper,
		$icon,
		esc_html( $message ),
		esc_url( docspress_blocks_versions_page_url( $counterpart->ID ) ),
		esc_html( $link_label ),
		$dismiss
	);
}

/**
 * Register the Version Notice block.
 */
function docspress_blocks_register_version_notice() {
	$block_url = DOCSPRESS_BLOCKS_URL . 'blocks/version-notice/';
	wp_register_script(
		'docspress-version-notice-editor',
		$block_url . 'editor.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n' ),
		DOCSPRESS_BLOCKS_VERSION,
		true
	);
	wp_register_style( 'docspress-version-notice', $block_url . 'style.css', array(), DOCSPRESS_BLOCKS_VERSION );
	wp_register_style( 'docspress-version-notice-editor', $block_url . 'editor.css', array( 'wp-edit-blocks', 'docspress-version-notice' ), DOCSPRESS_BLOCKS_VERSION );

	register_block_type(
		'docspress/version-notice',
		array(
			'api_version'     => 3,
			'editor_script'   => 'docspress-version-notice-editor',
			'view_script'     => 'docspress-blocks-view',
			'style'           => 'docspress-version-notice',
			'editor_style'    => 'docspress-version-notice-editor',
			'render_callback' => 'docspress_blocks_render_version_notice',
			'uses_context'    => array( 'postId' ),
			'attributes'      => array(
				'message'          => array( 'type' => 'string', 'default' => 'You are viewing {current}. The latest version is {latest}.' ),
				'latestLinkLabel'  => array( 'type' => 'string', 'default' => 'Switch to latest' ),
				'showIcon'         => array( 'type' => 'boolean', 'default' => true ),
				'dismissible'      => array( 'type' => 'boolean', 'default' => false ),
			),
			'supports'        => docspress_blocks_design_supports( array( 'wide', 'full' ) ),
		)
	);
}
add_action( 'init', 'docspress_blocks_register_version_notice', 10 );
