<?php $this->load->view('sw/header'); ?>
				<?php echo build_tabs($tabs, $page_title); ?>
				<div class="content" id="editor">
					<?php if($show['edit_preview']): ?>
					<h1>Preview:</h1>
					<div id="edit_preview">
						<?php echo $preview_text; ?>
					</div>
					<?php endif; ?>
					<?php echo edit_page_locked($locked_status); ?>
					<?php if($show['newpage_notice']): ?>
					<div class="new_page new_page_img top_bar">
						The page you're editing doesn't exist, when you save you will create it.
					</div>
					<?php endif; ?>
					<?php if($show['previous_deleted']): ?>
					<div class="delete delete_img top_bar">
						This page has previously deleted versions.
					</div>
					<?php endif; ?>
					<?php echo form_open($page_link.'/edit'); ?>
						<?php 
							$data = array(
								'name' => 'editbox',
								'id' => 'editbox',
								'cols' => '30',
								'rows' => '20',
								'tabindex' => '1'
							);
							echo form_textarea($data, $editText);
						 ?>
						<br />
						<?php if($show['save_buttons']): ?>
						<div id="afterArea">
							<div id="reason">
								<label for="reason" style="margin-right: 6px;">Reason:
								<?php 
									$data = array(
										'name' => 'comment',
										'maxlength' => '200',
										'size' => '55',
										'tabindex' => '2',
										'value' => $comment
									);
									echo form_input($data);
								 ?>
								</label>
							</div>
							<div id="buttons">
								<?php 
									$data = array('name' => 'save', 'tabindex' => '3');
									echo form_submit($data, 'Save'); 
								?>
								<?php
									$data = array('name' => 'preview', 'tabindex' => '4');
									echo form_submit($data, 'Preview');
								?>
							</div>
						</div>
						<?php endif; ?>
					<?php echo form_hidden('pageid', $pageid); ?>
					<?php echo form_close(); ?>
					<?php if($show['tools']): ?>
					<hr />
					<div id="editorTools">
						<ul>
							<li><a href="<?php echo site_url($page_link.'/history'); ?>"><img src="<?php echo base_url(); ?>img/history.png" alt="History" />History</a></li>
							<?php if($show['report']): ?>
							<li><a href="#report"><img src="<?php echo base_url(); ?>img/report.png" alt="Report" />Report</a></li>
							<?php endif; ?>
							<?php if($show['mod_tools']): ?>
							<li><a href="#protect"><img src="<?php echo base_url(); ?>img/protection.png" alt="Protection" />Protection</a></li>
							<li><a href="#delete"><img src="<?php echo base_url(); ?>img/delete.png" alt="Delete" />Delete</a></li>
							<?php endif; ?>
						</ul>
					</div>
					<div class="panels">
						<?php if($show['report']): ?>
						<div id="report" class="report panel">
							<h2>Report this page</h2>
							<p>If you feel that this page is in violation of the rules, you should report it.</p>
							<br />
							<form action="#" id="report_form">
								<p>I feel that this page is:</p>
								<div class="options">
									<label for="reason"><input id="reason_radio" type="radio" name="reason" value="1" /> Spam/Advertising</label>
									<label for="reason"><input id="reason_radio" type="radio" name="reason" value="2" /> Hateful</label>
									<label for="reason"><input id="reason_radio" type="radio" name="reason" value="3" /> Vandalism</label>
									<label for="reason"><input id="reason_radio" type="radio" name="reason" value="9999" /> Other:</label>
									<input type="text" id="other_box" name="other" size="30" style="margin-left: 15px;" />
								</div>
								<input type="submit" value="Report" />
								<?php echo form_button('cancel', 'Cancel', 'class="cancel"'); ?>
							</form>
						</div>
						<?php endif; ?>
						<?php if($show['mod_tools']): ?>
						<div id="protect" class="protection panel">
							<h2>Protection Options</h2>
							<?php 
								$extra = array('id' => 'protect_form');
								echo form_open($page_link.'/edit/lock', $extra); ?>
								<p>Protection level</p>
								<div class="options" id="protect_options">
									<label for="level"><?php echo form_radio('level', '0', ($locked_status == 0)); ?> None</label>
									<label for="level"><?php echo form_radio('level', '1', ($locked_status == 1)); ?> Logged in users only</label>
									<label for="level"><?php echo form_radio('level', '2', ($locked_status == 2)); ?> Admins only</label>
								</div>
								<?php echo form_submit('update_lock', 'Save'); ?>
								<?php echo form_button('cancel', 'Cancel', 'class="cancel"'); ?>
							</form>
						</div>
						<div id="delete" class="delete panel">
							<h2>Delete this page</h2>
							<p>If this page is breaking rules, remove it!</p>
							<br />
							<?php 
								$extra = array('id' => 'delete_form');
								echo form_open($page_link.'/edit/delete', $extra); ?>
								<div class="options">
									<label for="delete_reason"><strong>Reason:</strong> <br />
										<?php 	$data = array(
													'name' => 'reason',
													'id' => 'delete_reason',
													'size' => '30'
												);
												echo form_input($data); ?></label>
								</div><br /><br />
								<?php echo form_submit('delete', 'Delete'); ?>
								<?php echo form_button('cancel', 'Cancel', 'class="cancel"'); ?>
							<?php echo form_close(); ?>
						</div>
						<?php endif; ?>
					</div>
					<?php endif; ?>
				</div>
				<script type="text/javascript"> var locked_status = <?php echo $locked_status; ?>; var protection_link = '<?php echo $protection_link; ?>'; var pageid = <?php echo $pageid; ?>; var delete_link = '<?php echo $delete_link; ?>';</script>
<?php $this->load->view('sw/footer'); ?>