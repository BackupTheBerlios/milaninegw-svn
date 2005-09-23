<?php
  /**************************************************************************\
  * eGroupWare - Coppermine Setup                                            *
  * http://www.eGroupWare.org                                                *
  * Adapted Coppersmine's install.php by ralfbecker@outdoor-training.de      *
  * --------------------------------------------                             *
  * This program is free software; you can redistribute it and/or modify it  *
  * under the terms of the GNU General Public License as published by the    *
  * Free Software Foundation; either version 2 of the License, or (at your   *
  * option) any later version.                                               *
  \**************************************************************************/

  /* $Id: class.db_tools.inc.php,v 1.27 2004/08/15 20:58:12 ralfbecker Exp $ */
  
// ------------------------------------------------------------------------- //
// Coppermine Photo Gallery 1.3.2                                            //
// ------------------------------------------------------------------------- //
// Copyright (C) 2002-2004 Gregory DEMAR                                     //
// http://www.chezgreg.net/coppermine/                                       //
// ------------------------------------------------------------------------- //
// Updated by the Coppermine Dev Team                                        //
// (http://coppermine.sf.net/team/)                                          //
// see /docs/credits.html for details                                        //
// ------------------------------------------------------------------------- //
// This program is free software; you can redistribute it and/or modify      //
// it under the terms of the GNU General Public License as published by      //
// the Free Software Foundation; either version 2 of the License, or         //
// (at your option) any later version.                                       //
// ------------------------------------------------------------------------- //
// CVS version: $Id: install.php,v 1.3 2004/08/24 20:49:04 joestewart Exp $
// ------------------------------------------------------------------------- //

require('../cpg/include/sql_parse.php');

// ------------------------- SQL QUERIES TO CREATE TABLES ------------------ //

	$gallery_url_prefix = ($_SERVER['HTTPS']?'https://':'http://') . $_SERVER['HTTP_HOST']. '/'. dirname(dirname($_SERVER['PHP_SELF'])) . '/cpg/';
	
	/* done via eGW's setup & cpg/setup/tables_current.inc.php
	$db_schema = '../cpg/sql/schema.sql';
	$sql_query = fread(fopen($db_schema, 'r'), filesize($db_schema));
	*/
	$db_basic = '../cpg/sql/basic.sql';
	$sql_query .= fread(fopen($db_basic, 'r'), filesize($db_basic));
	
	// Insert the admin account
	// not necessary, accounts for authed eGW users are created on the fly and admin group is used for eGW admins
	//$sql_query .= "INSERT INTO CPG_users VALUES (1, 1, 'YES', '" . 'ralf' . "', '" . 'ralbec32' . "', NOW(), NOW(), '', '', '', '', '', '', '');\n";
	// Set configuration values for image package
	$sql_query .= "REPLACE INTO CPG_config VALUES ('thumb_method', '" . 'gd2' . "');\n";
	$sql_query .= "REPLACE INTO CPG_config VALUES ('impath', '" . '' . "');\n";
	$sql_query .= "REPLACE INTO CPG_config VALUES ('ecards_more_pic_target', '" . $gallery_url_prefix . "');\n";
	// Test write permissions for main dir
	if (!is_writable('.')) {
		$sql_query .= "REPLACE INTO CPG_config VALUES ('default_dir_mode', '0777');\n";
		$sql_query .= "REPLACE INTO CPG_config VALUES ('default_file_mode', '0666');\n";
	}
	// Update table prefix
	$sql_query = preg_replace('/CPG_/', 'phpgw_cpg_', $sql_query);
	
	$sql_query = remove_remarks($sql_query);
	$sql_query = split_sql_file($sql_query, ';');

	foreach($sql_query as $q) {
		if (! $oProc->query($q)) {
			$errors .= "mySQL Error: " . mysql_error() . "<br /><br />";
			return;
		}
	}
    
	// give default group access to Coppermine
	$defaultgroup = $GLOBALS['phpgw_setup']->add_account('Default','Default','Group',False,False);
	$GLOBALS['phpgw_setup']->add_acl('cpg','run',$defaultgroup);

?>
