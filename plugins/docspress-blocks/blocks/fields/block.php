<?php
/**
 * Fields / Schema block registration and rendering.
 *
 * @package DocsPressBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default schema fields.
 *
 * @return array
 */
function docspress_blocks_fields_defaults() {
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
function docspress_blocks_normalize_field( $field ) {
	$types = array( 'string', 'number', 'boolean', 'object', 'array', 'enum', 'url', 'date', 'any' );
	return array(
		'name'         => isset( $field['name'] ) ? sanitize_text_field( $field['name'] ) : '',
		'type'         => docspress_blocks_allowed_value( isset( $field['type'] ) ? $field['type'] : '', $types, 'string' ),
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
function docspress_blocks_render_fields( $attributes ) {
	$title       = isset( $attributes['title'] ) ? sanitize_text_field( $attributes['title'] ) : 'Configuration fields';
	$description = isset( $attributes['description'] ) ? wp_kses_post( $attributes['description'] ) : '';
	$searchable  = ! isset( $attributes['searchable'] ) || (bool) $attributes['searchable'];
	$compact     = ! empty( $attributes['compact'] );
	$raw_fields  = isset( $attributes['fields'] ) && is_array( $attributes['fields'] ) ? array_slice( $attributes['fields'], 0, 40 ) : docspress_blocks_fields_defaults();
	$fields      = array_map( 'docspress_blocks_normalize_field', $raw_fields );
	$fields      = array_values(
		array_filter(
			$fields,
			static function ( $field ) {
				return '' !== $field['name'];
			}
		)
	);
	$input_id    = wp_unique_id( 'docspress-fields-search-' );
	$wrapper     = get_block_wrapper_attributes(
		array(
			'class' => 'docspress-fields' . ( $compact ? ' is-compact' : '' ),
		)
	);

	if ( $searchable ) {
		wp_enqueue_script( 'docspress-fields-view' );
	}

	ob_start();
	?>
	<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-docspress-fields>
		<header class="docspress-fields__header">
			<div>
				<span class="docspress-fields__eyebrow"><?php esc_html_e( 'Reference', 'docspress-blocks' ); ?></span>
				<h3 class="docspress-fields__title"><?php echo esc_html( $title ); ?></h3>
				<?php if ( $description ) : ?>
					<div class="docspress-fields__description"><?php echo $description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php endif; ?>
			</div>
			<span class="docspress-fields__count" data-docspress-fields-count><?php echo esc_html( sprintf( _n( '%d field', '%d fields', count( $fields ), 'docspress-blocks' ), count( $fields ) ) ); ?></span>
		</header>
		<?php if ( $searchable && count( $fields ) > 3 ) : ?>
			<label class="docspress-fields__search" for="<?php echo esc_attr( $input_id ); ?>">
				<svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="8.5" cy="8.5" r="5.5"/><path d="m13 13 4 4"/></svg>
				<span class="screen-reader-text"><?php esc_html_e( 'Filter fields', 'docspress-blocks' ); ?></span>
				<input id="<?php echo esc_attr( $input_id ); ?>" type="search" placeholder="<?php esc_attr_e( 'Filter fields…', 'docspress-blocks' ); ?>" data-docspress-fields-search />
			</label>
		<?php endif; ?>
		<dl class="docspress-fields__list">
			<?php foreach ( $fields as $field ) : ?>
				<div class="docspress-fields__item<?php echo $field['deprecated'] ? ' is-deprecated' : ''; ?>" data-docspress-fields-item data-search="<?php echo esc_attr( strtolower( implode( ' ', $field ) ) ); ?>">
					<dt class="docspress-fields__term">
						<code><?php echo esc_html( $field['name'] ); ?></code>
						<span class="docspress-fields__type"><?php echo esc_html( $field['type'] ); ?></span>
						<?php if ( $field['required'] ) : ?>
							<span class="docspress-fields__badge is-required"><?php esc_html_e( 'required', 'docspress-blocks' ); ?></span>
						<?php endif; ?>
						<?php if ( $field['deprecated'] ) : ?>
							<span class="docspress-fields__badge is-deprecated"><?php esc_html_e( 'deprecated', 'docspress-blocks' ); ?></span>
						<?php endif; ?>
					</dt>
					<dd class="docspress-fields__definition">
						<?php if ( $field['description'] ) : ?>
							<div class="docspress-fields__copy"><?php echo $field['description']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
						<?php endif; ?>
						<?php if ( '' !== $field['defaultValue'] || '' !== $field['values'] ) : ?>
							<div class="docspress-fields__metadata">
								<?php if ( '' !== $field['defaultValue'] ) : ?>
									<span><b><?php esc_html_e( 'Default', 'docspress-blocks' ); ?></b><code><?php echo esc_html( $field['defaultValue'] ); ?></code></span>
								<?php endif; ?>
								<?php if ( '' !== $field['values'] ) : ?>
									<span><b><?php esc_html_e( 'Values', 'docspress-blocks' ); ?></b><code><?php echo esc_html( $field['values'] ); ?></code></span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</dd>
				</div>
			<?php endforeach; ?>
		</dl>
		<p class="docspress-fields__empty" data-docspress-fields-empty hidden><?php esc_html_e( 'No fields match this filter.', 'docspress-blocks' ); ?></p>
	</section>
	<?php
	return trim( ob_get_clean() );
}

/**
 * Register the Fields / Schema block.
 */
function docspress_blocks_register_fields() {
	$block_url = DOCSPRESS_BLOCKS_URL . 'blocks/fields/';
	$defaults  = docspress_blocks_fields_defaults();

	wp_register_script( 'docspress-fields-editor', $block_url . 'editor.js', array( 'wp-blocks', 'docspress-blocks-editor-shared' ), DOCSPRESS_BLOCKS_VERSION, true );
	wp_register_script( 'docspress-fields-view', $block_url . 'view.js', array(), DOCSPRESS_BLOCKS_VERSION, true );
	wp_register_style( 'docspress-fields', $block_url . 'style.css', array(), DOCSPRESS_BLOCKS_VERSION );
	wp_register_style( 'docspress-fields-editor-style', $block_url . 'editor.css', array( 'wp-edit-blocks', 'docspress-fields' ), DOCSPRESS_BLOCKS_VERSION );

	wp_add_inline_script( 'docspress-fields-editor', 'window.docspressFieldsDefaults = ' . wp_json_encode( $defaults ) . ';', 'before' );

	register_block_type(
		'docspress/fields',
		array(
			'api_version'     => 3,
			'editor_script'   => 'docspress-fields-editor',
			'style'           => 'docspress-fields',
			'editor_style'    => 'docspress-fields-editor-style',
			'render_callback' => 'docspress_blocks_render_fields',
			'attributes'      => array(
				'title'       => array( 'type' => 'string', 'default' => 'Configuration fields' ),
				'description' => array( 'type' => 'string', 'default' => 'Typed options, defaults, and constraints in one scannable reference.' ),
				'fields'      => array( 'type' => 'array', 'default' => $defaults ),
				'searchable'  => array( 'type' => 'boolean', 'default' => true ),
				'compact'     => array( 'type' => 'boolean', 'default' => false ),
			),
			'supports'        => docspress_blocks_design_supports(),
		)
	);
}
add_action( 'init', 'docspress_blocks_register_fields', 10 );
