<?php

	// Get communities
	
		$communities = db_query("select * from users where owner = " . $_SESSION['userid']);
	
		if (sizeof($communities) > 0) {
			foreach($communities as $community) {
				
				$data['access'][] = array("Community: " . $community->name, "community" . $community->ident);
				
			}
		}
		
		$communities = db_query("select users.* from friends 
										left join users on users.ident = friends.friend 
										where users.user_type = 'community' 
										and users.owner <> " . $_SESSION['userid'] . "
										and friends.owner = " . $_SESSION['userid']);
		
		if (sizeof($communities) > 0) {
			foreach($communities as $community) {
				
				$data['access'][] = array("Community: " . $community->name, "community" . $community->ident);
				
			}
		}
		
		$communities = db_query("select * from users where owner = " . $_SESSION['userid']);

?>