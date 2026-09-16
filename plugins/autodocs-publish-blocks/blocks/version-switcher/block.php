<?php
/**
 * Version Switcher block.
 *
 * @package AutodocsPublishBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render a version choice and resolve missing counterparts to the version root.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content Inner content.
 * @param WP_Block $block Block instance.
 * @return string
 */
function autodocs_publish_blocks_render_version_switcher( $attributes, $content = '', $block = null ) {
	$post_id = $block instanceof WP_Block && ! empty( $block->context['postId'] )
		? absint( $block->context['postId'] )
		: get_queried_object_id();
	$context = autodocs_publish_blocks_versions_page_context( $post_id );
	$terms   = autodocs_publish_blocks_versions_terms();
	if ( ! $context || ( count( $terms ) < 2 && ! empty( $attributes['hideSingle'] ) ) ) {
		return '';
	}

	$label               = isset( $attributes['label'] ) ? sanitize_text_field( $attributes['label'] ) : __( 'Version', 'autodocs-publish-blocks' );
	$show_label          = ! isset( $attributes['showLabel'] ) || (bool) $attributes['showLabel'];
	$presentation        = isset( $attributes['presentation'] ) && 'links' === $attributes['presentation'] ? 'links' : 'select';
	$show_latest_badge   = ! isset( $attributes['showLatestBadge'] ) || (bool) $attributes['showLatestBadge'];
	$unavailable_wording = isset( $attributes['unavailableLabel'] ) ? sanitize_text_field( $attributes['unavailableLabel'] ) : __( 'Page unavailable', 'autodocs-publish-blocks' );
	$latest              = autodocs_publish_blocks_versions_effective_slug();
	$choices             = array();

	foreach ( $terms as $term ) {
		$counterpart = autodocs_publish_blocks_versions_find_page( $term->slug, $context['logical_route'], $context['root'] );
		$missing     = ! $counterpart;
		if ( ! $counterpart ) {
			$counterpart = autodocs_publish_blocks_versions_find_page( $term->slug, '', $context['root'] );
		}
		if ( ! $counterpart ) {
			continue;
		}
		$choices[] = array(
			'term'    => $term,
			'url'     => autodocs_publish_blocks_versions_page_url( $counterpart->ID ),
			'current' => $term->slug === $context['version'],
			'latest'  => $term->slug === $latest,
			'missing' => $missing,
		);
	}

	/**
	 * Filter the resolved version switcher choices.
	 *
	 * @param array[] $choices Resolved choices.
	 * @param array   $context Current Page version context.
	 */
	$choices = apply_filters( 'docspress_version_switcher_choices', $choices, $context );
	$choices = apply_filters( 'autodocs_publish_version_switcher_choices', $choices, $context );
	if ( ! $choices ) {
		return '';
	}

	$wrapper = get_block_wrapper_attributes(
		array(
			'class' => 'autodocs-publish-version-switcher autodocs-publish-version-switcher--' . $presentation,
		)
	);
	$label_class = $show_label ? 'autodocs-publish-version-switcher__label' : 'autodocs-publish-version-switcher__label screen-reader-text';
	$label_html  = '<span class="' . esc_attr( $label_class ) . '">' . esc_html( $label ) . '</span>';

	if ( 'links' === $presentation ) {
		$items = '';
		foreach ( $choices as $choice ) {
			$text = esc_html( $choice['term']->name );
			if ( $choice['latest'] && $show_latest_badge ) {
				$text .= ' <span class="autodocs-publish-version-switcher__badge">' . esc_html__( 'Latest', 'autodocs-publish-blocks' ) . '</span>';
			}
			if ( $choice['missing'] ) {
				$text .= ' <span class="autodocs-publish-version-switcher__unavailable">— ' . esc_html( $unavailable_wording ) . '</span>';
			}
			$items .= sprintf(
				'<li><a href="%1$s"%2$s%3$s>%4$s</a></li>',
				esc_url( $choice['url'] ),
				$choice['current'] ? ' aria-current="page"' : '',
				$choice['missing'] ? ' data-autodocs-publish-version-missing="true"' : '',
				$text
			);
		}
		return '<nav ' . $wrapper . ' aria-label="' . esc_attr( $label ) . '">' . $label_html . '<ul class="autodocs-publish-version-switcher__links">' . $items . '</ul></nav>';
	}

	$select_id = wp_unique_id( 'autodocs-publish-version-' );
	$options   = '';
	foreach ( $choices as $choice ) {
		$text = $choice['term']->name;
		if ( $choice['latest'] && $show_latest_badge ) {
			$text .= ' — ' . __( 'Latest', 'autodocs-publish-blocks' );
		}
		if ( $choice['missing'] ) {
			$text .= ' — ' . $unavailable_wording;
		}
		$options .= sprintf(
			'<option value="%1$s"%2$s>%3$s</option>',
			esc_url( $choice['url'] ),
			$choice['current'] ? ' selected' : '',
			esc_html( $text )
		);
	}

	return '<div ' . $wrapper . '><label for="' . esc_attr( $select_id ) . '">' . $label_html . '</label><span class="autodocs-publish-version-switcher__control"><select id="' . esc_attr( $select_id ) . '" data-autodocs-publish-version-select>' . $options . '</select><span class="autodocs-publish-version-switcher__chevron" aria-hidden="true"></span></span></div>';
}

/**
 * Register the Version Switcher block.
 */
function autodocs_publish_blocks_register_version_switcher() {
	$block_url = AUTODOCS_PUBLISH_BLOCKS_URL . 'blocks/version-switcher/';
	wp_register_script(
		'autodocs-publish-version-switcher-editor',
		$block_url . 'editor.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n', 'autodocs-publish-blocks-editor-shared' ),
		AUTODOCS_PUBLISH_BLOCKS_VERSION,
		true
	);
	wp_register_style( 'autodocs-publish-version-switcher', $block_url . 'style.css', array(), AUTODOCS_PUBLISH_BLOCKS_VERSION );
	wp_register_style( 'autodocs-publish-version-switcher-editor', $block_url . 'editor.css', array( 'wp-edit-blocks', 'autodocs-publish-version-switcher' ), AUTODOCS_PUBLISH_BLOCKS_VERSION );

	autodocs_publish_blocks_register_block_type(
		'autodocs-publish/version-switcher',
		array(
			'api_version'     => 3,
			'editor_script'   => 'autodocs-publish-version-switcher-editor',
			'view_script'     => 'autodocs-publish-blocks-view',
			'style'           => 'autodocs-publish-version-switcher',
			'editor_style'    => 'autodocs-publish-version-switcher-editor',
			'render_callback' => 'autodocs_publish_blocks_render_version_switcher',
			'uses_context'    => array( 'postId' ),
			'attributes'      => array(
				'label'             => array( 'type' => 'string', 'default' => 'Version' ),
				'showLabel'         => array( 'type' => 'boolean', 'default' => true ),
				'presentation'      => array( 'type' => 'string', 'default' => 'select' ),
				'showLatestBadge'   => array( 'type' => 'boolean', 'default' => true ),
				'hideSingle'        => array( 'type' => 'boolean', 'default' => true ),
				'unavailableLabel'  => array( 'type' => 'string', 'default' => 'Page unavailable' ),
			),
			'supports'        => autodocs_publish_blocks_design_supports(),
		)
	);
}
add_action( 'init', 'autodocs_publish_blocks_register_version_switcher', 10 );
