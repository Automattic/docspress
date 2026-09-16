<?php
/**
 * Prompt block registration and rendering.
 *
 * @package AutodocsPublishBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classify a prompt context item for presentation.
 *
 * @param string $item Raw context item.
 * @return string
 */
function autodocs_publish_blocks_prompt_context_kind( $item ) {
	if ( '$' === substr( $item, 0, 1 ) ) {
		return 'skill';
	}
	if ( '@' === substr( $item, 0, 1 ) ) {
		return 'mention';
	}
	if ( '#' === substr( $item, 0, 1 ) ) {
		return 'image';
	}
	if ( preg_match( '#^https?://#i', $item ) ) {
		return 'url';
	}

	return 'file';
}

/**
 * Render prompt text with first-class $skill-name references.
 *
 * Skill names use the portable lowercase slug form. Escaping happens before
 * the fixed presentation spans are inserted.
 *
 * @param string $prompt Raw prompt text.
 * @return string
 */
function autodocs_publish_blocks_render_prompt_text( $prompt ) {
	$escaped = esc_html( $prompt );

	return preg_replace(
		'/(?<![a-zA-Z0-9_-])(\$[a-z][a-z0-9-]*)\b/',
		'<span class="autodocs-publish-prompt__skill-ref">$1</span>',
		$escaped
	);
}

/**
 * Parse comma-separated prompt context.
 *
 * @param string $context Raw context value.
 * @return array
 */
function autodocs_publish_blocks_prompt_context_items( $context ) {
	$items = array();
	foreach ( explode( ',', (string) $context ) as $item ) {
		$item = trim( sanitize_text_field( $item ) );
		if ( '' === $item ) {
			continue;
		}
		$items[] = array(
			'label' => $item,
			'kind'  => autodocs_publish_blocks_prompt_context_kind( $item ),
		);
	}

	return array_slice( $items, 0, 12 );
}

/**
 * Render the Prompt block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function autodocs_publish_blocks_render_prompt( $attributes ) {
	wp_enqueue_script( 'autodocs-publish-blocks-view' );

	$prompt      = isset( $attributes['prompt'] ) ? (string) $attributes['prompt'] : '';
	$model       = isset( $attributes['model'] ) ? sanitize_text_field( $attributes['model'] ) : 'GPT-5';
	$mode        = autodocs_publish_blocks_allowed_value( isset( $attributes['mode'] ) ? $attributes['mode'] : '', array( 'chat', 'code', 'ask', 'plan' ), 'chat' );
	$thinking    = ! empty( $attributes['thinking'] );
	$context     = isset( $attributes['context'] ) ? autodocs_publish_blocks_prompt_context_items( $attributes['context'] ) : array();
	$caption     = isset( $attributes['caption'] ) ? wp_kses_post( $attributes['caption'] ) : __( 'Prompt example', 'autodocs-publish-blocks' );
	$prompt_id   = wp_unique_id( 'autodocs-publish-prompt-text-' );
	$mode_labels = array(
		'chat' => __( 'Chat', 'autodocs-publish-blocks' ),
		'code' => __( 'Code', 'autodocs-publish-blocks' ),
		'ask'  => __( 'Ask', 'autodocs-publish-blocks' ),
		'plan' => __( 'Plan', 'autodocs-publish-blocks' ),
	);

	ob_start();
	?>
	<section <?php echo get_block_wrapper_attributes( array( 'class' => 'autodocs-publish-prompt', 'data-mode' => $mode, 'aria-label' => __( 'AI prompt example', 'autodocs-publish-blocks' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<header class="autodocs-publish-prompt__header">
			<span class="autodocs-publish-prompt__mark" aria-hidden="true"><svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M10 2.5 11.4 7l4.1 1.4-4.1 1.4L10 14.5 8.6 9.8 4.5 8.4 8.6 7 10 2.5Z"/><path d="m15.5 13 .6 1.9 1.9.6-1.9.6-.6 1.9-.6-1.9-1.9-.6 1.9-.6.6-1.9Z"/></svg></span>
			<span class="autodocs-publish-prompt__label"><?php esc_html_e( 'Prompt', 'autodocs-publish-blocks' ); ?></span>
			<span class="autodocs-publish-prompt__model"><?php echo esc_html( $model ); ?></span>
			<span class="autodocs-publish-prompt__mode"><?php echo esc_html( $mode_labels[ $mode ] ); ?></span>
			<?php if ( $thinking ) : ?><span class="autodocs-publish-prompt__thinking"><span aria-hidden="true">✦</span><?php esc_html_e( 'Thinking', 'autodocs-publish-blocks' ); ?></span><?php endif; ?>
		</header>
		<div class="autodocs-publish-prompt__composer">
			<div id="<?php echo esc_attr( $prompt_id ); ?>" class="autodocs-publish-prompt__text"><?php echo autodocs_publish_blocks_render_prompt_text( $prompt ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Prompt is escaped before fixed spans are inserted. ?></div>
			<?php if ( $context ) : ?>
				<div class="autodocs-publish-prompt__context" aria-label="<?php esc_attr_e( 'Prompt context', 'autodocs-publish-blocks' ); ?>">
					<?php foreach ( $context as $item ) : ?>
						<span class="autodocs-publish-prompt__chip autodocs-publish-prompt__chip--<?php echo esc_attr( $item['kind'] ); ?>"><?php echo esc_html( $item['label'] ); ?></span>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<footer class="autodocs-publish-prompt__footer">
				<?php if ( $caption ) : ?>
					<span class="autodocs-publish-prompt__caption"><?php echo $caption; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<?php endif; ?>
				<button class="autodocs-publish-prompt__copy" type="button" data-autodocs-publish-copy data-autodocs-publish-copy-target="<?php echo esc_attr( $prompt_id ); ?>" aria-label="<?php esc_attr_e( 'Copy prompt', 'autodocs-publish-blocks' ); ?>">
					<svg viewBox="0 0 20 20" aria-hidden="true"><rect x="7" y="7" width="9" height="9" rx="1.5"/><path d="M13 7V5.5A1.5 1.5 0 0 0 11.5 4h-7A1.5 1.5 0 0 0 3 5.5v7A1.5 1.5 0 0 0 4.5 14H7"/></svg>
					<b><?php esc_html_e( 'Copy prompt', 'autodocs-publish-blocks' ); ?></b>
				</button>
			</footer>
		</div>
	</section>
	<?php
	return trim( ob_get_clean() );
}

/**
 * Register the Prompt block and its folder-owned assets.
 */
