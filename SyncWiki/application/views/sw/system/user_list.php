<?php $this->load->view('sw/header'); ?>
				<?php echo build_tabs($tabs, $page_title); ?>
				<div class="content" id="page_list">
					<ul>
						<?php foreach($users->result() as $row):
						?><li><a href="<?php echo site_url('User/'.$row->username); ?>"><?php echo $row->username; ?></a><?php if($row->group != 'user'): ?> (<?php echo $row->group_description; ?>)<?php endif; ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
<?php $this->load->view('sw/footer'); ?>