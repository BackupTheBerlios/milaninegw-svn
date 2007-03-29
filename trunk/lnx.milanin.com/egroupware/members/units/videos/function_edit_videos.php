<?
global $page_owner;
	$url = url;
		
	// Get all videos associated with a user
		$videos = db_query("select * from ".tbl_prefix."videos where owner = $page_owner");
		if ($page_owner != $_SESSION['userid']) {
			$currentvideo = db_query("select videos.filename, ".tbl_prefix."users.video from ".tbl_prefix."users left join ".tbl_prefix."videos videos 
                        on videos.ident = ".tbl_prefix."users.video where ".tbl_prefix."users.ident = $page_owner");
			$currentvideo = $currentvideo[0]->filename;
		} else {
			$currentvideo = $_SESSION['video'];
		}

		$body = <<< END
		<h2>
			Site videos
		</h2>
END;
		
	// If we have some icons, display them; otherwise explain that there isn't anything to edit
		if (sizeof($videos) > 0) {
			
			$body .= <<< END
		<form action="" method="post" />		
			<p>
				Site videos are small movies that act as a representative video throughout the system.
			</p>
END;
			foreach($videos as $video) {
                                if ($video->video_thumb==-1){
                                  $video_thumb="default.jpg";
                                }else{
                                  $video_thumb=$video->video_thumb;
                                }
				$name = <<< END
						<label>Delete:
							<input type="checkbox" name="videos_delete[]" value="{$video->ident}" />
						</label>
END;
				$column1 = <<< END
						<p align="center">
                                                <a href="{$url}_videos/data/{$video->filename}" target="_blank">
                                                <img width="128" height="128"
                                                 src="{$url}_videos/data/{$video_thumb}"/>
                                                 </a>
                                                </p>
END;
				if ($video->filename == $currentvideo) {
					$checked = "checked=\"checked\"";
				} else {
					$checked = "";
				}
				$defaultvideo = htmlentities(stripslashes($video->description));
				$column3 = <<< END
						<label>Name:
							<input	type="text" name="description[{$video->ident}]" 
									value="{$defaultvideo}" />
						</label><br />
						<label>Default: <input type="radio" name="defaultvideo" value="{$video->ident}" {$checked} /></label>
END;
                                $column2="<pre>".$video->video_info."</pre>\n";

				$body .= run("templates:draw", array(
								'context' => 'databox',
								'name' => $column1,
								'column1' => $column2,
                                                                'column2' => $column3.$name
                                                                
							)
							);

			}
			
			if ($_SESSION['video'] == "default.wmv") {
				$checked = "checked = \"checked\"";
			} else {
				$checked = "";
			}
			$column1 = <<< END
						<label>No default:
						<input type="radio" name="defaultvideo" value="-1" {$checked} /></label>
END;
			$body .= run("templates:draw", array(
							'context' => 'databox',
							'column1' => $column1
						)
						);
			$body .= <<< END
				<p align="center">
					<input type="hidden" name="action" value="videos:edit" />
					<input type="submit" value="Save" />		
				</p>
			</form>
END;
			
		} else {

	$body .= <<< END
		<p>
			You do not have any site videos loaded yet.
		</p>
END;

		}

		$run_result .= $body;
?>