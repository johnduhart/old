				<h2>Edit Moderators</h2>
				<img src="images/mod_edit.png" align="right" alt="Mods" height="32" width="32" />
				<hr />
				<?php if(!empty($error)) {
					?><p class="ErrorBox" style="margin-top: 10px;"><?php echo $error; ?></p><?php } ?>
				<img src="http://www.facepunch.com/image.php?u=<?php echo $userid; ?>&amp;dateline=<?php echo time(); ?>" align="right" alt="<?php echo $username."'s avatar"; ?>" />
				<br />
				<p>
				<form name="add_mod" action="<?php echo getUrl("admin/mods/edit"); ?>" method="post">
					<label for="username"><p>Username:</p></label>
					<input type="text" name="username" style="width: 16em;" value="<?php echo $username; ?>" /><br /><br />
					<label for="uid"><p>User ID:</p></label>
					<input type="text" name="uid" style="width: 10em;" value="<?php echo $userid; ?>" /><br /><br />
					<label for="role"><p>Role:</p></label>
					<input type="text" name="role" style="width: 16em;" value="<?php echo $userrole; ?>" /><br /><br />
					<input type="checkbox" name="active" value="Yes" <?php if($active) { echo "checked=\"Yes\""; } ?>/><span > Active</span><br /><br />
					<input type="hidden" name="id" value="<?php echo $modid; ?>">
					<input type="submit" name="save" value="Save moderator" />
				</form>
				</p>