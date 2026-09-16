<?php
/**
 * Result block registration and rendering.
 *
 * @package AutodocsPublishBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the icon for a result status.
 *
 * @param string $status Result status.
 * @return string
 */
function autodocs_publish_blocks_result_icon( $status ) {
	$icons = array(
		'success' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg>',
		'neutral' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M8 12h8"/></svg>',
		'warning' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m12 4 9 16H3L12 4Z"/><path d="M12 9v5m0 3h.01"/></svg>',
		'error'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m9 9 6 6m0-6-6 6"/></svg>',
	);

	return $icons[ $status ];
}

/**
 * Render the Result block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function autodocs_publish_blocks_render_result( $attributes ) {
	$status  = autodocs_publish_blocks_allowed_value( isset( $attributes['status'] ) ? $attributes['status'] : '', array( 'success', 'neutral', 'warning', 'error' ), 'success' );
	$title   = isset( $attributes['title'] ) ? wp_kses_post( $attributes['title'] ) : '';
	$content = isset( $attributes['content'] ) ? wp_kses_post( $attributes['content'] ) : '';
	$meta    = isset( $attributes['meta'] ) ? sanitize_text_field( $attributes['meta'] ) : '';

	ob_start();
	?>
	<section <?php echo get_block_wrapper_attributes( array( 'class' => 'autodocs-publish-result autodocs-publish-result--' . $status ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<span class="autodocs-publish-result__icon" aria-hidden="true"><?php echo autodocs_publish_blocks_result_icon( $status ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<div class="autodocs-publish-result__body">
			<div class="autodocs-publish-result__kicker"><?php echo esc_html( ucfirst( $status ) ); ?></div>
			<?php if ( $title ) : ?><div class="autodocs-publish-result__title"><?php echo $title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div><?php endif; ?>
			<?php if ( $content ) : ?><div class="autodocs-publish-result__content"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div><?php endif; ?>
		</div>
		<?php if ( $meta ) : ?><div class="autodocs-publish-result__meta"><?php echo esc_html( $meta ); ?></div><?php endif; ?>
	</section>
	<?php
	return trim( ob_get_clean() );
}

/**
 * Register the Result block and its folder-owned assets.
 */
function autodocs_publish_blocks_register_result() {
	$block_url = AUTODOCS_PUBLISH_BLOCKS_URL . 'blocks/result/';

	wp_register_script( 'autodocs-publish-result-editor', $block_url . 'editor.js', array( 'wp-blocks', 'autodocs-publish-blocks-editor-shared' ), AUTODOCS_PUBLISH_BLOCKS_VERSION, true );
	wp_register_style( 'autodocs-publish-result', $block_url . 'style.css', array(), AUTODOCS_PUBLISH_BLOCKS_VERSION );
	wp_register_style( 'autodocs-publish-result-editor-style', $block_url . 'editor.css', array( 'wp-edit-blocks', 'autodocs-publish-result' ), AUTODOCS_PUBLISH_BLOCKS_VERSION );

	autodocs_publish_blocks_register_block_type(
		'autodocs-publish/result',
		array(
			'api_version'     => 3,
			'editor_script'   => 'autodocs-publish-result-editor',
			'style'           => 'autodocs-publish-result',
			'editor_style'    => 'autodocs-publish-result-editor-style',
			'render_callback' => 'autodocs_publish_blocks_render_result',
			'attributes'      => array(
				'status'  => array( 'type' => 'string', 'default' => 'success' ),
				'title'   => array( 'type' => 'string', 'default' => 'Deployment completed' ),
				'content' => array( 'type' => 'string', 'default' => '<p>All documentation pages are up to date.</p>' ),
				'meta'    => array( 'type' => 'string', 'default' => '12 pages · 1.8s' ),
			),
			'supports'        => autodocs_publish_blocks_design_supports(),
		)
	);
}
add_action( 'init', 'autodocs_publish_blocks_register_result', 10 );
