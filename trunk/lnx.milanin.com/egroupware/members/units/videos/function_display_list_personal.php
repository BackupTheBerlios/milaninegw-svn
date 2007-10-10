<?
global $page_owner;
global $videos;
global $selected_video;
global $selected_video_id;

$url = url;

if (sizeof($videos) > 0) {
  $run_result.="<script type=\"text/javascript\">
<!--
  var prev=\"playing_now_".$selected_video_id."\"
  //-->
  </script>
";
  $run_result.="<ul>\n";
  foreach($videos as $video) {
    $run_result.="\t<li><span id=\"playing_now_".$video->ident."\">".
    ( ( basename($selected_video) != $video->filename) 
      ? ""
      : "Playing :: "
    ).
    "<a href=\"".url."_videos/data/".$video->filename.
    "\" onclick=\"PlayIt(this.href,'playing_now_".$video->ident."');return false\">".
    $video->description.
    "</a></span></li>\n";
  }
}else{
  $run_result="<center><p>No videos in library</p></center>";
}
/*		
	// If we have some icons, display them; otherwise explain that there isn't anything to edit

			
			$body .= <<< END
		<form action="" method="post" />		
			<p>
				Site videos are small movies that act as a representative video throughout the system.
			</p>
END;
			
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

		$run_result .= $body;*/
?>