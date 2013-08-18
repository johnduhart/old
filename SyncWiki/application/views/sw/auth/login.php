<?php $this->load->view('sw/header'); ?>
				<?php echo build_tabs($tabs, 'Login'); ?>
				<div class="content">
					<div id="login_box">
						<div id="login_box_form">
							<?php if($error != ''): ?>
							<div class="delete delete_img"><?php echo $error; ?></div>
							<?php endif; ?>
							<?php echo form_open(site_url('auth/login')); ?>
								<div id="login_box_form_text"><?php 
									$data = array(
										'name' => 'username',
										'id' => 'username',
										'tabindex' => '1',
										'value' => set_value('username')
									);
									echo form_label('Username', 'username');
									echo form_input($data);
									
									$data = array(
										'name' => 'password',
										'id' => 'password',
										'tabindex' => '2'
									);
									echo form_label('Password', 'password');
									echo form_password($data);
								?><div class="clear"></div></div>
								
								<div style="clear: both; padding: 3px 0 3px 8px; overflow: hidden; vertical-align: middle;">
									<span style="float: right;"><?php echo form_checkbox('remember', '1', TRUE); ?> <?php echo form_label('Remember me', 'remember'); ?></span>
									<a href="<?php echo site_url('auth/register'); ?>">Register</a>
								</div>
								<div style="float: right; padding-top: 10px;">
									<?php echo form_submit('login', 'Login', 'tabindex="3"'); ?>
								</div>
							<?php echo form_close(); ?>
						</div>
					</div>
				</div>
<?php $this->load->view('sw/footer'); ?>