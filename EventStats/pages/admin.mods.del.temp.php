				<h2>Delete Moderator</h2>
				<img src="images/mod_error.png" align="right" alt="Mods" height="32" width="32" />
				<hr />
				<img src="http://www.facepunch.com/image.php?u=<?php echo $userid; ?>&dateline=<?php echo time(); ?>" align="right" alt="<?php echo $username."'s avatar"; ?>" />
				<div style="color: #ED1717; font-size: 7em;">STOP! READ THIS</div>
				<br />
				<p>When you delete a moderator, you don't only remove that mod from the system, you also remove their records. All of their data is lost and can not be recoverd. Don't do this because you're demodding someone, just mark them inactive.</p>
				<br />
				<div style="font-size: 3em;">Once you delete <?php echo $username; ?>, there's no going back</div>
				<br />
				<form name="del_mod" action="<?php echo getUrl("admin/mods/del"); ?>" method="post">
					<input type="hidden" name="id" value="<?php echo $modid; ?>">
					<input type="submit" name="no" value="No I do not want to do this" />
					<input type="submit" name="del" value="Delete <?php echo $username; ?> forever." />
				</form>