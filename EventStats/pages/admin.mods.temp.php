				<h2>Moderators Admin</h2>
				<img src="images/mods.png" align="right" alt="Mods" height="32" width="32" />
				<hr />
				<p>These moderators are part of the tracking system</p>
				<br />
				<table class="set" cellpadding="2">
					<tr class="thead">
						<th>Username</th>
						<th>Active</th>
						<th>Role</th>
						<th width="15%">Action</th>
					</tr>
					<?php while($row = mysql_fetch_array($r, MYSQL_BOTH)) {
					if($row['active']) { echo '<tr class="rowa">'; } else { echo '<tr class="rowred">'; } ?>
						<td><a href="<?php echo getUrl("mods/".$row['name']); ?>"><?php echo $row['name']; ?></a></td>
						<td><?php if($row['active']) { echo "Yes"; } else { echo "No"; } ?></td>
						<td><?php echo $row['role']; ?></td>
						<td><a href="<?php echo getUrl("admin/mods/edit/".$row['id']); ?>" id="edit" class="Action">Edit</a> - <a href="<?php echo getUrl("admin/mods/del/".$row['id']); ?>" id="delete" class="Action">Delete</a></td>
					</tr>
					<?php } ?>
					
				</table>
				<br />
				<a href="<?php echo getUrl('admin/mods/add'); ?>" class="Action" id="add">Add</a> 