<?php
	
	if (logged_on) {
		$result = db_query("select ".tbl_prefix."users.ident from ".tbl_prefix."friends
									left join ".tbl_prefix."users on ".tbl_prefix."users.ident = ".tbl_prefix."friends.friend
									where owner = ".$_SESSION['userid']."
									and ".tbl_prefix."users.user_type = 'person'
									limit 8");
	
			
		$friends = array();
		if (sizeof($result) > 0) {
			foreach($result as $row) {
				$friends[] = $row->ident;
			}
		}
		//run("users:infobox",array("Your ".tbl_prefix."friends.,array($friends),"<a href=\"friends/\">Friends Screen</a>"));
			
	}

?>