<?php
/**
 * Live Code Playground block registration and rendering.
 *
 * @package AutodocsPublishBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the Live Code Playground block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function autodocs_publish_blocks_render_code_playground( $attributes ) {
	wp_enqueue_script( 'autodocs-publish-code-playground-view' );

	$title         = isset( $attributes['title'] ) ? sanitize_text_field( $attributes['title'] ) : 'Live example';
	$html          = isset( $attributes['html'] ) ? autodocs_publish_blocks_decode_source( $attributes['html'] ) : '';
	$css           = isset( $attributes['css'] ) ? autodocs_publish_blocks_decode_source( $attributes['css'] ) : '';
	$javascript    = isset( $attributes['javascript'] ) ? autodocs_publish_blocks_decode_source( $attributes['javascript'] ) : '';
	$height        = isset( $attributes['height'] ) ? max( 180, min( 720, (int) $attributes['height'] ) ) : 320;
	$auto_run      = ! isset( $attributes['autoRun'] ) || (bool) $attributes['autoRun'];
	$show_console  = ! isset( $attributes['showConsole'] ) || (bool) $attributes['showConsole'];
	$allow_network = ! empty( $attributes['allowNetwork'] );
	$playground_id = wp_unique_id( 'autodocs-publish-playground-' );
	$config        = array(
		'id'           => $playground_id,
		'html'         => $html,
		'css'          => $css,
		'javascript'   => $javascript,
		'autoRun'      => $auto_run,
		'showConsole'  => $show_console,
		'allowNetwork' => $allow_network,
	);
	$wrapper       = get_block_wrapper_attributes(
		array(
			'class' => 'autodocs-publish-playground',
			'style' => '--autodocs-publish-playground-height:' . $height . 'px',
		)
	);

	ob_start();
	?>
	<figure <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-autodocs-publish-playground>
		<script type="application/json" class="autodocs-publish-playground__config"><?php
			echo wp_json_encode( $config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );
		?></script>
		<div class="autodocs-publish-playground__bar">
			<div class="autodocs-publish-playground__identity">
				<span class="autodocs-publish-playground__lights" aria-hidden="true"><i></i><i></i><i></i></span>
				<div>
					<span class="autodocs-publish-playground__eyebrow"><?php esc_html_e( 'Browser sandbox', 'autodocs-publish-blocks' ); ?></span>
					<strong><?php echo esc_html( $title ); ?></strong>
				</div>
			</div>
			<div class="autodocs-publish-playground__actions">
				<span class="autodocs-publish-playground__policy"><?php echo esc_html( $allow_network ? __( 'Network enabled', 'autodocs-publish-blocks' ) : __( 'Network blocked', 'autodocs-publish-blocks' ) ); ?></span>
				<button type="button" data-autodocs-publish-playground-reset><?php esc_html_e( 'Reset', 'autodocs-publish-blocks' ); ?></button>
				<button class="is-primary" type="button" data-autodocs-publish-playground-run><?php esc_html_e( 'Run', 'autodocs-publish-blocks' ); ?></button>
			</div>
		</div>
		<div class="autodocs-publish-playground__workspace">
			<div class="autodocs-publish-playground__editors" data-autodocs-publish-playground-editors>
				<label>
					<span>HTML</span>
					<textarea data-autodocs-publish-playground-html spellcheck="false"><?php echo esc_textarea( $html ); ?></textarea>
				</label>
				<label>
					<span>CSS</span>
					<textarea data-autodocs-publish-playground-css spellcheck="false"><?php echo esc_textarea( $css ); ?></textarea>
				</label>
				<label>
					<span>JavaScript</span>
					<textarea data-autodocs-publish-playground-js spellcheck="false"><?php echo esc_textarea( $javascript ); ?></textarea>
				</label>
			</div>
			<div class="autodocs-publish-playground__preview">
				<div class="autodocs-publish-playground__preview-label">
					<span><?php esc_html_e( 'Result', 'autodocs-publish-blocks' ); ?></span>
					<span data-autodocs-publish-playground-status role="status" aria-live="polite"><?php echo esc_html( $auto_run ? __( 'Starting…', 'autodocs-publish-blocks' ) : __( 'Ready', 'autodocs-publish-blocks' ) ); ?></span>
				</div>
				<iframe title="<?php echo esc_attr( sprintf( __( '%s live result', 'autodocs-publish-blocks' ), $title ) ); ?>" sandbox="allow-scripts" referrerpolicy="no-referrer" data-autodocs-publish-playground-frame></iframe>
			</div>
		</div>
		<?php if ( $show_console ) : ?>
			<div class="autodocs-publish-playground__console">
				<div class="autodocs-publish-playground__console-label">
					<span><?php esc_html_e( 'Console', 'autodocs-publish-blocks' ); ?></span>
					<button type="button" data-autodocs-publish-playground-clear><?php esc_html_e( 'Clear', 'autodocs-publish-blocks' ); ?></button>
				</div>
				<output data-autodocs-publish-playground-console aria-live="polite"><span class="is-muted"><?php esc_html_e( 'Console output appears here.', 'autodocs-publish-blocks' ); ?></span></output>
			</div>
		<?php endif; ?>
	</figure>
	<?php
	return trim( ob_get_clean() );
}

/**
 * Register the Live Code Playground block.
 */
