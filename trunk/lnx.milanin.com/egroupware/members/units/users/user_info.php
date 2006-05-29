<?php

	global $profile_id;

	$url = url;
	
	// Given a title and series of user IDs as a parameter, will display a box containing the icons and names of each specified user
	// $parameter[0] is the title of the box; $parameter[1..n] is the user ID

	if (isset($parameter[0]) && sizeof($parameter) > 1 /*&& $parameter[1][0] != 0*/) {

		if (sizeof($parameter[1]) > 1) {
			$span = 2;
		} else {
			$span = 1;
		}
		
		$name = $parameter[0];
		
		$i = 1;
		if (sizeof($parameter[1]) == 0) {
			
			$body = "None.";
			
		} else {
			$body .= <<< END
			
	<table align="center" border="0" class="userlist">
		<tr>
			
END;
			foreach($parameter[1] as $key => $ident) {
				$ident = (int) $ident;
				// if (!isset($_SESSION['user_info_cache'][$ident])) {
					$info = db_query("select * from ".tbl_prefix."users where ident = $ident");
					$_SESSION['user_info_cache'][$ident] = $info[0];
					$info = $info[0];
				// }
				$info = $_SESSION['user_info_cache'][$ident];
				if ($info->icon != -1 && $info->icon != NULL) {
					// if (!isset($_SESSION['icon_cache'][$info->icon]) || (time() - $_SESSION['icon_cache'][$info->icon]->created > 60)) {
						$icon = db_query("select filename from ".tbl_prefix."icons where ident = " . $info->icon . " and owner = $ident");
						//$_SESSION['icon_cache'][$info->icon]->created = time();
						if (sizeof($icon) == 1) {
							//$_SESSION['icon_cache'][$info->icon]->data = $icon[0]->filename;
							$icon = $icon[0]->filename;
						} else {
							//$_SESSION['icon_cache'][$info->icon]->data = "default.png";
							$icon = "default.png";
						}
					// }
					// $icon = $_SESSION['icon_cache'][$info->icon]->data;
				} else {
					$icon = "default.png";
				}
				list($width, $height, $type, $attr) = getimagesize(path . "_icons/data/" . $icon);
				if (sizeof($parameter[1]) > 4) {
					$width = round($width / 2);
					$height = round($height / 2);
				}

				$username = htmlentities(stripslashes($info->name));
				$usermenu = run("users:infobox:menu",array($info->ident));
				if ($info->ident == $profile_id || (logged_on && (!isset($profile_id) && $info->ident == $_SESSION['userid']))) {
					$rsslink = "<br />(<a href=\"{$url}{$info->username}/rss/\">RSS</a>)";
				}
				$homelink = "<br /><a href=\"{$url}{$info->username}/home/\">Home Page</a>";
				$body .= <<< END
		<td align="center" valign="top">
			<a href="{$url}{$info->username}/">
			<img src="{$url}_icons/data/{$icon}" width="{$width}" height="{$height}" alt="{$username}" border="0" /></a><br />
			<span class="userdetails"><a href="{$url}{$info->username}/">{$username}'s Profile {$usermenu}</span>
			<span class="userdetails">{$homelink}</span><span>{$rsslink}</span>
		</td>
		
END;
		
				if ($span == 1 || ($span == 2 && ($i % 2 == 0))) {
					$body .= "</tr><tr>";
				}
				$i++;
			}
			 $body .= "</tr></table>";
		}
		if (isset($parameter[2]) && $parameter[2] != "") {
			$body .= "<p align='center'>" . $parameter[2] . "</p>";
		}
		
			$run_result .= run("templates:draw", array(
						'context' => 'infobox',
						'name' => $name,
						'contents' => $body
					)
					);
		
	}

?>
