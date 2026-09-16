<?php
/**
 * Terminal Session block registration and rendering.
 *
 * @package AutodocsPublishBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the Terminal Session block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function autodocs_publish_blocks_render_terminal_session( $attributes ) {
	wp_enqueue_script( 'autodocs-publish-blocks-view' );

	$title      = isset( $attributes['title'] ) ? sanitize_text_field( $attributes['title'] ) : 'Terminal';
	$shell      = isset( $attributes['shell'] ) ? sanitize_text_field( $attributes['shell'] ) : 'bash';
	$prompt     = isset( $attributes['prompt'] ) ? sanitize_text_field( $attributes['prompt'] ) : '$';
	$command    = isset( $attributes['command'] ) ? (string) $attributes['command'] : '';
	$output     = isset( $attributes['output'] ) ? (string) $attributes['output'] : '';
	$command_id = wp_unique_id( 'autodocs-publish-terminal-command-' );

	ob_start();
	?>
	<figure <?php echo get_block_wrapper_attributes( array( 'class' => 'autodocs-publish-terminal' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<div class="autodocs-publish-terminal__bar">
			<span class="autodocs-publish-terminal__icon" aria-hidden="true">›_</span>
			<span class="autodocs-publish-terminal__title"><?php echo esc_html( $title ); ?></span>
			<span class="autodocs-publish-terminal__shell"><?php echo esc_html( $shell ); ?></span>
			<button class="autodocs-publish-terminal__copy" type="button" data-autodocs-publish-copy data-autodocs-publish-copy-target="<?php echo esc_attr( $command_id ); ?>" aria-label="<?php esc_attr_e( 'Copy command', 'autodocs-publish-blocks' ); ?>">
				<svg viewBox="0 0 20 20" aria-hidden="true"><rect x="7" y="7" width="9" height="9" rx="1.5"/><path d="M13 7V5.5A1.5 1.5 0 0 0 11.5 4h-7A1.5 1.5 0 0 0 3 5.5v7A1.5 1.5 0 0 0 4.5 14H7"/></svg>
				<b><?php esc_html_e( 'Copy', 'autodocs-publish-blocks' ); ?></b>
			</button>
		</div>
		<div class="autodocs-publish-terminal__body">
			<div class="autodocs-publish-terminal__command"><span class="autodocs-publish-terminal__prompt" aria-hidden="true"><?php echo esc_html( $prompt ); ?></span><pre><code id="<?php echo esc_attr( $command_id ); ?>"><?php echo esc_html( $command ); ?></code></pre></div>
			<?php if ( '' !== trim( $output ) ) : ?>
				<pre class="autodocs-publish-terminal__output" aria-label="<?php esc_attr_e( 'Command output', 'autodocs-publish-blocks' ); ?>"><code><?php echo esc_html( $output ); ?></code></pre>
			<?php endif; ?>
		</div>
	</figure>
	<?php
	return trim( ob_get_clean() );
}

/**
 * Register the Terminal Session block and its folder-owned assets.
 */
function autodocs_publish_blocks_register_terminal_session() {
	$block_url = AUTODOCS_PUBLISH_BLOCKS_URL . 'blocks/terminal-session/';

	wp_register_script( 'autodocs-publish-terminal-session-editor', $block_url . 'editor.js', array( 'wp-blocks', 'autodocs-publish-blocks-editor-shared' ), AUTODOCS_PUBLISH_BLOCKS_VERSION, true );
	wp_register_style( 'autodocs-publish-terminal-session', $block_url . 'style.css', array(), AUTODOCS_PUBLISH_BLOCKS_VERSION );
	wp_register_style( 'autodocs-publish-terminal-session-editor-style', $block_url . 'editor.css', array( 'wp-edit-blocks', 'autodocs-publish-terminal-session' ), AUTODOCS_PUBLISH_BLOCKS_VERSION );

	autodocs_publish_blocks_register_block_type(
		'autodocs-publish/terminal-session',
		array(
			'api_version'     => 3,
			'editor_script'   => 'autodocs-publish-terminal-session-editor',
			'style'           => 'autodocs-publish-terminal-session',
			'editor_style'    => 'autodocs-publish-terminal-session-editor-style',
			'render_callback' => 'autodocs_publish_blocks_render_terminal_session',
			'attributes'      => array(
				'title'   => array( 'type' => 'string', 'default' => 'Terminal' ),
				'shell'   => array( 'type' => 'string', 'default' => 'bash' ),
				'prompt'  => array( 'type' => 'string', 'default' => '$' ),
				'command' => array( 'type' => 'string', 'default' => 'npx autodocs-publish publish ./docs' ),
				'output'  => array( 'type' => 'string', 'default' => "✓ Read 12 documents\n✓ Published 12 WordPress pages" ),
			),
			'supports'        => autodocs_publish_blocks_design_supports(),
		)
	);
}
add_action( 'init', 'autodocs_publish_blocks_register_terminal_session', 10 );
