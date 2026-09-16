<?php
/**
 * Colorful Code block registration and rendering.
 *
 * @package AutodocsPublishBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the Colorful Code block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function autodocs_publish_blocks_render_colorful_code( $attributes ) {
	wp_enqueue_script( 'autodocs-publish-blocks-view' );

	$caption = isset( $attributes['caption'] ) ? wp_kses_post( $attributes['caption'] ) : '';

	ob_start();
	?>
	<figure <?php echo get_block_wrapper_attributes( array( 'class' => 'autodocs-publish-code' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php echo autodocs_publish_blocks_code_surface( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php if ( $caption ) : ?>
			<figcaption class="autodocs-publish-code__caption"><?php echo $caption; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></figcaption>
		<?php endif; ?>
	</figure>
	<?php
	return trim( ob_get_clean() );
}

/**
 * Register the Colorful Code block and its folder-owned assets.
 */
function autodocs_publish_blocks_register_colorful_code() {
	$block_url = AUTODOCS_PUBLISH_BLOCKS_URL . 'blocks/colorful-code/';

	wp_register_script(
		'autodocs-publish-colorful-code-editor',
		$block_url . 'editor.js',
		array( 'wp-blocks', 'autodocs-publish-blocks-editor-shared' ),
		AUTODOCS_PUBLISH_BLOCKS_VERSION,
		true
	);

	wp_register_style(
		'autodocs-publish-colorful-code',
		$block_url . 'style.css',
		array( 'autodocs-publish-blocks-code' ),
		AUTODOCS_PUBLISH_BLOCKS_VERSION
	);

	wp_register_style(
		'autodocs-publish-colorful-code-editor-style',
		$block_url . 'editor.css',
		array( 'autodocs-publish-blocks-code-editor', 'autodocs-publish-colorful-code' ),
		AUTODOCS_PUBLISH_BLOCKS_VERSION
	);

	autodocs_publish_blocks_register_block_type(
		'autodocs-publish/colorful-code',
		array(
			'api_version'     => 3,
			'editor_script'   => 'autodocs-publish-colorful-code-editor',
			'style'           => 'autodocs-publish-colorful-code',
			'editor_style'    => 'autodocs-publish-colorful-code-editor-style',
			'render_callback' => 'autodocs_publish_blocks_render_colorful_code',
			'attributes'      => array(
				'language'          => array( 'type' => 'string', 'default' => 'javascript' ),
				'filename'          => array( 'type' => 'string', 'default' => '' ),
				'code'              => array( 'type' => 'string', 'default' => "const hello = \"Autodocs Publish\";\nconsole.log( hello );" ),
				'highlightedLines'  => array( 'type' => 'string', 'default' => '' ),
				'showLineNumbers'   => array( 'type' => 'boolean', 'default' => true ),
				'caption'           => array( 'type' => 'string', 'default' => '' ),
				'diffMode'          => array( 'type' => 'string', 'default' => 'none' ),
				'copyMode'          => array( 'type' => 'string', 'default' => 'all' ),
				'annotations'       => array( 'type' => 'array', 'default' => array() ),
			),
			'supports'        => autodocs_publish_blocks_design_supports(),
		)
	);
}
add_action( 'init', 'autodocs_publish_blocks_register_colorful_code', 10 );
