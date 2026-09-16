<?php
/**
 * Autodocs Publish block patterns.
 *
 * @package AutodocsPublishBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Serialize a dynamic block for use inside a pattern.
 *
 * @param string $name       Block name.
 * @param array  $attributes Block attributes.
 * @return string
 */
function autodocs_publish_blocks_serialize( $name, $attributes = array() ) {
	$attributes_json = $attributes ? ' ' . serialize_block_attributes( $attributes ) : '';
	return '<!-- wp:' . $name . $attributes_json . ' /-->';
}

/**
 * Register the plugin's starter patterns.
 */
function autodocs_publish_blocks_register_patterns() {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}

	register_block_pattern_category(
		'autodocs-publish',
		array( 'label' => __( 'Autodocs Publish', 'autodocs-publish-blocks' ) )
	);

	register_block_pattern(
		'autodocs-publish/homepage-hero',
		array(
			'title'       => __( 'Homepage hero', 'autodocs-publish-blocks' ),
			'description' => __( 'An editable Autodocs Publish hero with two actions, optional media, and responsive layout controls.', 'autodocs-publish-blocks' ),
			'categories'  => array( 'autodocs-publish' ),
			'content'     => autodocs_publish_blocks_serialize(
				'autodocs-publish/hero',
				array(
					'primaryLabel' => 'Choose your path',
					'primaryUrl'   => '#choose-your-path',
					'secondaryUrl' => '/#latest-updates',
				)
			),
		)
	);

	register_block_pattern(
		'autodocs-publish/audience-onboarding',
		array(
			'title'       => __( 'Documentation starting paths', 'autodocs-publish-blocks' ),
			'description' => __( 'Route readers based on whether they already have Markdown docs or need to create them.', 'autodocs-publish-blocks' ),
			'categories'  => array( 'autodocs-publish' ),
			'content'     => autodocs_publish_blocks_serialize(
				'autodocs-publish/audience-paths',
				array(
					'anchor' => 'choose-your-path',
					'align'  => 'wide',
				)
			),
		)
	);

	register_block_pattern(
		'autodocs-publish/documentation-page-starter',
		array(
			'title'       => __( 'Documentation page starter', 'autodocs-publish-blocks' ),
			'description' => __( 'A documentation outline with a callout, copyable terminal command, and verification result.', 'autodocs-publish-blocks' ),
			'categories'  => array( 'autodocs-publish' ),
			'content'     => '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Overview</h2><!-- /wp:heading -->'
				. '<!-- wp:paragraph --><p>Explain what the reader will accomplish and what they need before starting.</p><!-- /wp:paragraph -->'
				. autodocs_publish_blocks_serialize(
					'autodocs-publish/callout',
					array(
						'tone'    => 'tip',
						'title'   => 'Before you begin',
						'content' => '<p>Keep credentials in environment variables and out of committed files.</p>',
					)
				)
				. '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Run the example</h2><!-- /wp:heading -->'
				. autodocs_publish_blocks_serialize(
					'autodocs-publish/terminal-session',
					array(
						'title'   => 'Publish documentation',
						'shell'   => 'bash',
						'command' => 'npx autodocs-publish publish ./docs',
						'output'  => "✓ Read 12 documents\n✓ Published 12 WordPress pages",
					)
				)
				. autodocs_publish_blocks_serialize(
					'autodocs-publish/result',
					array(
						'status'  => 'success',
						'title'   => 'Publication verified',
						'content' => '<p>The documentation tree is ready to review.</p>',
						'meta'    => '12 pages',
					)
				),
		)
	);

	register_block_pattern(
		'autodocs-publish/api-request-example',
		array(
			'title'       => __( 'API request example', 'autodocs-publish-blocks' ),
			'description' => __( 'A structured API request and response followed by equivalent client examples.', 'autodocs-publish-blocks' ),
			'categories'  => array( 'autodocs-publish' ),
			'content'     => '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Make a request</h2><!-- /wp:heading -->'
				. autodocs_publish_blocks_serialize(
					'autodocs-publish/api-request',
					array(
						'method'         => 'POST',
						'endpoint'       => '/wp-json/wp/v2/pages',
						'headers'        => "Content-Type: application/json\nAuthorization: Bearer \$WP_ACCESS_TOKEN",
						'requestBody'    => "{\n  \"title\": \"Getting Started\",\n  \"status\": \"draft\"\n}",
						'requestBodyFormat' => 'json',
						'responseStatus' => '201 Created',
						'responseBody'   => "{\n  \"id\": 42,\n  \"slug\": \"getting-started\",\n  \"status\": \"draft\"\n}",
						'responseBodyFormat' => 'json',
					)
				)
				. autodocs_publish_blocks_serialize(
					'autodocs-publish/code-tabs',
					array(
						'tabs' => array(
							array( 'label' => 'cURL', 'language' => 'bash', 'filename' => 'Terminal', 'code' => "curl https://example.com/wp-json/wp/v2/pages \\\n  -H 'Authorization: Bearer \$WP_ACCESS_TOKEN'" ),
							array( 'label' => 'JavaScript', 'language' => 'javascript', 'filename' => 'request.js', 'code' => "const response = await fetch( '/wp-json/wp/v2/pages' );\nconst pages = await response.json();" ),
						),
					)
				),
		)
	);

	register_block_pattern(
		'autodocs-publish/runnable-api-console',
		array(
			'title'       => __( 'Runnable API console', 'autodocs-publish-blocks' ),
			'description' => __( 'A safe same-origin GET request readers can edit, run, reset, and copy as cURL.', 'autodocs-publish-blocks' ),
			'categories'  => array( 'autodocs-publish' ),
			'content'     => autodocs_publish_blocks_serialize(
				'autodocs-publish/api-request',
				array(
					'method'             => 'GET',
					'endpoint'           => '/wp-json/',
					'headers'            => 'Accept: application/json',
					'requestBody'        => '',
					'requestBodyFormat'  => 'json',
					'responseStatus'     => '200 OK',
					'responseBody'       => "{\n  \"name\": \"WordPress\",\n  \"namespaces\": [ \"wp/v2\" ]\n}",
					'responseBodyFormat' => 'json',
					'runnable'           => true,
					'editable'           => true,
					'allowUnsafe'        => false,
					'timeout'            => 10000,
				)
			),
		)
	);

	register_block_pattern(
		'autodocs-publish/api-reference-toolkit',
		array(
			'title'       => __( 'API reference toolkit', 'autodocs-publish-blocks' ),
			'description' => __( 'Typed fields, an annotated response diff, and a publishing sequence diagram.', 'autodocs-publish-blocks' ),
			'categories'  => array( 'autodocs-publish' ),
			'content'     => '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Request fields</h2><!-- /wp:heading -->'
				. autodocs_publish_blocks_serialize( 'autodocs-publish/fields' )
				. '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Response change</h2><!-- /wp:heading -->'
				. autodocs_publish_blocks_serialize(
					'autodocs-publish/colorful-code',
					array(
						'language'        => 'json',
						'filename'        => 'response.diff',
						'code'            => "@@ page @@\n-  \"status\": \"draft\"\n+  \"status\": \"publish\"",
						'diffMode'        => 'unified',
						'copyMode'        => 'final',
						'annotations'     => array(
							array(
								'line'    => 3,
								'content' => '<p>The final copied value omits the removed line and diff marker.</p>',
							),
						),
						'showLineNumbers' => true,
					)
				)
				. autodocs_publish_blocks_serialize(
					'autodocs-publish/diagram',
					array(
						'title'   => 'Request lifecycle',
						'type'    => 'sequence',
						'source'  => "Client -> WordPress: POST page\nWordPress -> Database: save draft\nWordPress -> Client: 201 Created",
						'caption' => 'A dependency-free sequence diagram generated from editable relationships.',
					)
				),
		)
	);

	register_block_pattern(
		'autodocs-publish/interactive-guide',
		array(
			'title'       => __( 'Interactive guide', 'autodocs-publish-blocks' ),
			'description' => __( 'A sandboxed browser example followed by a guided decision tree.', 'autodocs-publish-blocks' ),
			'categories'  => array( 'autodocs-publish' ),
			'content'     => autodocs_publish_blocks_serialize( 'autodocs-publish/code-playground' )
				. autodocs_publish_blocks_serialize( 'autodocs-publish/troubleshooter' ),
		)
	);

	register_block_pattern(
		'autodocs-publish/ai-prompt-example',
		array(
			'title'       => __( 'AI prompt example', 'autodocs-publish-blocks' ),
			'description' => __( 'A documented AI prompt with model, mode, context, and a concise expected result.', 'autodocs-publish-blocks' ),
			'categories'  => array( 'autodocs-publish' ),
			'content'     => '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Try this prompt</h2><!-- /wp:heading -->'
				. '<!-- wp:paragraph --><p>Use this prompt to review the implementation before proposing a change.</p><!-- /wp:paragraph -->'
				. autodocs_publish_blocks_serialize(
					'autodocs-publish/prompt',
					array(
						'prompt'   => "Use \$autodocs-publish-install to review the synchronization logic for failure modes.\n\nReturn a short risk list, then propose the smallest safe patch.",
						'model'    => 'GPT-5',
						'mode'     => 'code',
						'thinking' => true,
						'context'  => '$autodocs-publish-install, @repository, src/sync.js, test/sync.test.js',
						'caption'  => 'Synchronization review prompt',
					)
				)
				. autodocs_publish_blocks_serialize(
					'autodocs-publish/result',
					array(
						'status'  => 'neutral',
						'title'   => 'Expected output',
						'content' => '<p>A focused review with risks, a minimal patch, and matching tests.</p>',
						'meta'    => 'review checklist',
					)
				),
		)
	);
}
add_action( 'init', 'autodocs_publish_blocks_register_patterns', 20 );
