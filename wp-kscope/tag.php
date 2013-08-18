<?php get_header() ?>
<div id="wrapper" class="container_12">
	<div id="content" class="container_12">
		<div id="main" class="grid_8 archive">
			<h2><?php single_tag_title( '', true ) ?></h2>

			<?php echo tag_description() ?>
			<hr>
			<?php
			get_template_part( 'archive-result' );
			?>
		</div>

		<?php get_sidebar() ?>
		<div class="clear"></div>
	</div>
</div>
<?php get_footer() ?>