<?php

	$run_result .= run("templates:draw", array(
						'context' => 'menuitem',
						'name' => 'Your Home',
						'location' => url . $_SESSION['username'] . '/home'
					)
					);
	/*$run_result .= run("templates:draw", array(
						'context' => 'menuitem',
						'name' => 'Design your Home',
						'location' => url . '_home/edit.php'
					)
					);*/

?>