				<?php $isAlerts = false; ?>
				<h2>Alerts</h2>
				<img src="images/warning.png" align="right" alt="Warning" height="32" width="32" />
				<hr />
				<p>The following are categories of problems and moderators who fall under them.</p>
				<br/>
				<?php if (count($low_activity) != 0) { $isAlerts = true; ?>
				<h3>Low activity</h3>
				<p><span class="small_text">The following mods have not made any moderation actions in five days</span></p>
				<br />
				<ul class="modlist">
					<?php foreach ($low_activity as $name => $id) { ?>
					<li><a href="<?php echo getUrl('mods/'.$name); ?>"><?php echo $name; ?></a></li>
					<?php } ?>
				</ul>
				<br />
				<?php } ?>
				<?php if (count($low_quota) != 0) { $isAlerts = true; ?>
				<h3>Not meeting quota</h3>
				<p><span class="small_text">The following mods have less then 25 events for the last 5 days.</span></p>
				<br />
				<ul class="modlist">
					<?php foreach ($low_quota as $name => $id) { ?>
					<li><a href="<?php echo getUrl('mods/'.$name); ?>"><?php echo $name; ?></a></li>
					<?php } ?>
				</ul>
				<br />
				<?php } ?>
				<?php if (count($mia) != 0) { ?>
				<h3>Missing in action</h3>
				<p><span class="small_text">The following mods have not made any moderation actions in two weeks</span></p>
				<br />
				<ul class="modlist">
					<?php foreach ($mia as $name => $id) { $isAlerts = true; ?>
					<li><a href="<?php echo getUrl('mods/'.$name); ?>"><?php echo $name; ?></a></li>
					<?php } ?>
				</ul>
				<?php } ?>
				<?php if (!$isAlerts) { ?>
					<p>No alerts. Everyone is good =)</p>
				<?php } ?>