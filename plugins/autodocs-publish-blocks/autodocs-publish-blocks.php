<?php
/**
 * Plugin Name:       Autodocs Publish Blocks
 * Plugin URI:        https://github.com/Automattic/autodocs-publish/tree/main/plugins/autodocs-publish-blocks
 * Description:       Documentation-focused Gutenberg blocks for interactive API examples, schemas, code playgrounds, diagrams, troubleshooting, prompts, flows, and polished documentation layouts.
 * Version:           0.11.0
 * Requires at least: 6.6
 * Requires PHP:      7.4
 * Author:            Automattic
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       autodocs-publish-blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AUTODOCS_PUBLISH_BLOCKS_VERSION', '0.11.0' );
define( 'AUTODOCS_PUBLISH_BLOCKS_FILE', __FILE__ );
define( 'AUTODOCS_PUBLISH_BLOCKS_PATH', plugin_dir_path( __FILE__ ) );
define( 'AUTODOCS_PUBLISH_BLOCKS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Deactivate the package installed under the pre-rename plugin slug.
 *
 * WordPress treats a renamed plugin directory as a second plugin. Leaving both
 * active would register the same compatibility taxonomies and block aliases
 * twice, so activation of the renamed package completes the slug migration.
 * The legacy plugin files are kept in place and can still be restored manually.
 *
 * @param bool $network_wide Whether this is a network activation.
 */
