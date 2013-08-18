<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title><?php echo $title ?> - SyncWiki</title>
		<link rel="stylesheet" href="<?php echo base_url(); ?>style.css" />
		<?php if(isset($redirect)): ?>
		<meta http-equiv="refresh" content="<?php echo $redirect['length']; ?>;URL=<?php echo $redirect['url']; ?>" />
		<?php endif; ?>
		<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-1.4.2.min.js"></script>
		<script type="text/javascript">
			var toolbox_toggle_link = "<?php echo site_url('ajax/toolbox_toggle'); ?>";
		</script>
		<script type="text/javascript" src="<?php echo base_url(); ?>js/syncwiki.js"></script>
		<?php if(isset($headinclude)) { echo $headinclude; } ?>
	</head>
	<body>
		<div class="spacer">
			<?php echo top_user_bar(); ?>
			<div class="container">
