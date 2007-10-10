<?php

	// ELGG weblog system initialisation
	
	// ID of profile to view / edit

		global $profile_id;
		if (isset($_GET['profile_name'])) {
			$profile_id = (int) run("users:name_to_id", $_GET['profile_name']);
		} else if (isset($_GET['profile_id'])) {
			$profile_id = (int) $_GET['profile_id'];
		} else if (isset($_POST['profile_id'])) {
			$profile_id = (int) $_POST['profileid'];
		} else if (isset($_SESSION['userid'])) {
			$profile_id = (int) $_SESSION['userid'];
		} else {
			$profile_id = -1;
		}

		global $page_owner;
		
		$page_owner = $profile_id;
		
		global $page_userid;
		
		$page_userid = run("users:id_to_name", $profile_id);
                // Get all videos associated with a user
                global $videos;
		$videos = db_query("select * from ".tbl_prefix."videos where owner = $page_owner");
                global $selected_video;
                global $selected_video_id;
                $selected_video = db_query("select videos.ident,videos.filename, ".tbl_prefix."users.video from ".tbl_prefix."users left join ".tbl_prefix."videos videos 
                on videos.ident = ".tbl_prefix."users.video where ".tbl_prefix."users.ident = $page_owner");
                $selected_video_id=$selected_video[0]->ident;
                $selected_video = url."_videos/data/".$selected_video[0]->filename;
	// Add RSS to metatags
	
		global $metatags;
		if (isset($_GET['weblog_name'])){
		  $metatags .= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS\" href=\"".url."$page_userid/video/rss\" />\n";
		}else{
		  $metatags .= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS\" href=\"".url."video/rss\" />\n";
		}
				
?>
