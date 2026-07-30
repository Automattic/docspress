<?php
/**
 * Version Switcher block.
 *
 * @package DocsPressBlocks
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
function docspress_blocks_render_version_switcher( $attributes, $content = '', $block = null ) {
	$post_id = $block instanceof WP_Block && ! empty( $block->context['postId'] )
		? absint( $block->context['postId'] )
		: get_queried_object_id();
	$context = docspress_blocks_versions_page_context( $post_id );
	$terms   = docspress_blocks_versions_terms();
	if ( ! $context || ( count( $terms ) < 2 && ! empty( $attributes['hideSingle'] ) ) ) {
		return '';
	}

	$label               = isset( $attributes['label'] ) ? sanitize_text_field( $attributes['label'] ) : __( 'Version', 'docspress-blocks' );
	$show_label          = ! isset( $attributes['showLabel'] ) || (bool) $attributes['showLabel'];
	$presentation        = isset( $attributes['presentation'] ) && 'links' === $attributes['presentation'] ? 'links' : 'select';
	$show_latest_badge   = ! isset( $attributes['showLatestBadge'] ) || (bool) $attributes['showLatestBadge'];
	$unavailable_wording = isset( $attributes['unavailableLabel'] ) ? sanitize_text_field( $attributes['unavailableLabel'] ) : __( 'Page unavailable', 'docspress-blocks' );
	$latest              = docspress_blocks_versions_effective_slug();
	$choices             = array();

	foreach ( $terms as $term ) {
		$counterpart = docspress_blocks_versions_find_page( $term->slug, $context['logical_route'], $context['root'] );
		$missing     = ! $counterpart;
		if ( ! $counterpart ) {
			$counterpart = docspress_blocks_versions_find_page( $term->slug, '', $context['root'] );
		}
		if ( ! $counterpart ) {
			continue;
		}
		$choices[] = array(
			'term'    => $term,
			'url'     => docspress_blocks_versions_page_url( $counterpart->ID ),
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
	if ( ! $choices ) {
		return '';
	}

	$wrapper = get_block_wrapper_attributes(
		array(
			'class' => 'docspress-version-switcher docspress-version-switcher--' . $presentation,
		)
	);
	$label_class = $show_label ? 'docspress-version-switcher__label' : 'docspress-version-switcher__label screen-reader-text';
	$label_html  = '<span class="' . esc_attr( $label_class ) . '">' . esc_html( $label ) . '</span>';

	if ( 'links' === $presentation ) {
		$items = '';
		foreach ( $choices as $choice ) {
			$text = esc_html( $choice['term']->name );
			if ( $choice['latest'] && $show_latest_badge ) {
				$text .= ' <span class="docspress-version-switcher__badge">' . esc_html__( 'Latest', 'docspress-blocks' ) . '</span>';
			}
			if ( $choice['missing'] ) {
				$text .= ' <span class="docspress-version-switcher__unavailable">— ' . esc_html( $unavailable_wording ) . '</span>';
			}
			$items .= sprintf(
				'<li><a href="%1$s"%2$s%3$s>%4$s</a></li>',
				esc_url( $choice['url'] ),
				$choice['current'] ? ' aria-current="page"' : '',
				$choice['missing'] ? ' data-docspress-version-missing="true"' : '',
				$text
			);
		}
		return '<nav ' . $wrapper . ' aria-label="' . esc_attr( $label ) . '">' . $label_html . '<ul class="docspress-version-switcher__links">' . $items . '</ul></nav>';
	}

	$select_id = wp_unique_id( 'docspress-version-' );
	$options   = '';
	foreach ( $choices as $choice ) {
		$text = $choice['term']->name;
		if ( $choice['latest'] && $show_latest_badge ) {
			$text .= ' — ' . __( 'Latest', 'docspress-blocks' );
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

	return '<div ' . $wrapper . '><label for="' . esc_attr( $select_id ) . '">' . $label_html . '</label><span class="docspress-version-switcher__control"><select id="' . esc_attr( $select_id ) . '" data-docspress-version-select>' . $options . '</select><span class="docspress-version-switcher__chevron" aria-hidden="true"></span></span></div>';
}

/**
 * Register the Version Switcher block.
 */
function docspress_blocks_register_version_switcher() {
	$block_url = DOCSPRESS_BLOCKS_URL . 'blocks/version-switcher/';
	wp_register_script(
		'docspress-version-switcher-editor',
		$block_url . 'editor.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n' ),
		DOCSPRESS_BLOCKS_VERSION,
		true
	);
	wp_register_style( 'docspress-version-switcher', $block_url . 'style.css', array(), DOCSPRESS_BLOCKS_VERSION );
	wp_register_style( 'docspress-version-switcher-editor', $block_url . 'editor.css', array( 'wp-edit-blocks', 'docspress-version-switcher' ), DOCSPRESS_BLOCKS_VERSION );

	register_block_type(
		'docspress/version-switcher',
		array(
			'api_version'     => 3,
			'editor_script'   => 'docspress-version-switcher-editor',
			'view_script'     => 'docspress-blocks-view',
			'style'           => 'docspress-version-switcher',
			'editor_style'    => 'docspress-version-switcher-editor',
			'render_callback' => 'docspress_blocks_render_version_switcher',
			'uses_context'    => array( 'postId' ),
			'attributes'      => array(
				'label'             => array( 'type' => 'string', 'default' => 'Version' ),
				'showLabel'         => array( 'type' => 'boolean', 'default' => true ),
				'presentation'      => array( 'type' => 'string', 'default' => 'select' ),
				'showLatestBadge'   => array( 'type' => 'boolean', 'default' => true ),
				'hideSingle'        => array( 'type' => 'boolean', 'default' => true ),
				'unavailableLabel'  => array( 'type' => 'string', 'default' => 'Page unavailable' ),
			),
			'supports'        => docspress_blocks_design_supports(),
		)
	);
}
add_action( 'init', 'docspress_blocks_register_version_switcher', 10 );
