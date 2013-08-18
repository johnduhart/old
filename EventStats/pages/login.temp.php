				<h2>Login</h2>
				<img src="images/lock.png" align="right" alt="Lock" height="32" width="32" />
				<hr />
				<div id="login_box">
					<?php if(!empty($error)) {
					?><p class="ErrorBox"><?php echo $error; ?></p><?php } ?>
					<form name="login_form" action="<?php echo getUrl("login"); ?>" method="post">
						<label for="username">Username<br />
						<input type="text" name="username" style="width: 16em;" /></label>
						<label for="password">Password<br />
						<input type="password" name="password" style="width: 16em;" /></label>
						<input type="submit" name="login" value="Log in" />
					</form>
				</div>