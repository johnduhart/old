				<h2>Add Moderator</h2>
				<img src="images/mod_add.png" align="right" alt="Mods" height="32" width="32" />
				<hr />
				<br />
				<form name="add_mod" action="<?php echo getUrl("admin/mods/add"); ?>" method="post">
					<?php if(!empty($error)) {
					?><p class="ErrorBox" style="margin-top: 10px;"><?php echo $error; ?></p><?php } ?>
					<label for="username"><p>Username:</p></label>
					<input type="text" name="username" style="width: 16em;" value="<?php echo $username; ?>" /><br /><br />
					<label for="uid"><p>User ID:</p></label>
					<input type="text" name="uid" style="width: 10em;" value="<?php echo $userid; ?>" /><br /><br />
					<label for="role"><p>Role:</p></label>
					<input type="text" name="role" style="width: 16em;" value="<?php echo $userrole; ?>" /><br /><br />
					<input type="submit" name="add" value="Add moderator" />
				</form>