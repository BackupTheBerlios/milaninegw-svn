<?php

	//	ELGG manage icons page

	// Run includes
		require("../includes.php");
		
	// Initialise functions for user details, icon management and profile management
		run("userdetails:init");
		run("profile:init");
		run("videos:init");

	// You must be logged on to view this!
		protect(1);
		
		$title = run("profile:display:name") . " :: Manage user videos";
		
		$body = run("content:videos:manage");
                if (!$_REQUEST['new']){
                  $body .= run("videos:edit");
                }
		$body .= run("videos:add");
                
		
		$mainbody = run("templates:draw", array(
							'context' => 'infobox',
							'name' => $title,
							'contents' => $body
						)
						);
						
		echo run("templates:draw:page", array(
					$title, $mainbody
				)
				);

?>