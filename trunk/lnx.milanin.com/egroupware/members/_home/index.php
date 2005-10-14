<?php

	//	ELGG profile view page

	// Run includes
		require("../includes.php");
		
		run("home:init");
		if (isset($_POST['submit'])){
                  run("home:update_data");
                  $messages[] = "Your Home Page was updated.";
                }
		$title = run("home:display:name");
                
                $body = run("home:display");
                
		$body = run("templates:draw", array(
						'context' => 'infobox',
						'name' => $title,
						'contents' => $body
					)
					);
					
		echo run("templates:draw:page", array(
					$title, $body
				)
				);

?>