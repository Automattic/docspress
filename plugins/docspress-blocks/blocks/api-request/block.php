<?php
/**
 * API Request / Response block registration and rendering.
 *
 * @package DocsPressBlocks
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
function docspress_blocks_api_payload( $label, $content, $class, $format = 'raw' ) {
	if ( '' === trim( $content ) ) {
		return '';
	}

	$format       = docspress_blocks_allowed_value( $format, array( 'headers', 'json', 'raw' ), 'raw' );
	$format_label = 'headers' === $format ? __( 'Key: value', 'docspress-blocks' ) : strtoupper( $format );
	$code         = esc_html( $content );

	if ( 'headers' === $format ) {
		$lines = preg_split( '/\r\n|\r|\n/', $content );
		$code  = '';
		foreach ( $lines as $line ) {
			if ( preg_match( '/^([^:]+)(:)(.*)$/', $line, $matches ) ) {
				$code .= '<span class="docspress-api__header-line"><strong class="docspress-api__header-name">' . esc_html( trim( $matches[1] ) ) . '<span aria-hidden="true">:</span></strong><span class="docspress-api__header-value">' . esc_html( $matches[3] ) . '</span></span>';
			} else {
				$code .= '<span class="docspress-api__header-line"><span class="docspress-api__header-value">' . esc_html( $line ) . '</span></span>';
			}
		}
	}

	return sprintf(
		'<section class="docspress-api__payload docspress-api__payload--%1$s" data-docspress-api-format="%2$s"><div class="docspress-api__payload-label"><span>%3$s</span><span class="docspress-api__format">%4$s</span></div><pre><code>%5$s</code></pre></section>',
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
function docspress_blocks_render_api_request( $attributes ) {
	wp_enqueue_script( 'docspress-blocks-view' );

	$method          = strtoupper( docspress_blocks_allowed_value( isset( $attributes['method'] ) ? $attributes['method'] : '', array( 'get', 'post', 'put', 'patch', 'delete' ), 'get' ) );
	$endpoint        = isset( $attributes['endpoint'] ) ? trim( docspress_blocks_decode_source( $attributes['endpoint'] ) ) : '/wp-json/wp/v2/pages';
	$headers         = isset( $attributes['headers'] ) ? docspress_blocks_decode_source( $attributes['headers'] ) : '';
	$request_body    = isset( $attributes['requestBody'] ) ? docspress_blocks_decode_source( $attributes['requestBody'] ) : '';
	$response_body   = isset( $attributes['responseBody'] ) ? docspress_blocks_decode_source( $attributes['responseBody'] ) : '';
	$request_format  = docspress_blocks_allowed_value( isset( $attributes['requestBodyFormat'] ) ? $attributes['requestBodyFormat'] : '', array( 'json', 'raw' ), 'json' );
	$response_format = docspress_blocks_allowed_value( isset( $attributes['responseBodyFormat'] ) ? $attributes['responseBodyFormat'] : '', array( 'json', 'raw' ), 'json' );
	$status          = isset( $attributes['responseStatus'] ) ? sanitize_text_field( $attributes['responseStatus'] ) : '200 OK';
	$runnable        = ! empty( $attributes['runnable'] );
	$editable        = ! isset( $attributes['editable'] ) || (bool) $attributes['editable'];
	$allow_unsafe    = ! empty( $attributes['allowUnsafe'] );
	$base_url        = isset( $attributes['baseUrl'] ) ? esc_url_raw( trim( (string) $attributes['baseUrl'] ) ) : '';
	$allowed_origins = isset( $attributes['allowedOrigins'] ) ? (string) $attributes['allowedOrigins'] : '';
	$timeout         = isset( $attributes['timeout'] ) ? max( 1000, min( 30000, (int) $attributes['timeout'] ) ) : 10000;
	$endpoint_id     = wp_unique_id( 'docspress-api-endpoint-' );
	$runner_id       = wp_unique_id( 'docspress-api-runner-' );
	$response_id     = wp_unique_id( 'docspress-api-response-' );
	$wrapper         = get_block_wrapper_attributes(
		array(
			'class'       => 'docspress-api' . ( $runnable ? ' is-runnable' : '' ),
			'data-method' => strtolower( $method ),
		)
	);

	if ( $runnable ) {
		wp_enqueue_script( 'docspress-api-request-view' );
	}

	ob_start();
	?>
	<figure <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<div class="docspress-api__request-line">
			<span class="docspress-api__eyebrow"><?php esc_html_e( 'Request', 'docspress-blocks' ); ?></span>
			<span class="docspress-api__method"><?php echo esc_html( $method ); ?></span>
			<code id="<?php echo esc_attr( $endpoint_id ); ?>" class="docspress-api__endpoint"><?php echo esc_html( $endpoint ); ?></code>
			<button class="docspress-api__copy" type="button" data-docspress-copy data-docspress-copy-target="<?php echo esc_attr( $endpoint_id ); ?>" aria-label="<?php esc_attr_e( 'Copy endpoint', 'docspress-blocks' ); ?>">
				<svg viewBox="0 0 20 20" aria-hidden="true"><rect x="7" y="7" width="9" height="9" rx="1.5"/><path d="M13 7V5.5A1.5 1.5 0 0 0 11.5 4h-7A1.5 1.5 0 0 0 3 5.5v7A1.5 1.5 0 0 0 4.5 14H7"/></svg>
				<b><?php esc_html_e( 'Copy URL', 'docspress-blocks' ); ?></b>
			</button>
		</div>
		<?php echo docspress_blocks_api_payload( __( 'Headers', 'docspress-blocks' ), $headers, 'headers', 'headers' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php echo docspress_blocks_api_payload( __( 'Request body', 'docspress-blocks' ), $request_body, 'request', $request_format ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php if ( $runnable ) : ?>
			<div id="<?php echo esc_attr( $runner_id ); ?>" class="docspress-api__runner" data-docspress-api-runner>
				<script type="application/json" class="docspress-api__config"><?php
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
				<div class="docspress-api__runner-heading">
					<div>
						<span class="docspress-api__eyebrow"><?php esc_html_e( 'Try it', 'docspress-blocks' ); ?></span>
						<strong><?php esc_html_e( 'Run this request', 'docspress-blocks' ); ?></strong>
					</div>
					<span class="docspress-api__runner-policy"><?php esc_html_e( 'Credentials omitted', 'docspress-blocks' ); ?></span>
				</div>
				<div class="docspress-api__runner-grid">
					<label class="docspress-api__field docspress-api__field--endpoint">
						<span><?php esc_html_e( 'Request URL', 'docspress-blocks' ); ?></span>
						<input type="text" value="<?php echo esc_attr( $endpoint ); ?>" data-docspress-api-endpoint-input<?php echo $editable ? '' : ' readonly'; ?> />
					</label>
					<label class="docspress-api__field">
						<span><?php esc_html_e( 'Headers', 'docspress-blocks' ); ?></span>
						<textarea rows="4" data-docspress-api-headers-input<?php echo $editable ? '' : ' readonly'; ?>><?php echo esc_textarea( $headers ); ?></textarea>
					</label>
					<label class="docspress-api__field">
						<span><?php esc_html_e( 'Body', 'docspress-blocks' ); ?></span>
						<textarea rows="4" data-docspress-api-body-input<?php echo $editable ? '' : ' readonly'; ?>><?php echo esc_textarea( $request_body ); ?></textarea>
					</label>
				</div>
				<div class="docspress-api__runner-actions">
					<button class="docspress-api__run" type="button" data-docspress-api-run>
						<span class="docspress-api__run-dot" aria-hidden="true"></span>
						<b><?php echo esc_html( sprintf( __( 'Run %s', 'docspress-blocks' ), $method ) ); ?></b>
					</button>
					<button class="docspress-api__runner-button" type="button" data-docspress-api-reset><?php esc_html_e( 'Reset', 'docspress-blocks' ); ?></button>
					<button class="docspress-api__runner-button" type="button" data-docspress-api-copy-curl><?php esc_html_e( 'Copy as cURL', 'docspress-blocks' ); ?></button>
					<span class="docspress-api__runner-status" data-docspress-api-runner-status role="status" aria-live="polite"><?php esc_html_e( 'Ready', 'docspress-blocks' ); ?></span>
				</div>
			</div>
		<?php endif; ?>
		<section class="docspress-api__response">
			<div class="docspress-api__response-line">
				<span class="docspress-api__eyebrow" data-docspress-api-response-label><?php echo esc_html( $runnable ? __( 'Example response', 'docspress-blocks' ) : __( 'Response', 'docspress-blocks' ) ); ?></span>
				<span class="docspress-api__response-meta" data-docspress-api-response-meta hidden></span>
				<span class="docspress-api__status" data-docspress-api-response-status><?php echo esc_html( $status ); ?></span>
			</div>
			<div data-docspress-api-response-headers hidden>
				<section class="docspress-api__payload docspress-api__payload--response-headers" data-docspress-api-format="headers">
					<div class="docspress-api__payload-label">
						<span><?php esc_html_e( 'Response headers', 'docspress-blocks' ); ?></span>
						<span class="docspress-api__format"><?php esc_html_e( 'Key: value', 'docspress-blocks' ); ?></span>
					</div>
					<pre><code data-docspress-api-response-header-code></code></pre>
				</section>
			</div>
			<div id="<?php echo esc_attr( $response_id ); ?>" data-docspress-api-response-body>
				<?php echo docspress_blocks_api_payload( __( 'Body', 'docspress-blocks' ), $response_body, 'response', $response_format ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</section>
	</figure>
	<?php
	return trim( ob_get_clean() );
}

/**
 * Register the API Request / Response block and its folder-owned assets.
 */
