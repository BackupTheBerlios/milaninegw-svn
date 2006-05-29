<?php

	// Gets all the ".tbl_prefix."groups.owned by a particular user, as specified in $parameter[0],
	// and return it in a data structure with the idents of all the ".tbl_prefix."users.in each group
	
		$ident = (int) $parameter[0];
		
		// if (!isset($_SESSION['groups_cache']) || (time() - $_SESSION['groups_cache']->created > 60)) {
		
			$where1 = run("users:access_level_sql_where",$ident);
			$groups = db_query("select ".tbl_prefix."groups.name, ".tbl_prefix."groups.ident, ".tbl_prefix."groups.access, ".tbl_prefix."groups.owner, 
										users.name as ownername, ".tbl_prefix."users.ident as owneruserid, ".tbl_prefix."users.username as ownerusername
										from ".tbl_prefix."group_membership 
										left join ".tbl_prefix."groups on ".tbl_prefix."groups.ident = ".tbl_prefix."group_membership.group_id
										left join ".tbl_prefix."users on ".tbl_prefix."users.ident = ".tbl_prefix."groups.owner
										where ($where1) and ".tbl_prefix."group_membership.user_id = $ident");
			$tempdata = "";
			
			$groupslist = array();
			if (sizeof($groups) > 0) {
				foreach($groups as $group) {
					
					// @unset($data);
					$tempdata->name = stripslashes($group->name);
					$tempdata->ident = $group->ident;
					$tempdata->access = $group->access;
					$tempdata->ownername = stripslashes($group->ownername);
					$tempdata->ownerusername = stripslashes($group->ownerusername);
					$tempdata->owneruserid = stripslashes($group->owneruserid);
					$groupslist[] = $tempdata;
					
				}
			}
			
		// }
		
		$run_result = $groupslist;

?>
