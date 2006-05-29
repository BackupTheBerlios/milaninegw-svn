<?php

	// Gets all the ".tbl_prefix."groups.owned by a particular user, as specified in $parameter[0],
	// and return it in a data structure with the idents of all the ".tbl_prefix."users.in each group
	
		$ident = (int) $parameter[0];
		
		// if (!isset($_SESSION['groups_cache']) || (time() - $_SESSION['groups_cache']->created > 60)) {
		
			$groups = db_query("select * from ".tbl_prefix."groups where owner = $ident");
			$tempdata = "";
			
			$groupslist = array();
			if (sizeof($groups) > 0) {
				foreach($groups as $group) {
					
					// @unset($data);
					$tempdata->name = stripslashes($group->name);
					$tempdata->ident = $group->ident;
					$tempdata->access = $group->access;
					$members = db_query("select ".tbl_prefix."group_membership.user_id,
												users.name from ".tbl_prefix."group_membership 
												left join ".tbl_prefix."users on ".tbl_prefix."users.ident = ".tbl_prefix."group_membership.user_id
												where ".tbl_prefix."group_membership.group_id = " . $tempdata->ident);
					$tempdata->members = $members;
					
					$groupslist[] = $tempdata;
					
				}
			}
			
			$_SESSION['groups_cache']->created = time();
			$_SESSION['groups_cache']->data = $groupslist;
			
		// }
		
		$run_result = $_SESSION['groups_cache']->data;

?>