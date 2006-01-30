<?php

	// Invite a friend
	
	// Actions
		$function['invite:init'][] = path . "units/invite/invite_actions.php";
	// languages
		$function['invite:invite_lang'][] = path . "units/invite/function_default_invite_message_lang.php";
		$function['invite:invite_title'][] = path . "units/invite/function_default_invite_message_title.php";
		$function['invite:invite_body'][] = path . "units/invite/function_default_invite_message_body.php";
	
	// Introductory text
		$function['content:invite:invite'][] = path . "content/invite/invite.php";

  // Invitation letter language select
		$function['invite:invite_lang_select'][] = path . "units/invite/function_invite_lang_select.php";
		$function['invite:invite_title_select'][] = path . "units/invite/function_invite_title_select.php";
		$function['invite:invite_text_select'][] = path . "units/invite/function_invite_text_select.php";
		
	// Allow user to invite a friend
		$function['invite:invite'][] = path . "units/invite/invite.php";
		$function['invite:join'][] = path . "units/invite/invite_join.php";
		
	// Allow a new user to sign up
		$function['join:no_invite'][] = path . "units/invite/join_noinvite.php";

	// Allow the user to request a new password
		$function['invite:password:request'][] = path . "units/invite/password_request.php";
		$function['invite:password:new'][] = path . "units/invite/new_password.php";
		
	// Menu bar
		$function['menu:main'][] = path . "units/invite/menu_main.php";
		
?>