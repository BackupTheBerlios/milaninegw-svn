<?php

	$run_result .= run("templates:draw", array(
						'context' => 'menuitem',
						'name' => 'Profile',
						'location' => url . $_SESSION['username'] . '/'
					)
					);
	/*$run_result .= run("templates:draw", array(
						'context' => 'menuitem',
						'name' => 'Edit your profile',
						'location' => url . 'profile/edit.php'
					)
					);*/

?>