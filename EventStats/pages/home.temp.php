				<h2>Home</h2>
				<!--<img src="images/chart.png" align="right" alt="Chart" height="32" width="32" />-->
				<hr />
				<img src="images/chart.png" align="right" alt="Chart" />
				<p>Mod Stats tracks actions taken by moderators on Facepunch. It displayes them in charts, tables, and rankings. The site was ogrinally created in August with minimal styling and basic features. Version two introduces more robust features, such as an control panel and pruning of records.</p>
				<br/>
				<h3>Pages</h3>
				<div class="page_info">
					<p><a href="<?php echo getUrl('mods'); ?>"><img src="images/mods.png" align="left" alt="Mods" height="32" width="32" />
					<strong>Moderators</strong></a><br />
					Simple list of moderators that the site is tracking</p>
				</div>
				<div class="page_info">
					<p><a href="<?php echo getUrl('alerts'); ?>"><img src="images/warning.png" align="left" alt="Info" height="32" width="32" />
					<strong>Alerts</strong></a><br />
					Alerts about moderator's activity and behaviour</p>
				</div>
				<div class="page_info">
					<p><a href="<?php echo getUrl('awards'); ?>"><img src="images/star.png" align="left" alt="Star" height="32" width="32" />
					<strong>Top Mods</strong></a><br />
					Moderation rankings per action</p>
				</div>
				<div class="page_info">
					<p><a href="<?php echo getUrl('sets'); ?>"><img src="images/clock.png" align="left" alt="Clock" height="32" width="32" />
					<strong>Action Sets</strong></a><br />
					Each day the system takes a count of actions each moderator takes and records them.</p>
				</div><?php if(empty($_SESSION['loggedin'])) { ?>
				<div class="page_info">
					<p><a href="<?php echo getUrl('login'); ?>"><img src="images/lock.png" align="left" alt="Lock" height="32" width="32" />
					<strong>Login</strong></a><br />
					Login to access administration pages</p>
				</div><?php } else { ?>
				<div class="page_info">
					<p><a href="<?php echo getUrl('admin'); ?>"><img src="images/admincp.png" align="left" alt="AdminCP" height="32" width="32" />
					<strong>AdminCP</strong></a><br />
					Administration pages. Modify mods and the system.</p>
				</div><?php } ?>
