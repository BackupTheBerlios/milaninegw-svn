<?php

	// Allow ".tbl_prefix."users.to post to a weblog if they have permission
	
		if (run("permissions:check", "weblog")) {
			
            /*<script language="javascript" type="text/javascript">
					function show(whichLayer) {
							if (document.getElementById)
							{
								// this is the way the standards work
								var style2 = document.getElementById(whichLayer).style;
								style2.display = style2.display? "":"block";
							}
							else if (document.all)
							{
								// this is the way old msie versions work
								var style2 = document.all[whichLayer].style;
								style2.display = style2.display? "":"block";
							}
							else if (document.layers)
							{
								// this is the way nn4 works
								var style2 = document.layers[whichLayer].style;
								style2.display = style2.display? "":"block";
							}
					}
					function hide(whichLayer) {
							if (document.getElementById)
							{
								// this is the way the standards work
								var style2 = document.getElementById(whichLayer).style;
								style2.display = style2.display? "":"none";
							}
							else if (document.all)
							{
								// this is the way old msie versions work
								var style2 = document.all[whichLayer].style;
								style2.display = style2.display? "":"none";
							}
							else if (document.layers)
							{
								// this is the way nn4 works
								var style2 = document.layers[whichLayer].style;
								style2.display = style2.display? "":"none";
							}
					}
				</script>*/ 
			$title = "
				<p>
					<a href=\"".url."_weblog/edit.php\">Click here to post to this weblog.</a>
				</p>";




/*			$body = <<< END
			<div id="add_weblog_post" style="display:block; width: 90%">
			
END;
			$body .= run("weblogs:posts:add");
			$body .= <<< END
			
				<p>
					<a href="javascript:hide('add_weblog_post');">Click here to hide this form.</a>
				</p>
			
END;
		
			$body .= <<< END
			
			</div>
			
END;
*/
			$run_result .= run("templates:draw", array(
									'context' => 'databoxvertical',
									'name' => $title,
									'contents' => $body
								)
								);


		}

?>
