<?php

	$run_result .= run("templates:draw", array(
						'context' => 'menuitem',
						'name' => 'Log off',
						'location' => 'https://egw.milanin.eu/logout.php?sessionid='.session_id()
					)
					);

?>
