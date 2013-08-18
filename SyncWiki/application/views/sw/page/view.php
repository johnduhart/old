<?php $this->load->view('sw/header'); ?>
				<?php echo build_tabs($tabs, $page_title); ?>
				<div class="content">
					<?php if($show['page_rev']): ?>
					<div class="view view_img top_bar">
						<?php if($next_rev != 0 OR $prev_rev != 0): ?><span style="float: right;"><?php if($prev_rev != 0): ?><a href="<?php echo site_url($page_title.'/view/'.$prev_rev); ?>">&laquo; Back</a><?php endif; ?><?php if($next_rev != 0 AND $prev_rev != 0): ?> | <?php endif; ?><?php if($next_rev != 0): ?><a href="<?php echo site_url($page_title.'/view/'.$next_rev); ?>">Next &raquo;</a><?php endif; ?></span><?php endif; ?>
						Currently viewing the page revision from <strong><?php echo date('g:i A, M j Y', $timestamp); ?></strong> by <a href="#"><?php echo $user; ?></a><?php 
						if($comment != ''):
						 ?><br />
						<em>(<?php echo $comment; ?>)</em><?php endif; ?>
					</div>
					<?php endif ?>
					<?php echo $text; ?>
				</div>
<?php $this->load->view('sw/footer'); ?>