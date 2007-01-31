<?php

	$run_result .= run("templates:draw", array(
						'context' => 'menuitem',
						'name' => 'Weblog',
						'location' => url . $_SESSION['username'] . '/weblog/'
					)
					);

	/*$run_result .= run("templates:draw", array(
						'context' => 'menuitem',
						'name' => 'Update your weblog',
						'location' => url . '/_weblog/edit.php'
					)
					);*/
					
?>