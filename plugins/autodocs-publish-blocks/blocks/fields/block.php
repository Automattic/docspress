<?php
/**
 * Fields / Schema block registration and rendering.
 *
 * @package AutodocsPublishBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default schema fields.
 *
 * @return array
 */
function autodocs_publish_blocks_fields_defaults() {
	return array(
		array(
			'name'         => 'site',
			'type'         => 'string',
			'required'     => true,
			'defaultValue' => '',
			'description'  => 'WordPress site domain or numeric site ID.',
			'values'       => '',
			'deprecated'   => false,
		),
		array(
			'name'         => 'status',
			'type'         => 'string',
			'required'     => false,
			'defaultValue' => 'draft',
			'description'  => 'Publication status for synchronized Pages.',
			'values'       => 'draft, publish, private',
			'deprecated'   => false,
		),
		array(
			'name'         => 'dryRun',
			'type'         => 'boolean',
			'required'     => false,
			'defaultValue' => 'false',
			'description'  => 'Preview reconciliation without writing changes.',
			'values'       => 'true, false',
			'deprecated'   => false,
		),
	);
}

/**
 * Normalize one field definition.
 *
 * @param array $field Raw field.
 * @return array
 */
function autodocs_publish_blocks_normalize_field( $field ) {
	$types = array( 'string', 'number', 'boolean', 'object', 'array', 'enum', 'url', 'date', 'any' );
	return array(
		'name'         => isset( $field['name'] ) ? sanitize_text_field( $field['name'] ) : '',
		'type'         => autodocs_publish_blocks_allowed_value( isset( $field['type'] ) ? $field['type'] : '', $types, 'string' ),
		'required'     => ! empty( $field['required'] ),
		'defaultValue' => isset( $field['defaultValue'] ) ? sanitize_text_field( $field['defaultValue'] ) : '',
		'description'  => isset( $field['description'] ) ? wp_kses_post( $field['description'] ) : '',
		'values'       => isset( $field['values'] ) ? sanitize_text_field( $field['values'] ) : '',
		'deprecated'   => ! empty( $field['deprecated'] ),
	);
}

/**
 * Render the Fields / Schema block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function autodocs_publish_blocks_render_fields( $attributes ) {
	$title       = isset( $attributes['title'] ) ? sanitize_text_field( $attributes['title'] ) : 'Configuration fields';
	$description = isset( $attributes['description'] ) ? wp_kses_post( $attributes['description'] ) : '';
	$searchable  = ! isset( $attributes['searchable'] ) || (bool) $attributes['searchable'];
	$compact     = ! empty( $attributes['compact'] );
	$raw_fields  = isset( $attributes['fields'] ) && is_array( $attributes['fields'] ) ? array_slice( $attributes['fields'], 0, 40 ) : autodocs_publish_blocks_fields_defaults();
	$fields      = array_map( 'autodocs_publish_blocks_normalize_field', $raw_fields );
	$fields      = array_values(
		array_filter(
			$fields,
			static function ( $field ) {
				return '' !== $field['name'];
			}
		)
	);
	$input_id    = wp_unique_id( 'autodocs-publish-fields-search-' );
	$wrapper     = get_block_wrapper_attributes(
		array(
			'class' => 'autodocs-publish-fields' . ( $compact ? ' is-compact' : '' ),
		)
	);

	if ( $searchable ) {
		wp_enqueue_script( 'autodocs-publish-fields-view' );
	}

	ob_start();
	?>
	<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-autodocs-publish-fields>
		<header class="autodocs-publish-fields__header">
			<div>
				<span class="autodocs-publish-fields__eyebrow"><?php esc_html_e( 'Reference', 'autodocs-publish-blocks' ); ?></span>
				<h3 class="autodocs-publish-fields__title"><?php echo esc_html( $title ); ?></h3>
				<?php if ( $description ) : ?>
					<div class="autodocs-publish-fields__description"><?php echo $description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php endif; ?>
			</div>
			<span class="autodocs-publish-fields__count" data-autodocs-publish-fields-count><?php echo esc_html( sprintf( _n( '%d field', '%d fields', count( $fields ), 'autodocs-publish-blocks' ), count( $fields ) ) ); ?></span>
		</header>
		<?php if ( $searchable && count( $fields ) > 3 ) : ?>
			<label class="autodocs-publish-fields__search" for="<?php echo esc_attr( $input_id ); ?>">
				<svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="8.5" cy="8.5" r="5.5"/><path d="m13 13 4 4"/></svg>
				<span class="screen-reader-text"><?php esc_html_e( 'Filter fields', 'autodocs-publish-blocks' ); ?></span>
				<input id="<?php echo esc_attr( $input_id ); ?>" type="search" placeholder="<?php esc_attr_e( 'Filter fields…', 'autodocs-publish-blocks' ); ?>" data-autodocs-publish-fields-search />
			</label>
		<?php endif; ?>
		<dl class="autodocs-publish-fields__list">
			<?php foreach ( $fields as $field ) : ?>
				<div class="autodocs-publish-fields__item<?php echo $field['deprecated'] ? ' is-deprecated' : ''; ?>" data-autodocs-publish-fields-item data-search="<?php echo esc_attr( strtolower( implode( ' ', $field ) ) ); ?>">
					<dt class="autodocs-publish-fields__term">
						<code><?php echo esc_html( $field['name'] ); ?></code>
						<span class="autodocs-publish-fields__type"><?php echo esc_html( $field['type'] ); ?></span>
						<?php if ( $field['required'] ) : ?>
							<span class="autodocs-publish-fields__badge is-required"><?php esc_html_e( 'required', 'autodocs-publish-blocks' ); ?></span>
						<?php endif; ?>
						<?php if ( $field['deprecated'] ) : ?>
							<span class="autodocs-publish-fields__badge is-deprecated"><?php esc_html_e( 'deprecated', 'autodocs-publish-blocks' ); ?></span>
						<?php endif; ?>
					</dt>
					<dd class="autodocs-publish-fields__definition">
						<?php if ( $field['description'] ) : ?>
							<div class="autodocs-publish-fields__copy"><?php echo $field['description']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
						<?php endif; ?>
						<?php if ( '' !== $field['defaultValue'] || '' !== $field['values'] ) : ?>
							<div class="autodocs-publish-fields__metadata">
								<?php if ( '' !== $field['defaultValue'] ) : ?>
									<span><b><?php esc_html_e( 'Default', 'autodocs-publish-blocks' ); ?></b><code><?php echo esc_html( $field['defaultValue'] ); ?></code></span>
								<?php endif; ?>
								<?php if ( '' !== $field['values'] ) : ?>
									<span><b><?php esc_html_e( 'Values', 'autodocs-publish-blocks' ); ?></b><code><?php echo esc_html( $field['values'] ); ?></code></span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</dd>
				</div>
			<?php endforeach; ?>
		</dl>
		<p class="autodocs-publish-fields__empty" data-autodocs-publish-fields-empty hidden><?php esc_html_e( 'No fields match this filter.', 'autodocs-publish-blocks' ); ?></p>
	</section>
	<?php
	return trim( ob_get_clean() );
}

/**
 * Register the Fields / Schema block.
 */
