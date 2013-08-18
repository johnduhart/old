<?php

get_header(); ?>
<div id="wrapper" class="container_12">
	<div id="content" class="container_12">
		<div class="grid_6">
			<div id="featured-carousel">
				<ul id="carousel">
					<?php
					$featured = new WP_Query();
					$featured->query('posts_per_page=7&cat=54');
					while ($featured->have_posts()) : $featured->the_post();
						?>
						<li class="post">
							<div class="featured-image">
								<a href="<?php the_permalink() ?>">
									<?php
									the_post_thumbnail('carousel');
									?></a></div>

							<h1><a href="<?php the_permalink() ?>">
								<?php the_title(); ?></a></h1>

							<div class="tease"><?php the_excerpt(); ?></div>
						</li>
						<?php endwhile; ?>
				</ul>
			</div>
			<ul id="featured-controls">
				<?php
				$featured = new WP_Query();
				$featured->query('posts_per_page=7&cat=54');
				$i = 1;
				while ($featured->have_posts()): $featured->the_post();
					?>
					<li id="featured-control-<?php echo $i; ?>"<?php if ($i == 1): ?>class="selected"<?php endif; ?>>
						<?php the_post_thumbnail('carousel-control'); ?>
					</li>

					<?php $i++; ?>
					<?php endwhile; ?>
				<div class="clear"></div>
			</ul>
			<!-- end carousel -->

			<div id="top-items">
				<h4>Popular posts</h4>
				<?php echo do_shortcode('[wpp range=weekly stats_comments=0 stats_views=true]') ?>
			</div>
		</div>
	</div>
	<div class="grid_6">
		<div class="grid_6" id="infobox">
			<?php if (wm_day_letter() !== null): ?>
			<div id="timebox">
				<?php echo date('l F jS', current_time('timestamp')) ?>
				<div id="day-letter">
					<?php if (wm_day_letter() !== null): ?><?php echo wm_day_letter() ?><?php else: ?>
					&nbsp;<?php endif ?>
				</div>
				Day
			</div>
			<div id="weatherbox">
				<?php echo do_shortcode('[forecast]') ?>
			</div>
			<?php else: ?>
			<?php echo do_shortcode('[forecast numdays="5"]') ?>
			<?php endif ?>
		</div>
		<div class="clear"></div>
		<div class="grid_6 front-posts">
			<div id="twitter-box">
				<h4>Kaleidoscope Twitter</h4>
				<?php if ( function_exists('aktt_sidebar_tweets') ) aktt_sidebar_tweets(2) ?>
			</div>

			<h1>Latest News</h1>
			<?php
			$leftNews = new WP_Query();
			$leftNews->query('cat=-54&showposts=6');
			while ($leftNews->have_posts()) : $leftNews->the_post();
				?>

				<div id="post-<?php the_ID(); ?>" class="post" <?php post_class(); ?>>
					<a href="<?php the_permalink(); ?>"
					   class="post-list-thumbnail"><?php the_post_thumbnail('front-post'); ?></a>
					<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>

					<div class="meta">
						By <span class="author"><?php coauthors(', ', '& '); ?></span> <span
						class="timestamp"><?php echo get_the_date(); ?></span>
					</div>
					<div class="entry"><?php the_excerpt(); ?></div>
					<div class="clear"></div>
				</div>

				<?php endwhile; ?>
		</div>
	</div>
</div>
<div class="clear"></div>
</div>
</div>
<script type="text/javascript">

	function featured_initCallback(carousel) {
		// Disable autoscrolling if the user clicks the prev or next button.
		carousel.buttonNext.bind('click', function () {
			carousel.startAuto(0);
		});

		carousel.buttonPrev.bind('click', function () {
			carousel.startAuto(0);
		});

		// Pause autoscrolling if the user moves with the cursor over the clip.
		carousel.clip.hover(function () {
			carousel.stopAuto();
		}, function () {
			carousel.startAuto();
		});

		jQuery('#featured-next').bind('click', function () {
			carousel.next();
			return false;
		});

		jQuery('#featured-prev').bind('click', function () {
			carousel.prev();
			return false;
		});

		jQuery('#featured-controls li').bind('click', function () {
			var st = jQuery(this).attr('id').split('-')[2];
			carousel.scroll(jQuery.jcarousel.intval(st));
			return false;
		});


	}

	function selectNextControl(carousel, control, enabled) {
		$('#featured-controls li.selected').removeClass('selected');
		$('#featured-control-' + carousel.first).addClass('selected');
	}

	function selectPrevControl(carousel, control, enabled) {
		$('#featured-controls li.selected').removeClass('selected');
		$('#featured-control-' + carousel.first).addClass('selected');
	}

	function featured_itemVisibleInCallback(carousel, item, i, state, evt) {
		$('#featured-controls li.selected').removeClass('selected');
		$('#featured-control-' + i).addClass('selected');
	}


	jQuery(document).ready(function () {
		jQuery('#carousel').jcarousel({
			scroll:1, //auto: 2
			wrap:'last',
			vertical:false,
			auto:6,
			initCallback:featured_initCallback,
			itemVisibleInCallback:{onAfterAnimation:featured_itemVisibleInCallback}
		});
	});

</script>
<?php get_footer() ?>