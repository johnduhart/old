<div id="wrapper" class="container_12">
	<div id="content" class="container_12">
		<div id="page">
			<div id="main" class="grid_8">
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<div class="post">
					<h2><?php the_title(); ?></h2>
					<em class="postMetaData">Posted under <?php the_category(', '); ?>.</em>

					<div class="entry">
						<?php the_content('Read complete article'); ?>
					</div>
				</div><!-- end div.post -->
				<?php endwhile; ?>
				<?php endif; ?>

			</div>

			<?php get_sidebar() ?>
			<div class="clear"></div>
		</div>
	</div>
</div>

<?php get_footer() ?>