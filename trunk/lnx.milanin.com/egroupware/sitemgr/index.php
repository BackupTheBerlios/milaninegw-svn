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

	/* $Id: index.php,v 1.5 2004/02/14 14:35:48 ralfbecker Exp $ */

	$GLOBALS['phpgw_info']['flags'] = array
	(
			'currentapp' => 'sitemgr',
			'noheader'   => True,
			'nonavbar'   => True,
			'noapi'      => False
	);
	include('../header.inc.php');

	$CommonUI = CreateObject('sitemgr.Common_UI');

	if (!$CommonUI->do_sites_exist && $GLOBALS['phpgw_info']['user']['apps']['admin'])
	{
		$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=>'sitemgr.Sites_UI.edit'));
	}
	$CommonUI->DisplayIFrame();

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
