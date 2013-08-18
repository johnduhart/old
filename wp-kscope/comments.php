<?php
/**
 * KScope comments form
 */
?>

<div id="comments">
	<?php if (post_password_required()): ?>
	<p class="nopassword">This post is password protected. Enter the password to view comments</p>
</div>
	<?php endif ?>
<?php /* hack */
if (false) { ?> <div id="comments"> <?php } ?>

<?php if (have_comments()): ?>
	<h3 id="comments-title">
		<?php comments_number('No Responses', 'One Response', '% Responses'); ?>
	</h3>

	<?php if ( get_comment_pages_count() > 1 ): ?>
	<div class="navigation">
		<div class="alignleft"><?php previous_comments_link() ?></div>
		<div class="alignright"><?php next_comments_link() ?></div>
	</div>
	<?php endif; ?>

	<ol id="commentlist">
		<?php wp_list_comments(array('callback' => 'kscope_comment', 'end-callback' => 'kscope_comment_end')) ?>
	</ol>

	<?php if ( get_comment_pages_count() > 1 ): ?>
	<div class="navigation">
		<div class="alignleft"><?php previous_comments_link() ?></div>
		<div class="alignright"><?php next_comments_link() ?></div>
	</div>
	<?php endif; ?>

	<?php endif ?>

<?php
$commenter = wp_get_current_commenter();

comment_form(array(
	'fields' => array(
		'author' => '<p class="comment-form-author">
		<label for="author">Name:</label>
		<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="45" aria-required="true" />
		</p>',
		'email' => '<p class="comment-form-email">
		<label for="email">Email</label>
			<input id="email" name="email" type="text" value="' . esc_attr($commenter['comment_author_email']) . '" size="45" aria-required="true" />
		</p>',

	),
	'comment_notes_after' => '',
)); ?>
</div>