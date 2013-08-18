				<h2>Prune Set</h2>
				<img src="images/set_manage.png" align="right" alt="Mods" height="32" width="32" />
				<hr />
				<div style="font-size: 2em;">Are sure you want to prune this set?</div>
				<br />
				<form name="del_mod" action="<?php echo getUrl("admin/sets/del"); ?>" method="post">
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<input type="submit" name="no" value="No I do not want to do this" />
					<input type="submit" name="del" value="Prune set #<?php echo $id; ?> forever." />
				</form>