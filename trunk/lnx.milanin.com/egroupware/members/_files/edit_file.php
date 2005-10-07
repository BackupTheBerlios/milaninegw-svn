<?php

	//	ELGG manage files page

	// Run includes
		require("../includes.php");
		
	// Initialise functions for user details, icon management and profile management
		run("userdetails:init");
		run("profile:init");
		run("files:init");

	// Whose files are we looking at?

		global $page_owner;
		$title = run("profile:display:name") . " :: Edit File";

		$body = run("content:files:edit");
		$body .= run("files:edit");
		
		echo run("templates:draw:page", array(
					$title,
					run("templates:draw", array(
							'context' => 'infobox',
							'name' => $title,
							'contents' => $body
						)
						)
				)
				);
				
?>