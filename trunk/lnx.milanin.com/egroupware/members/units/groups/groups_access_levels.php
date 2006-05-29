<?php

	// Get ".tbl_prefix."groups.	
		$groups = run("groups:get",array($_SESSION['userid']));
		
		if (sizeof($groups) > 0) {
			foreach($groups as $group) {
				
				$data['access'][] = array("Group: " . $group->name, "group" . $group->ident);
				
			}
		}

?>