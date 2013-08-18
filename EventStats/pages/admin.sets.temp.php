				<h2>Sets Admin</h2>
				<img src="images/set_manage.png" align="right" alt="Mods" height="32" width="32" />
				<hr />
				<p>Sets management. Yeah.</p>
				<br />
				<p>Page: <?php echo $page_links; ?></p>
				<table class="set" cellpadding="2">
					<tr class="thead">
						<th>ID</th>
						<th>Caputure time</th>
						<th>Status</th>
						<th width="10%">Action</th>
					</tr>
					<?php while($row = mysql_fetch_array($r, MYSQL_BOTH)) { ?>
					<tr class="rowa">
						<td><?php echo $row['id']; ?></td>
						<td><?php echo date("l, F jS g:i A \E\S\T", $row['stamp']);  ?></td>
						<td><?php _set_status($row['pruned']); ?></td>
						<td><a href="<?php echo getUrl("admin/sets/del/".$row['id']); ?>" id="delete" class="Action">Prune</a></td>
					</tr>
					<?php } ?>
					
				</table>
				<p>Page: <?php echo $page_links; ?></p>
				<br />
				<h3>Tools</h3>
				<ul class="modlist">
					<li><a href="<?php echo getUrl("admin/sets/top"); ?>"><strong>Recount totals</strong></a> - Recounts all mods totals</li>
				</ul>
				