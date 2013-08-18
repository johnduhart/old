<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
	<title><?php if(!empty($title)) {echo $title." - ";} ?>Facepunch Mod Stats</title>
	<link href="style.css" rel="stylesheet" type="text/css" media="screen" />
	<?php if(!empty($headerinclude)) { echo $headerinclude; } ?>
</head>
<body>

	<div id="main">
	
		<div id="top_bar">
			<div class="container">
				<p id="login">Hey there, <?php if(empty($_SESSION['loggedin'])) { 
				?><a href="<?php echo getUrl('login'); ?>">Login</a><?php
			} else { echo $_SESSION['username']; ?> - <a href="<?php echo getUrl('logout'); ?>">Logout</a><?php } ?></p>
			</div><!-- end top bar container div -->
		</div><!-- end top bar div -->
		
		<div id="header">

			<div id="branding" class="container">
				<img src="images/logo.png" alt="Logo" />
				<h1>Facepunch</h1>
				<p class="desc">Mod Stats</p>
				<ul id="menu">
					<li><a href="<?php echo getUrl('home'); ?>" style="margin-left: 2px;">Home</a></li>
					<li><a href="<?php echo getUrl('mods'); ?>">Mods</a></li>
					<li><a href="<?php echo getUrl('alerts'); ?>">Alerts</a></li>
					<li><a href="<?php echo getUrl('awards'); ?>">Top Mods</a></li>
					<?php if(check_auth('login')) { ?><li><a href="<?php echo getUrl('admin'); ?>">Admin CP</a></li><?php } ?>
				</ul>
			</div><!-- end branding div -->
			
		</div><!-- end header div -->
		
		<div id="content" class="container">
			<div id="in_content" class="container">