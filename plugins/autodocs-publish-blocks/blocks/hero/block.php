<?php
/**
 * Hero block registration and rendering.
 *
 * @package AutodocsPublishBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build an optional hero action.
 *
 * @param string $label  Action label.
 * @param string $url    Action URL.
 * @param string $class  Action class.
 * @param bool   $new_tab Whether the action opens in a new tab.
 * @return string
 */
function autodocs_publish_blocks_render_hero_action( $label, $url, $class, $new_tab ) {
	$label = wp_kses_post( $label );
	$url   = esc_url( $url );

	if ( ! $label || ! $url ) {
		return '';
	}

	return sprintf(
		'<a class="%1$s" href="%2$s"%3$s>%4$s</a>',
		esc_attr( $class ),
		$url,
		$new_tab ? ' target="_blank" rel="noopener noreferrer"' : '',
		$label
	);
}

/**
 * Render the HTML-first GitHub to WordPress workflow diagram.
 *
 * @param string $mascot_image Optional mascot image markup.
 * @return string
 */
function autodocs_publish_blocks_render_hero_sync_diagram( $mascot_image ) {
	$mascot = $mascot_image
		? $mascot_image
		: '<span class="autodocs-publish-hero-diagram__fallback" aria-hidden="true">DP</span>';

	return '<div class="autodocs-publish-hero-diagram" aria-label="GitHub Markdown flows through Autodocs Publish to WordPress Pages and machine-readable documentation. WordPress edits return to GitHub as reviewable pull requests.">'
		. '<div class="autodocs-publish-hero-diagram__pipeline">'
		. '<section class="autodocs-publish-hero-diagram__source" aria-label="GitHub Markdown source">'
		. '<div class="autodocs-publish-hero-diagram__bar"><span class="autodocs-publish-hero-diagram__dot"></span><strong>github.com/repo</strong></div>'
		. '<div class="autodocs-publish-hero-diagram__folder">docs/</div>'
		. '<span class="autodocs-publish-hero-diagram__file">index.md</span>'
		. '<span class="autodocs-publish-hero-diagram__file">quickstart.md</span>'
		. '<span class="autodocs-publish-hero-diagram__file">api.md</span>'
		. '</section>'
		. '<div class="autodocs-publish-hero-diagram__hub" aria-label="Autodocs Publish">'
		. '<div class="autodocs-publish-hero-diagram__mascot">' . $mascot . '</div>'
		. '<strong>Autodocs Publish</strong>'
		. '<span>keeps one source</span>'
		. '</div>'
		. '<div class="autodocs-publish-hero-diagram__outputs" aria-label="Published documentation surfaces">'
		. '<section class="autodocs-publish-hero-diagram__surface autodocs-publish-hero-diagram__surface--wordpress">'
		. '<span class="autodocs-publish-hero-diagram__surface-label">For readers</span>'
		. '<strong>WordPress Pages</strong>'
		. '<span>Native Gutenberg</span>'
		. '</section>'
		. '<section class="autodocs-publish-hero-diagram__surface autodocs-publish-hero-diagram__surface--agents">'
		. '<span class="autodocs-publish-hero-diagram__surface-label">For agents</span>'
		. '<strong>/llms.txt</strong>'
		. '<span>Exact page.md routes</span>'
		. '</section>'
		. '</div>'
		. '</div>'
		. '<div class="autodocs-publish-hero-diagram__return"><span aria-hidden="true">↑</span><strong>Reviewable pull request</strong><span>WordPress edits return to GitHub</span></div>'
		. '</div>';
}

