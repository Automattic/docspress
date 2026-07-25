<?php
/**
 * Flow block registration and rendering.
 *
 * @package DocsPressBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the starter steps used by a new Flow block.
 *
 * @return array<int,array<string,string>>
 */
function docspress_blocks_flow_default_steps() {
	return array(
		array(
			'title'   => 'Choose',
			'content' => '<p>Select the option that matches your project.</p>',
		),
		array(
			'title'   => 'Configure',
			'content' => '<p>Set the values required by your environment.</p>',
		),
		array(
			'title'   => 'Verify',
			'content' => '<p>Run the check and confirm the expected result.</p>',
		),
	);
}

/**
 * Normalize Flow steps before rendering.
 *
 * @param mixed $steps Candidate steps.
 * @return array<int,array<string,string>>
 */
function docspress_blocks_normalize_flow_steps( $steps ) {
	if ( ! is_array( $steps ) ) {
		return docspress_blocks_flow_default_steps();
	}

	$normalized = array();
	foreach ( array_slice( $steps, 0, 20 ) as $step ) {
		if ( ! is_array( $step ) ) {
			continue;
		}

		$normalized[] = array(
			'title'   => isset( $step['title'] ) ? wp_kses_post( (string) $step['title'] ) : '',
			'content' => isset( $step['content'] ) ? wp_kses_post( (string) $step['content'] ) : '',
		);
	}

	return $normalized ? $normalized : docspress_blocks_flow_default_steps();
}

/**
 * Render the Flow block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function docspress_blocks_render_flow( $attributes ) {
	$start = isset( $attributes['start'] ) ? max( 1, min( 99, absint( $attributes['start'] ) ) ) : 1;
	$steps = docspress_blocks_normalize_flow_steps( isset( $attributes['steps'] ) ? $attributes['steps'] : null );

	ob_start();
	?>
	<ol <?php echo get_block_wrapper_attributes( array( 'class' => 'docspress-flow', 'start' => $start, 'role' => 'list' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php foreach ( $steps as $index => $step ) : ?>
			<li class="docspress-flow__step">
				<div class="docspress-flow__rail" aria-hidden="true">
					<span class="docspress-flow__marker"><?php echo esc_html( $start + $index ); ?></span>
				</div>
				<div class="docspress-flow__content">
					<?php if ( '' !== trim( wp_strip_all_tags( $step['title'] ) ) ) : ?>
						<h3 class="docspress-flow__title"><?php echo $step['title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
					<?php endif; ?>
					<?php if ( '' !== trim( wp_strip_all_tags( $step['content'] ) ) ) : ?>
						<div class="docspress-flow__body"><?php echo $step['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					<?php endif; ?>
				</div>
			</li>
		<?php endforeach; ?>
	</ol>
	<?php
	return trim( ob_get_clean() );
}

/**
 * Register the Flow block and its folder-owned assets.
 */
function docspress_blocks_register_flow() {
	$block_url = DOCSPRESS_BLOCKS_URL . 'blocks/flow/';

	wp_register_script( 'docspress-flow-editor', $block_url . 'editor.js', array( 'wp-blocks', 'docspress-blocks-editor-shared' ), DOCSPRESS_BLOCKS_VERSION, true );
	wp_register_style( 'docspress-flow', $block_url . 'style.css', array(), DOCSPRESS_BLOCKS_VERSION );
	wp_register_style( 'docspress-flow-editor-style', $block_url . 'editor.css', array( 'wp-edit-blocks', 'docspress-flow' ), DOCSPRESS_BLOCKS_VERSION );

	register_block_type(
		'docspress/flow',
		array(
			'api_version'     => 3,
			'editor_script'   => 'docspress-flow-editor',
			'style'           => 'docspress-flow',
			'editor_style'    => 'docspress-flow-editor-style',
			'render_callback' => 'docspress_blocks_render_flow',
			'attributes'      => array(
				'start' => array( 'type' => 'number', 'default' => 1 ),
				'steps' => array( 'type' => 'array', 'default' => docspress_blocks_flow_default_steps() ),
			),
			'supports'        => docspress_blocks_design_supports(),
		)
	);
}
add_action( 'init', 'docspress_blocks_register_flow', 10 );
