<?php get_header(); ?>
<div id="wrapper" class="container_12">
	<div id="content" class="container_12">
		<div id="main" class="grid_8 author">
			<?php
			the_post();
			?>
			<div id="author-data">
				<div id="avatar">
					<?php echo get_avatar(get_the_author_meta('id'), 125); ?>
				</div>

				<h2><?php the_author() ?></h2>

				<h3>
					<?php echo get_cimyFieldValue(get_the_author_meta('id'), 'KSCOPE_POSITION') ?>
					<?php
					$year = get_cimyFieldValue(get_the_author_meta('id'), 'KSCOPE_YEAR');

					if (!empty($year)):
						?>
						- <em><?php echo $year ?></em>
						<?php endif; ?>
				</h3>

				<p>
					<?php
					$desc = get_the_author_meta('description');

					if (empty($desc)):
						?>
						<em>This author does not have a bio :(</em>
						<?php else: ?>
						<?php echo $desc ?>
						<?php endif; ?>
				</p>
			</div>

			<hr>

			<?php global $wp_query; $total_pages = $wp_query->max_num_pages; if ($total_pages > 1) { ?>
			<div id="nav-above" class="navigation">
				<div
					class="nav-previous"><?php next_posts_link(__('<span class="meta-nav">&laquo;</span> Older posts', 'your-theme')) ?></div>
				<div
					class="nav-next"><?php previous_posts_link(__('Newer posts <span class="meta-nav">&raquo;</span>', 'your-theme')) ?></div>
			</div>
			<?php } ?>

			<?php rewind_posts(); ?>
			<?php while (have_posts()) : the_post() ?>
			<?php if ($post->post_type == 'post'): ?>
				<div id="post-<?php the_ID(); ?>" class="post" <?php post_class(); ?>>
					<a href="<?php the_permalink(); ?>" class="post-list-thumbnail"><?php the_post_thumbnail(); ?></a>
					<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>

					<div class="meta">
						By <span class="author"><?php coauthors_links(); ?></span> <span
						class="timestamp"><?php echo get_the_date(); ?></span>
					</div>
					<div class="entry"><?php the_excerpt(); ?></div>
					<div class="clear"></div>
				</div>
				<?php endif; ?>
			<?php endwhile; ?>

			<?php global $wp_query; $total_pages = $wp_query->max_num_pages; if ($total_pages > 1) { ?>
			<div id="nav-below" class="navigation">
				<div
					class="nav-previous"><?php next_posts_link(__('<span class="meta-nav">&laquo;</span> Older posts', 'your-theme')) ?></div>
				<div
					class="nav-next"><?php previous_posts_link(__('Newer posts <span class="meta-nav">&raquo;</span>', 'your-theme')) ?></div>
			</div>
			<?php } ?>
		</div>

		<?php get_sidebar() ?>
		<div class="clear"></div>
	</div>
</div>
<?php get_footer() ?>