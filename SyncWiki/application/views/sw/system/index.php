<?php $this->load->view('sw/header'); ?>
				<?php echo build_tabs($tabs, $page_title); ?>
				<div class="content" id="system_pages">
					<h2>Information</h2>
					<ul>
						<li><a href="<?php echo site_url('System/Page_List'); ?>">Page List</a></li>
						<li><a href="<?php echo site_url('System/User_List'); ?>">User List</a></li>
					</ul>
				</div>
<?php $this->load->view('sw/footer'); ?>