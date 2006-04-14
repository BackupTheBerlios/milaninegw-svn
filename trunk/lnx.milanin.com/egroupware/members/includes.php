<?php

	ini_set("display_errors", "1");
	error_reporting(E_ERROR | E_WARNING | E_PARSE);

	// ELGG system includes
	
	// System constants: set values as necessary
	// Supply your values within the second set of speech marks in the pair
	// i.e., define("system constant name", "your value");
	
		// Name of the site (eg Elgg, Apcala, University of Bogton's Learning Landscape, etc)
			define("sitename", "Business Club MilanIN");
		// External URL to the site (eg http://milanin.gfdsa.org/)
		// NB: **MUST** have a final slash at the end
			define("url",((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') ? "https" : "http")
		                                . "://".$_SERVER['SERVER_NAME']."/members/");
		// Physical path to the files (eg /home/elggserver/httpdocs/)
		// NB: **MUST** have a final slash at the end
			define("path", "/web/htdocs/www.milanin.com/home/members/");
		// Email address of the master admin (eg elgg-admin@bogton.edu)
			define("email", "webmaster@milanin.com");

	// Database config:
	
		// Database server (eg localhost)
			define("db_server", "62.149.150.38");
		// Database username
			define("db_user", "Sql73134");
		// Database password
			define("db_pass", "4e455633");
		// Database name
			define("db_name", "Sql73134_3");
		// Tables names prefix
                        define("tbl_prefix","members_");
		// Groupware database name
			define("db_gw_name", "Sql73134_1");
				
	//Egroupware bridge
                        define("egw_bridge",1);
                        
        // Load required system files: do not edit this line.
		require("includes_system.php");
		
	/***************************************************************************
	*	INSERT PLUGINS HERE
	*	Eventually this should be replaced with plugin autodiscovery
	****************************************************************************/
	// XMLRPC
	//	@include(path . "units/rpc/main.php");
	
	/***************************************************************************
	*	CONTENT MODULES
	*	This should make languages easier, although some kind of
	*	selection process will be required
	****************************************************************************/
		
	// General
		@include(path . "content/general/main.php");
	// Main index
		@include(path . "content/mainindex/main.php");
	// User-related
		@include(path . "content/users/main.php");
	
	/***************************************************************************
	*	START-OF-PAGE RUNNING
	****************************************************************************/
	
		run("init");
		
?>
