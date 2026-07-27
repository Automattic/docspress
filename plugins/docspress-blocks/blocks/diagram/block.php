<?php
/**
 * Diagram block registration and rendering.
 *
 * @package DocsPressBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Parse the compact diagram source.
 *
 * One relationship per line: Source -> Target: optional label.
 *
 * @param string $source Diagram source.
 * @return array
 */
function docspress_blocks_parse_diagram( $source ) {
	$actors = array();
	$edges  = array();
	$source = docspress_blocks_decode_source( $source );
	$lines  = preg_split( '/\r\n|\r|\n/', $source );

	foreach ( array_slice( $lines, 0, 30 ) as $line ) {
		$line = trim( $line );
		if ( '' === $line || 0 === strpos( $line, '#' ) ) {
			continue;
		}
		if ( ! preg_match( '/^(.+?)\s*(?:-->|->)\s*(.+?)(?:\s*:\s*(.+))?$/u', $line, $matches ) ) {
			continue;
		}
		$from  = sanitize_text_field( trim( $matches[1] ) );
		$to    = sanitize_text_field( trim( $matches[2] ) );
		$label = isset( $matches[3] ) ? sanitize_text_field( trim( $matches[3] ) ) : '';
		if ( '' === $from || '' === $to ) {
			continue;
		}
		foreach ( array( $from, $to ) as $actor ) {
			if ( ! in_array( $actor, $actors, true ) && count( $actors ) < 8 ) {
				$actors[] = $actor;
			}
		}
		if ( in_array( $from, $actors, true ) && in_array( $to, $actors, true ) ) {
			$edges[] = array(
				'from'  => $from,
				'to'    => $to,
				'label' => $label,
			);
		}
	}

	return array(
		'actors' => $actors,
		'edges'  => array_slice( $edges, 0, 24 ),
	);
}

/**
 * Truncate an SVG label while retaining the full value in accessible text.
 *
 * @param string $label Label.
 * @param int    $limit Limit.
 * @return string
 */
function docspress_blocks_diagram_label( $label, $limit = 22 ) {
	if ( function_exists( 'mb_strlen' ) && mb_strlen( $label ) > $limit ) {
		return mb_substr( $label, 0, $limit - 1 ) . '…';
	}
	if ( strlen( $label ) > $limit ) {
		return substr( $label, 0, $limit - 1 ) . '…';
	}
	return $label;
}

/**
 * Render a flow diagram SVG.
 *
 * @param array  $diagram Parsed diagram.
 * @param string $marker_id Arrow marker ID.
 * @return string
 */
function docspress_blocks_render_flow_diagram( $diagram, $marker_id ) {
	$count  = max( 1, count( $diagram['actors'] ) );
	$width  = max( 520, 80 + ( $count * 170 ) );
	$height = 220;
	$index  = array_flip( $diagram['actors'] );

	ob_start();
	?>
	<svg class="docspress-diagram__svg" viewBox="0 0 <?php echo esc_attr( (string) $width ); ?> <?php echo esc_attr( (string) $height ); ?>" role="img" aria-hidden="true">
		<defs><marker id="<?php echo esc_attr( $marker_id ); ?>" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M 0 0 L 10 5 L 0 10 z"/></marker></defs>
		<g class="docspress-diagram__edges">
			<?php foreach ( $diagram['edges'] as $edge ) : ?>
				<?php
				$from_index = $index[ $edge['from'] ];
				$to_index   = $index[ $edge['to'] ];
				$x1         = 60 + ( $from_index * 170 ) + 130;
				$x2         = 60 + ( $to_index * 170 );
				$reverse    = $to_index < $from_index;
				if ( $reverse ) {
					$x1 = 60 + ( $from_index * 170 );
					$x2 = 60 + ( $to_index * 170 ) + 130;
				}
				$mid = ( $x1 + $x2 ) / 2;
				?>
				<path d="M <?php echo esc_attr( (string) $x1 ); ?> 110 C <?php echo esc_attr( (string) $mid ); ?> <?php echo $reverse ? '165' : '70'; ?>, <?php echo esc_attr( (string) $mid ); ?> <?php echo $reverse ? '165' : '70'; ?>, <?php echo esc_attr( (string) $x2 ); ?> 110" marker-end="url(#<?php echo esc_attr( $marker_id ); ?>)"/>
				<?php if ( $edge['label'] ) : ?>
					<text x="<?php echo esc_attr( (string) $mid ); ?>" y="<?php echo esc_attr( $reverse ? '171' : '65' ); ?>" text-anchor="middle"><?php echo esc_html( docspress_blocks_diagram_label( $edge['label'], 26 ) ); ?></text>
				<?php endif; ?>
			<?php endforeach; ?>
		</g>
		<g class="docspress-diagram__nodes">
			<?php foreach ( $diagram['actors'] as $actor_index => $actor ) : ?>
				<?php $x = 60 + ( $actor_index * 170 ); ?>
				<g transform="translate(<?php echo esc_attr( (string) $x ); ?> 82)">
					<title><?php echo esc_html( $actor ); ?></title>
					<rect width="130" height="56" rx="8"/>
					<text x="65" y="33" text-anchor="middle"><?php echo esc_html( docspress_blocks_diagram_label( $actor ) ); ?></text>
				</g>
			<?php endforeach; ?>
		</g>
	</svg>
	<?php
	return trim( ob_get_clean() );
}

