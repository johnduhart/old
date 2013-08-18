<?php get_header() ?>
<div id="wrapper" class="container_12">
	<div id="content" class="container_12">
		<div id="page">
			<div id="main" class="grid_8">
				<?php if (have_posts()) : the_post(); ?>
				<div class="post single">
					<h2><?php the_title(); ?></h2>
					<!-- comment test, meta class is for info like author and date by aaron lifton -->
					<div class="post-data">
						<!--<div class="social-media">
							<a href="https://twitter.com/share" class="twitter-share-button" data-via="wmkaleidoscope">Tweet</a>
							<?php /*sfc_like_button(array('showfaces' => 'false', 'width' => '110')) */?>
							<g:plusone size="medium"></g:plusone>
						</div>-->
						<a href="<?php echo get_author_posts_url(get_the_author_meta('id')) ?>"><?php echo get_avatar(get_the_author_meta('ID'), 75) ?></a>

						<div class="meta">By <?php coauthors_posts_links(); ?></div>
						<?php the_date(); ?> &#8211; <em class="postMetaData">Posted under <?php the_category(', '); ?></em>

						<div class="social-buttons">
							<?php sfc_like_button(array('showfaces' => 'false', 'width' => '90')) ?>
							<g:plusone size="medium"></g:plusone>
							<a href="https://twitter.com/share" class="twitter-share-button" data-via="wmkaleidoscope">Tweet</a>
						</div>

						<div class="clear"></div>
					</div>
					<div class="entry">
						<?php the_content('Read complete article'); ?>
						<div class="social-buttons">
							<?php sfc_like_button(array('showfaces' => 'false', 'width' => '90')) ?>
							<g:plusone size="medium"></g:plusone>
							<a href="https://twitter.com/share" class="twitter-share-button" data-via="wmkaleidoscope">Tweet</a>
						</div>
						<?php
						$rawTags = wp_get_post_tags( $post->ID );
						$tags = array();
						foreach ($rawTags as $tag) {
							$tags[$tag->slug] = $tag;
						}
						?>
						<?php
						foreach($tags as $tag) {
							if (substr($tag->name, -7) == 'Edition') { ?>
								<div class="tag-june2012">
									<img src="<?php bloginfo('stylesheet_directory'); ?>/images/newspaper.png"
									     alt="Newspaper">
									This article is from our <a href="<?php echo get_tag_link( $tag->term_id ) ?>"><?php echo $tag->name ?></a>.
								</div>
								<?php
								break;
							}
						}
						?>
					</div>
					<!--<div class="share-article">
						<h3>Share</h3>
						<?php /*sfc_like_button(array('showfaces' => 'false', 'width' => '55', 'layout' => 'box_count')) */?>
						<a href="https://twitter.com/share" class="twitter-share-button" data-via="wmkaleidoscope" data-count="vertical">Tweet</a>
					</div>-->
					<?php
					$realPost = $GLOBALS['post'];
					$realQuery = $GLOBALS['wp_query'];
					related_posts();
					$GLOBALS['post'] = $realPost;
					$GLOBALS['wp_query'] = $realQuery;
					?>
					<div class="comments">

						<?php comments_template('', true); ?>
					</div>
				</div><!-- end div.post -->
				<?php endif; ?>

			</div>

			<?php get_sidebar() ?>
			<div class="clear"></div>
		</div>
	</div>
</div>
<script type="text/javascript">
	(function() {
		var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		po.src = 'https://apis.google.com/js/plusone.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	})();
</script>
<?php get_footer() ?>