/**
 * Render the Hero block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function autodocs_publish_blocks_render_hero( $attributes ) {
	$eyebrow       = isset( $attributes['eyebrow'] ) ? wp_kses_post( $attributes['eyebrow'] ) : '';
	$title         = isset( $attributes['title'] ) ? wp_kses_post( $attributes['title'] ) : '';
	$description   = isset( $attributes['description'] ) ? wp_kses_post( $attributes['description'] ) : '';
	$primary_label = isset( $attributes['primaryLabel'] ) ? $attributes['primaryLabel'] : '';
	$primary_url   = isset( $attributes['primaryUrl'] ) ? $attributes['primaryUrl'] : '';
	$secondary_label = isset( $attributes['secondaryLabel'] ) ? $attributes['secondaryLabel'] : '';
	$secondary_url = isset( $attributes['secondaryUrl'] ) ? $attributes['secondaryUrl'] : '';
	$media_id      = isset( $attributes['mediaId'] ) ? absint( $attributes['mediaId'] ) : 0;
	$media_url     = isset( $attributes['mediaUrl'] ) ? esc_url( $attributes['mediaUrl'] ) : '';
	$media_alt     = isset( $attributes['mediaAlt'] ) ? sanitize_text_field( $attributes['mediaAlt'] ) : '';
	$visual_label  = isset( $attributes['visualLabel'] ) ? sanitize_text_field( $attributes['visualLabel'] ) : '';
	$visual_variant = autodocs_publish_blocks_allowed_value( isset( $attributes['visualVariant'] ) ? $attributes['visualVariant'] : '', array( 'image', 'sync-diagram' ), 'image' );
	$layout         = autodocs_publish_blocks_allowed_value( isset( $attributes['layout'] ) ? $attributes['layout'] : '', array( 'split', 'editorial' ), 'split' );
	$media_position = autodocs_publish_blocks_allowed_value( isset( $attributes['mediaPosition'] ) ? $attributes['mediaPosition'] : '', array( 'left', 'right' ), 'right' );
	$height         = autodocs_publish_blocks_allowed_value( isset( $attributes['height'] ) ? $attributes['height'] : '', array( 'compact', 'standard', 'tall' ), 'standard' );
	$tone           = autodocs_publish_blocks_allowed_value( isset( $attributes['tone'] ) ? $attributes['tone'] : '', array( 'theme', 'midnight', 'paper', 'brand' ), 'theme' );
	$text_align     = autodocs_publish_blocks_allowed_value( isset( $attributes['textAlign'] ) ? $attributes['textAlign'] : '', array( 'left', 'center' ), 'left' );
	$media_width    = isset( $attributes['mediaWidth'] ) ? min( 58, max( 34, absint( $attributes['mediaWidth'] ) ) ) : 44;
	$image_scale    = isset( $attributes['imageScale'] ) ? min( 120, max( 60, absint( $attributes['imageScale'] ) ) ) : 100;
	$show_grid      = ! empty( $attributes['showGrid'] );
	$show_orbit     = ! empty( $attributes['showOrbit'] );
	$primary_new_tab = ! empty( $attributes['primaryNewTab'] );
	$secondary_new_tab = ! empty( $attributes['secondaryNewTab'] );
	$has_visual     = 'sync-diagram' === $visual_variant || (bool) ( $media_url || $media_id );
	$classes        = array(
		'autodocs-publish-hero',
		'autodocs-publish-hero--' . $tone,
		'autodocs-publish-hero--visual-' . $visual_variant,
		'autodocs-publish-hero--layout-' . $layout,
		'autodocs-publish-hero--media-' . $media_position,
		'autodocs-publish-hero--height-' . $height,
		'autodocs-publish-hero--align-' . $text_align,
	);
	$styles         = array(
		'--adpb-hero-media-width:' . $media_width . '%',
		'--adpb-hero-image-scale:' . $image_scale . '%',
	);

	if ( ! $show_grid ) {
		$classes[] = 'autodocs-publish-hero--no-grid';
	}
	if ( ! $show_orbit ) {
		$classes[] = 'autodocs-publish-hero--no-orbit';
	}
	if ( ! $has_visual ) {
		$classes[] = 'autodocs-publish-hero--no-visual';
	}

	$custom_colors = array(
		'panelColor'  => array( '--adpb-hero-panel', 'autodocs-publish-hero--has-panel-color' ),
		'visualColor' => array( '--adpb-hero-visual', 'autodocs-publish-hero--has-visual-color' ),
		'accentColor' => array( '--adpb-hero-accent', 'autodocs-publish-hero--has-accent-color' ),
	);
	foreach ( $custom_colors as $attribute_name => $color_config ) {
		$color = isset( $attributes[ $attribute_name ] ) ? sanitize_hex_color( $attributes[ $attribute_name ] ) : '';
		if ( $color ) {
			$styles[]  = $color_config[0] . ':' . $color;
			$classes[] = $color_config[1];
		}
	}

	$primary_action   = autodocs_publish_blocks_render_hero_action( $primary_label, $primary_url, 'autodocs-publish-hero__button autodocs-publish-hero__button--primary wp-element-button', $primary_new_tab );
	$secondary_action = autodocs_publish_blocks_render_hero_action( $secondary_label, $secondary_url, 'autodocs-publish-hero__button autodocs-publish-hero__button--secondary wp-element-button', $secondary_new_tab );
	$image             = '';
	if ( $media_id ) {
		$image = wp_get_attachment_image(
			$media_id,
			'full',
			false,
			array(
				'class'         => 'autodocs-publish-hero__image',
				'alt'           => $media_alt,
				'loading'       => 'eager',
				'decoding'      => 'async',
				'fetchpriority' => 'high',
			)
		);
	}
	if ( ! $image && $media_url ) {
		$image = sprintf(
			'<img class="autodocs-publish-hero__image" src="%1$s" alt="%2$s" loading="eager" decoding="async" fetchpriority="high">',
			$media_url,
			esc_attr( $media_alt )
		);
	}
	$visual = 'sync-diagram' === $visual_variant
		? autodocs_publish_blocks_render_hero_sync_diagram( $image )
		: ( $image ? '<figure class="autodocs-publish-hero__media">' . $image . '</figure>' : '' );

	ob_start();
	?>
	<section <?php echo get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ), 'style' => implode( ';', $styles ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<div class="autodocs-publish-hero__copy">
			<?php if ( $eyebrow ) : ?><p class="autodocs-publish-hero__eyebrow"><?php echo $eyebrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p><?php endif; ?>
			<?php if ( $title ) : ?><h1 class="autodocs-publish-hero__title"><?php echo $title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1><?php endif; ?>
			<?php if ( $description ) : ?><p class="autodocs-publish-hero__description"><?php echo $description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p><?php endif; ?>
			<?php if ( $primary_action || $secondary_action ) : ?>
				<div class="autodocs-publish-hero__actions"><?php echo $primary_action . $secondary_action; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php endif; ?>
		</div>
		<?php if ( $has_visual ) : ?>
			<div class="autodocs-publish-hero__visual">
				<?php if ( $visual_label ) : ?><span class="autodocs-publish-hero__visual-label" aria-hidden="true"><?php echo esc_html( $visual_label ); ?></span><?php endif; ?>
				<?php echo $visual; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php endif; ?>
	</section>
	<?php
	return trim( ob_get_clean() );
}

/**
 * Register the Hero block and its folder-owned assets.
 */
