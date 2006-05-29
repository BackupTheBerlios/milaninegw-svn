<?php

	// Gets all the ".tbl_prefix."groups.owned by a particular user, as specified in $parameter[0],
	// and return it in a data structure with the idents of all the ".tbl_prefix."users.in each group
	
		$ident = (int) $parameter[0];
		
		if (!isset($_SESSION['groups_membership_cache'][$ident]) || (time() - $_SESSION['groups_membership_cache'][$ident]->created > 60)) {
		
			$groups = db_query("select ".tbl_prefix."groups.* from ".tbl_prefix."group_membership left join ".tbl_prefix."groups on ".tbl_prefix."groups.ident = ".tbl_prefix."group_membership.group_id where user_id = $ident");
			$tempdata = "";
			
			$membership = array();
			if (sizeof($groups) > 0) {
				foreach($groups as $group) {
					
					// @unset($data);
					$tempdata->name = stripslashes($group->name);
					$tempdata->ident = $group->ident;
					$members = db_query("select ".tbl_prefix."group_membership.user_id,
												users.name from ".tbl_prefix."group_membership 
												left join ".tbl_prefix."users on ".tbl_prefix."users.ident = ".tbl_prefix."group_membership.user_id
												where ".tbl_prefix."group_membership.group_id = " . $tempdata->ident);
					$tempdata->members = $members;
					
					$membership[] = $tempdata;
					
				}
			}
			
			$_SESSION['groups_membership_cache'][$ident]->created = time();
			$_SESSION['groups_membership_cache'][$ident]->data = $membership;
			
		}
		
		$run_result = $_SESSION['groups_membership_cache'][$ident]->data;

?>