function autodocs_publish_blocks_deactivate_legacy_plugin( $network_wide = false ) {
	$legacy_plugin = 'docspress-blocks/docspress-blocks.php';

	if ( ! function_exists( 'deactivate_plugins' ) || ! function_exists( 'is_plugin_active' ) || ! function_exists( 'is_plugin_active_for_network' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$network_active = is_multisite() && is_plugin_active_for_network( $legacy_plugin );
	if ( ! is_plugin_active( $legacy_plugin ) && ! $network_active ) {
		return;
	}

	deactivate_plugins( $legacy_plugin, false, $network_active );
}
register_activation_hook( AUTODOCS_PUBLISH_BLOCKS_FILE, 'autodocs_publish_blocks_deactivate_legacy_plugin' );

/**
 * Design tools shared by every Autodocs Publish block.
 *
 * These map directly to the Block Editor's Styles panel. Content-specific
 * options remain in each block's inspector while colors, typography, spacing,
 * borders, dimensions, and positioning use native WordPress controls.
 *
 * @param array<int,string>|false $align Optional supported alignments.
 * @return array<string,mixed>
 */
function autodocs_publish_blocks_design_supports( $align = false ) {
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

/**
 * Add the canonical block class to markup rendered through a legacy alias.
 *
 * @param string $html           Rendered block markup.
 * @param string $canonical_name Canonical block name.
 * @return string
 */
function autodocs_publish_blocks_add_canonical_class( $html, $canonical_name ) {
	if ( ! is_string( $html ) || '' === $html || ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return $html;
	}

	$processor = new WP_HTML_Tag_Processor( $html );
	if ( ! $processor->next_tag() ) {
		return $html;
	}

	$processor->add_class( 'wp-block-' . str_replace( '/', '-', $canonical_name ) );
	return $processor->get_updated_html();
}

/**
 * Register a canonical block and its hidden DocsPress compatibility alias.
 *
 * @param string              $name Canonical block name.
 * @param array<string,mixed> $args Block registration settings.
 * @return WP_Block_Type|false
 */
function autodocs_publish_blocks_register_block_type( $name, $args ) {
	$registered = register_block_type( $name, $args );
	$prefix     = 'autodocs-publish/';

	if ( ! $registered || 0 !== strpos( $name, $prefix ) ) {
		return $registered;
	}

	$legacy_name = 'docspress/' . substr( $name, strlen( $prefix ) );
	$registry    = WP_Block_Type_Registry::get_instance();
	if ( $registry->is_registered( $legacy_name ) ) {
		return $registered;
	}

	$legacy_args                         = $args;
	$legacy_args['supports']             = isset( $args['supports'] ) && is_array( $args['supports'] ) ? $args['supports'] : array();
	$legacy_args['supports']['inserter'] = false;

	if ( ! empty( $args['render_callback'] ) && is_callable( $args['render_callback'] ) ) {
		$render_callback = $args['render_callback'];
		$legacy_args['render_callback'] = static function ( $attributes, $content = '', $block = null ) use ( $render_callback, $name ) {
			$html = call_user_func( $render_callback, $attributes, $content, $block );
			return autodocs_publish_blocks_add_canonical_class( $html, $name );
		};
	}

	register_block_type( $legacy_name, $legacy_args );
	return $registered;
}

require_once AUTODOCS_PUBLISH_BLOCKS_PATH . 'includes/code-surface.php';
require_once AUTODOCS_PUBLISH_BLOCKS_PATH . 'includes/versioning.php';
require_once AUTODOCS_PUBLISH_BLOCKS_PATH . 'blocks/version-switcher/block.php';
require_once AUTODOCS_PUBLISH_BLOCKS_PATH . 'blocks/version-notice/block.php';
require_once AUTODOCS_PUBLISH_BLOCKS_PATH . 'blocks/hero/block.php';
require_once AUTODOCS_PUBLISH_BLOCKS_PATH . 'blocks/audience-paths/block.php';
require_once AUTODOCS_PUBLISH_BLOCKS_PATH . 'blocks/colorful-code/block.php';
require_once AUTODOCS_PUBLISH_BLOCKS_PATH . 'blocks/code-tabs/block.php';
require_once AUTODOCS_PUBLISH_BLOCKS_PATH . 'blocks/callout/block.php';
require_once AUTODOCS_PUBLISH_BLOCKS_PATH . 'blocks/api-request/block.php';
require_once AUTODOCS_PUBLISH_BLOCKS_PATH . 'blocks/terminal-session/block.php';
require_once AUTODOCS_PUBLISH_BLOCKS_PATH . 'blocks/result/block.php';
require_once AUTODOCS_PUBLISH_BLOCKS_PATH . 'blocks/flow/block.php';
require_once AUTODOCS_PUBLISH_BLOCKS_PATH . 'blocks/file-tree/block.php';
require_once AUTODOCS_PUBLISH_BLOCKS_PATH . 'blocks/prompt/block.php';
require_once AUTODOCS_PUBLISH_BLOCKS_PATH . 'blocks/fields/block.php';
require_once AUTODOCS_PUBLISH_BLOCKS_PATH . 'blocks/code-playground/block.php';
require_once AUTODOCS_PUBLISH_BLOCKS_PATH . 'blocks/diagram/block.php';
require_once AUTODOCS_PUBLISH_BLOCKS_PATH . 'blocks/troubleshooter/block.php';
require_once AUTODOCS_PUBLISH_BLOCKS_PATH . 'includes/patterns.php';

/**
 * Register the small shared layer used by multiple block folders.
 */
function autodocs_publish_blocks_register_shared_assets() {
	$asset_url = AUTODOCS_PUBLISH_BLOCKS_URL . 'assets/';

	wp_register_script(
		'autodocs-publish-blocks-editor-shared',
		$asset_url . 'editor-shared.js',
		array( 'wp-block-editor', 'wp-components', 'wp-element', 'wp-hooks', 'wp-i18n' ),
		AUTODOCS_PUBLISH_BLOCKS_VERSION,
		true
	);

	wp_add_inline_script(
		'autodocs-publish-blocks-editor-shared',
		'window.autodocsPublishBlocksSettings = ' . wp_json_encode(
			array(
				'preset' => 'site-editor',
				'tokens' => array(),
			)
		) . ';',
		'before'
	);

	wp_register_script(
		'autodocs-publish-blocks-view',
		$asset_url . 'view.js',
		array(),
		AUTODOCS_PUBLISH_BLOCKS_VERSION,
		true
	);

	wp_register_style(
		'autodocs-publish-blocks-code',
		$asset_url . 'code.css',
		array(),
		AUTODOCS_PUBLISH_BLOCKS_VERSION
	);

	wp_register_style(
		'autodocs-publish-blocks-code-editor',
		$asset_url . 'code-editor.css',
		array( 'wp-edit-blocks', 'autodocs-publish-blocks-code' ),
		AUTODOCS_PUBLISH_BLOCKS_VERSION
	);
}
add_action( 'init', 'autodocs_publish_blocks_register_shared_assets', 5 );
