				<?php if(!$hide_toolbox): ?>
				<div class="toolbox"<?php if(!$show_toolbox): ?> style="display: none;"<?php endif; ?>>
					<span id="hide_button"><a href="#">Hide</a></span>
					<h3>TOOLBOX</h3>
					<ul>
						<li><a href="<?php echo site_url('System/'); ?>">System Pages</a></li>
						<li>Sup</li>
					</ul>
				</div>
				<?php endif; ?>
				<div id="footer">
					<?php if(!$hide_toolbox): ?>
					<div id="show_toolbox"<?php if($show_toolbox): ?> style="display: none;"<?php endif; ?>>
						<a href="#">Show Toolbox</a>
					</div>
					<?php endif; ?>
					Powered by SyncWiki, <a href="<?php echo base_url(); ?>CHANGELOG.md">Version <?php echo syncwiki_version(); ?></a><br />
					&copy; 2010 compwhizii
				</div>
			</div>
		</div>
		<?php if(isset($bottom_script)) { echo $bottom_script; } ?>
	</body>
</html>