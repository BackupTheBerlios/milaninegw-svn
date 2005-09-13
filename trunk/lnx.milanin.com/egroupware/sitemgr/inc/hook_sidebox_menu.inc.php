<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - Web Content Management                              *
	* http://www.egroupware.org                                                *
	* Written by Pim Snel <pim@lingewoud.nl>                                   *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: hook_sidebox_menu.inc.php,v 1.3 2004/02/22 23:32:56 ralfbecker Exp $ */

{

 /*
	This hookfile is for generating an app-specific side menu used in the idots 
	template set.

	$menu_title speaks for itself
	$file is the array with link to app functions

	display_sidebox can be called as much as you like
 */

	$menu_title = lang('Website') . ' ' . $GLOBALS['Common_BO']->sites->current_site['site_name'];
	$file = $GLOBALS['Common_BO']->sitemenu;
	display_sidebox($appname,$menu_title,$file);
	$file = $GLOBALS['Common_BO']->othermenu;
	if ($file)
	{
		$menu_title = lang('Other websites');
		display_sidebox($appname,$menu_title,$file);
	}
	$menu_title = lang('Documentation');
	$docs = $GLOBALS['phpgw_info']['server']['webserver_url'].'/sitemgr/doc/';
	$file = array(
		array(
			'text'   => 'SiteMgr manual',
			'link'   => $docs.'sitemgr.html',
			'target' => 'docs'
		),
	);
	display_sidebox($appname,$menu_title,$file);
}
?>
