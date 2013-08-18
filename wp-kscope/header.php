<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">

	<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>

	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen"/>
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>"/>
	<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if (is_singular() && get_option('thread_comments'))
		wp_enqueue_script('comment-reply');

	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	wp_head();
	?>

	<!--[if lt IE 7]>
	<style type="text/css">
		#wrap {
			display: table;
			height: 100%
		}
	</style>
	<![endif]-->

	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript"
	        src="<?php bloginfo('stylesheet_directory'); ?>/jcarousel/lib/jquery.jcarousel.min.js"></script>

</head>
<body>
<div id="wrap">
	<div id="top">
		<div id="header">
			<div id="dateAndTime">
				&nbsp;
			</div>
			<h1><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></h1>

			<p id="slogan"><?php bloginfo('description'); ?></p>

			<form method="get" id="searchform" action="<?php bloginfo('home'); ?>/">
				<input type="text" value="<?php echo wp_specialchars($s, 1); ?>" name="s" id="s" size="15"/>
				<input type="submit" id="searchSubmit" value="Search"/>
			</form>
			<div class="clear"></div>
		</div>
		<div id="categories-wrapper">
			<ul id="categories">
				<?php wp_list_categories('orderby=name&exclude=54,1&title_li='); ?>
				<div class="clear"></div>
			</ul>
		</div>

	</div>