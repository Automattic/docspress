<?php
/**
 * Live Code Playground block registration and rendering.
 *
 * @package DocsPressBlocks
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
function docspress_blocks_render_code_playground( $attributes ) {
	wp_enqueue_script( 'docspress-code-playground-view' );

	$title         = isset( $attributes['title'] ) ? sanitize_text_field( $attributes['title'] ) : 'Live example';
	$html          = isset( $attributes['html'] ) ? docspress_blocks_decode_source( $attributes['html'] ) : '';
	$css           = isset( $attributes['css'] ) ? docspress_blocks_decode_source( $attributes['css'] ) : '';
	$javascript    = isset( $attributes['javascript'] ) ? docspress_blocks_decode_source( $attributes['javascript'] ) : '';
	$height        = isset( $attributes['height'] ) ? max( 180, min( 720, (int) $attributes['height'] ) ) : 320;
	$auto_run      = ! isset( $attributes['autoRun'] ) || (bool) $attributes['autoRun'];
	$show_console  = ! isset( $attributes['showConsole'] ) || (bool) $attributes['showConsole'];
	$allow_network = ! empty( $attributes['allowNetwork'] );
	$playground_id = wp_unique_id( 'docspress-playground-' );
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
			'class' => 'docspress-playground',
			'style' => '--docspress-playground-height:' . $height . 'px',
		)
	);

	ob_start();
	?>
	<figure <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-docspress-playground>
		<script type="application/json" class="docspress-playground__config"><?php
			echo wp_json_encode( $config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );
		?></script>
		<div class="docspress-playground__bar">
			<div class="docspress-playground__identity">
				<span class="docspress-playground__lights" aria-hidden="true"><i></i><i></i><i></i></span>
				<div>
					<span class="docspress-playground__eyebrow"><?php esc_html_e( 'Browser sandbox', 'docspress-blocks' ); ?></span>
					<strong><?php echo esc_html( $title ); ?></strong>
				</div>
			</div>
			<div class="docspress-playground__actions">
				<span class="docspress-playground__policy"><?php echo esc_html( $allow_network ? __( 'Network enabled', 'docspress-blocks' ) : __( 'Network blocked', 'docspress-blocks' ) ); ?></span>
				<button type="button" data-docspress-playground-reset><?php esc_html_e( 'Reset', 'docspress-blocks' ); ?></button>
				<button class="is-primary" type="button" data-docspress-playground-run><?php esc_html_e( 'Run', 'docspress-blocks' ); ?></button>
			</div>
		</div>
		<div class="docspress-playground__workspace">
			<div class="docspress-playground__editors" data-docspress-playground-editors>
				<label>
					<span>HTML</span>
					<textarea data-docspress-playground-html spellcheck="false"><?php echo esc_textarea( $html ); ?></textarea>
				</label>
				<label>
					<span>CSS</span>
					<textarea data-docspress-playground-css spellcheck="false"><?php echo esc_textarea( $css ); ?></textarea>
				</label>
				<label>
					<span>JavaScript</span>
					<textarea data-docspress-playground-js spellcheck="false"><?php echo esc_textarea( $javascript ); ?></textarea>
				</label>
			</div>
			<div class="docspress-playground__preview">
				<div class="docspress-playground__preview-label">
					<span><?php esc_html_e( 'Result', 'docspress-blocks' ); ?></span>
					<span data-docspress-playground-status role="status" aria-live="polite"><?php echo esc_html( $auto_run ? __( 'Starting…', 'docspress-blocks' ) : __( 'Ready', 'docspress-blocks' ) ); ?></span>
				</div>
				<iframe title="<?php echo esc_attr( sprintf( __( '%s live result', 'docspress-blocks' ), $title ) ); ?>" sandbox="allow-scripts" referrerpolicy="no-referrer" data-docspress-playground-frame></iframe>
			</div>
		</div>
		<?php if ( $show_console ) : ?>
			<div class="docspress-playground__console">
				<div class="docspress-playground__console-label">
					<span><?php esc_html_e( 'Console', 'docspress-blocks' ); ?></span>
					<button type="button" data-docspress-playground-clear><?php esc_html_e( 'Clear', 'docspress-blocks' ); ?></button>
				</div>
				<output data-docspress-playground-console aria-live="polite"><span class="is-muted"><?php esc_html_e( 'Console output appears here.', 'docspress-blocks' ); ?></span></output>
			</div>
		<?php endif; ?>
	</figure>
	<?php
	return trim( ob_get_clean() );
}

/**
 * Register the Live Code Playground block.
 */
function docspress_blocks_register_code_playground() {
	$block_url = DOCSPRESS_BLOCKS_URL . 'blocks/code-playground/';

	wp_register_script( 'docspress-code-playground-editor', $block_url . 'editor.js', array( 'wp-blocks', 'docspress-blocks-editor-shared' ), DOCSPRESS_BLOCKS_VERSION, true );
	wp_register_script( 'docspress-code-playground-view', $block_url . 'view.js', array(), DOCSPRESS_BLOCKS_VERSION, true );
	wp_register_style( 'docspress-code-playground', $block_url . 'style.css', array(), DOCSPRESS_BLOCKS_VERSION );
	wp_register_style( 'docspress-code-playground-editor-style', $block_url . 'editor.css', array( 'wp-edit-blocks', 'docspress-code-playground' ), DOCSPRESS_BLOCKS_VERSION );

	register_block_type(
		'docspress/code-playground',
		array(
			'api_version'     => 3,
			'editor_script'   => 'docspress-code-playground-editor',
			'style'           => 'docspress-code-playground',
			'editor_style'    => 'docspress-code-playground-editor-style',
			'render_callback' => 'docspress_blocks_render_code_playground',
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
			'supports'        => docspress_blocks_design_supports( array( 'wide' ) ),
		)
	);
}
add_action( 'init', 'docspress_blocks_register_code_playground', 10 );
