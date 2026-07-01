
<div class="full_w">
	<?php
		if (isset($msg) && !empty($msg)) {
			echo '<div class="n_ok"><p>';
			if(is_array($msg)) {
				foreach ($msg as $m) :
					echo esc($m);
				endforeach;
			} else {
				echo $msg;
			}
			echo '</p></div>';
		}
	?>
	<?php
		helper('form');
		
		echo form_open(current_url(true));
		if (isset($errors) && !empty($errors)) {
			echo '<div class="n_error">';
			foreach ($errors as $error) :
				echo esc($error);
			endforeach;
			echo '</div><div class="sep"></div>';
		}
	?>
	<label for="username">Username:</label>
	<input id="username" name="username" class="text" type="text" maxlength="100" />
	<label for="password">Password:</label>
	<input id="password" name="password" type="password" class="text" maxlength="25" />
	<div class="sep"></div>
	<input type="submit" name="login" id="login" value="Login" />
	<?php
	echo form_close();
	?>
</div>