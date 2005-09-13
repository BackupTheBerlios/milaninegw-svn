<?php
	/**************************************************************************\
	* eGroupWare - E-Mail	  					           *
	* http://www.egroupware.org						   *
	* Based on Aeromail by Mark Cushman <mark@cushman.net>			   *
	*          http://the.cushman.net/					   *
	* --------------------------------------------				   *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.						   *
	\**************************************************************************/

	/* $Id: functions.inc.php,v 1.120 2004/01/27 16:27:24 reinerj Exp $ */

	$d1 = strtolower(substr(APP_INC,0,3));
	if($d1 == 'htt' || $d1 == 'ftp' )
	{
		echo "Failed attempt to break in via an old Security Hole!<br>\n";
		$GLOBALS['phpgw']->common->phpgw_exit();
	}
	unset($d1);
	
	// ----  Turn Off Magic Quotes Runtime    -----
	/*!
	@concept Turn Off Magic Quotes Runtime
	@discussion magic_quotes_runtime essentially handles slashes when communicating with databases.
	PHP MANUAL says:
		If magic_quotes_runtime is enabled, most functions that return data from any sort of 
		external source including databases and text files will have quotes escaped with a backslash.
	this is undesirable - turn it off.
	*/
	set_magic_quotes_runtime(0);

?>
