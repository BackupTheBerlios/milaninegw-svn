<?php

	// Returns the user_type of a particular user as specified in $parameter
	
		global $user_type;
		
		if (!isset($user_type[$parameter])) {
			$temp_user_type = db_query("select ".tbl_prefix."users.user_type from ".tbl_prefix."users where ".tbl_prefix."users.ident = $parameter");
			$user_type[$parameter] = $temp_user_type[0]->user_type;
		}
		
		$run_result = $user_type[$parameter];
		
?>