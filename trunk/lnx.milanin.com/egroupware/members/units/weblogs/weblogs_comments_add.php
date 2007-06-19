<?php

	if (isset($parameter)) {
	
		$post = $parameter;
                if (logged_on) {
			$comment_name = $_SESSION['user_info_cache'][$_SESSION['userid']]->name ;
                        $label="Commenting as ".$comment_name;
		} else {
			$comment_name = "Guest";
                        $label="Your IP and UA will be shown with the comment";
                        $minilogin="<table>
                        	   <tr><td>Please, logon first if you are a member</td></tr>
                                   <tr><td>
                        	   <form id=\"minilogin\" action=\"https://".$_SERVER['SERVER_NAME']."/egroupware/login.php\" method=\"post\">
                        	   
                                   <label>U:&nbsp;<input type=\"text\" name=\"login\" id=\"username\" style=\"size: 50px\" title=\"Username\"/>
                                   </label>
                                   <label>P:&nbsp;<input type=\"password\" name=\"passwd\" id=\"password\" style=\"size: 50px\" />
                                   </label>
                                   <input type=\"hidden\" name=\"action\" value=\"log_on\" />
				   <input type=\"submit\" name=\"submit\" value=\"Go\" />
                                   <input type=\"hidden\" name=\"passwd_type\" value=\"text\"/>
				   <input type=\"hidden\" name=\"account_type\" value=\"u\"/>
                                   <input type=\"hidden\" name=\"phpgw_forward\" value=\"..".
                                   $_SERVER['REDIRECT_SCRIPT_URL']."#add_comment_form\" /></form>
                                   </td></tr></table>";
                                   /*"<!--".
                                   print_r($_SERVER,1)."
				   -->*/
		}
		$run_result.=$minilogin;
		$run_result .= <<< END
	
	<form action="" method="post" name="milanin_add_comment_form"
         id="milanin_add_comment_form">
	
	<a name="add_comment_form"><h2>Add a comment</h2></a>
	
END;

		$field = run("display:input_field",array("milanin_new_weblog_comment","","longtext"));
		if (logged_on) {
			$userid = $_SESSION['userid'];
		} else {
			$userid = -1;
		}
		$field .= <<< END
		
		<input type="hidden" name="action" value="weblogs:comment:add" />
		<input type="hidden" name="milanin_post_id" value="{$post->ident}" />
		<input type="hidden" name="milanin_owner" value="{$userid}" />
		
END;

		$run_result .= run("templates:draw", array(
		
								'context' => 'databox1',
								'name' => "Your comment text",
								'column1' => $field
		
							)
							);
							
		

		$run_result .= run("templates:draw", array(
		
								'context' => 'databox1',
								'name' => 'Your name',
								'column1' => "<label for=\"milanin_postedname\">$label
                    						<input type=\"text\" name=\"milanin_postedname\" 
                    						id=\"milanin_postedname\" 
                    						 value=\"".htmlentities($comment_name)."\" /></label>"
		
							)
							);
		
		$run_result .= run("templates:draw", array(
		
								'context' => 'databox1',
								'name' => '&nbsp;',
								'column1' => "<input type=\"submit\" ".((logged_on) ? "":"onclick=\"addField()\" "). "value=\"Add comment\" />"
		
							)
							);
							
		$run_result .= <<< END
	
	</form>
		
END;
		
	}

?>