function autodocs_publish_blocks_register_prompt() {
	$block_url = AUTODOCS_PUBLISH_BLOCKS_URL . 'blocks/prompt/';

	wp_register_script( 'autodocs-publish-prompt-editor', $block_url . 'editor.js', array( 'wp-blocks', 'autodocs-publish-blocks-editor-shared' ), AUTODOCS_PUBLISH_BLOCKS_VERSION, true );
	wp_register_style( 'autodocs-publish-prompt', $block_url . 'style.css', array(), AUTODOCS_PUBLISH_BLOCKS_VERSION );
	wp_register_style( 'autodocs-publish-prompt-editor-style', $block_url . 'editor.css', array( 'wp-edit-blocks', 'autodocs-publish-prompt' ), AUTODOCS_PUBLISH_BLOCKS_VERSION );

	autodocs_publish_blocks_register_block_type(
		'autodocs-publish/prompt',
		array(
			'api_version'     => 3,
			'editor_script'   => 'autodocs-publish-prompt-editor',
			'style'           => 'autodocs-publish-prompt',
			'editor_style'    => 'autodocs-publish-prompt-editor-style',
			'render_callback' => 'autodocs_publish_blocks_render_prompt',
			'attributes'      => array(
				'prompt'   => array( 'type' => 'string', 'default' => 'Use $autodocs-publish-install to review this repository\'s documentation setup. Return a short plan before writing code.' ),
				'model'    => array( 'type' => 'string', 'default' => 'GPT-5' ),
				'mode'     => array( 'type' => 'string', 'default' => 'code' ),
				'thinking' => array( 'type' => 'boolean', 'default' => true ),
				'context'  => array( 'type' => 'string', 'default' => '$autodocs-publish-install, @repository, src/sync.js, docs/' ),
				'caption'  => array( 'type' => 'string', 'default' => 'Prompt example' ),
			),
			'supports'        => autodocs_publish_blocks_design_supports(),
		)
	);
}
add_action( 'init', 'autodocs_publish_blocks_register_prompt', 10 );
