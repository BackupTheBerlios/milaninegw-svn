<?
	global $page_owner;
        
	// Allow the user to add more videos
		$numvideos = db_query("select count(ident) as videonum from ".tbl_prefix."videos where owner = " . $page_owner);
		$numvideos = $numicons[0]->videonum;
		if ($page_owner != $_SESSION['userid']) {
			$videosquota = db_query("select icon_quota from ".tbl_prefix."users where ident = " . $page_owner);
			$videosquota = $videosquota[0]->icon_quota;
		} else {
			$videosquota = $_SESSION['icon_quota'];
		}
                if ($numvideos < $videosquota) {

			$body = <<< END
			<p>
				<h2>Upload a new video</h2>
			</p>
			<p>
				Upload a video for this profile below.
				You may upload up to
				{$videosquota} videos in total.
                                And each video can be up to 2M in size
			</p>
			<form action="" method="post" enctype="multipart/form-data">
END;
			$name = "<label for=\"videofile\">Video to upload:</label>";
			$column1 = "
						<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"2000000\" />
						<input name=\"videofile\" id=\"iconfile\" type=\"file\" />
						";
                        $body .= run("templates:draw", array(
							'context' => 'databox',
							'name' => $name,
							'column1' => $column1
						)
						);
                        $name = "<label for=\"videodescription\">Video description:</label>";
			$column1 = "<input type=\"text\" name=\"videodescription\" id=\"videodescription\" value=\"\" />";
			$body .= run("templates:draw", array(
							'context' => 'databox',
							'name' => $name,
							'column1' => $column1
						)
						);
			$name = "<label for=\"videodefault\">Make this the default video:</label>";
			$column1 = "
							<select name=\"videodefault\" id=\"videodefault\">
								<option value=\"yes\">Yes</option>
								<option value=\"no\">No</option>
							</select>
						";
			$body .= run("templates:draw", array(
							'context' => 'databox',
							'name' => $name,
							'column1' => $column1
						)
						);
			$body .= <<< END
						<p align="center"><input type="hidden" name="action" value="videos:add" />
							<input type="submit" value="Upload new video" /></p>
			</form>

END;
		} else {
			$body = <<< END
			<p>
				The video quota is {$videosquota} and you have {$numvideos} videos uploaded.
				You may not upload any more videos until you have deleted some.
			</p>
END;
		}

	$run_result .= $body;
		
?>
