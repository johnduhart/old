</div> <!-- #wrap -->

<div id="footer">
	<div class="container_12">
		<ul>
			<li>&copy; <?php echo date('Y') ?> <?php bloginfo('name'); ?></li>
			<?php wp_list_categories('orderby=name&exclude=54,1&title_li='); ?>
		</ul>
		<div class="clear"></div>
	</div>
</div>
<!--<a href="https://twitter.com/WMKaleidoscope" class="twitter-follow-button" data-show-count="false">Follow @WMKaleidoscope</a>-->
<script type="text/javascript">
	$(function (){
		$('#twitter-box li.aktt_more_updates').prepend(
			$( '<span />' )
				.css( 'float', 'left' )
				.append( $( '<a href="https://twitter.com/WMKaleidoscope" class="twitter-follow-button" data-show-count="false">Follow @WMKaleidoscope</a>' ) )
		);
	});
</script>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
<?php wp_footer(); ?>
</body>
</html>
