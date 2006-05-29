<?php

	// Gets all the ".tbl_prefix."friends.of a particular user, as specified in $parameter[0],
	// and return it in a data structure with the idents of all the ".tbl_prefix."users.	
		$ident = (int) $parameter[0];
		
		if (!isset($_SESSION['friends_cache'][$ident]) || (time() - $_SESSION['friends_cache'][$ident]->created > 120)) {
			$_SESSION['friends_cache'][$ident]->created = time();
			$_SESSION['friends_cache'][$ident]->data = db_query("select ".tbl_prefix."friends.friend as user_id,
										users.name from ".tbl_prefix."friends
										left join ".tbl_prefix."users on ".tbl_prefix."users.ident = ".tbl_prefix."friends.friend
										where ".tbl_prefix."friends.owner = $ident");
		}
		$run_result = $_SESSION['friends_cache'][$ident]->data;
				
?>