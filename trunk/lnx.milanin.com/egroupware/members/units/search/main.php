<?php

	// ELGG Keyword search system

	// Parse REQUEST field
		$function['search:display'][] = path . "units/search/function_display.php";		
		$function['search:all:display'][] = path . "units/search/function_search_all_display.php";
		$function['search:all:display:rss'][] = path . "units/search/function_search_all_display_rss.php";
		$function['search:tags:display'][] = path . "units/search/tags_display.php";		
		
	// Suggest tags
		$function['search:tags:suggest'][] = path . "units/search/search_suggest_tags.php";
		
	// Suggest ".tbl_prefix."users.		$function['search:users:suggest'][] = path . "units/search/search_suggest_users.php";
		
	// Suggest RSS
		$function['search:rss:suggest'][] = path . "units/search/search_suggest_rss.php";
		
	// Log on bar down the right hand side
		// $function['profile:log_on_pane'][] = path . "units/weblogs/weblogs_user_info_menu.php";
		$function['display:sidebar'][] = path . "units/search/search_user_info_menu.php";
?>