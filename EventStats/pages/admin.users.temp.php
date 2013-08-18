				<h2>Users Admin</h2>
				<img src="images/users.png" align="right" alt="Mods" height="32" width="32" />
				<hr />
				<p>These users can access the admin panel</p>
				<br />
				<table class="set" cellpadding="2">
					<tr class="thead">
						<th>Username</th>
						<th>E-mail</th>
						<th>Login</th>
						<th>Edit mods</th>
						<th>Edit users</th>
						<th>Edit sets</th>
						<th width="15%">Action</th>
					</tr>
					<?php while($row = mysql_fetch_array($r, MYSQL_BOTH)) { ?>
					<tr class="rowa">
						<td><?php echo $row['username']; ?></td>
						<td><?php echo $row['email']; ?></td>
						<td><?php _can_icon($row['can_login']); ?></td>
						<td><?php _can_icon($row['can_editmods']); ?></td>
						<td><?php _can_icon($row['can_editusers']); ?></td>
						<td><?php _can_icon($row['can_editsets']); ?></td>
						<td><a href="<?php echo getUrl("admin/users/edit/".$row['id']); ?>" id="edit" class="Action">Edit</a> - <a href="<?php echo getUrl("admin/users/del/".$row['id']); ?>" id="delete" class="Action">Delete</a></td>
					</tr>
					<?php } ?>
					
				</table>
				<br />
				<a href="<?php echo getUrl('admin/users/add'); ?>" class="Action" id="add">Add</a> 