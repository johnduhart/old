				<h2>Moderators</h2>
				<img src="images/mods.png" align="right" alt="Mods" height="32" width="32" />
				<hr />
				<p>The following is a list of moderators tracked by the system</p>
				<br />
				<ul class="modlist">
					<?php while ($row = mysql_fetch_array($active, MYSQL_BOTH)) {
							echo "<li><a href=\"".getUrl("mods/".$row['name'])."\">".$row['name']."</a></li>\n"; }
					?>
				</ul>
				<?php if (check_auth("editmods")) { ?>
				<br />
				<h3>Retired mods</h3>
				<ul class="modlist">
					<?php while ($row = mysql_fetch_array($unactive, MYSQL_BOTH)) {
							echo "<li><a href=\"".getUrl("mods/".$row['name'])."\">".$row['name']."</a></li>\n"; }
					?>
				</ul>
				<p class="EditBox" id="pageNotice" style="color: rgb(226, 226, 226); font-size: 1em;"><a href="<?php echo getUrl('admin/mods/add'); ?>" id="add">Add</a> - <a href="<?php echo getUrl('admin/mods'); ?>" id="modedit">Moderators Admin</a> - <a href="<?php echo getUrl('admin'); ?>" id="admincp">AdminCP</a></p><?php } ?>