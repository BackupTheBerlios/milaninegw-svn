<?php

	/*
	*	Videos plug-in
	*/

	// Actions
		$function["videos:init"][] = path . "units/videos/function_actions.php";
	
	// Icon management
		$function["videos:edit"][] = path . "units/videos/function_edit_videos.php";	
		$function["videos:add"][] = path . "units/videos/function_add_videos.php";
	
	// Menu button
		$function["menu:main"][] = path . "units/videos/menu_main.php";
		
	// Permissions check
		$function["permissions:check"][] = path . "units/videos/permissions_check.php";
        //Mplayer runner
                $function["mplayer:run"][] = path ."units/videos/function_mplayer_run.php";
		
?> 
