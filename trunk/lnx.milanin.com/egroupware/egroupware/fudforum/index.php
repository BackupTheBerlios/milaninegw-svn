<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: index.php,v 1.8 2004/07/08 14:25:46 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

	if (!empty($_COOKIE['domain'])) {
		$dom = sprintf("%u", crc32(stripslashes($_COOKIE['domain'])));
	} else if (!empty($_GET['domain'])) {
		$dom = sprintf("%u", crc32(stripslashes($_GET['domain'])));
	} else {
		$d = opendir('.');
		while (($dom = readdir($d)) && !is_numeric($dom));
		closedir($d);

		if (!$dom) {
			/* should not happen */
			exit("Fatal Error");
		}
	}

	/* this is needed for installations where REQUEST_URI is not avaliable */
	if (empty($_SERVER['REQUEST_URI'])) {
		if (!empty($_SERVER['SCRIPT_NAME'])) {
			$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
		} else if (!empty($_SERVER['PHP_SELF'])) {
			$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'];
		} else if (!empty($_ENV['PATH_INFO'])) {
			$_SERVER['REQUEST_URI'] = $_SERVER['PATH_INFO'];
		} else {
			exit("Fatal Error cannot determine forum path");
		}
	}

	$prefix = strpos($_SERVER["REQUEST_URI"], 'index.php') ? dirname($_SERVER["REQUEST_URI"]) : $_SERVER["REQUEST_URI"]; 

	$path = $prefix . "/" . $dom . "/index.php?" . $_SERVER["QUERY_STRING"];
	header("Location: ".$path);
?>