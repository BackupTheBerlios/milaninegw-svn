<?php

	global $search_exclusions;

	$url = url;
		
	if (isset($parameter) && $parameter[0] == "weblog" || $parameter[0] == "weblogall") {
		
		if ($parameter[0] == "weblog") {
			$search_exclusions[] = "weblogall";
			$owner = (int) $_REQUEST['owner'];
			$searchline = "tagtype = 'weblog' and owner = $owner and tag = '".addslashes($parameter[1])."'";
			$searchline = "(" . run("users:access_level_sql_where",$_SESSION['userid']) . ") and " . $searchline;
			$searchline = str_replace("owner",tbl_prefix."tags.owner",$searchline);
			$refs = db_query("select ref from ".tbl_prefix."tags where $searchline");
			$searchline = "";
			if (sizeof($refs) > 0) {
	
				foreach($refs as $ref) {
					if ($searchline != "") {
						$searchline .= " or ";
					}
					$searchline .= tbl_prefix."weblog_posts.ident = " . $ref->ref;
				}
				$posts = db_query("select ".tbl_prefix."users.name, ".tbl_prefix."users.username, ".tbl_prefix."weblog_posts.title, ".tbl_prefix."weblog_posts.ident, ".tbl_prefix."weblog_posts.weblog, ".tbl_prefix."weblog_posts.owner, ".tbl_prefix."weblog_posts.posted from ".tbl_prefix."weblog_posts left join ".tbl_prefix."users on ".tbl_prefix."users.ident = ".tbl_prefix."weblog_posts.owner where ($searchline) order by posted desc");
				$run_result .= "<h2>Weblog posts by " . stripslashes($posts[0]->name) . " in category '".$parameter[1]."'</h2>\n<ul>";
				foreach($posts as $post) {
					$run_result .= "<li>";
					$weblogusername = run("users:id_to_name",$post->weblog);
					$run_result .= "<a href=\"" . url . $weblogusername . "/weblog/" . $post->ident . ".html\">" . gmdate("F d, Y",$post->posted) . " - " . stripslashes($post->title) . "</a>\n";
					if ($post->owner != $post->weblog) {
						$run_result .= " @ " . "<a href=\"" . url . $weblogusername . "/weblog/\">" . $weblogusername . "</a>\n";
					}
					$run_result .= "</li>";
				}
				$run_result .= "</ul>";
				$run_result .= "<p><small>[ <a href=\"". url . $post->username . "/weblog/rss/" . $parameter[1] . "\">RSS feed for weblog posts by " . stripslashes($posts[0]->name) . " in category '".$parameter[1]."'</a> ]</small></p>\n";
			}
		}
		$searchline = "tagtype = 'weblog' and tag = '".addslashes($parameter[1])."'";
		$searchline = "(" . run("users:access_level_sql_where",$_SESSION['userid']) . ") and " . $searchline;
		$searchline = str_replace("owner",tbl_prefix."tags.owner",$searchline);
		$sql = "select distinct ".tbl_prefix."users.* from ".tbl_prefix."tags left join ".tbl_prefix."users on ".tbl_prefix."users.ident = ".tbl_prefix."tags.owner where ($searchline)";
		if ($parameter[0] == "weblog") {
			$sql .= " and ".tbl_prefix."users.ident != " . $owner;
		}
		$users = db_query($sql);
		
		if (sizeof($users) > 0) {
			if ($parameter[0] == "weblog") {
				$run_result .= "<h2>Other members users with weblog posts in category '".$parameter[1]."'</h2>\n";
			} else {
				$run_result .= "<h2>Users with weblog posts in category '".$parameter[1]."'</h2>\n";
			}
			$body = "<table><tr>";
			$i = 1;
			foreach($users as $key => $info) {
	
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
					if (sizeof($users) > 4) {
						$width = round($width / 2);
						$height = round($height / 2);
					}
		$friends_userid = $info->ident;
		$friends_name = htmlentities(stripslashes($info->name));
		$friends_menu = run("users:infobox:menu",array($info->ident));
		$link_keyword = urlencode($parameter[1]);
		$body .= <<< END
		<td align="center">
			<a href="{$url}search/index.php?weblog={$link_keyword}&owner={$friends_userid}">
			<img src="{$url}_icons/data/{$icon}" width="{$width}" height="{$height}" alt="{$friends_name}" border="0" /></a><br />
			<span class="userdetails">
				{$friends_name}
				{$friends_menu}
			</span>
		</td>
END;
					if ($i % 5 == 0) {
						$body .= "\n</tr><tr>\n";
					}
					$i++;
			}
			$body .= "</tr></table>";
			$run_result .= $body;
		}
		
	}

?>
