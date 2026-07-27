<?php
/**
 * Plugin Name:       DocsPress Blocks
 * Plugin URI:        https://github.com/Automattic/docspress/tree/main/plugins/docspress-blocks
 * Description:       Documentation-focused Gutenberg blocks for interactive API examples, schemas, code playgrounds, diagrams, troubleshooting, prompts, flows, and polished documentation layouts.
 * Version:           0.9.9
 * Requires at least: 6.6
 * Requires PHP:      7.4
 * Author:            Automattic
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       docspress-blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DOCSPRESS_BLOCKS_VERSION', '0.9.9' );
define( 'DOCSPRESS_BLOCKS_FILE', __FILE__ );
define( 'DOCSPRESS_BLOCKS_PATH', plugin_dir_path( __FILE__ ) );
define( 'DOCSPRESS_BLOCKS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Design tools shared by every DocsPress block.
 *
 * These map directly to the Block Editor's Styles panel. Content-specific
 * options remain in each block's inspector while colors, typography, spacing,
 * borders, dimensions, and positioning use native WordPress controls.
 *
 * @param array<int,string>|false $align Optional supported alignments.
 * @return array<string,mixed>
 */
function docspress_blocks_design_supports( $align = false ) {
	$supports = array(
		'anchor'     => true,
		'html'       => false,
		'className'  => true,
		'color'      => array(
			'background' => true,
			'gradients'  => true,
			'link'       => true,
			'text'       => true,
		),
		'spacing'    => array(
			'blockGap' => true,
			'margin'   => true,
			'padding'  => true,
		),
		'typography' => array(
			'fontFamily'    => true,
			'fontSize'      => true,
			'fontStyle'     => true,
			'fontWeight'    => true,
			'letterSpacing' => true,
			'lineHeight'    => true,
			'textDecoration' => true,
			'textTransform' => true,
		),
		'border'     => array(
			'color'  => true,
			'radius' => true,
			'style'  => true,
			'width'  => true,
		),
		'dimensions' => array(
			'minHeight' => true,
		),
		'position'   => array(
			'sticky' => true,
		),
		'shadow'     => true,
	);

	if ( $align ) {
		$supports['align'] = $align;
	}

	return $supports;
}

require_once DOCSPRESS_BLOCKS_PATH . 'includes/code-surface.php';
require_once DOCSPRESS_BLOCKS_PATH . 'blocks/hero/block.php';
require_once DOCSPRESS_BLOCKS_PATH . 'blocks/audience-paths/block.php';
require_once DOCSPRESS_BLOCKS_PATH . 'blocks/colorful-code/block.php';
require_once DOCSPRESS_BLOCKS_PATH . 'blocks/code-tabs/block.php';
require_once DOCSPRESS_BLOCKS_PATH . 'blocks/callout/block.php';
require_once DOCSPRESS_BLOCKS_PATH . 'blocks/api-request/block.php';
require_once DOCSPRESS_BLOCKS_PATH . 'blocks/terminal-session/block.php';
require_once DOCSPRESS_BLOCKS_PATH . 'blocks/result/block.php';
require_once DOCSPRESS_BLOCKS_PATH . 'blocks/flow/block.php';
require_once DOCSPRESS_BLOCKS_PATH . 'blocks/file-tree/block.php';
require_once DOCSPRESS_BLOCKS_PATH . 'blocks/prompt/block.php';
require_once DOCSPRESS_BLOCKS_PATH . 'blocks/fields/block.php';
require_once DOCSPRESS_BLOCKS_PATH . 'blocks/code-playground/block.php';
require_once DOCSPRESS_BLOCKS_PATH . 'blocks/diagram/block.php';
require_once DOCSPRESS_BLOCKS_PATH . 'blocks/troubleshooter/block.php';
require_once DOCSPRESS_BLOCKS_PATH . 'includes/patterns.php';

/**
 * Register the small shared layer used by multiple block folders.
 */
function docspress_blocks_register_shared_assets() {
	$asset_url = DOCSPRESS_BLOCKS_URL . 'assets/';

	wp_register_script(
		'docspress-blocks-editor-shared',
		$asset_url . 'editor-shared.js',
		array( 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n' ),
		DOCSPRESS_BLOCKS_VERSION,
		true
	);

	wp_add_inline_script(
		'docspress-blocks-editor-shared',
		'window.docspressBlocksSettings = ' . wp_json_encode(
			array(
				'preset' => 'site-editor',
				'tokens' => array(),
			)
		) . ';',
		'before'
	);

	wp_register_script(
		'docspress-blocks-view',
		$asset_url . 'view.js',
		array(),
		DOCSPRESS_BLOCKS_VERSION,
		true
	);

	wp_register_style(
		'docspress-blocks-code',
		$asset_url . 'code.css',
		array(),
		DOCSPRESS_BLOCKS_VERSION
	);

	wp_register_style(
		'docspress-blocks-code-editor',
		$asset_url . 'code-editor.css',
		array( 'wp-edit-blocks', 'docspress-blocks-code' ),
		DOCSPRESS_BLOCKS_VERSION
	);
}
add_action( 'init', 'docspress_blocks_register_shared_assets', 5 );
