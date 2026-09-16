<?php
/**
 * Interactive Troubleshooter block registration and rendering.
 *
 * @package AutodocsPublishBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Starter decision tree.
 *
 * @return array<string,array<int,array<string,mixed>>>
 */
function autodocs_publish_blocks_troubleshooter_defaults() {
	return array(
		'questions' => array(
			array(
				'id'       => 'source',
				'question' => 'Do you already have Markdown documentation?',
				'yesLabel' => 'Yes, the docs exist',
				'yesNext'  => 'connected',
				'noLabel'  => 'Not yet',
				'noNext'   => 'generate',
			),
			array(
				'id'       => 'connected',
				'question' => 'Is the repository connected to WordPress?',
				'yesLabel' => 'Yes, it is connected',
				'yesNext'  => 'sync',
				'noLabel'  => 'No, connect it',
				'noNext'   => 'install',
			),
		),
		'outcomes'  => array(
			array(
				'id'      => 'install',
				'status'  => 'warning',
				'title'   => 'Connect the publishing target',
				'content' => '<p>Run the Autodocs Publish installer, add the WordPress access token, and verify the repository connection before publishing.</p>',
			),
			array(
				'id'      => 'sync',
				'status'  => 'success',
				'title'   => 'Publish the documentation',
				'content' => '<p>Run the sync command, review the proposed changes, and verify the rendered documentation on WordPress.</p>',
			),
			array(
				'id'      => 'generate',
				'status'  => 'neutral',
				'title'   => 'Generate a documentation starter',
				'content' => '<p>Generate a small documentation tree from the source, then review every example against the implementation before publishing.</p>',
			),
		),
	);
}

/**
 * Normalize a decision-tree ID.
 *
 * @param mixed  $value    Candidate ID.
 * @param string $fallback Fallback ID.
 * @return string
 */
function autodocs_publish_blocks_troubleshooter_id( $value, $fallback ) {
	$id = sanitize_key( (string) $value );
	return $id ? $id : $fallback;
}

/**
 * Normalize questions and guarantee unique IDs.
 *
 * @param mixed $questions Candidate questions.
 * @return array<int,array<string,string>>
 */
function autodocs_publish_blocks_normalize_troubleshooter_questions( $questions ) {
	$defaults  = autodocs_publish_blocks_troubleshooter_defaults();
	$source    = is_array( $questions ) ? array_slice( $questions, 0, 12 ) : $defaults['questions'];
	$seen      = array();
	$normalized = array();

	foreach ( $source as $index => $question ) {
		if ( ! is_array( $question ) ) {
			continue;
		}

		$id = autodocs_publish_blocks_troubleshooter_id( isset( $question['id'] ) ? $question['id'] : '', 'question-' . ( $index + 1 ) );
		if ( isset( $seen[ $id ] ) ) {
			$id .= '-' . ( $index + 1 );
		}
		$seen[ $id ] = true;

		$normalized[] = array(
			'id'       => $id,
			'question' => isset( $question['question'] ) ? sanitize_text_field( $question['question'] ) : '',
			'yesLabel' => isset( $question['yesLabel'] ) ? sanitize_text_field( $question['yesLabel'] ) : 'Yes',
			'yesNext'  => autodocs_publish_blocks_troubleshooter_id( isset( $question['yesNext'] ) ? $question['yesNext'] : '', '' ),
			'noLabel'  => isset( $question['noLabel'] ) ? sanitize_text_field( $question['noLabel'] ) : 'No',
			'noNext'   => autodocs_publish_blocks_troubleshooter_id( isset( $question['noNext'] ) ? $question['noNext'] : '', '' ),
		);
	}

	return $normalized ? $normalized : $defaults['questions'];
}

/**
 * Normalize outcomes and guarantee unique IDs.
 *
 * @param mixed $outcomes Candidate outcomes.
 * @return array<int,array<string,string>>
 */
function autodocs_publish_blocks_normalize_troubleshooter_outcomes( $outcomes ) {
	$defaults   = autodocs_publish_blocks_troubleshooter_defaults();
	$source     = is_array( $outcomes ) ? array_slice( $outcomes, 0, 12 ) : $defaults['outcomes'];
	$statuses   = array( 'success', 'neutral', 'warning', 'error' );
	$seen       = array();
	$normalized = array();

	foreach ( $source as $index => $outcome ) {
		if ( ! is_array( $outcome ) ) {
			continue;
		}

		$id = autodocs_publish_blocks_troubleshooter_id( isset( $outcome['id'] ) ? $outcome['id'] : '', 'outcome-' . ( $index + 1 ) );
		if ( isset( $seen[ $id ] ) ) {
			$id .= '-' . ( $index + 1 );
		}
		$seen[ $id ] = true;

		$normalized[] = array(
			'id'      => $id,
			'status'  => autodocs_publish_blocks_allowed_value( isset( $outcome['status'] ) ? $outcome['status'] : '', $statuses, 'neutral' ),
			'title'   => isset( $outcome['title'] ) ? sanitize_text_field( $outcome['title'] ) : '',
			'content' => isset( $outcome['content'] ) ? wp_kses_post( $outcome['content'] ) : '',
		);
	}

	return $normalized ? $normalized : $defaults['outcomes'];
}