function docspress_blocks_register_api_request() {
	$block_url = DOCSPRESS_BLOCKS_URL . 'blocks/api-request/';

	wp_register_script( 'docspress-api-request-editor', $block_url . 'editor.js', array( 'wp-blocks', 'docspress-blocks-editor-shared' ), DOCSPRESS_BLOCKS_VERSION, true );
	wp_register_script( 'docspress-api-request-view', $block_url . 'view.js', array( 'docspress-blocks-view' ), DOCSPRESS_BLOCKS_VERSION, true );
	wp_register_style( 'docspress-api-request', $block_url . 'style.css', array(), DOCSPRESS_BLOCKS_VERSION );
	wp_register_style( 'docspress-api-request-editor-style', $block_url . 'editor.css', array( 'wp-edit-blocks', 'docspress-api-request' ), DOCSPRESS_BLOCKS_VERSION );

	register_block_type(
		'docspress/api-request',
		array(
			'api_version'     => 3,
			'editor_script'   => 'docspress-api-request-editor',
			'style'           => 'docspress-api-request',
			'editor_style'    => 'docspress-api-request-editor-style',
			'render_callback' => 'docspress_blocks_render_api_request',
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
			'supports'        => docspress_blocks_design_supports(),
		)
	);
}
add_action( 'init', 'docspress_blocks_register_api_request', 10 );
