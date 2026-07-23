<?php
/**
 * Shared icon registry for the Audience Paths block.
 *
 * The registry is the single source of truth for both server rendering and the
 * block editor. Legacy abbreviations remain aliases so existing serialized
 * blocks gain the vector icon system without a content migration.
 *
 * @package DocsPressBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the curated Audience Paths icon registry.
 *
 * @return array
 */
function docspress_blocks_audience_path_icons() {
	return array(
		'site'        => array(
			'label'    => __( 'Site', 'docspress-blocks' ),
			'aliases'  => array( 'WP', 'SITE' ),
			'elements' => array(
				array( 'tag' => 'rect', 'attrs' => array( 'x' => '3', 'y' => '4', 'width' => '18', 'height' => '16', 'rx' => '2' ) ),
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M3 8h18M7 6h.01' ) ),
			),
		),
		'code'        => array(
			'label'    => __( 'Code', 'docspress-blocks' ),
			'aliases'  => array( 'DEV', 'PHP', 'CODE', '{ }' ),
			'elements' => array(
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'm8.5 8-4 4 4 4M15.5 8l4 4-4 4M13.5 5l-3 14' ) ),
			),
		),
		'agency'      => array(
			'label'    => __( 'Agency', 'docspress-blocks' ),
			'aliases'  => array( 'AG', 'AGENCY' ),
			'elements' => array(
				array( 'tag' => 'rect', 'attrs' => array( 'x' => '3', 'y' => '7', 'width' => '18', 'height' => '13', 'rx' => '2' ) ),
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M8 7V5h8v2M3 12h18M10 12v2h4v-2' ) ),
			),
		),
		'contribute'  => array(
			'label'    => __( 'Contribute', 'docspress-blocks' ),
			'aliases'  => array( 'JP', 'PR', 'CONTRIBUTE' ),
			'elements' => array(
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '6', 'cy' => '5', 'r' => '2' ) ),
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '6', 'cy' => '19', 'r' => '2' ) ),
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M6 7v10M10 6h4a4 4 0 0 1 4 4v5m-3-2 3 3 3-3' ) ),
			),
		),
		'rocket'      => array(
			'label'    => __( 'Launch', 'docspress-blocks' ),
			'aliases'  => array( 'GO', 'SHIP', 'FAST' ),
			'elements' => array(
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M14 5c2.2-2.2 4.8-2 5.8-1.8.2 1 .4 3.6-1.8 5.8l-5.5 5.5-3-3L14 5ZM9.5 11.5 6 11l-3 3 5 2M12.5 14.5 13 18l-3 3-2-5M15.5 6.5h.01' ) ),
			),
		),
		'products'    => array(
			'label'    => __( 'Products', 'docspress-blocks' ),
			'aliases'  => array( 'PICK', 'FEAT', 'AREA', 'PRODUCTS' ),
			'elements' => array(
				array( 'tag' => 'rect', 'attrs' => array( 'x' => '3', 'y' => '3', 'width' => '7', 'height' => '7', 'rx' => '1.5' ) ),
				array( 'tag' => 'rect', 'attrs' => array( 'x' => '14', 'y' => '3', 'width' => '7', 'height' => '7', 'rx' => '1.5' ) ),
				array( 'tag' => 'rect', 'attrs' => array( 'x' => '3', 'y' => '14', 'width' => '7', 'height' => '7', 'rx' => '1.5' ) ),
				array( 'tag' => 'rect', 'attrs' => array( 'x' => '14', 'y' => '14', 'width' => '7', 'height' => '7', 'rx' => '1.5' ) ),
			),
		),
		'terminal'    => array(
			'label'    => __( 'Terminal', 'docspress-blocks' ),
			'aliases'  => array( 'CLI', 'TERMINAL' ),
			'elements' => array(
				array( 'tag' => 'rect', 'attrs' => array( 'x' => '3', 'y' => '4', 'width' => '18', 'height' => '16', 'rx' => '2' ) ),
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'm7 9 3 3-3 3M13 15h4' ) ),
			),
		),
		'testing'     => array(
			'label'    => __( 'Testing', 'docspress-blocks' ),
			'aliases'  => array( 'QA', 'TEST', 'AUTO', 'BETA', 'REG', 'β' ),
			'elements' => array(
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M9 3h6M10 3v5l-5.5 9.2A2.5 2.5 0 0 0 6.7 21h10.6a2.5 2.5 0 0 0 2.2-3.8L14 8V3M7.2 16h9.6' ) ),
			),
		),
		'troubleshoot' => array(
			'label'    => __( 'Troubleshoot', 'docspress-blocks' ),
			'aliases'  => array( 'FIX', 'DBG', 'TOOL', 'TROUBLESHOOT' ),
			'elements' => array(
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M14.7 6.3a4 4 0 0 0-5 5L3.5 17.5a2.1 2.1 0 0 0 3 3l6.2-6.2a4 4 0 0 0 5-5l-2.5 2.5-3-3 2.5-2.5Z' ) ),
			),
		),
		'document'    => array(
			'label'    => __( 'Document', 'docspress-blocks' ),
			'aliases'  => array( 'MD', 'DOC', 'DOCUMENT' ),
			'elements' => array(
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M6 3h8l4 4v14H6V3Zm8 0v5h4M9 12h6M9 16h6' ) ),
			),
		),
		'sparkles'    => array(
			'label'    => __( 'AI and ideas', 'docspress-blocks' ),
			'aliases'  => array( 'AI', 'SPARKLES' ),
			'elements' => array(
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'm12 3 1.3 3.7L17 8l-3.7 1.3L12 13l-1.3-3.7L7 8l3.7-1.3L12 3ZM18.5 14l.8 2.2 2.2.8-2.2.8-.8 2.2-.8-2.2-2.2-.8 2.2-.8.8-2.2ZM5.5 13l.9 2.6L9 16.5l-2.6.9L5.5 20l-.9-2.6-2.6-.9 2.6-.9.9-2.6Z' ) ),
			),
		),
		'api'         => array(
			'label'    => __( 'API', 'docspress-blocks' ),
			'aliases'  => array( 'API' ),
			'elements' => array(
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '5', 'cy' => '12', 'r' => '2' ) ),
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '19', 'cy' => '6', 'r' => '2' ) ),
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '19', 'cy' => '18', 'r' => '2' ) ),
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'm7 11 10-4M7 13l10 4' ) ),
			),
		),
		'compass'     => array(
			'label'    => __( 'Direction', 'docspress-blocks' ),
			'aliases'  => array( 'MAP', '?', 'COMPASS' ),
			'elements' => array(
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '12', 'cy' => '12', 'r' => '9' ) ),
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'm15.5 8.5-2.1 4.9-4.9 2.1 2.1-4.9 4.9-2.1Z' ) ),
			),
		),
		'bug'         => array(
			'label'    => __( 'Bug', 'docspress-blocks' ),
			'aliases'  => array( 'BUG' ),
			'elements' => array(
				array( 'tag' => 'rect', 'attrs' => array( 'x' => '7', 'y' => '7', 'width' => '10', 'height' => '12', 'rx' => '5' ) ),
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M9 7V5m6 2V5M4 10h3m10 0h3M4 15h3m10 0h3M9 12h6' ) ),
			),
		),
		'branch'      => array(
			'label'    => __( 'Branch and CI', 'docspress-blocks' ),
			'aliases'  => array( 'CI', 'BRANCH' ),
			'elements' => array(
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '6', 'cy' => '5', 'r' => '2' ) ),
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '18', 'cy' => '5', 'r' => '2' ) ),
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '6', 'cy' => '19', 'r' => '2' ) ),
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M6 7v10M18 7v2a5 5 0 0 1-5 5H6' ) ),
			),
		),
		'environment' => array(
			'label'    => __( 'Environment', 'docspress-blocks' ),
			'aliases'  => array( 'ENV', 'WIN', 'ENVIRONMENT' ),
			'elements' => array(
				array( 'tag' => 'rect', 'attrs' => array( 'x' => '3', 'y' => '4', 'width' => '18', 'height' => '14', 'rx' => '2' ) ),
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M8 21h8M12 18v3M3 8h18' ) ),
			),
		),
		'performance' => array(
			'label'    => __( 'Performance', 'docspress-blocks' ),
			'aliases'  => array( 'PERF', 'SPEED' ),
			'elements' => array(
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M4.2 18a9 9 0 1 1 15.6 0M12 12l4-4M7 18h10' ) ),
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '12', 'cy' => '12', 'r' => '1' ) ),
			),
		),
		'growth'      => array(
			'label'    => __( 'Growth', 'docspress-blocks' ),
			'aliases'  => array( 'GROW', 'GROWTH' ),
			'elements' => array(
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M4 19V5M4 19h16M7 15l4-4 3 2 5-6M15 7h4v4' ) ),
			),
		),
		'help'        => array(
			'label'    => __( 'Help and support', 'docspress-blocks' ),
			'aliases'  => array( 'HELP', 'SUPPORT' ),
			'elements' => array(
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '12', 'cy' => '12', 'r' => '9' ) ),
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '12', 'cy' => '12', 'r' => '4' ) ),
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'm5.6 5.6 3.6 3.6m5.6 5.6 3.6 3.6m0-12.8-3.6 3.6m-5.6 5.6-3.6 3.6' ) ),
			),
		),
		'plug'        => array(
			'label'    => __( 'Integration', 'docspress-blocks' ),
			'aliases'  => array( 'HOOK', 'PLUG', 'INTEGRATION' ),
			'elements' => array(
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M9 3v5m6-5v5M7 8h10v3a5 5 0 0 1-5 5v5M5 21h14' ) ),
			),
		),
		'globe'       => array(
			'label'    => __( 'Global', 'docspress-blocks' ),
			'aliases'  => array( 'L10N', 'GLOBAL', 'GLOBE' ),
			'elements' => array(
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '12', 'cy' => '12', 'r' => '9' ) ),
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18' ) ),
			),
		),
		'ownership'   => array(
			'label'    => __( 'Ownership', 'docspress-blocks' ),
			'aliases'  => array( 'OWN', 'KEY', 'OWNERSHIP' ),
			'elements' => array(
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '8', 'cy' => '12', 'r' => '4' ) ),
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M12 12h9M17 12v3m3-3v2' ) ),
			),
		),
		'plan'        => array(
			'label'    => __( 'Plan', 'docspress-blocks' ),
			'aliases'  => array( 'PLAN' ),
			'elements' => array(
				array( 'tag' => 'rect', 'attrs' => array( 'x' => '5', 'y' => '4', 'width' => '14', 'height' => '17', 'rx' => '2' ) ),
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M9 4V2h6v2M8.5 10h7M8.5 14h7M8.5 18h4' ) ),
			),
		),
		'repository'  => array(
			'label'    => __( 'Repository', 'docspress-blocks' ),
			'aliases'  => array( 'REPO', 'REPOSITORY' ),
			'elements' => array(
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M3 6h7l2 2h9v11H3V6Z' ) ),
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '9', 'cy' => '12', 'r' => '1' ) ),
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '15', 'cy' => '16', 'r' => '1' ) ),
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M10 12h2a3 3 0 0 1 3 3' ) ),
			),
		),
		'security'    => array(
			'label'    => __( 'Security', 'docspress-blocks' ),
			'aliases'  => array( 'SEC', 'RULE', 'SECURITY' ),
			'elements' => array(
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M12 3 5 6v5c0 4.6 2.8 8.3 7 10 4.2-1.7 7-5.4 7-10V6l-7-3Z' ) ),
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'm9 12 2 2 4-5' ) ),
			),
		),
		'settings'    => array(
			'label'    => __( 'Settings', 'docspress-blocks' ),
			'aliases'  => array( 'SET', 'SETTINGS' ),
			'elements' => array(
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M4 6h8M16 6h4M4 12h3M11 12h9M4 18h10M18 18h2' ) ),
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '14', 'cy' => '6', 'r' => '2' ) ),
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '9', 'cy' => '12', 'r' => '2' ) ),
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '16', 'cy' => '18', 'r' => '2' ) ),
			),
		),
		'sync'        => array(
			'label'    => __( 'Sync', 'docspress-blocks' ),
			'aliases'  => array( 'SYNC' ),
			'elements' => array(
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M20 7h-5V2M4 17h5v5M19 12a7 7 0 0 0-12-5l-2 2M5 12a7 7 0 0 0 12 5l2-2' ) ),
			),
		),
		'ui'          => array(
			'label'    => __( 'Interface', 'docspress-blocks' ),
			'aliases'  => array( 'UI', 'INTERFACE' ),
			'elements' => array(
				array( 'tag' => 'rect', 'attrs' => array( 'x' => '3', 'y' => '4', 'width' => '18', 'height' => '16', 'rx' => '2' ) ),
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M9 4v16M9 10h12' ) ),
			),
		),
		'operations'  => array(
			'label'    => __( 'Operations', 'docspress-blocks' ),
			'aliases'  => array( 'RUN', 'OPERATIONS' ),
			'elements' => array(
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M3 12h4l2-5 4 10 2-5h6' ) ),
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '12', 'cy' => '12', 'r' => '9' ) ),
			),
		),
		'cloud'       => array(
			'label'    => __( 'Cloud', 'docspress-blocks' ),
			'aliases'  => array( 'WPC', 'CLOUD' ),
			'elements' => array(
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M7 18h11a4 4 0 0 0 .5-8A6.5 6.5 0 0 0 6 8.5 4.8 4.8 0 0 0 7 18Z' ) ),
			),
		),
		'steps'       => array(
			'label'    => __( 'Steps', 'docspress-blocks' ),
			'aliases'  => array( '01', '02', '03', '04', 'STEPS' ),
			'elements' => array(
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '5', 'cy' => '6', 'r' => '1.5' ) ),
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '5', 'cy' => '12', 'r' => '1.5' ) ),
				array( 'tag' => 'circle', 'attrs' => array( 'cx' => '5', 'cy' => '18', 'r' => '1.5' ) ),
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M9 6h10M9 12h10M9 18h10' ) ),
			),
		),
		'arrow-right' => array(
			'label'    => __( 'Continue', 'docspress-blocks' ),
			'aliases'  => array( '→', 'ARROW' ),
			'elements' => array(
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M5 12h14m-5-5 5 5-5 5' ) ),
			),
		),
		'arrow-up-right' => array(
			'label'    => __( 'Open destination', 'docspress-blocks' ),
			'aliases'  => array( '↗', 'EXTERNAL' ),
			'elements' => array(
				array( 'tag' => 'path', 'attrs' => array( 'd' => 'M7 17 17 7M8 7h9v9' ) ),
			),
		),
	);
}

