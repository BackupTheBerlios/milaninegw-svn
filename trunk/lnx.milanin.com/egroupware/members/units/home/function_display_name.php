<?php

	global $profile_id;
	$profile_id = (int) $profile_id;
		
		$result = db_query("select value from home_data where  owner = $profile_id AND name = 'title'");
	if ($result[0]->value){
          $run_result = $result[0]->value;
        }else{
          $result = db_query("select name from ".tbl_prefix."users where ident = '$profile_id'");
          $run_result = $result[0]->name."'s Home Page";
        }
?>