<?php

	/*
	*	Friends plug-in
	*/

	// Functions to perform upon initialisation
		$function['friends:init'][] = path . "units/friends/friends_init.php";
		$function['friends:init'][] = path . "units/friends/friends_actions.php";
	
	// Get list of ".tbl_prefix."friends.		$function['friends:get'][] = path . "units/friends/get_friends.php";
		
	// 'Friends' aspect to the little menus beneath peoples' icons
		$function['users:infobox:menu'][] = path . "units/friends/user_info_menu.php";
	
	// 'Friends' list in the portfolio view
		$function['profile:log_on_pane'][] = path . "units/friends/profile_friends.php";
		$function['display:sidebar'][] = path . "units/friends/profile_friends.php";
		
	// ".tbl_prefix."friends.full view / edit section
		$function['friends:editpage'][] = path . "units/friends/friends_edit_wrapper.php";
		$function['friends:edit'][] = path . "units/friends/friends_edit.php";
	// 'Friends of' full view / edit section
		$function['friends:editpage'][] = path . "units/friends/friends_of_edit_wrapper.php";
		$function['friends:of:edit'][] = path . "units/friends/friends_of_edit.php";
		
	// Menu button
		$function['menu:main'][] = path . "units/friends/menu_main.php";	

	// FOAF file
		$function['foaf:generate'][] = path . "units/friends/generate_foaf.php";
			
?>