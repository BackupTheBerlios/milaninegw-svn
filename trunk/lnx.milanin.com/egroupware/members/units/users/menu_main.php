<?php
/*
	$run_result .= run("templates:draw", array(
						'context' => 'menuitem',
						'name' => 'Edit user details',
						'location' => url . '_userdetails/'
					)
					);
*/
	$run_result .= run("templates:draw", array(
						'context' => 'menuitem',
						'name' => 'Log off',
						'location' => url . '../egroupware/logout.php?sessionid='.session_id()
					)
					);
?>