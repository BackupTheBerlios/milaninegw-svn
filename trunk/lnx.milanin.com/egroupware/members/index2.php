<?php

	require("includes.php");
	
	echo run("templates:draw:page", array(
					sitename,
					run("templates:draw", array(
													'contents' => run("content:mainindex"),
													'name' => "Main Index",
													'context' => 'infobox'
												)
												)
			)
			);
			
?>