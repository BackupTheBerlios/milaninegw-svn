<?php

	if (substr_count($parameter, "community") > 0 && logged_on) {
		$commnum = (int) substr($parameter, 9, 15);
		$result = db_query("select ".tbl_prefix."friends.owner from ".tbl_prefix."friends
												 left join ".tbl_prefix."users on ".tbl_prefix."users.ident = ".tbl_prefix."friends.friend
												 where ".tbl_prefix."users.user_type = 'community'
												 and ".tbl_prefix."users.ident = $commnum
												 and ".tbl_prefix."friends.owner = " . $_SESSION['userid']);
		if (sizeof($result) > 0) {
			$run_result = true;
		} else {
			
			$result = db_query("select ident from ".tbl_prefix."users where user_type = 'community' and owner = " . $_SESSION['userid']);
			if (sizeof($result) > 0) {
				$run_result = true;
			}
			
		}
	}

?>