function autodocs_publish_blocks_register_fields() {
	$block_url = AUTODOCS_PUBLISH_BLOCKS_URL . 'blocks/fields/';
	$defaults  = autodocs_publish_blocks_fields_defaults();

	wp_register_script( 'autodocs-publish-fields-editor', $block_url . 'editor.js', array( 'wp-blocks', 'autodocs-publish-blocks-editor-shared' ), AUTODOCS_PUBLISH_BLOCKS_VERSION, true );
	wp_register_script( 'autodocs-publish-fields-view', $block_url . 'view.js', array(), AUTODOCS_PUBLISH_BLOCKS_VERSION, true );
	wp_register_style( 'autodocs-publish-fields', $block_url . 'style.css', array(), AUTODOCS_PUBLISH_BLOCKS_VERSION );
	wp_register_style( 'autodocs-publish-fields-editor-style', $block_url . 'editor.css', array( 'wp-edit-blocks', 'autodocs-publish-fields' ), AUTODOCS_PUBLISH_BLOCKS_VERSION );

	wp_add_inline_script( 'autodocs-publish-fields-editor', 'window.autodocsPublishFieldsDefaults = ' . wp_json_encode( $defaults ) . ';', 'before' );

	autodocs_publish_blocks_register_block_type(
		'autodocs-publish/fields',
		array(
			'api_version'     => 3,
			'editor_script'   => 'autodocs-publish-fields-editor',
			'style'           => 'autodocs-publish-fields',
			'editor_style'    => 'autodocs-publish-fields-editor-style',
			'render_callback' => 'autodocs_publish_blocks_render_fields',
			'attributes'      => array(
				'title'       => array( 'type' => 'string', 'default' => 'Configuration fields' ),
				'description' => array( 'type' => 'string', 'default' => 'Typed options, defaults, and constraints in one scannable reference.' ),
				'fields'      => array( 'type' => 'array', 'default' => $defaults ),
				'searchable'  => array( 'type' => 'boolean', 'default' => true ),
				'compact'     => array( 'type' => 'boolean', 'default' => false ),
			),
			'supports'        => autodocs_publish_blocks_design_supports(),
		)
	);
}
add_action( 'init', 'autodocs_publish_blocks_register_fields', 10 );