/**
 * Render the Interactive Troubleshooter block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function autodocs_publish_blocks_render_troubleshooter( $attributes ) {
	$title         = isset( $attributes['title'] ) ? sanitize_text_field( $attributes['title'] ) : 'Find the next step';
	$intro         = isset( $attributes['intro'] ) ? wp_kses_post( $attributes['intro'] ) : '';
	$questions     = autodocs_publish_blocks_normalize_troubleshooter_questions( isset( $attributes['questions'] ) ? $attributes['questions'] : null );
	$outcomes      = autodocs_publish_blocks_normalize_troubleshooter_outcomes( isset( $attributes['outcomes'] ) ? $attributes['outcomes'] : null );
	$show_progress = ! isset( $attributes['showProgress'] ) || (bool) $attributes['showProgress'];
	$question_ids  = wp_list_pluck( $questions, 'id' );
	$start_id      = autodocs_publish_blocks_troubleshooter_id( isset( $attributes['startId'] ) ? $attributes['startId'] : '', $questions[0]['id'] );

	if ( ! in_array( $start_id, $question_ids, true ) ) {
		$start_id = $questions[0]['id'];
	}

	wp_enqueue_script( 'autodocs-publish-troubleshooter-view' );

	$wrapper = get_block_wrapper_attributes(
		array(
			'class' => 'autodocs-publish-troubleshooter',
		)
	);

	ob_start();
	?>
	<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-autodocs-publish-troubleshooter data-start-id="<?php echo esc_attr( $start_id ); ?>">
		<header class="autodocs-publish-troubleshooter__header">
			<div>
				<span class="autodocs-publish-troubleshooter__eyebrow"><?php esc_html_e( 'Guided diagnosis', 'autodocs-publish-blocks' ); ?></span>
				<h3><?php echo esc_html( $title ); ?></h3>
				<?php if ( $intro ) : ?>
					<div class="autodocs-publish-troubleshooter__intro"><?php echo $intro; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php endif; ?>
			</div>
			<span class="autodocs-publish-troubleshooter__route" aria-hidden="true">
				<span></span><span></span><span></span>
			</span>
		</header>
		<?php if ( $show_progress ) : ?>
			<div class="autodocs-publish-troubleshooter__progress">
				<span class="autodocs-publish-troubleshooter__progress-bar" aria-hidden="true"><i data-autodocs-publish-troubleshooter-progress-bar></i></span>
				<span class="autodocs-publish-troubleshooter__progress-label" data-autodocs-publish-troubleshooter-progress aria-live="polite"><?php esc_html_e( 'Question 1', 'autodocs-publish-blocks' ); ?></span>
			</div>
		<?php endif; ?>
		<div class="autodocs-publish-troubleshooter__stage">
			<?php foreach ( $questions as $index => $question ) : ?>
				<section class="autodocs-publish-troubleshooter__question" data-autodocs-publish-troubleshooter-question="<?php echo esc_attr( $question['id'] ); ?>"<?php echo $question['id'] === $start_id ? '' : ' hidden'; ?>>
					<span class="autodocs-publish-troubleshooter__step"><?php echo esc_html( sprintf( __( 'Question %d', 'autodocs-publish-blocks' ), $index + 1 ) ); ?></span>
					<h4 tabindex="-1"><?php echo esc_html( $question['question'] ); ?></h4>
					<div class="autodocs-publish-troubleshooter__choices">
						<button type="button" class="autodocs-publish-troubleshooter__choice is-primary" data-autodocs-publish-troubleshooter-next="<?php echo esc_attr( $question['yesNext'] ); ?>">
							<span><?php echo esc_html( $question['yesLabel'] ); ?></span><svg viewBox="0 0 20 20" aria-hidden="true"><path d="m7 4 6 6-6 6"/></svg>
						</button>
						<button type="button" class="autodocs-publish-troubleshooter__choice" data-autodocs-publish-troubleshooter-next="<?php echo esc_attr( $question['noNext'] ); ?>">
							<span><?php echo esc_html( $question['noLabel'] ); ?></span><svg viewBox="0 0 20 20" aria-hidden="true"><path d="m7 4 6 6-6 6"/></svg>
						</button>
					</div>
				</section>
			<?php endforeach; ?>
			<?php foreach ( $outcomes as $outcome ) : ?>
				<section class="autodocs-publish-troubleshooter__outcome is-<?php echo esc_attr( $outcome['status'] ); ?>" data-autodocs-publish-troubleshooter-outcome="<?php echo esc_attr( $outcome['id'] ); ?>" hidden>
					<span class="autodocs-publish-troubleshooter__outcome-icon" aria-hidden="true"></span>
					<div>
						<span class="autodocs-publish-troubleshooter__step"><?php esc_html_e( 'Recommended next step', 'autodocs-publish-blocks' ); ?></span>
						<h4 tabindex="-1"><?php echo esc_html( $outcome['title'] ); ?></h4>
						<div class="autodocs-publish-troubleshooter__outcome-copy"><?php echo $outcome['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					</div>
				</section>
			<?php endforeach; ?>
			<section class="autodocs-publish-troubleshooter__outcome is-error" data-autodocs-publish-troubleshooter-invalid hidden>
				<span class="autodocs-publish-troubleshooter__outcome-icon" aria-hidden="true"></span>
				<div>
					<span class="autodocs-publish-troubleshooter__step"><?php esc_html_e( 'Route unavailable', 'autodocs-publish-blocks' ); ?></span>
					<h4 tabindex="-1"><?php esc_html_e( 'This answer needs a valid destination.', 'autodocs-publish-blocks' ); ?></h4>
					<p><?php esc_html_e( 'Edit the block and connect this answer to another question or outcome.', 'autodocs-publish-blocks' ); ?></p>
				</div>
			</section>
		</div>
		<footer class="autodocs-publish-troubleshooter__footer">
			<button type="button" data-autodocs-publish-troubleshooter-back disabled><?php esc_html_e( 'Back', 'autodocs-publish-blocks' ); ?></button>
			<button type="button" data-autodocs-publish-troubleshooter-restart><?php esc_html_e( 'Start over', 'autodocs-publish-blocks' ); ?></button>
		</footer>
	</section>
	<?php
	return trim( ob_get_clean() );
}

/**
 * Register the Interactive Troubleshooter block.
 */
