<?php
  /**************************************************************************\
  * eGroupWare - Coppermine configuration file                               *
  * http://www.eGroupWare.org                                                *
  * --------------------------------------------                             *
  * This program is free software; you can redistribute it and/or modify it  *
  * under the terms of the GNU General Public License as published by the    *
  * Free Software Foundation; either version 2 of the License, or (at your   *
  * option) any later version.                                               *
  \**************************************************************************/

  /* $Id: class.db_tools.inc.php,v 1.27 2004/08/15 20:58:12 ralfbecker Exp $ */
  
// Coppermine configuration file

$GLOBALS['phpgw_info']['flags'] = array(
   'currentapp'              => 'cpg',
   'noheader'                => false,
   'nonavbar'                => false,
   'enable_nextmatchs_class' => True,
   'disable_Template_class'  => True
);
include_once('../header.inc.php');

if ($GLOBALS['phpgw_info']['server']['db_type'] != 'mysql')
{
	echo "<h1>Error: Coppermine supports only MySQL !!!</h1>\n";
	$GLOBALS['phpgw']->common->phpgw_exit();
}
// MySQL configuration
$CONFIG['dbserver'] = $GLOBALS['phpgw_info']['server']['db_host']; // Your database server
$CONFIG['dbuser'] = $GLOBALS['phpgw_info']['server']['db_user'];   // Your mysql username
$CONFIG['dbpass'] = $GLOBALS['phpgw_info']['server']['db_pass'];   // Your mysql password
$CONFIG['dbname'] = $GLOBALS['phpgw_info']['server']['db_name'];   // Your mysql database name


// MySQL TABLE NAMES PREFIX
$CONFIG['TABLE_PREFIX'] =                'phpgw_cpg_';
?>