<?php

add_theme_support('post-thumbnails', array('post'));
set_post_thumbnail_size(150, 100, true); // Normal post thumbnails
add_image_size('carousel-control', 50, 50, true);
add_image_size('carousel', 410, 300, true); // Carousel image size
add_image_size('smallish', 75, 75, true); // Right top posts image size
add_image_size('front-post', 230, 75, true); // Front posts under carousel image size
add_image_size('middle', 175, 75, true);

if (function_exists('register_sidebar')) {
	register_sidebar();
}

/*function new_excerpt_more($post) {
	return '<a href="'. get_permalink($post->ID) . '" class="more-link">' . 'More...' . '</a>';
}
add_filter('excerpt_more', 'new_excerpt_more');*/

if (function_exists('register_sidebar')) {
	register_sidebar(array(
		'name' => 'rightSidebar',
		'before_widget' => '<div class="module">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	));
}

if (!is_category()) {
	function new_excerpt_length($length)
	{
		return 25;
	}

	add_filter('excerpt_length', 'new_excerpt_length');
}

function kscope_comment($comment, $args, $depth)
{
	$GLOBALS['comment'] = $comment;
	if ($comment->comment_type == 'pingback'
		|| $comment->comment_type == 'trackback'
	) {
		return;
	}

	$avatarSize = ($depth == 1) ? 75 : 50;

	?>
<li id="li-comment-<?php comment_ID() ?>">
	<div class="comment" id="comment-<?php comment_ID() ?>">
		<?php echo get_avatar($comment, $avatarSize) ?>
		<div class="comment-meta">
			<span class="comment-name"><?php comment_author_link() ?></span>
			<span class="comment-date"><?php comment_date() ?> at <?php comment_time() ?></span>
		</div>

		<?php if ($comment->comment_approved == '0') : ?>
		<div class="comment-moderated">
			Your comment is currently waiting approval from one of our editors.
		</div>
		<?php endif; ?>

		<div class="comment-content">
			<?php comment_text() ?>
		</div>

		<div class="comment-reply">
			<?php edit_comment_link('Edit') ?>
			<?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
		</div>
	</div>
</li>
<?php
}

function kscope_comment_end($comment, $args, $depth)
{
	// Do nothing
}

// Add a default avatar to Settings > Discussion
function kscope_addgravatar($avatar_defaults)
{
	$myavatar = get_bloginfo('template_directory') . '/images/default.png';
	$avatar_defaults[$myavatar] = 'Kscope Default';

	return $avatar_defaults;
}

add_filter('avatar_defaults', 'kscope_addgravatar');