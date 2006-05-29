<?php

	$ident = (int) $parameter;
	
	$result = db_query("select name from ".tbl_prefix."users where ident = $ident");
	$run_result .= stripslashes($result[0]->name);

?>