function autodocs_publish_blocks_register_troubleshooter() {
	$block_url = AUTODOCS_PUBLISH_BLOCKS_URL . 'blocks/troubleshooter/';
	$defaults  = autodocs_publish_blocks_troubleshooter_defaults();

	wp_register_script( 'autodocs-publish-troubleshooter-editor', $block_url . 'editor.js', array( 'wp-blocks', 'autodocs-publish-blocks-editor-shared' ), AUTODOCS_PUBLISH_BLOCKS_VERSION, true );
	wp_register_script( 'autodocs-publish-troubleshooter-view', $block_url . 'view.js', array(), AUTODOCS_PUBLISH_BLOCKS_VERSION, true );
	wp_register_style( 'autodocs-publish-troubleshooter', $block_url . 'style.css', array(), AUTODOCS_PUBLISH_BLOCKS_VERSION );
	wp_register_style( 'autodocs-publish-troubleshooter-editor-style', $block_url . 'editor.css', array( 'wp-edit-blocks', 'autodocs-publish-troubleshooter' ), AUTODOCS_PUBLISH_BLOCKS_VERSION );

	wp_add_inline_script( 'autodocs-publish-troubleshooter-editor', 'window.autodocsPublishTroubleshooterDefaults = ' . wp_json_encode( $defaults ) . ';', 'before' );

	autodocs_publish_blocks_register_block_type(
		'autodocs-publish/troubleshooter',
		array(
			'api_version'     => 3,
			'editor_script'   => 'autodocs-publish-troubleshooter-editor',
			'style'           => 'autodocs-publish-troubleshooter',
			'editor_style'    => 'autodocs-publish-troubleshooter-editor-style',
			'render_callback' => 'autodocs_publish_blocks_render_troubleshooter',
			'attributes'      => array(
				'title'        => array( 'type' => 'string', 'default' => 'Find the next step' ),
				'intro'        => array( 'type' => 'string', 'default' => 'Answer two quick questions to get the right Autodocs Publish workflow.' ),
				'startId'      => array( 'type' => 'string', 'default' => 'source' ),
				'questions'    => array( 'type' => 'array', 'default' => $defaults['questions'] ),
				'outcomes'     => array( 'type' => 'array', 'default' => $defaults['outcomes'] ),
				'showProgress' => array( 'type' => 'boolean', 'default' => true ),
			),
			'supports'        => autodocs_publish_blocks_design_supports( array( 'wide' ) ),
		)
	);
}
add_action( 'init', 'autodocs_publish_blocks_register_troubleshooter', 10 );
