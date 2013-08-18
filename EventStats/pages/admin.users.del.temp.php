				<h2>Delete User</h2>
				<img src="images/user_delete.png" align="right" alt="Mods" height="32" width="32" />
				<hr />
				<div style="font-size: 2em;">Are sure you want to delete <?php echo $username; ?>?</div>
				<br />
				<form name="del_mod" action="<?php echo getUrl("admin/users/del"); ?>" method="post">
					<input type="hidden" name="id" value="<?php echo $userid; ?>">
					<input type="submit" name="no" value="No I do not want to do this" />
					<input type="submit" name="del" value="Delete <?php echo $username; ?> forever." />
				</form>