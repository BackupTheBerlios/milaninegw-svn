<?php

	// View a weblog
	
	// Get the current profile ID
	
		global $profile_id;
		
	// If the weblog offset hasn't been set, it's 0
		if (!isset($_REQUEST['weblog_offset'])) {
			$weblog_offset = 0;
		} else {
			$weblog_offset = $_REQUEST['weblog_offset'];
		}
		$weblog_offset = (int) $weblog_offset;

		
	// Get all posts in the system that we can see
	
		$where = run("users:access_level_sql_where",$_SESSION['userid']);
		$posts = db_query("select * from ".tbl_prefix."weblog_posts where ($where) and weblog = $profile_id order by posted desc limit $weblog_offset,25");
		$numberofposts = db_query("select count(ident) as numberofposts from ".tbl_prefix."weblog_posts where ($where) and weblog = $profile_id");
		$numberofposts = $numberofposts[0]->numberofposts;
				
		if (sizeof($posts > 0) || sizeof($friendsposts > 0)) {
			
			$lasttime = "";
			
			foreach($posts as $post) {
				
				$time = gmdate("F d, Y",$post->posted);
				if ($time != $lasttime) {
					$run_result .= "<h2 class=\"weblogdateheader\">$time</h2>\n";
					$lasttime = $time;
				}
				
				$run_result .= run("weblogs:posts:view",$post);
				
			}
			
			$weblog_name = htmlentities(stripslashes($_REQUEST['weblog_name']));
			
			if ($numberofposts - ($weblog_offset + 25) > 0) {
				$display_weblog_offset = $weblog_offset + 25;
				$run_result .= <<< END
				
				<a href="/{$weblog_name}/weblog/skip={$display_weblog_offset}">&lt;&lt; Previous 25</a>
				
END;
			}
			if ($weblog_offset > 0) {
				$display_weblog_offset = $weblog_offset - 25;
				if ($display_weblog_offset < 0) {
					$display_weblog_offset = 0;
				}
				$run_result .= <<< END
				
				<a href="/{$weblog_name}/weblog/skip={$display_weblog_offset}">Next 25 &gt;&gt;</a>
				
END;
			}
			
		}

?>