<?php

	// Given a user ID as a parameter, will display a list of communities

	$url = url;
	
	if (isset($parameter[0])) {

		$user_id = (int) $parameter[0];
		
		$result = db_query("select ".tbl_prefix."users.*, ".tbl_prefix."friends.ident as friendident from ".tbl_prefix."friends
									left join ".tbl_prefix."users on ".tbl_prefix."users.ident = ".tbl_prefix."friends.friend
									where ".tbl_prefix."friends.owner = $user_id and ".tbl_prefix."users.user_type = 'community'");
									
		$body = <<< END
	<table class="userlist">
		<tr>
END;
		$i = 1;
		if (sizeof ($result) > 0) {
			foreach($result as $key => $info) {
					// $info = $info[0];
					if ($info->icon != -1) {
						$icon = db_query("select filename from ".tbl_prefix."icons where ident = " . $info->icon . " and owner = " . $info->ident);
						if (sizeof($icon) == 1) {
							$icon = $icon[0]->filename;
						} else {
							$icon = "default.png";
						}
					} else {
						$icon = "default.png";
					}
					list($width, $height, $type, $attr) = getimagesize(path . "_icons/data/" . $icon);
					if (sizeof($parameter[1]) > 4) {
						$width = round($width / 2);
						$height = round($height / 2);
					}
		$friends_username = stripslashes($info->username);
		$friends_name = htmlentities(stripslashes($info->name));
		$friends_menu = run("users:infobox:menu",array($info->ident));
		$body .= <<< END
		<td align="center">
			<a href="{$url}{$friends_username}/">
			<img src="{$url}_icons/data/{$icon}" width="{$width}" height="{$height}" alt="{$friends_name}" border="0" /></a><br />
			<span class="userdetails">
				{$friends_name}
				{$friends_menu}
			</span>
		</td>
END;
					if ($i % 5 == 0) {
						$body .= "</tr><tr>";
					}
					$i++;
			}
		} else {
			if ($user_id == $_SESSION['userid']) {
				$body .= "<td>You don't have any communities listed! To join a community, click the 'join' button underneath a community's icon. You can find communities you're interested in by using the search function.</td>";
			} else {
				$body .= "<td>This user is not currently a member of any communities.</td>";
			}
		}
		$body .= <<< END
	</tr>
	</table>
END;


		$run_result = $body;

	}

?>