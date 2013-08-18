				<h2>Edit User</h2>
				<img src="images/user.png" align="right" alt="Mods" height="32" width="32" />
				<hr />
				<?php if(!empty($error)) {
					?><p class="ErrorBox" style="margin-top: 10px;"><?php echo $error; ?></p><?php } ?>
				<br />
				<form name="edit_user" action="<?php echo getUrl("admin/users/edit"); ?>" method="post">
					<label for="username"><p>Username:</p></label>
					<input type="text" name="username" style="width: 16em;" value="<?php echo $username; ?>" /><br /><br />
					<label for="email"><p>E-Mail:</p></label>
					<input type="text" name="email" style="width: 16em;" value="<?php echo $email; ?>" /><br /><br />
					<label for="newpass"><p>New Password:</p></label>
					<input type="text" name="newpass" style="width: 16em;" /><br /><br />
					<input type="checkbox" name="can_login" value="Yes" <?php if($can_login != 0) { echo "checked=\"Yes\""; } ?>/><span > Login</span><br />
					<input type="checkbox" name="can_editmods" value="Yes" <?php if($can_editmods != 0) { echo "checked=\"Yes\""; } ?>/><span > Edit Moderators</span><br />
					<input type="checkbox" name="can_editusers" value="Yes" <?php if($can_editusers != 0) { echo "checked=\"Yes\""; } ?>/><span > Edit Users</span><br />
					<input type="checkbox" name="can_editsets" value="Yes" <?php if($can_editsets != 0) { echo "checked=\"Yes\""; } ?>/><span > Edit Sets</span><br /><br />
					<input type="hidden" name="id" value="<?php echo $userid; ?>">
					<input type="submit" name="save" value="Save user" />
				</form>