<?php if (have_posts()) : ?>

<?php global $wp_query;
	$total_pages = $wp_query->max_num_pages;
	if ($total_pages > 1) { ?>
	<div id="nav-above" class="navigation">
		<div
			class="nav-previous"><?php next_posts_link(__('<span class="meta-nav">&laquo;</span> Older posts', 'your-theme')) ?></div>
		<div
			class="nav-next"><?php previous_posts_link(__('Newer posts <span class="meta-nav">&raquo;</span>', 'your-theme')) ?></div>
	</div>
	<?php } ?>

<?php while (have_posts()) : the_post() ?>
	<?php if ($post->post_type == 'post'): ?>
		<div id="post-<?php the_ID(); ?>" class="post" <?php post_class(); ?>>
			<a href="<?php the_permalink(); ?>"
			   class="post-list-thumbnail"><?php the_post_thumbnail(); ?></a>
			<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>

			<div class="meta">
				By <span class="author"><?php coauthors(); ?></span> <span
				class="timestamp"><?php echo get_the_date(); ?></span>
			</div>
			<div class="entry"><?php the_excerpt(); ?></div>
			<div class="clear"></div>
		</div>
		<?php endif; ?>
	<?php endwhile; ?>

<?php global $wp_query;
	$total_pages = $wp_query->max_num_pages;
	if ($total_pages > 1) { ?>
	<div id="nav-below" class="navigation">
		<div
			class="nav-previous"><?php next_posts_link(__('<span class="meta-nav">&laquo;</span> Older posts', 'your-theme')) ?></div>
		<div
			class="nav-next"><?php previous_posts_link(__('Newer posts <span class="meta-nav">&raquo;</span>', 'your-theme')) ?></div>
	</div>
	<?php } ?>

<?php else : ?>
<p><?php _e('Sorry, no results', 'your-theme') ?></p>
<?php endif; ?>