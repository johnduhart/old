			<div class="container" id="top">
				<a href="<?php echo site_url('auth/login'); ?>">Login</a>
				<?php if(isset($home_link) && $home_link === TRUE): ?>
				<span style="float: left;">
					<a href="<?php echo base_url(); ?>">&laquo; Go home</a><?php 
					if(isset($top_extra)): ?>
					<?php echo $top_extra; ?>
					<?php endif; 
				?></span>
				<?php endif; ?>
			</div>