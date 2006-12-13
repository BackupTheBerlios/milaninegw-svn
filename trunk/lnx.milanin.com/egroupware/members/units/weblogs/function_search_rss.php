<?php

	global $search_exclusions;

	if (isset($parameter) && $parameter[0] == "weblog" || $parameter[0] == "weblogall") {
		
		$search_exclusions[] = "weblogall";
		$owner = (int) $_REQUEST['owner'];
		$searchline = "tagtype = 'weblog' and tag = '".addslashes($parameter[1])."'";
		$searchline = "(" . run("users:access_level_sql_where",$_SESSION['userid']) . ") and " . $searchline;
		$searchline = str_replace("access", tbl_prefix."weblog_posts.access", $searchline);
		$searchline = str_replace("owner", tbl_prefix."weblog_posts.weblog", $searchline);
		$refs = db_query("select ".tbl_prefix."weblog_posts.owner, ".tbl_prefix."weblog_posts.weblog, ".tbl_prefix."weblog_posts.ident, ".tbl_prefix."weblog_posts.title, ".tbl_prefix."users.name, ".tbl_prefix."tags.ref from ".tbl_prefix."tags left join ".tbl_prefix."weblog_posts on ".tbl_prefix."weblog_posts.ident = ref left join ".tbl_prefix."users on ".tbl_prefix."users.ident = ".tbl_prefix."tags.owner where $searchline order by ".tbl_prefix."weblog_posts.posted desc limit 50");
		
		if (sizeof($refs) > 0) {
			foreach($refs as $post) {
				$run_result .= "\t<item>\n";
				$run_result .= "\t\t<title>Weblog post :: " . htmlentities(stripslashes($post->name));
				if ($post->title != "") {
					$run_result .= " :: " . htmlentities(stripslashes($post->title));
				}
				$weblogusername = run("users:id_to_name",$post->weblog);
				$run_result .= "</title>\n";
				$run_result .= "\t\t<link>" . url . htmlentities(stripslashes($weblogusername)) . "/weblog/" . $post->ident . ".html</link>\n";
				$run_result .= "\t</item>\n";
			}
		}
		
	}

?>
