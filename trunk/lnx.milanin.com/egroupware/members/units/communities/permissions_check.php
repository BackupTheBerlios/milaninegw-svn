<?php

	global $page_owner;
	
		if (isset($parameter) && $page_owner != -1) {
			switch($parameter) {
				
				case	"profile":		$result = db_query("select owner from ".tbl_prefix."users where ident = $page_owner and user_type = 'community'");
										$result = $result[0]->owner;
										if ($result == $_SESSION['userid']) {
											$run_result = true;
										}
										break;
				case	"files":
				case	"weblog":		$result = db_query("select owner from ".tbl_prefix."users where ident = $page_owner and user_type = 'community'");
										$result = $result[0]->owner;
										if ($owner == $_SESSION['userid']) {
											$run_result = true;
										}
										if ($run_result != true) {
											$result = db_query("select count(".tbl_prefix."users.ident) as num from ".tbl_prefix."friends
																		left join ".tbl_prefix."users on ".tbl_prefix."users.ident = ".tbl_prefix."friends.friend
																		where ".tbl_prefix."users.ident = $page_owner
																		and ".tbl_prefix."friends.owner = ". $_SESSION['userid'] . "
																		and ".tbl_prefix."users.user_type = 'community'");
											if ($result[0]->num > 0) {
												$run_result = true;
											}
										}
										break;
				case 	"uploadicons":	$result = db_query("select owner from ".tbl_prefix."users where ident = $page_owner and user_type = 'community'");
										$result = $result[0]->owner;
										if ($result == $_SESSION['userid']) {
											$run_result = true;
										}
										break;
				case	"userdetails:change":
										$result = db_query("select owner from ".tbl_prefix."users where ident = $page_owner and user_type = 'community'");
										$result = $result[0]->owner;
										if ($result == $_SESSION['userid']) {
											$run_result = true;
										}
										break;
				
			}
			
		}

?>
