<?php

	global $page_owner;
	/*
	if ($page_owner != -1 && run("users:type:get", $page_owner) == "person") {
		$result = db_query("select ".tbl_prefix."users.ident from ".tbl_prefix."friends
									left join ".tbl_prefix."users on ".tbl_prefix."users.ident = ".tbl_prefix."friends.friend
									where ".tbl_prefix."friends.owner = $page_owner
									and ".tbl_prefix."users.user_type = 'person'
									group by ".tbl_prefix."friends.friend
									limit 8");
			
		$friends = array();
		if (sizeof($result) > 0) {
			foreach($result as $row) {
				$friends[] = (int) $row->ident;
			}
		}
		$run_result .= "<div class=\"box_friends\">";
		if ($page_owner != $_SESSION['userid']) {
			$run_result .= run("users:infobox",
												array(
														"Friends",
														$friends,
														"<a href=\"".url."_friends/?owner=$profile_id\">Friends Screen</a>
														 (<a href=\"".url."_friends/foaf.php?owner=$profile_id\">FOAF</a>)"
														)
								);
			
		} else {
			$run_result .= run("users:infobox",
												array(
														"Your ".tbl_prefix."friends.,
														$friends,
														"<a href=\"".url.$_SESSION['username']."/friends/\">Friends Screen</a>
														 (<a href=\"".url.$_SESSION['username']."/foaf/\">FOAF</a>)"
													)
								);
		}
		$run_result .= "</div>";
			
	}
*/
?>