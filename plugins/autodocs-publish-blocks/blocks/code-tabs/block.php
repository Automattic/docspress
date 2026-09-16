<?php
/**
 * Code Tabs block registration and rendering.
 *
 * @package AutodocsPublishBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normalize tab data before rendering it.
 *
 * @param mixed $tabs Raw tabs attribute.
 * @return array
 */
function autodocs_publish_blocks_normalize_tabs( $tabs ) {
	if ( ! is_array( $tabs ) ) {
		return array();
	}

	$normalized = array();
	foreach ( array_slice( $tabs, 0, 8 ) as $tab ) {
		if ( ! is_array( $tab ) ) {
			continue;
		}

		$normalized[] = array(
			'label'    => isset( $tab['label'] ) ? sanitize_text_field( $tab['label'] ) : '',
			'language' => autodocs_publish_blocks_allowed_value(
				isset( $tab['language'] ) ? $tab['language'] : '',
				array( 'bash', 'css', 'html', 'javascript', 'json', 'jsx', 'markdown', 'php', 'plaintext', 'python', 'shell', 'sql', 'tsx', 'typescript', 'yaml' ),
				'plaintext'
			),
			'filename' => isset( $tab['filename'] ) ? sanitize_text_field( $tab['filename'] ) : '',
			'code'     => isset( $tab['code'] ) ? (string) $tab['code'] : '',
		);
	}

	return $normalized;
}

/**
 * Render the Code Tabs block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function autodocs_publish_blocks_render_code_tabs( $attributes ) {
	$tabs = autodocs_publish_blocks_normalize_tabs( isset( $attributes['tabs'] ) ? $attributes['tabs'] : array() );
	if ( ! $tabs ) {
		return '';
	}

	wp_enqueue_script( 'autodocs-publish-blocks-view' );
	$instance_id  = wp_unique_id( 'autodocs-publish-code-tabs-' );
	$caption      = isset( $attributes['caption'] ) ? wp_kses_post( $attributes['caption'] ) : '';
	$line_numbers = ! isset( $attributes['showLineNumbers'] ) || (bool) $attributes['showLineNumbers'];

	ob_start();
	?>
	<figure <?php echo get_block_wrapper_attributes( array( 'class' => 'autodocs-publish-code-tabs', 'data-autodocs-publish-tabs' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<div class="autodocs-publish-code-tabs__surface">
			<div class="autodocs-publish-code-tabs__header">
				<div class="autodocs-publish-code-tabs__list" role="tablist" aria-label="<?php esc_attr_e( 'Code examples', 'autodocs-publish-blocks' ); ?>">
					<?php foreach ( $tabs as $index => $tab ) :
						$tab_id   = $instance_id . '-tab-' . $index;
						$panel_id = $instance_id . '-panel-' . $index;
						?>
						<button id="<?php echo esc_attr( $tab_id ); ?>" class="autodocs-publish-code-tabs__tab<?php echo 0 === $index ? ' is-active' : ''; ?>" type="button" role="tab" aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $panel_id ); ?>" tabindex="<?php echo 0 === $index ? '0' : '-1'; ?>"><?php echo esc_html( $tab['label'] ? $tab['label'] : __( 'Untitled', 'autodocs-publish-blocks' ) ); ?></button>
					<?php endforeach; ?>
				</div>
				<div class="autodocs-publish-code-tabs__tools">
					<?php foreach ( $tabs as $index => $tab ) :
						$panel_id = $instance_id . '-panel-' . $index;
						$filename = $tab['filename'] ? $tab['filename'] : $tab['language'];
						?>
						<span class="autodocs-publish-code-tabs__filename" data-autodocs-publish-tab-meta="<?php echo esc_attr( $panel_id ); ?>"<?php echo 0 === $index ? '' : ' hidden'; ?>><?php echo esc_html( $filename ); ?></span>
					<?php endforeach; ?>
					<button class="autodocs-publish-code__copy autodocs-publish-code-tabs__copy" type="button" data-autodocs-publish-copy aria-label="<?php esc_attr_e( 'Copy code', 'autodocs-publish-blocks' ); ?>">
						<span aria-hidden="true">⧉</span><b><?php esc_html_e( 'Copy', 'autodocs-publish-blocks' ); ?></b>
					</button>
				</div>
			</div>
			<?php foreach ( $tabs as $index => $tab ) :
				$tab_id   = $instance_id . '-tab-' . $index;
				$panel_id = $instance_id . '-panel-' . $index;
				$surface  = array(
					'language'        => $tab['language'],
					'filename'        => $tab['filename'],
					'code'            => $tab['code'],
					'showLineNumbers' => $line_numbers,
				);
				?>
				<div id="<?php echo esc_attr( $panel_id ); ?>" class="autodocs-publish-code-tabs__panel" role="tabpanel" aria-labelledby="<?php echo esc_attr( $tab_id ); ?>"<?php echo 0 === $index ? '' : ' hidden'; ?>><?php echo autodocs_publish_blocks_code_surface( $surface, false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php endforeach; ?>
		</div>
		<?php if ( $caption ) : ?>
			<figcaption class="autodocs-publish-code__caption"><?php echo $caption; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></figcaption>
		<?php endif; ?>
	</figure>
	<?php
	return trim( ob_get_clean() );
}

/**
 * Register the Code Tabs block and its folder-owned assets.
 */
function autodocs_publish_blocks_register_code_tabs() {
	$block_url = AUTODOCS_PUBLISH_BLOCKS_URL . 'blocks/code-tabs/';

	wp_register_script(
		'autodocs-publish-code-tabs-editor',
		$block_url . 'editor.js',
		array( 'wp-blocks', 'autodocs-publish-blocks-editor-shared' ),
		AUTODOCS_PUBLISH_BLOCKS_VERSION,
		true
	);

	wp_register_style(
		'autodocs-publish-code-tabs',
		$block_url . 'style.css',
		array( 'autodocs-publish-blocks-code' ),
		AUTODOCS_PUBLISH_BLOCKS_VERSION
	);

	wp_register_style(
		'autodocs-publish-code-tabs-editor-style',
		$block_url . 'editor.css',
		array( 'autodocs-publish-blocks-code-editor', 'autodocs-publish-code-tabs' ),
		AUTODOCS_PUBLISH_BLOCKS_VERSION
	);

	autodocs_publish_blocks_register_block_type(
		'autodocs-publish/code-tabs',
		array(
			'api_version'     => 3,
			'editor_script'   => 'autodocs-publish-code-tabs-editor',
			'style'           => 'autodocs-publish-code-tabs',
			'editor_style'    => 'autodocs-publish-code-tabs-editor-style',
			'render_callback' => 'autodocs_publish_blocks_render_code_tabs',
			'attributes'      => array(
				'tabs'            => array(
					'type'    => 'array',
					'default' => array(
						array( 'label' => 'JavaScript', 'language' => 'javascript', 'filename' => 'example.js', 'code' => 'const docs = await publish();' ),
						array( 'label' => 'PHP', 'language' => 'php', 'filename' => 'example.php', 'code' => '$docs = autodocs_publish_publish();' ),
					),
				),
				'showLineNumbers' => array( 'type' => 'boolean', 'default' => true ),
				'caption'         => array( 'type' => 'string', 'default' => '' ),
			),
			'supports'        => autodocs_publish_blocks_design_supports(),
		)
	);
}
add_action( 'init', 'autodocs_publish_blocks_register_code_tabs', 10 );
