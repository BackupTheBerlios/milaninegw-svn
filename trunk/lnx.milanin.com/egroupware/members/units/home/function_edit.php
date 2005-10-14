<?php

	global $page_owner;

	//if (run("permissions:check", "home")) {
	
		$profile_username = run("users:id_to_name",$page_owner);
		
		$body = "<form action=\"".url . $profile_username ."/home/\" method=\"post\">";
	
		// Cycle through all defined profile detail fields and display them
                $result = db_query("select value from home_data where  owner = $page_owner AND name = 'title'");
                $body .='<p>Title: <input type="text" name="home[title]" id="home[title]" style="width: 85%;" value=\''.$result[0]->value.'\'/>'."</p>\n";
		
                $result = db_query("select value from home_data where  owner = $page_owner AND name = 'body'");
                $body .= '<p>Page Content:<br/><textarea name="home[data]" id="home[data]" style="width: 95%; height: 500px">'.$result[0]->value."</textarea></p>\n";
		$body .= <<< END

	<p align="center">
		<label>
			Submit details:
			<input type="submit" name="submit" value="Go" />
		</label>
		<input type="hidden" name="action" value="home:edit" />
		<input type="hidden" name="profile_id" value="$page_owner" />
	</p>

</form>
END;

		$run_result .= $body;
	
	//}
	
?>