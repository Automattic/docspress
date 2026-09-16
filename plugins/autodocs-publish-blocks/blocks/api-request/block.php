<?php
/**
 * API Request / Response block registration and rendering.
 *
 * @package AutodocsPublishBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render one API payload section.
 *
 * @param string $label   Section label.
 * @param string $content Plain-text payload.
 * @param string $class   Section modifier.
 * @param string $format  Payload format.
 * @return string
 */
function autodocs_publish_blocks_api_payload( $label, $content, $class, $format = 'raw' ) {
	if ( '' === trim( $content ) ) {
		return '';
	}

	$format       = autodocs_publish_blocks_allowed_value( $format, array( 'headers', 'json', 'raw' ), 'raw' );
	$format_label = 'headers' === $format ? __( 'Key: value', 'autodocs-publish-blocks' ) : strtoupper( $format );
	$code         = esc_html( $content );

	if ( 'headers' === $format ) {
		$lines = preg_split( '/\r\n|\r|\n/', $content );
		$code  = '';
		foreach ( $lines as $line ) {
			if ( preg_match( '/^([^:]+)(:)(.*)$/', $line, $matches ) ) {
				$code .= '<span class="autodocs-publish-api__header-line"><strong class="autodocs-publish-api__header-name">' . esc_html( trim( $matches[1] ) ) . '<span aria-hidden="true">:</span></strong><span class="autodocs-publish-api__header-value">' . esc_html( $matches[3] ) . '</span></span>';
			} else {
				$code .= '<span class="autodocs-publish-api__header-line"><span class="autodocs-publish-api__header-value">' . esc_html( $line ) . '</span></span>';
			}
		}
	}

	return sprintf(
		'<section class="autodocs-publish-api__payload autodocs-publish-api__payload--%1$s" data-autodocs-publish-api-format="%2$s"><div class="autodocs-publish-api__payload-label"><span>%3$s</span><span class="autodocs-publish-api__format">%4$s</span></div><pre><code>%5$s</code></pre></section>',
		esc_attr( $class ),
		esc_attr( $format ),
		esc_html( $label ),
		esc_html( $format_label ),
		$code
	);
}

