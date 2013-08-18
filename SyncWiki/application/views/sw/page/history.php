<?php $this->load->view('sw/header'); ?>
				<?php echo build_tabs($tabs, $page_title); ?>
				<div class="content" id="history">
					<div id="backFloat">
						<a href="<?php echo site_url($page_link.'/edit'); ?>">&lt; Back</a>
					</div>
					<div id="editList">
						<?php foreach($revs->result() as $row): ?>
						<div class="alt<?php echo alt_switcher('historyPage', 1, 2); ?>"><?php echo revision_type_icon($row->pagerev_type, (base_url().'img/')); ?><a href="<?php echo site_url($page_link.'/view/'.$row->pagerev_id); ?>">View</a> - <strong><?php echo date('g:i A, M j Y', $row->pagerev_timestamp); ?></strong> - by <?php if($row->username != ''): ?><a href="<?php echo site_url('User/'.$row->username); ?>"><?php echo $row->username; ?></a><?php else: ?><?php echo $row->pagerev_userip; ?><?php endif; ?> <?php if($row->pagerev_comment != ''): ?><em>(<?php echo $row->pagerev_comment; ?>)</em><?php endif; ?></div>
						<?php endforeach; ?>
					</div>
				</div>
<?php $this->load->view('sw/footer'); ?>