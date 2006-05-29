<?php

	// Returns an SQL "where" clause containing all the access codes that the user can see
	
		if (logged_on) {
			
			$communities = db_query("select ".tbl_prefix."users.* from ".tbl_prefix."friends left join ".tbl_prefix."users on ".tbl_prefix."users.ident = ".tbl_prefix."friends.friend where ".tbl_prefix."users.user_type = 'community' and ".tbl_prefix."users.owner <> " . $_SESSION['userid'] . " and ".tbl_prefix."friends.owner = " . $_SESSION['userid']);
			if (sizeof($communities) > 0) {
				foreach($communities as $community) {
					$run_result .= "or access = \"community" . $community->ident . "\" ";
				}
			}
			$communities = db_query("select ".tbl_prefix."users.* from ".tbl_prefix."users where ".tbl_prefix."users.owner = " . $_SESSION['userid']);
			if (sizeof($communities) > 0) {
				foreach($communities as $community) {
					$run_result .= "or access = \"community" . $community->ident . "\" ";
				}
			}
						
		}

?>