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
END;
$body.=run('clubincall:display:small')."<tr>\n";

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
							$icon = db_query("SELECT CONCAT( CONCAT_WS( '-', 'default',
							CASE sex.value
								WHEN 1
								THEN 'w'
								ELSE 'm'
							END ,
							CASE acc.account_status
								WHEN 'A'
									THEN 'active'
								ELSE 'disabled'
							END 
							) , 
							'.png' ) AS filename
							FROM phpgw_accounts acc
							LEFT JOIN ".tbl_prefix."users u ON u.username = acc.account_lid
							LEFT JOIN ".tbl_prefix."profile_data sex ON sex.owner = u.ident
							WHERE u.ident =". $ident . "
							AND sex.name = 'sex'");
							if (sizeof($icon) == 1) {
							//$_SESSION['icon_cache'][$info->icon]->data = $icon[0]->filename;
								$icon = $icon[0]->filename;
							}else{
								$icon = "default.png";
							}
					 	}
					// $icon = $_SESSION['icon_cache'][$info->icon]->data;
				} else {
					$icon = db_query("SELECT CONCAT( CONCAT_WS( '-', 'default',
							CASE sex.value
								WHEN 1
								THEN 'w'
								ELSE 'm'
							END ,
							CASE acc.account_status
								WHEN 'A'
									THEN 'active'
								ELSE 'disabled'
							END 
							) , 
							'.png' ) AS filename
							FROM phpgw_accounts acc
							LEFT JOIN ".tbl_prefix."users u ON u.username = acc.account_lid
							LEFT JOIN ".tbl_prefix."profile_data sex ON sex.owner = u.ident
							WHERE u.ident =". $ident . "
							AND sex.name = 'sex'");
							if (sizeof($icon) == 1) {
							//$_SESSION['icon_cache'][$info->icon]->data = $icon[0]->filename;
								$icon = $icon[0]->filename;
							}else{
								$icon = "default.png";
							}
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
				if ($_SESSION['userid']==$profile_id){
                                  $profilelink = "<a href=\"{$url}{$info->username}/\">My Profile</a>";
                                  $homelink = "<br /><a href=\"{$url}_home/edit.php\">My Homepage</a>";
                                  $pictureslink = "<br /><a href=\"{$url}_icons/\">My Picture</a>";
                                  $videoslink = "<br /><a href=\"{$url}_videos/\">My video</a>";
				}else{
                                  $homelink = "<br /><a href=\"{$url}{$info->username}/home/\">".$username."&rsquo;s Homepage</a>";
                                  $profilelink = "<a href=\"{$url}{$info->username}/\">".$username."&rsquo;s Profile</a>";
                                  $videoslink = "<br /><a href=\"{$url}_videos/\">".$username."&rsquo;s Videos</a>";
                                }
                                $body .= <<< END
                <td align="center" valign="top">
                        <a href="{$url}{$info->username}/">
                        <img src="{$url}_icons/data/{$icon}" width="{$width}" height="{$height}" alt="{$username}" border="0
" /></a><br />
END;
                                if (basename($_SERVER['REQUEST_URI'])=="home"){
                                        $body .= "<span class=\"userdetails\">{$profilelink}</span>";
                                }elseif (basename($_SERVER['REQUEST_URI'])==$info->username){
                                        $body .= "<span class=\"userdetails\">{$homelink}</span>";
                                } else {
                                        $body .= "<span class=\"userdetails\">{$profilelink}</span>";
                                        $body .= "<span class=\"userdetails\">{$homelink}</span>";
                                }
                                $body .="<span>{$pictureslink}</span><!--span>{$videoslink}</span--><span>{$rsslink}</span>
                </td>";
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
