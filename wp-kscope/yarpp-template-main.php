<div id="related-posts">
	<h3>Related Posts</h3>
	<?php
	if ( !have_posts() ) {
		query_posts("orderby=rand&order=asc&posts_per_page=3");
	}

	rewind_posts();

	?>
	<ul class="related-posts">
		<?php while (have_posts()) : the_post(); ?>
		<li>
			<a href="<?php the_permalink() ?>"><?php the_post_thumbnail( array( 100, 100 ) ) ?></a>
			<a href="<?php the_permalink() ?>"><?php the_title() ?></a><br>
			<?php the_excerpt() ?>
		</li>
		<?php endwhile; ?>
	</ul>
</div>