				<h2>Top Moderators</h2>
				<img src="images/star.png" align="right" alt="Star" height="32" width="32" />
				<hr />
				<p>The following are top moderators since the start of logging</p>
				<br/>
				<h3>Top banners</h3>
				<ol class="modlist">
					<?php foreach ($top_banners as $name => $count) {  ?>
					<li><a href="<?php echo getUrl('mods/'.$name); ?>"><?php echo $name; ?></a> - <?php echo $count; ?></li>
					<?php } ?>
				</ol><br />
				<h3>Top permabanners</h3>
				<ol class="modlist">
					<?php foreach ($top_pbanners as $name => $count) {  ?>
					<li><a href="<?php echo getUrl('mods/'.$name); ?>"><?php echo $name; ?></a> - <?php echo $count; ?></li>
					<?php } ?>
				</ol><br />
				<!--<h3>Most unbans</h3>
				<p>compwhizii</p>-->
				<h3>Top lockers</h3>
				<ol class="modlist">
					<?php foreach ($top_closers as $name => $count) {  ?>
					<li><a href="<?php echo getUrl('mods/'.$name); ?>"><?php echo $name; ?></a> - <?php echo $count; ?></li>
					<?php } ?>
				</ol><br />
				<!--<h3>Most opens</h3>
				<p>compwhizii</p>-->
				<h3>Top movers</h3>
				<ol class="modlist">
					<?php foreach ($top_movers as $name => $count) {  ?>
					<li><a href="<?php echo getUrl('mods/'.$name); ?>"><?php echo $name; ?></a> - <?php echo $count; ?></li>
					<?php } ?>
				</ol><br />
				<h3>Most renames</h3>
				<ol class="modlist">
					<?php foreach ($top_renames as $name => $count) {  ?>
					<li><a href="<?php echo getUrl('mods/'.$name); ?>"><?php echo $name; ?></a> - <?php echo $count; ?></li>
					<?php } ?>
				</ol><br />
				<h3>Most DDTs</h3>
				<ol class="modlist">
					<?php foreach ($top_ddters as $name => $count) {  ?>
					<li><a href="<?php echo getUrl('mods/'.$name); ?>"><?php echo $name; ?></a> - <?php echo $count; ?></li>
					<?php } ?>
				</ol>