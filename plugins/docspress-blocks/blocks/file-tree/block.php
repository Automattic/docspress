<?php
/**
 * File Tree block registration and rendering.
 *
 * @package DocsPressBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return a file or folder icon.
 *
 * @param bool $folder Whether the entry is a folder.
 * @return string
 */
function docspress_blocks_file_tree_icon( $folder ) {
	if ( $folder ) {
		return '<svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M2.5 5.5h5l1.5 2h8.5v7.5a1.5 1.5 0 0 1-1.5 1.5H4A1.5 1.5 0 0 1 2.5 15V5.5Z"/><path d="M2.5 7.5V5A1.5 1.5 0 0 1 4 3.5h3l2 2h7A1.5 1.5 0 0 1 17.5 7v.5"/></svg>';
	}

	return '<svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M5 2.5h6l4 4V17H5V2.5Z"/><path d="M11 2.5v4h4"/></svg>';
}

/**
 * Parse indentation-aware file tree source into nested entries.
 *
 * @param string $tree File tree source.
 * @return array<int,array<string,mixed>>
 */
function docspress_blocks_parse_file_tree( $tree ) {
	$nodes = array();
	$path  = array();
	$lines = preg_split( '/\R/', str_replace( "\t", '  ', $tree ) );

	foreach ( $lines as $line ) {
		preg_match( '/^(\s*)(.*)$/', $line, $matches );
		$label = isset( $matches[2] ) ? trim( $matches[2] ) : '';
		if ( '' === $label ) {
			continue;
		}

		$depth = isset( $matches[1] ) ? min( 12, (int) floor( strlen( $matches[1] ) / 2 ) ) : 0;
		while ( count( $path ) > $depth ) {
			array_pop( $path );
		}

		$parent =& $nodes;
		foreach ( $path as $index ) {
			$parent =& $parent[ $index ]['children'];
		}

		$parent[] = array(
			'label'    => $label,
			'folder'   => '/' === substr( $label, -1 ),
			'children' => array(),
		);
		$path[]  = array_key_last( $parent );
		unset( $parent );
	}

	return $nodes;
}

/**
 * Render nested File Tree entries.
 *
 * @param array<int,array<string,mixed>> $nodes       Parsed entries.
 * @param bool                           $collapsible Whether folders can collapse.
 * @param bool                           $open        Whether folders begin open.
 * @return string
 */
function docspress_blocks_render_file_tree_nodes( $nodes, $collapsible, $open ) {
	ob_start();
	foreach ( $nodes as $node ) {
		$children   = isset( $node['children'] ) && is_array( $node['children'] ) ? $node['children'] : array();
		$folder     = ! empty( $node['folder'] ) || ! empty( $children );
		$can_toggle = $folder && ! empty( $children ) && $collapsible;
		?>
		<li class="docspress-file-tree__item <?php echo $folder ? 'is-folder' : 'is-file'; ?><?php echo $children ? ' has-children' : ''; ?>">
			<?php if ( $can_toggle ) : ?>
				<details class="docspress-file-tree__folder"<?php echo $open ? ' open' : ''; ?>>
					<summary class="docspress-file-tree__entry">
						<span class="docspress-file-tree__toggle" aria-hidden="true"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6"><path d="m4 2 4 4-4 4"/></svg></span>
						<span class="docspress-file-tree__icon" aria-hidden="true"><?php echo docspress_blocks_file_tree_icon( true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<code><?php echo esc_html( $node['label'] ); ?></code>
					</summary>
					<ul class="docspress-file-tree__children">
						<?php echo docspress_blocks_render_file_tree_nodes( $children, $collapsible, $open ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</ul>
				</details>
			<?php else : ?>
				<div class="docspress-file-tree__entry">
					<span class="docspress-file-tree__toggle" aria-hidden="true"></span>
					<span class="docspress-file-tree__icon" aria-hidden="true"><?php echo docspress_blocks_file_tree_icon( $folder ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<code><?php echo esc_html( $node['label'] ); ?></code>
				</div>
				<?php if ( $children ) : ?>
					<ul class="docspress-file-tree__children">
						<?php echo docspress_blocks_render_file_tree_nodes( $children, $collapsible, $open ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</ul>
				<?php endif; ?>
			<?php endif; ?>
		</li>
		<?php
	}

	return trim( ob_get_clean() );
}

/**
 * Render the File Tree block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function docspress_blocks_render_file_tree( $attributes ) {
	$root        = isset( $attributes['root'] ) ? sanitize_text_field( $attributes['root'] ) : 'project/';
	$tree        = isset( $attributes['tree'] ) ? (string) $attributes['tree'] : '';
	$caption     = isset( $attributes['caption'] ) ? wp_kses_post( $attributes['caption'] ) : '';
	$collapsible = ! isset( $attributes['collapsible'] ) || (bool) $attributes['collapsible'];
	$open        = ! isset( $attributes['open'] ) || (bool) $attributes['open'];
	$nodes       = docspress_blocks_parse_file_tree( $tree );

	ob_start();
	?>
	<figure <?php echo get_block_wrapper_attributes( array( 'class' => 'docspress-file-tree' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<div class="docspress-file-tree__bar">
			<span class="docspress-file-tree__root-icon" aria-hidden="true"><?php echo docspress_blocks_file_tree_icon( true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<code><?php echo esc_html( $root ); ?></code>
			<span><?php esc_html_e( 'File tree', 'docspress-blocks' ); ?></span>
		</div>
		<ul class="docspress-file-tree__entries" aria-label="<?php echo esc_attr( sprintf( __( 'Files in %s', 'docspress-blocks' ), $root ) ); ?>">
			<?php echo docspress_blocks_render_file_tree_nodes( $nodes, $collapsible, $open ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</ul>
		<?php if ( $caption ) : ?><figcaption class="docspress-file-tree__caption"><?php echo $caption; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></figcaption><?php endif; ?>
	</figure>
	<?php
	return trim( ob_get_clean() );
}

/**
 * Register the File Tree block and its folder-owned assets.
 */
function docspress_blocks_register_file_tree() {
	$block_url = DOCSPRESS_BLOCKS_URL . 'blocks/file-tree/';

	wp_register_script( 'docspress-file-tree-editor', $block_url . 'editor.js', array( 'wp-blocks', 'docspress-blocks-editor-shared' ), DOCSPRESS_BLOCKS_VERSION, true );
	wp_register_style( 'docspress-file-tree', $block_url . 'style.css', array(), DOCSPRESS_BLOCKS_VERSION );
	wp_register_style( 'docspress-file-tree-editor-style', $block_url . 'editor.css', array( 'wp-edit-blocks', 'docspress-file-tree' ), DOCSPRESS_BLOCKS_VERSION );

	register_block_type(
		'docspress/file-tree',
		array(
			'api_version'     => 3,
			'editor_script'   => 'docspress-file-tree-editor',
			'style'           => 'docspress-file-tree',
			'editor_style'    => 'docspress-file-tree-editor-style',
			'render_callback' => 'docspress_blocks_render_file_tree',
			'attributes'      => array(
				'root'        => array( 'type' => 'string', 'default' => 'project/' ),
				'tree'        => array( 'type' => 'string', 'default' => "docs/\n  getting-started.md\n  api/\n    endpoints.md\npackage.json" ),
				'caption'     => array( 'type' => 'string', 'default' => '' ),
				'collapsible' => array( 'type' => 'boolean', 'default' => true ),
				'open'        => array( 'type' => 'boolean', 'default' => true ),
			),
			'supports'        => docspress_blocks_design_supports(),
		)
	);
}
add_action( 'init', 'docspress_blocks_register_file_tree', 10 );
