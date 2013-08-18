				<h2>Add User</h2>
				<img src="images/user_add.png" align="right" alt="Mods" height="32" width="32" />
				<hr />
				<?php if(!empty($error)) {
					?><p class="ErrorBox" style="margin-top: 10px;"><?php echo $error; ?></p><?php } ?>
				<br />
				<form name="add_user" action="<?php echo getUrl("admin/users/add"); ?>" method="post">
					<label for="username"><p>Username:</p></label>
					<input type="text" name="username" style="width: 16em;" value="<?php echo $username; ?>" /><br /><br />
					<label for="email"><p>E-Mail:</p></label>
					<input type="text" name="email" style="width: 16em;" value="<?php echo $email; ?>" /><br /><br />
					<label for="pass"><p>Password:</p></label>
					<input type="text" name="pass" style="width: 16em;" value="<?php echo $password; ?>"/><br /><br />
					<input type="checkbox" name="can_login" value="Yes" <?php if($can_login != 0) { echo "checked=\"Yes\""; } ?>/><span> Login</span><br />
					<input type="checkbox" name="can_editmods" value="Yes" <?php if($can_editmods != 0) { echo "checked=\"Yes\""; } ?>/><span> Edit Moderators</span><br />
					<input type="checkbox" name="can_editusers" value="Yes" <?php if($can_editusers != 0) { echo "checked=\"Yes\""; } ?>/><span> Edit Users</span><br />
					<input type="checkbox" name="can_editsets" value="Yes" <?php if($can_editsets != 0) { echo "checked=\"Yes\""; } ?>/><span> Edit Sets</span><br /><br />
					<input type="submit" name="add" value="Add user" />
				</form>