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

	/* $Id: index.php,v 1.10.2.1 2004/08/27 18:24:41 ralfbecker Exp $ */

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp' => 'sitemgr-link',
		'noheader'   => True,
		'nonavbar'   => True,
		'noapi'      => False
	);
	$parentdir = dirname(dirname($_SERVER['SCRIPT_FILENAME']));
	if (file_exists($parentdir.'/header.inc.php'))
	{
		include($parentdir.'/header.inc.php');
	}
	else
	{
		die("You need to make sure the sitemgr-link app is in the eGroupWare directory.  Under *nix you can make a symbolic link.");
	}
	$sites_bo = createobject('sitemgr.Sites_BO');
	$siteinfo = $sites_bo->get_currentsiteinfo();
	$location = $siteinfo['site_url'];
	if ($location && file_exists($siteinfo['site_dir'] . '/functions.inc.php'))
	{
		$location .= '?sessionid='.@$GLOBALS['phpgw_info']['user']['sessionid'] .
					'&kp3=' . @$GLOBALS['phpgw_info']['user']['kp3'] .
					'&domain=' . @$GLOBALS['phpgw_info']['user']['domain'];
		$GLOBALS['phpgw']->redirect($location);
		exit;
	}
	else
	{
		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();
		$aclbo = CreateObject('sitemgr.ACL_BO', True);
		echo '<table width="50%"><tr><td>';
		if ($aclbo->is_admin())
		{
			echo lang('Before the public web site can be viewed, you must configure the various locations and preferences.  Please go to the sitemgr setup page by following this link:') . 
			  '<a href="' . 
			  $GLOBALS['phpgw']->link('/index.php', 'menuaction=sitemgr.Common_UI.DisplayPrefs') . 
			  '">' .
			  lang('sitemgr setup page') .
			  '</a>. ' .
			  lang('Note that you may get this message if your preferences are incorrect.  For example, if config.inc.php is not found in the directory that you specified.');
		}
		else
		{
			echo lang('Your administrator has not yet setup the web content manager for public viewing.  Go bug your administrator to get their butt in gear.');
		}
		echo '</td></tr></table>';
		$GLOBALS['phpgw']->common->phpgw_footer();
	}
?>
