				<h2><?php echo $modName; ?></h2>
				<img src="images/mod.png" align="right" alt="Mods" height="32" width="32" />
				<hr />
				<?php if(!$modActive) { ?><p class="WarningBox" id="pageNotice" style="color: #000000">This mod is marked as retired. They are currently not being tracked</p><?php } ?>
				<img src="http://www.facepunch.com/image.php?u=<?php echo $modUid; ?>&amp;dateline=<?php echo time(); ?>" align="right" alt="<?php echo $modName."'s avatar"; ?>" />
				<p><strong>Role:</strong> <?php echo $modRole ?></p><br />
				<h3>Events since logging:</h3>
				<table id="event_count" class="modlist">
					<tr>
						<td id="ban">Bans: </td>
						<td><?php echo $totals['ban']; ?></td>
					</tr>
					<tr>
						<td id="pban">Perma Bans: </td>
						<td><?php echo $totals['pban']; ?></td>
					</tr>
					<tr>
						<td id="unban">Unbans: </td>
						<td><?php echo $totals['unban']; ?></td>
					</tr>
					<tr>
						<td id="close">Closes: </td>
						<td><?php echo $totals['closed']; ?></td>
					</tr>
					<tr>
						<td id="open">Opens: </td>
						<td><?php echo $totals['opened']; ?></td>
					</tr>
					<tr>
						<td id="move">Moves: </td>
						<td><?php echo $totals['mov']; ?></td>
					</tr>
					<tr>
						<td id="rename">Renames: </td>
						<td><?php echo $totals['rename']; ?></td>
					</tr>
					<tr>
						<td id="ddt">DDTs: </td>
						<td><?php echo $totals['ddt']; ?></td>
					</tr>
					<tr>
						<td id="delete">Deletes: </td>
						<td><?php echo $totals['delsoft']; ?></td>
					</tr>
					<tr>
						<td id="capfix">Capfixes: </td>
						<td><?php echo $totals['capsfix']; ?></td>
					</tr>
					<tr>
						<td><strong>Total:</strong> </td>
						<td><?php echo $totals['total']; ?></td>
					</tr>
				</table>
				<h3>Last 10 sets:</h3>
				<table class="set">
					<tr class="thead">
						<th width="30%"></th>
						<th><img src="images/icons/ban.png" alt="Ban" /></th>
						<th><img src="images/icons/pban.png" alt="PermaBan" /></th>
						<th><img src="images/icons/unban.png" alt="Unban" /></th>
						<th><img src="images/icons/closed.png" alt="Close" /></th>
						<th><img src="images/icons/opened.png" alt="Open" /></th>
						<th><img src="images/icons/mov.png" alt="Move" /></th>
						<th><img src="images/icons/rename.png" alt="Rename" /></th>
						<th><img src="images/icons/ddt.png" alt="DDT" /></th>
						<th><img src="images/icons/delsoft.png" alt="Delete" /></th>
						<th><img src="images/icons/capsfix.png" alt="Capsfix" /></th>
						<th>Total</th>

					</tr>
						<?php while ($r = mysql_fetch_array($l10, MYSQL_BOTH)) {  ?>
						<tr class="rowa">
							<td><a href="<?php echo getUrl('sets/'.$r['set_id']); ?>"><?php echo date("l, F jS", ($r['stamp'] - 86400)); ?></a></td>
							<td><?php echo $r['ban']; ?></td>
							<td><?php echo $r['pban']; ?></td>
							<td><?php echo $r['unban']; ?></td>
							<td><?php echo $r['closed']; ?></td>
							<td><?php echo $r['opened']; ?></td>
							<td><?php echo $r['mov']; ?></td>
							<td><?php echo $r['rename']; ?></td>
							<td><?php echo $r['ddt']; ?></td>
							<td><?php echo $r['delsoft']; ?></td>
							<td><?php echo $r['capsfix']; ?></td>
							<?php $ttotal = $r['ban']+$r['pban']+$r['unban']+$r['closed']+$r['opened']+$r['mov']+
										$r['rename']+$r['ddt']+$r['delsoft']+$r['capsfix'];?>
							<td><?php echo $ttotal;  ?></td>
						</tr>
						<?php } ?>

							</table>
				<h3>Graphs:</h3>
				<?php
				$gData = array();
				while ($r = mysql_fetch_array($l5)) {
					$r['total'] = $r['ban']+$r['pban']+$r['unban']+$r['closed']+$r['opened']+$r['mov']+
									$r['rename']+$r['ddt']+$r['delsoft']+$r['capsfix'];
					$gData[] = $r;
				}
				$last5Chart = new GoogleChart();
				$last5Chart->setType("lc");
				$last5Chart->setSize(250, 350);
				foreach ($gData as $data) {
					$last5Chart->addData($data['total']);
				}
				foreach ($gData as $data) {
					$last5Chart->addLabel(date("D", ($data['stamp'] - 86400)), "x");
				}
				$last5Chart->addLabels(array('0','25','50', '75'), "y");
				$last5Chart->setTitle("Total events by day");
				$last5Chart->setRange(0, 75);
				$last5Chart->setChartColor("a70328");
				?>
				<img id="graph" alt="Last 5 days graph" src="<?php echo $last5Chart->buildSafeUrl()."&amp;chf=bg,s,9a9a9a"; ?>" />
				<h3>Links:</h3>
				<p><a href="http://www.facepunch.com/member.php?userid=<?php echo $modUid; ?>">FP Profile</a> - <a href="http://www.facepunch.com/fp_events.php?user=<?php echo $modUid; ?>">Event Log</a></p>
				<?php if (check_auth("editmods")) { ?>
				<p class="EditBox" id="pageNotice" style="color: rgb(226, 226, 226); font-size: 1em;"><a href="<?php echo getUrl('admin/mods/edit/'.$modId); ?>" id="edit">Edit</a> - <a href="<?php echo getUrl('admin/mods/del/'.$modId); ?>" id="delete">Delete</a> - <a href="<?php echo getUrl('admin'); ?>" id="admincp">AdminCP</a></p><?php } ?>