function autodocs_publish_blocks_register_hero() {
	$block_url = AUTODOCS_PUBLISH_BLOCKS_URL . 'blocks/hero/';

	wp_register_script(
		'autodocs-publish-hero-editor',
		$block_url . 'editor.js',
		array( 'wp-blocks', 'autodocs-publish-blocks-editor-shared' ),
		AUTODOCS_PUBLISH_BLOCKS_VERSION,
		true
	);
	wp_register_style( 'autodocs-publish-hero', $block_url . 'style.css', array(), AUTODOCS_PUBLISH_BLOCKS_VERSION );
	wp_register_style( 'autodocs-publish-hero-editor-style', $block_url . 'editor.css', array( 'wp-edit-blocks', 'autodocs-publish-hero' ), AUTODOCS_PUBLISH_BLOCKS_VERSION );

	autodocs_publish_blocks_register_block_type(
		'autodocs-publish/hero',
		array(
			'api_version'     => 3,
			'editor_script'   => 'autodocs-publish-hero-editor',
			'style'           => 'autodocs-publish-hero',
			'editor_style'    => 'autodocs-publish-hero-editor-style',
			'render_callback' => 'autodocs_publish_blocks_render_hero',
			'attributes'      => array(
				'eyebrow'       => array( 'type' => 'string', 'default' => 'Documentation, publishing, and community' ),
				'title'         => array( 'type' => 'string', 'default' => 'Docs that stay connected to your GitHub repo' ),
				'description'   => array( 'type' => 'string', 'default' => 'Write beside your code. Publish a WordPress experience that guides every reader to the docs written for them.' ),
				'primaryLabel'  => array( 'type' => 'string', 'default' => 'Browse documentation' ),
				'primaryUrl'    => array( 'type' => 'string', 'default' => '' ),
				'primaryNewTab' => array( 'type' => 'boolean', 'default' => false ),
				'secondaryLabel' => array( 'type' => 'string', 'default' => 'Latest updates' ),
				'secondaryUrl'  => array( 'type' => 'string', 'default' => '' ),
				'secondaryNewTab' => array( 'type' => 'boolean', 'default' => false ),
				'mediaId'       => array( 'type' => 'number', 'default' => 0 ),
				'mediaUrl'      => array( 'type' => 'string', 'default' => '' ),
				'mediaAlt'      => array( 'type' => 'string', 'default' => '' ),
				'visualLabel'   => array( 'type' => 'string', 'default' => '' ),
				'visualVariant' => array( 'type' => 'string', 'default' => 'image' ),
				'layout'        => array( 'type' => 'string', 'default' => 'split' ),
				'mediaPosition' => array( 'type' => 'string', 'default' => 'right' ),
				'mediaWidth'    => array( 'type' => 'number', 'default' => 44 ),
				'imageScale'    => array( 'type' => 'number', 'default' => 100 ),
				'height'        => array( 'type' => 'string', 'default' => 'standard' ),
				'tone'          => array( 'type' => 'string', 'default' => 'theme' ),
				'textAlign'     => array( 'type' => 'string', 'default' => 'left' ),
				'showGrid'      => array( 'type' => 'boolean', 'default' => false ),
				'showOrbit'     => array( 'type' => 'boolean', 'default' => false ),
				'panelColor'    => array( 'type' => 'string', 'default' => '' ),
				'visualColor'   => array( 'type' => 'string', 'default' => '' ),
				'accentColor'   => array( 'type' => 'string', 'default' => '' ),
			),
			'supports'        => autodocs_publish_blocks_design_supports( array( 'wide', 'full' ) ),
		)
	);
}
add_action( 'init', 'autodocs_publish_blocks_register_hero', 10 );
