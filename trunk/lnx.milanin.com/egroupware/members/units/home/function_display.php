<?php

	global $profile_id;
	$profile_id = (int) $profile_id;
		
		$result = db_query("select value from home_data where  owner = $profile_id AND name = 'body'");
	if ($result[0]->value){
          $run_result = $result[0]->value;
        }else{
          $result = db_query("select name from users where ident = '$profile_id'");
          $run_result = $result[0]->name." did not publish his Home Page yet.";
        }
?>