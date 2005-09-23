<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - Web Content Management                              *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: security.inc.php,v 1.4 2004/02/10 14:56:34 ralfbecker Exp $ */

	// Security precaution: prevent script tags: <script>, <javascript "">, etc.
	foreach ($_GET as $secvalue)
	{
		if (eregi("<[^>]*script*\"?[^>]*>", $secvalue)) 
		{
			die("A security breach has been attempted and refused.");
		}
	}

	// Security precaution: don't let anyone call xxx.inc.php files or
    // construct URLs with relative paths (ie, /dir1/../dir2/)
	// also deny direct access to blocks.
    if (eregi("\.inc\.php",$_SERVER['PHP_SELF']) || eregi("block-.*\.php",$_SERVER['PHP_SELF']) || ereg("\.\.",$_SERVER['PHP_SELF'])) 
	{
		die("Invalid URL");
	}
?>