/**
 * Render the API Request / Response block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function autodocs_publish_blocks_render_api_request( $attributes ) {
	wp_enqueue_script( 'autodocs-publish-blocks-view' );

	$method          = strtoupper( autodocs_publish_blocks_allowed_value( isset( $attributes['method'] ) ? $attributes['method'] : '', array( 'get', 'post', 'put', 'patch', 'delete' ), 'get' ) );
	$endpoint        = isset( $attributes['endpoint'] ) ? trim( autodocs_publish_blocks_decode_source( $attributes['endpoint'] ) ) : '/wp-json/wp/v2/pages';
	$headers         = isset( $attributes['headers'] ) ? autodocs_publish_blocks_decode_source( $attributes['headers'] ) : '';
	$request_body    = isset( $attributes['requestBody'] ) ? autodocs_publish_blocks_decode_source( $attributes['requestBody'] ) : '';
	$response_body   = isset( $attributes['responseBody'] ) ? autodocs_publish_blocks_decode_source( $attributes['responseBody'] ) : '';
	$request_format  = autodocs_publish_blocks_allowed_value( isset( $attributes['requestBodyFormat'] ) ? $attributes['requestBodyFormat'] : '', array( 'json', 'raw' ), 'json' );
	$response_format = autodocs_publish_blocks_allowed_value( isset( $attributes['responseBodyFormat'] ) ? $attributes['responseBodyFormat'] : '', array( 'json', 'raw' ), 'json' );
	$status          = isset( $attributes['responseStatus'] ) ? sanitize_text_field( $attributes['responseStatus'] ) : '200 OK';
	$runnable        = ! empty( $attributes['runnable'] );
	$editable        = ! isset( $attributes['editable'] ) || (bool) $attributes['editable'];
	$allow_unsafe    = ! empty( $attributes['allowUnsafe'] );
	$base_url        = isset( $attributes['baseUrl'] ) ? esc_url_raw( trim( (string) $attributes['baseUrl'] ) ) : '';
	$allowed_origins = isset( $attributes['allowedOrigins'] ) ? (string) $attributes['allowedOrigins'] : '';
	$timeout         = isset( $attributes['timeout'] ) ? max( 1000, min( 30000, (int) $attributes['timeout'] ) ) : 10000;
	$endpoint_id     = wp_unique_id( 'autodocs-publish-api-endpoint-' );
	$runner_id       = wp_unique_id( 'autodocs-publish-api-runner-' );
	$response_id     = wp_unique_id( 'autodocs-publish-api-response-' );
	$wrapper         = get_block_wrapper_attributes(
		array(
			'class'       => 'autodocs-publish-api' . ( $runnable ? ' is-runnable' : '' ),
			'data-method' => strtolower( $method ),
		)
	);

	if ( $runnable ) {
		wp_enqueue_script( 'autodocs-publish-api-request-view' );
	}

	ob_start();
	?>
	<figure <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<div class="autodocs-publish-api__request-line">
			<span class="autodocs-publish-api__eyebrow"><?php esc_html_e( 'Request', 'autodocs-publish-blocks' ); ?></span>
			<span class="autodocs-publish-api__method"><?php echo esc_html( $method ); ?></span>
			<code id="<?php echo esc_attr( $endpoint_id ); ?>" class="autodocs-publish-api__endpoint"><?php echo esc_html( $endpoint ); ?></code>
			<button class="autodocs-publish-api__copy" type="button" data-autodocs-publish-copy data-autodocs-publish-copy-target="<?php echo esc_attr( $endpoint_id ); ?>" aria-label="<?php esc_attr_e( 'Copy endpoint', 'autodocs-publish-blocks' ); ?>">
				<svg viewBox="0 0 20 20" aria-hidden="true"><rect x="7" y="7" width="9" height="9" rx="1.5"/><path d="M13 7V5.5A1.5 1.5 0 0 0 11.5 4h-7A1.5 1.5 0 0 0 3 5.5v7A1.5 1.5 0 0 0 4.5 14H7"/></svg>
				<b><?php esc_html_e( 'Copy URL', 'autodocs-publish-blocks' ); ?></b>
			</button>
		</div>
		<?php echo autodocs_publish_blocks_api_payload( __( 'Headers', 'autodocs-publish-blocks' ), $headers, 'headers', 'headers' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php echo autodocs_publish_blocks_api_payload( __( 'Request body', 'autodocs-publish-blocks' ), $request_body, 'request', $request_format ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php if ( $runnable ) : ?>
			<div id="<?php echo esc_attr( $runner_id ); ?>" class="autodocs-publish-api__runner" data-autodocs-publish-api-runner>
				<script type="application/json" class="autodocs-publish-api__config"><?php
					echo wp_json_encode(
						array(
							'method'         => $method,
							'endpoint'       => $endpoint,
							'baseUrl'        => $base_url,
							'headers'        => $headers,
							'body'           => $request_body,
							'allowedOrigins' => $allowed_origins,
							'timeout'        => $timeout,
							'allowUnsafe'    => $allow_unsafe,
							'editable'       => $editable,
						),
						JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
					);
				?></script>
				<div class="autodocs-publish-api__runner-heading">
					<div>
						<span class="autodocs-publish-api__eyebrow"><?php esc_html_e( 'Try it', 'autodocs-publish-blocks' ); ?></span>
						<strong><?php esc_html_e( 'Run this request', 'autodocs-publish-blocks' ); ?></strong>
					</div>
					<span class="autodocs-publish-api__runner-policy"><?php esc_html_e( 'Credentials omitted', 'autodocs-publish-blocks' ); ?></span>
				</div>
				<div class="autodocs-publish-api__runner-grid">
					<label class="autodocs-publish-api__field autodocs-publish-api__field--endpoint">
						<span><?php esc_html_e( 'Request URL', 'autodocs-publish-blocks' ); ?></span>
						<input type="text" value="<?php echo esc_attr( $endpoint ); ?>" data-autodocs-publish-api-endpoint-input<?php echo $editable ? '' : ' readonly'; ?> />
					</label>
					<label class="autodocs-publish-api__field">
						<span><?php esc_html_e( 'Headers', 'autodocs-publish-blocks' ); ?></span>
						<textarea rows="4" data-autodocs-publish-api-headers-input<?php echo $editable ? '' : ' readonly'; ?>><?php echo esc_textarea( $headers ); ?></textarea>
					</label>
					<label class="autodocs-publish-api__field">
						<span><?php esc_html_e( 'Body', 'autodocs-publish-blocks' ); ?></span>
						<textarea rows="4" data-autodocs-publish-api-body-input<?php echo $editable ? '' : ' readonly'; ?>><?php echo esc_textarea( $request_body ); ?></textarea>
					</label>
				</div>
				<div class="autodocs-publish-api__runner-actions">
					<button class="autodocs-publish-api__run" type="button" data-autodocs-publish-api-run>
						<span class="autodocs-publish-api__run-dot" aria-hidden="true"></span>
						<b><?php echo esc_html( sprintf( __( 'Run %s', 'autodocs-publish-blocks' ), $method ) ); ?></b>
					</button>
					<button class="autodocs-publish-api__runner-button" type="button" data-autodocs-publish-api-reset><?php esc_html_e( 'Reset', 'autodocs-publish-blocks' ); ?></button>
					<button class="autodocs-publish-api__runner-button" type="button" data-autodocs-publish-api-copy-curl><?php esc_html_e( 'Copy as cURL', 'autodocs-publish-blocks' ); ?></button>
					<span class="autodocs-publish-api__runner-status" data-autodocs-publish-api-runner-status role="status" aria-live="polite"><?php esc_html_e( 'Ready', 'autodocs-publish-blocks' ); ?></span>
				</div>
			</div>
		<?php endif; ?>
		<section class="autodocs-publish-api__response">
			<div class="autodocs-publish-api__response-line">
				<span class="autodocs-publish-api__eyebrow" data-autodocs-publish-api-response-label><?php echo esc_html( $runnable ? __( 'Example response', 'autodocs-publish-blocks' ) : __( 'Response', 'autodocs-publish-blocks' ) ); ?></span>
				<span class="autodocs-publish-api__response-meta" data-autodocs-publish-api-response-meta hidden></span>
				<span class="autodocs-publish-api__status" data-autodocs-publish-api-response-status><?php echo esc_html( $status ); ?></span>
			</div>
			<div data-autodocs-publish-api-response-headers hidden>
				<section class="autodocs-publish-api__payload autodocs-publish-api__payload--response-headers" data-autodocs-publish-api-format="headers">
					<div class="autodocs-publish-api__payload-label">
						<span><?php esc_html_e( 'Response headers', 'autodocs-publish-blocks' ); ?></span>
						<span class="autodocs-publish-api__format"><?php esc_html_e( 'Key: value', 'autodocs-publish-blocks' ); ?></span>
					</div>
					<pre><code data-autodocs-publish-api-response-header-code></code></pre>
				</section>
			</div>
			<div id="<?php echo esc_attr( $response_id ); ?>" data-autodocs-publish-api-response-body>
				<?php echo autodocs_publish_blocks_api_payload( __( 'Body', 'autodocs-publish-blocks' ), $response_body, 'response', $response_format ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</section>
	</figure>
	<?php
	return trim( ob_get_clean() );
}

/**
 * Register the API Request / Response block and its folder-owned assets.
 */
