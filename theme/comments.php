<?php
/**
 * Native WordPress discussion template.
 *
 * @package DocsPress
 */

if ( post_password_required() ) {
	return;
}

$comment_count      = get_comments_number();
$discussion_title   = get_theme_mod( 'docspress_discussion_title', __( 'Discussion', 'docspress' ) );
$show_comment_count = get_theme_mod( 'docspress_show_comment_count', true );
$show_avatars       = get_theme_mod( 'docspress_show_comment_avatars', true ) && get_option( 'show_avatars' );
$avatar_size        = min( 72, max( 24, absint( get_theme_mod( 'docspress_comment_avatar_size', 44 ) ) ) );
?>
<section id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<header class="comments-header">
			<span class="comments-eyebrow"><?php esc_html_e( 'Community', 'docspress' ); ?></span>
			<h2 class="comments-title" data-customize-discussion-title>
				<?php echo esc_html( $discussion_title ); ?>
				<?php if ( $show_comment_count ) : ?>
					<span class="comments-count"><?php echo esc_html( number_format_i18n( $comment_count ) ); ?></span>
				<?php endif; ?>
			</h2>
		</header>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'avatar_size' => $show_avatars ? $avatar_size : 0,
					'short_ping'  => true,
					'style'       => 'ol',
				)
			);
			?>
		</ol>

		<?php
		the_comments_pagination(
			array(
				'prev_text' => __( '← Older comments', 'docspress' ),
				'next_text' => __( 'Newer comments →', 'docspress' ),
			)
		);
		?>
	<?php endif; ?>

	<?php if ( ! comments_open() && $comment_count ) : ?>
		<?php $closed_message = get_theme_mod( 'docspress_comments_closed_message', __( 'This discussion is closed, but the existing replies remain available.', 'docspress' ) ); ?>
		<?php if ( $closed_message ) : ?>
			<p class="comments-closed"><?php echo esc_html( $closed_message ); ?></p>
		<?php endif; ?>
	<?php endif; ?>

	<?php
	if ( comments_open() ) {
		comment_form(
			array(
				'class_container'      => 'comment-respond-shell',
				'class_form'           => 'comment-form',
				'title_reply'          => get_theme_mod( 'docspress_comment_form_title', __( 'Join the discussion', 'docspress' ) ),
				'title_reply_before'   => '<h2 id="reply-title" class="comment-reply-title" data-customize-comment-form-title>',
				'title_reply_after'    => '</h2>',
				'cancel_reply_before'  => '<span class="cancel-reply">',
				'cancel_reply_after'   => '</span>',
				'label_submit'         => __( 'Post comment', 'docspress' ),
				'class_submit'         => 'submit',
				'comment_notes_before' => '<p class="comment-notes">' . esc_html__( 'Your email address will not be published. Required fields are marked.', 'docspress' ) . '</p>',
			)
		);
	}
	?>
</section>
