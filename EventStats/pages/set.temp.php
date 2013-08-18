				<h2>Set #<?php echo $setid; ?></h2>
				<img src="images/clock.png" align="right" alt="Clock" height="32" width="32" />
				<hr />
				<?php if ($setpruned) { ?>
				<p>Results from <?php echo date("l, F jS g:i A \E\S\T", ($setstamp-86400)); ?> to <?php echo date("l, F jS g:i A \E\S\T", $setstamp); ?> were pruned and are no longer avalible.</p>
				<?php } elseif (mysql_num_rows($counts) == 0) { ?>
				<p>No data!</p>
				<?php } else { ?>
				<p>Results from <?php echo date("l, F jS g:i A \E\S\T", ($setstamp-86400)); ?> to <?php echo date("l, F jS g:i A \E\S\T", $setstamp); ?></p>
				<table class="set">
					<tr class="thead">
						<th></th>
						<th><img src="images/icons/ban.png" /></th>
						<th><img src="images/icons/pban.png" /></th>
						<th><img src="images/icons/unban.png" /></th>
						<th><img src="images/icons/closed.png" /></th>
						<th><img src="images/icons/opened.png" /></th>
						<th><img src="images/icons/mov.png" /></th>
						<th><img src="images/icons/rename.png" /></th>
						<th><img src="images/icons/ddt.png" /></th>
						<th><img src="images/icons/delsoft.png" /></th>
						<th><img src="images/icons/capsfix.png" /></th>
						<th>Total</th>
					</tr>
					<?php while ($r = mysql_fetch_array($counts, MYSQL_BOTH)) {  ?>
					<tr class="rowa">
						<td><a href="<?php echo getUrl("mods/".$modArray[$r['uid']]); ?>"><?php echo $modArray[$r['uid']]; ?></a></td>
						<td><?php echo $r['ban'];$total['ban']=$total['ban']+$r['ban']; ?></td>
						<td><?php echo $r['pban'];$total['pban']=$total['pban']+$r['pban']; ?></td>
						<td><?php echo $r['unban'];$total['unban']=$total['unban']+$r['unban']; ?></td>
						<td><?php echo $r['closed'];$total['closed']=$total['closed']+$r['closed']; ?></td>
						<td><?php echo $r['opened'];$total['opened']=$total['opened']+$r['opened']; ?></td>
						<td><?php echo $r['mov'];$total['mov']=$total['mov']+$r['mov']; ?></td>
						<td><?php echo $r['rename'];$total['rename']=$total['rename']+$r['rename']; ?></td>
						<td><?php echo $r['ddt'];$total['ddt']=$total['ddt']+$r['ddt']; ?></td>
						<td><?php echo $r['delsoft'];$total['delsoft']=$total['delsoft']+$r['delsoft']; ?></td>
						<td><?php echo $r['capsfix'];$total['capsfix']=$total['capsfix']+$r['capsfix']; ?></td>
						<?php $ttotal = $r['ban']+$r['pban']+$r['unban']+$r['closed']+$r['opened']+$r['mov']+
									$r['rename']+$r['ddt']+$r['delsoft']+$r['capsfix'];?>
						<td><?php echo $ttotal; $total['total']=$total['total']+$ttotal; ?></td>
					</tr>
					<?php } ?>
					<tr class="rowa">
						<td><strong>Total</strong></td>
						<td><?php echo $total['ban']; ?></td>
						<td><?php echo $total['pban']; ?></td>
						<td><?php echo $total['unban']; ?></td>
						<td><?php echo $total['closed']; ?></td>
						<td><?php echo $total['opened']; ?></td>
						<td><?php echo $total['mov']; ?></td>
						<td><?php echo $total['rename']; ?></td>
						<td><?php echo $total['ddt']; ?></td>
						<td><?php echo $total['delsoft']; ?></td>
						<td><?php echo $total['capsfix']; ?></td>
						<td><?php echo $total['total']; ?></td>
					</tr>
				</table>
				<?php } ?>