<?php
	global $profile_id;
	$profile_id = (int) $profile_id;
	
	global $name_cache;
	
		$result = db_query("select value from ".tbl_prefix."profile_data where owner = '$profile_id' and name='weblog_title'");
                if (!empty($result[0]->value)){
                   $run_result = stripslashes($result[0]->value);
                }else{
                  $run_result=run("profile:display:name").' :: Weblog';
                }
?>
