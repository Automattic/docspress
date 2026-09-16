<?php
/**
 * Shared code rendering helpers.
 *
 * @package AutodocsPublishBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return a value only when it is in the allow-list.
 *
 * @param string $value    Candidate value.
 * @param array  $allowed  Allowed values.
 * @param string $fallback Fallback value.
 * @return string
 */
function autodocs_publish_blocks_allowed_value( $value, $allowed, $fallback ) {
	$value = sanitize_key( (string) $value );
	return in_array( $value, $allowed, true ) ? $value : $fallback;
}

/**
 * Restore HTML-sensitive source characters after Gutenberg-safe serialization.
 *
 * Autodocs Publish escapes these characters inside block-comment JSON so Markdown and
 * HTML parsers cannot terminate the comment early. Some WordPress parser paths
 * retain the entity or Unicode escape at the dynamic-render boundary.
 *
 * @param mixed $value Serialized source value.
 * @return string
 */
function autodocs_publish_blocks_decode_source( $value ) {
	$value = html_entity_decode( (string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	return str_ireplace(
		array( '\\u0026', '\\u003c', '\\u003e' ),
		array( '&', '<', '>' ),
		$value
	);
}

/**
 * Parse a human-friendly list such as 2,4-6 into line numbers.
 *
 * @param string $value Line expression.
 * @return array
 */
function autodocs_publish_blocks_highlighted_lines( $value ) {
	$lines = array();
	foreach ( explode( ',', (string) $value ) as $part ) {
		$part = trim( $part );
		if ( preg_match( '/^(\d{1,4})$/', $part, $match ) ) {
			$lines[ (int) $match[1] ] = true;
			continue;
		}

		if ( preg_match( '/^(\d{1,4})-(\d{1,4})$/', $part, $match ) ) {
			$start = min( (int) $match[1], (int) $match[2] );
			$end   = max( (int) $match[1], (int) $match[2] );
			$end   = min( $end, $start + 100 );
			for ( $line = $start; $line <= $end; $line++ ) {
				$lines[ $line ] = true;
			}
		}
	}

	return $lines;
}

/**
 * Build the reusable code surface used by code and code-tab blocks.
 *
 * @param array $attributes Block attributes.
 * @param bool  $show_header Whether to render the filename/language bar.
 * @return string
 */
function autodocs_publish_blocks_code_surface( $attributes, $show_header = true ) {
	$language          = autodocs_publish_blocks_allowed_value(
		isset( $attributes['language'] ) ? $attributes['language'] : '',
		array( 'bash', 'css', 'html', 'javascript', 'json', 'jsx', 'markdown', 'php', 'plaintext', 'python', 'shell', 'sql', 'tsx', 'typescript', 'yaml' ),
		'plaintext'
	);
	$filename          = isset( $attributes['filename'] ) ? sanitize_text_field( $attributes['filename'] ) : '';
	$code              = isset( $attributes['code'] ) ? autodocs_publish_blocks_decode_source( $attributes['code'] ) : '';
	$highlighted       = autodocs_publish_blocks_highlighted_lines( isset( $attributes['highlightedLines'] ) ? $attributes['highlightedLines'] : '' );
	$show_line_numbers = ! isset( $attributes['showLineNumbers'] ) || (bool) $attributes['showLineNumbers'];
	$diff_mode         = autodocs_publish_blocks_allowed_value(
		isset( $attributes['diffMode'] ) ? $attributes['diffMode'] : '',
		array( 'none', 'unified' ),
		'none'
	);
	$copy_mode         = autodocs_publish_blocks_allowed_value(
		isset( $attributes['copyMode'] ) ? $attributes['copyMode'] : '',
		array( 'all', 'final' ),
		'all'
	);
	$raw_annotations   = isset( $attributes['annotations'] ) && is_array( $attributes['annotations'] ) ? array_slice( $attributes['annotations'], 0, 20 ) : array();
	$annotations       = array();
	foreach ( $raw_annotations as $annotation_index => $annotation ) {
		$line_number = isset( $annotation['line'] ) ? max( 1, min( 9999, (int) $annotation['line'] ) ) : 1;
		$content     = isset( $annotation['content'] ) ? wp_kses_post( (string) $annotation['content'] ) : '';
		if ( '' === trim( wp_strip_all_tags( $content ) ) ) {
			continue;
		}
		$annotations[ $line_number ][] = array(
			'index'   => $annotation_index + 1,
			'content' => $content,
		);
	}
	$lines             = preg_split( '/\r\n|\r|\n/', $code );
	$classes           = 'autodocs-publish-code__surface';

	if ( $show_line_numbers ) {
		$classes .= ' has-line-numbers';
	}
	if ( 'unified' === $diff_mode ) {
		$classes .= ' is-diff';
	}
	$surface_id = wp_unique_id( 'autodocs-publish-code-surface-' );

	ob_start();
	?>
	<div id="<?php echo esc_attr( $surface_id ); ?>" class="<?php echo esc_attr( $classes ); ?>" data-language="<?php echo esc_attr( $language ); ?>" data-copy-mode="<?php echo esc_attr( $copy_mode ); ?>">
		<?php if ( $show_header ) : ?>
			<div class="autodocs-publish-code__bar">
				<span class="autodocs-publish-code__language"><?php echo esc_html( $language ); ?></span>
				<span class="autodocs-publish-code__filename"><?php echo esc_html( $filename ? $filename : $language ); ?></span>
				<button class="autodocs-publish-code__copy" type="button" data-autodocs-publish-copy aria-label="<?php esc_attr_e( 'Copy code', 'autodocs-publish-blocks' ); ?>">
					<span aria-hidden="true">⧉</span><b><?php esc_html_e( 'Copy', 'autodocs-publish-blocks' ); ?></b>
				</button>
			</div>
		<?php endif; ?>
		<pre class="autodocs-publish-code__pre" tabindex="0"><code><?php
		foreach ( $lines as $index => $line ) :
			$number       = $index + 1;
			$line_classes = 'autodocs-publish-code__line';
			if ( isset( $highlighted[ $number ] ) ) {
				$line_classes .= ' is-highlighted';
			}
			if ( 'unified' === $diff_mode ) {
				if ( 0 === strpos( $line, '+' ) && 0 !== strpos( $line, '+++' ) ) {
					$line_classes .= ' is-diff-added';
				} elseif ( 0 === strpos( $line, '-' ) && 0 !== strpos( $line, '---' ) ) {
					$line_classes .= ' is-diff-removed';
				} elseif ( 0 === strpos( $line, '@@' ) ) {
					$line_classes .= ' is-diff-meta';
				}
			}
			?><span class="<?php echo esc_attr( $line_classes ); ?>" data-line="<?php echo esc_attr( (string) $number ); ?>"><span class="autodocs-publish-code__line-content"><?php echo esc_html( $line ); ?></span><?php
			if ( isset( $annotations[ $number ] ) ) :
				foreach ( $annotations[ $number ] as $annotation ) :
					$annotation_id = $surface_id . '-annotation-' . $annotation['index'];
					?><button class="autodocs-publish-code__annotation-marker" type="button" data-autodocs-publish-code-annotation aria-expanded="false" aria-controls="<?php echo esc_attr( $annotation_id ); ?>"><span class="screen-reader-text"><?php echo esc_html( sprintf( __( 'Show annotation for line %d', 'autodocs-publish-blocks' ), $number ) ); ?></span><?php echo esc_html( (string) $annotation['index'] ); ?></button><?php
				endforeach;
			endif;
			?></span><?php
		endforeach;
		?></code></pre>
		<?php if ( $annotations ) : ?>
			<ol class="autodocs-publish-code__annotations" aria-label="<?php esc_attr_e( 'Code annotations', 'autodocs-publish-blocks' ); ?>">
				<?php foreach ( $annotations as $line_number => $line_annotations ) : ?>
					<?php foreach ( $line_annotations as $annotation ) : ?>
						<li id="<?php echo esc_attr( $surface_id . '-annotation-' . $annotation['index'] ); ?>" class="autodocs-publish-code__annotation" data-autodocs-publish-code-annotation-panel hidden>
							<span class="autodocs-publish-code__annotation-number" aria-hidden="true"><?php echo esc_html( (string) $annotation['index'] ); ?></span>
							<div>
								<strong><?php echo esc_html( sprintf( __( 'Line %d', 'autodocs-publish-blocks' ), $line_number ) ); ?></strong>
								<div class="autodocs-publish-code__annotation-content"><?php echo $annotation['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
							</div>
						</li>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</div>
	<?php
	return trim( ob_get_clean() );
}
