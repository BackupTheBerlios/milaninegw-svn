<?php

	// Vaguely useful database global variables
		global $db_connection;
		global $db;
	
	// Establish a connection to the server
		$db_connection = @mysql_pconnect(db_server, db_user, db_pass)
			or die(mysql_error());
	
	// Select the correct database
		$db = mysql_select_db(db_name);
		mysql_query("SET CHARACTER SET 'utf8'", $db_connection);

?>