function autodocs_publish_blocks_register_code_playground() {
	$block_url = AUTODOCS_PUBLISH_BLOCKS_URL . 'blocks/code-playground/';

	wp_register_script( 'autodocs-publish-code-playground-editor', $block_url . 'editor.js', array( 'wp-blocks', 'autodocs-publish-blocks-editor-shared' ), AUTODOCS_PUBLISH_BLOCKS_VERSION, true );
	wp_register_script( 'autodocs-publish-code-playground-view', $block_url . 'view.js', array(), AUTODOCS_PUBLISH_BLOCKS_VERSION, true );
	wp_register_style( 'autodocs-publish-code-playground', $block_url . 'style.css', array(), AUTODOCS_PUBLISH_BLOCKS_VERSION );
	wp_register_style( 'autodocs-publish-code-playground-editor-style', $block_url . 'editor.css', array( 'wp-edit-blocks', 'autodocs-publish-code-playground' ), AUTODOCS_PUBLISH_BLOCKS_VERSION );

	autodocs_publish_blocks_register_block_type(
		'autodocs-publish/code-playground',
		array(
			'api_version'     => 3,
			'editor_script'   => 'autodocs-publish-code-playground-editor',
			'style'           => 'autodocs-publish-code-playground',
			'editor_style'    => 'autodocs-publish-code-playground-editor-style',
			'render_callback' => 'autodocs_publish_blocks_render_code_playground',
			'attributes'      => array(
				'title'         => array( 'type' => 'string', 'default' => 'Live example' ),
				'html'          => array( 'type' => 'string', 'default' => '<button class="demo-button">Publish docs</button>' ),
				'css'           => array( 'type' => 'string', 'default' => ".demo-button {\n  padding: 0.75rem 1rem;\n  border: 0;\n  border-radius: 0.4rem;\n  background: #3858e9;\n  color: white;\n  font: inherit;\n}" ),
				'javascript'    => array( 'type' => 'string', 'default' => "document.querySelector( '.demo-button' ).addEventListener( 'click', () => {\n  console.log( 'Documentation published' );\n} );" ),
				'height'        => array( 'type' => 'number', 'default' => 320 ),
				'autoRun'       => array( 'type' => 'boolean', 'default' => true ),
				'showConsole'   => array( 'type' => 'boolean', 'default' => true ),
				'allowNetwork'  => array( 'type' => 'boolean', 'default' => false ),
			),
			'supports'        => autodocs_publish_blocks_design_supports( array( 'wide' ) ),
		)
	);
}
add_action( 'init', 'autodocs_publish_blocks_register_code_playground', 10 );
