<?

	global $page_owner;
if (isset($_POST['action']) && $_POST['action'] == "videos:edit" && logged_on && run("permissions:check", "uploadvideos")) {
if (isset($_POST['videos_delete'])) {
				if (sizeof($_POST['videos_delete']) > 0) {
					foreach($_POST['videos_delete'] as $delete_video) {
						echo "!";
						$delete_video = (int) $delete_video;
						$result = db_query("select filename from ".tbl_prefix."videos where ident = $delete_video and owner = " . $page_owner);
						if (sizeof($result) == 1) {
							db_query("delete from ".tbl_prefix."videos where ident = $delete_video");
							@unlink(path . "_videos/data/" . $result[0]->filename);
                                                        @unlink(path . "_videos/data/" . $result[0]->filename.".jpg");
						}
						if ($result[0]->filename = $_SESSION['video']) {
							db_query("update ".tbl_prefix."users set video = -1 where ident = " . $page_owner);
							if ($page_owner == $_SESSION['userid']) {
								$_SESSION['video'] = "default.wmv";
							}
						}
					}
					$messages[] = "Your selected videos were deleted.";
				}
			}
        if (isset($_POST['defaultvideo'])) {
				$videodefault = (int) $_POST['defaultvideo'];
				if ($videodefault == -1) {
					db_query("update ".tbl_prefix."users set video = -1 where ident = " . $page_owner);
					$_SESSION['video'] = "default.wmv";
				} else {
					$videofilename = db_query("select filename from ".tbl_prefix."videos where ident = $videodefault and owner = " . $page_owner);
					if (sizeof($videofilename) == 1) {
						$videofilename = $videofilename[0]->filename;
						if ($page_owner == $_SESSION['userid']) {
							$_SESSION['video'] = $videofilename;
						}
						db_query("update ".tbl_prefix."users set video = $videodefault where ident = " . $page_owner);
					}
				}
			}
        if (isset($_POST['description']) && sizeof($_POST['description'] > 0)) {
				foreach($_POST['description'] as $videoid => $newdescription) {
					$videoid = (int) $videoid;
					$newdescription = addslashes($newdescription);
					$result = db_query("select description from ".tbl_prefix."videos where ident = $videoid and owner = " . $page_owner);
					if (sizeof($result) > 0) {
						if ($result[0]->description != $newdescription) {
							db_query("update ".tbl_prefix."videos set description = '$newdescription' where ident = $videoid");
						}
					}
				}
			}
}                                
// Upload a new video ...
		if (isset($_POST['action']) && $_POST['action'] == "videos:add" && logged_on) {
		
			if (isset($_POST['videodescription']) && isset($_POST['videodefault'])
				&& isset($_FILES['videofile']['name'])) {
				
				$messages[] = "Attempting to upload video file ...";
				$file_extension=explode('.',$_FILES['videofile']['name']);
                                $file_extension=strtolower($file_extension[count($file_extension)-1]);
                                $messages[] = "Assuming video is ".$file_extension."...";
				$ok = true; 
				$templocation = $_FILES['videofile']['tmp_name'];
				
				if ($ok == true) {
					$numvideos = db_query("select count(ident) as numvideos from ".tbl_prefix."videos where owner = " . $page_owner);
					$numvideos = (int) $numvideos[0]->numvideos;
					if ($numvideos >= $_SESSION['icon_quota']) {
						$ok = false;
						$messages[] = "You have already met your video quota. You must delete some videos before you can upload any new ones.";
					}
				}
					$save_file = $page_owner . "_" . time() .".". $file_extension;
					$save_location = path . "_videos/data/" . $save_file;
					if (move_uploaded_file($_FILES['videofile']['tmp_name'], $save_location)) {
                                                $mplayerrun=run("mplayer:run",$save_location);
						if ($mplayerrun['result']==0){
                                                  $video_info=join("\n",$mplayerrun['description']);
                                                  $video_thumb=$save_file.".jpg";
                                                }else{
                                                  $video_info="No information about video:".
                                                  $mplayerrun['result'];
                                                  $messages[]="<pre>".
                                                  print_r($mplayerrun['debug'],1)."</pre>";
                                                  $messages[]="No information about video:".
                                                  $mplayerrun['result'];
                                                  $video_thumb="-1";
                                                }
						$filedescription = addslashes($_POST['videodescription']);
						db_query(
                                                    "insert into ".tbl_prefix.
                                                    "videos set filename = '".
                                                    $save_file."', owner = " .
                                                    $page_owner . ", description ='". $filedescription."',video_info='".
                                                    $video_info."', video_thumb='".
                                                    $video_thumb."'"
                                                    );
						if ($_POST['videodefault'] == "yes") {
							$ident = (int) db_id();
							db_query("update ".tbl_prefix."users set video = $ident where ident = " . $page_owner);
							$_SESSION['video'] = $save_file;
						}
						$messages[] = "Your video was uploaded successfully.";
												
					} else {
						$messages[] = "An unknown error occurred when saving your video. If this problem persists, please let us know and we'll do all we can to fix it quickly.";
					}

				}
				
			}
		
?>