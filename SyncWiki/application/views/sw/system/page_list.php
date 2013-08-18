<?php $this->load->view('sw/header'); ?>
				<?php echo build_tabs($tabs, $page_title); ?>
				<div class="content" id="page_list">
					<ul>
						<?php foreach($pages->result() as $row):
						?><li><a href="<?php echo site_url($row->page_title); ?>"><?php echo str_replace(array('_'), array(' '), $row->page_title); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
<?php $this->load->view('sw/footer'); ?>