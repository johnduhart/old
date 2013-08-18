<div class="grid_4" id="sidebar">
	<div id="popular-posts">
		<h4>Popular Posts</h4>
		<?php if ( function_exists('wpp_get_mostpopular') ) wpp_get_mostpopular() ?>
	</div>
	<div id="twitter-box">
		<h4>Kaleidoscope Twitter</h4>
		<?php if ( function_exists('aktt_sidebar_tweets') ) aktt_sidebar_tweets() ?>
	</div>
</div>