/**
 * Render a sequence diagram SVG.
 *
 * @param array  $diagram Parsed diagram.
 * @param string $marker_id Arrow marker ID.
 * @return string
 */
function docspress_blocks_render_sequence_diagram( $diagram, $marker_id ) {
	$count  = max( 1, count( $diagram['actors'] ) );
	$width  = max( 520, 120 + ( $count * 180 ) );
	$height = max( 260, 150 + ( count( $diagram['edges'] ) * 58 ) );
	$index  = array_flip( $diagram['actors'] );

	ob_start();
	?>
	<svg class="docspress-diagram__svg" viewBox="0 0 <?php echo esc_attr( (string) $width ); ?> <?php echo esc_attr( (string) $height ); ?>" role="img" aria-hidden="true">
		<defs><marker id="<?php echo esc_attr( $marker_id ); ?>" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M 0 0 L 10 5 L 0 10 z"/></marker></defs>
		<g class="docspress-diagram__lanes">
			<?php foreach ( $diagram['actors'] as $actor_index => $actor ) : ?>
				<?php $x = 100 + ( $actor_index * 180 ); ?>
				<line x1="<?php echo esc_attr( (string) $x ); ?>" y1="66" x2="<?php echo esc_attr( (string) $x ); ?>" y2="<?php echo esc_attr( (string) ( $height - 30 ) ); ?>"/>
				<g transform="translate(<?php echo esc_attr( (string) ( $x - 65 ) ); ?> 18)">
					<title><?php echo esc_html( $actor ); ?></title>
					<rect width="130" height="48" rx="8"/>
					<text x="65" y="29" text-anchor="middle"><?php echo esc_html( docspress_blocks_diagram_label( $actor ) ); ?></text>
				</g>
			<?php endforeach; ?>
		</g>
		<g class="docspress-diagram__messages">
			<?php foreach ( $diagram['edges'] as $message_index => $edge ) : ?>
				<?php
				$y  = 112 + ( $message_index * 58 );
				$x1 = 100 + ( $index[ $edge['from'] ] * 180 );
				$x2 = 100 + ( $index[ $edge['to'] ] * 180 );
				?>
				<line x1="<?php echo esc_attr( (string) $x1 ); ?>" y1="<?php echo esc_attr( (string) $y ); ?>" x2="<?php echo esc_attr( (string) $x2 ); ?>" y2="<?php echo esc_attr( (string) $y ); ?>" marker-end="url(#<?php echo esc_attr( $marker_id ); ?>)"/>
				<text x="<?php echo esc_attr( (string) ( ( $x1 + $x2 ) / 2 ) ); ?>" y="<?php echo esc_attr( (string) ( $y - 9 ) ); ?>" text-anchor="middle"><?php echo esc_html( docspress_blocks_diagram_label( $edge['label'] ? $edge['label'] : $edge['from'] . ' to ' . $edge['to'], 30 ) ); ?></text>
			<?php endforeach; ?>
		</g>
	</svg>
	<?php
	return trim( ob_get_clean() );
}