function autodocs_publish_blocks_register_api_request() {
	$block_url = AUTODOCS_PUBLISH_BLOCKS_URL . 'blocks/api-request/';

	wp_register_script( 'autodocs-publish-api-request-editor', $block_url . 'editor.js', array( 'wp-blocks', 'autodocs-publish-blocks-editor-shared' ), AUTODOCS_PUBLISH_BLOCKS_VERSION, true );
	wp_register_script( 'autodocs-publish-api-request-view', $block_url . 'view.js', array( 'autodocs-publish-blocks-view' ), AUTODOCS_PUBLISH_BLOCKS_VERSION, true );
	wp_register_style( 'autodocs-publish-api-request', $block_url . 'style.css', array(), AUTODOCS_PUBLISH_BLOCKS_VERSION );
	wp_register_style( 'autodocs-publish-api-request-editor-style', $block_url . 'editor.css', array( 'wp-edit-blocks', 'autodocs-publish-api-request' ), AUTODOCS_PUBLISH_BLOCKS_VERSION );

	autodocs_publish_blocks_register_block_type(
		'autodocs-publish/api-request',
		array(
			'api_version'     => 3,
			'editor_script'   => 'autodocs-publish-api-request-editor',
			'style'           => 'autodocs-publish-api-request',
			'editor_style'    => 'autodocs-publish-api-request-editor-style',
			'render_callback' => 'autodocs_publish_blocks_render_api_request',
			'attributes'      => array(
				'method'            => array( 'type' => 'string', 'default' => 'GET' ),
				'endpoint'          => array( 'type' => 'string', 'default' => '/wp-json/wp/v2/pages' ),
				'headers'           => array( 'type' => 'string', 'default' => "Accept: application/json\nAuthorization: Bearer \$WP_ACCESS_TOKEN" ),
				'requestBody'       => array( 'type' => 'string', 'default' => '' ),
				'requestBodyFormat' => array( 'type' => 'string', 'default' => 'json' ),
				'responseStatus'    => array( 'type' => 'string', 'default' => '200 OK' ),
				'responseBody'      => array( 'type' => 'string', 'default' => "{\n  \"id\": 42,\n  \"slug\": \"getting-started\"\n}" ),
				'responseBodyFormat' => array( 'type' => 'string', 'default' => 'json' ),
				'runnable'          => array( 'type' => 'boolean', 'default' => false ),
				'editable'          => array( 'type' => 'boolean', 'default' => true ),
				'allowUnsafe'       => array( 'type' => 'boolean', 'default' => false ),
				'baseUrl'           => array( 'type' => 'string', 'default' => '' ),
				'allowedOrigins'    => array( 'type' => 'string', 'default' => '' ),
				'timeout'           => array( 'type' => 'number', 'default' => 10000 ),
			),
			'supports'        => autodocs_publish_blocks_design_supports(),
		)
	);
}
add_action( 'init', 'autodocs_publish_blocks_register_api_request', 10 );
