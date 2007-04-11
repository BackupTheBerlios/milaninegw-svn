<?php

	//	ELGG manage icons page

	// Run includes
		require("../includes.php");
		
	// Initialise functions for user details, icon management and profile management
		run("userdetails:init");
		run("profile:init");
		run("videos:init");
                global $profile_id;
		$title = run("profile:display:name") . " ::  Personal Video Library";
// 		$body = run("content:videos:manage");
                $body .= run("videos:display:player:personal");
		
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