/**
 * Render the Diagram block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function docspress_blocks_render_diagram( $attributes ) {
	$title       = isset( $attributes['title'] ) ? sanitize_text_field( $attributes['title'] ) : 'Publishing flow';
	$type        = docspress_blocks_allowed_value( isset( $attributes['type'] ) ? $attributes['type'] : '', array( 'flow', 'sequence' ), 'flow' );
	$source      = isset( $attributes['source'] ) ? (string) $attributes['source'] : '';
	$caption     = isset( $attributes['caption'] ) ? wp_kses_post( $attributes['caption'] ) : '';
	$diagram     = docspress_blocks_parse_diagram( $source );
	$marker_id   = wp_unique_id( 'docspress-diagram-arrow-' );
	$description = implode(
		'. ',
		array_map(
			static function ( $edge ) {
				return $edge['from'] . ' to ' . $edge['to'] . ( $edge['label'] ? ': ' . $edge['label'] : '' );
			},
			$diagram['edges']
		)
	);
	$wrapper     = get_block_wrapper_attributes( array( 'class' => 'docspress-diagram is-' . $type ) );

	ob_start();
	?>
	<figure <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<div class="docspress-diagram__header">
			<div>
				<span class="docspress-diagram__eyebrow"><?php echo esc_html( 'sequence' === $type ? __( 'Sequence', 'docspress-blocks' ) : __( 'Flow', 'docspress-blocks' ) ); ?></span>
				<h3><?php echo esc_html( $title ); ?></h3>
			</div>
			<span><?php echo esc_html( sprintf( _n( '%d relationship', '%d relationships', count( $diagram['edges'] ), 'docspress-blocks' ), count( $diagram['edges'] ) ) ); ?></span>
		</div>
		<div class="docspress-diagram__canvas" role="img" aria-label="<?php echo esc_attr( $title . '. ' . $description ); ?>">
			<?php
			echo 'sequence' === $type
				? docspress_blocks_render_sequence_diagram( $diagram, $marker_id ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				: docspress_blocks_render_flow_diagram( $diagram, $marker_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div>
		<?php if ( $caption ) : ?>
			<figcaption><?php echo $caption; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></figcaption>
		<?php endif; ?>
	</figure>
	<?php
	return trim( ob_get_clean() );
}

/**
 * Register the Diagram block.
 */
function docspress_blocks_register_diagram() {
	$block_url = DOCSPRESS_BLOCKS_URL . 'blocks/diagram/';

	wp_register_script( 'docspress-diagram-editor', $block_url . 'editor.js', array( 'wp-blocks', 'docspress-blocks-editor-shared' ), DOCSPRESS_BLOCKS_VERSION, true );
	wp_register_style( 'docspress-diagram', $block_url . 'style.css', array(), DOCSPRESS_BLOCKS_VERSION );
	wp_register_style( 'docspress-diagram-editor-style', $block_url . 'editor.css', array( 'wp-edit-blocks', 'docspress-diagram' ), DOCSPRESS_BLOCKS_VERSION );

	register_block_type(
		'docspress/diagram',
		array(
			'api_version'     => 3,
			'editor_script'   => 'docspress-diagram-editor',
			'style'           => 'docspress-diagram',
			'editor_style'    => 'docspress-diagram-editor-style',
			'render_callback' => 'docspress_blocks_render_diagram',
			'attributes'      => array(
				'title'   => array( 'type' => 'string', 'default' => 'Publishing flow' ),
				'type'    => array( 'type' => 'string', 'default' => 'flow' ),
				'source'  => array( 'type' => 'string', 'default' => "Markdown -> DocsPress: collect\nDocsPress -> WordPress: publish\nWordPress -> Reader: serve" ),
				'caption' => array( 'type' => 'string', 'default' => '' ),
			),
			'supports'        => docspress_blocks_design_supports( array( 'wide' ) ),
		)
	);
}
add_action( 'init', 'docspress_blocks_register_diagram', 10 );