/**
 * Resolve a semantic icon ID or legacy symbol to a registry ID.
 *
 * @param string $value Stored icon value.
 * @return string
 */
function docspress_blocks_resolve_audience_path_icon( $value ) {
	$registry = docspress_blocks_audience_path_icons();
	$value    = trim( (string) $value );
	$id       = sanitize_key( $value );

	if ( isset( $registry[ $id ] ) ) {
		return $id;
	}

	foreach ( $registry as $icon_id => $icon ) {
		foreach ( $icon['aliases'] as $alias ) {
			if ( $value === $alias || strtoupper( $value ) === strtoupper( $alias ) ) {
				return $icon_id;
			}
		}
	}

	return 'compass';
}

/**
 * Render one icon from the registry.
 *
 * @param string $value      Semantic icon ID or legacy symbol.
 * @param string $class_name Optional SVG class name.
 * @return string
 */
function docspress_blocks_render_audience_path_icon( $value, $class_name = 'docspress-audience-paths__icon-svg' ) {
	$registry = docspress_blocks_audience_path_icons();
	$icon_id  = docspress_blocks_resolve_audience_path_icon( $value );
	$icon     = $registry[ $icon_id ];
	$output   = sprintf(
		'<svg class="%1$s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" focusable="false" aria-hidden="true">',
		esc_attr( $class_name )
	);
	$tags     = array( 'circle', 'path', 'rect' );
	$attrs    = array( 'cx', 'cy', 'd', 'fill', 'height', 'points', 'r', 'rx', 'width', 'x', 'y' );

	foreach ( $icon['elements'] as $element ) {
		if ( ! in_array( $element['tag'], $tags, true ) ) {
			continue;
		}

		$output .= '<' . $element['tag'];
		foreach ( $element['attrs'] as $attribute => $attribute_value ) {
			if ( in_array( $attribute, $attrs, true ) ) {
				$output .= sprintf( ' %s="%s"', $attribute, esc_attr( $attribute_value ) );
			}
		}
		$output .= '></' . $element['tag'] . '>';
	}

	return $output . '</svg>';
}

/**
 * Return JSON-ready icon data for the block editor.
 *
 * @return array
 */
function docspress_blocks_audience_path_icon_editor_data() {
	$registry = docspress_blocks_audience_path_icons();
	$icons    = array();
	$aliases  = array();

	foreach ( $registry as $icon_id => $icon ) {
		$icons[ $icon_id ] = array(
			'label'    => $icon['label'],
			'elements' => $icon['elements'],
		);

		foreach ( $icon['aliases'] as $alias ) {
			$aliases[ $alias ] = $icon_id;
		}
	}

	return array(
		'fallback' => 'compass',
		'icons'    => $icons,
		'aliases'  => $aliases,
	);
}
