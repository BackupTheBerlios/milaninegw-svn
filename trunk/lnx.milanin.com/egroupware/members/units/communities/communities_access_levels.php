<?php

	// Get communities
	
		$communities = db_query("select * from ".tbl_prefix."users where owner = " . $_SESSION['userid']);
	
		if (sizeof($communities) > 0) {
			foreach($communities as $community) {
				
				$data['access'][] = array("Community: " . $community->name, "community" . $community->ident);
				
			}
		}
		
		$communities = db_query("select ".tbl_prefix."users.* from ".tbl_prefix."friends 
										left join ".tbl_prefix."users on ".tbl_prefix."users.ident = ".tbl_prefix."friends.friend 
										where ".tbl_prefix."users.user_type = 'community' 
										and ".tbl_prefix."users.owner <> " . $_SESSION['userid'] . "
										and ".tbl_prefix."friends.owner = " . $_SESSION['userid']);
		
		if (sizeof($communities) > 0) {
			foreach($communities as $community) {
				
				$data['access'][] = array("Community: " . $community->name, "community" . $community->ident);
				
			}
		}
		
		$communities = db_query("select * from ".tbl_prefix."users where owner = " . $_SESSION['userid']);

?>