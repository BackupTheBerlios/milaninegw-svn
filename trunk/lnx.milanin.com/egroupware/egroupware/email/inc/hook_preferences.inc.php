<?php
  /**************************************************************************\
  * eGroupWare                                                             *
  * http://www.egroupware.org                                              *
  * Written by Joseph Engo <jengo@phpgroupware.org>                          *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: hook_preferences.inc.php,v 1.18 2004/02/05 17:54:16 angles Exp $ */
{
	$title = $appname;
	$file = Array(
		'E-Mail Preferences'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.uipreferences.preferences')),
		'Extra E-Mail Accounts'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.uipreferences.ex_accounts_list')),
		'E-Mail Filters'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.uifilters.filters_list')),
		'E-Mail Clear Cache'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.boaction.clearcache'))
	);
	// relfbecker recommends NOT using a version test for xslt check
	if (is_object($GLOBALS['phpgw']->xslttpl))
	{
		$phpgw_before_xslt = False;
	}
	else
	{
		$phpgw_before_xslt = True;
	}
	// now display according to the version of the template system in use
	if ($phpgw_before_xslt == True)
	{
		// the is the OLD, pre-xslt way to display pref items
		display_section($appname,$title,$file);
	}
	else
	{
		// this is the xslt template era
		display_section($appname,$file);
	}
	/*
	$this_ver = $GLOBALS['phpgw_info']['server']['versions']['phpgwapi'];
	$pre_xslt_ver = '0.9.14.0.1.1';
	if (function_exists(amorethanb))
	{
		if (($this_ver)
		&& (amorethanb($this_ver, $pre_xslt_ver)))
		{
			// this is the xslt template era
			display_section($appname,$file);
		}
		else
		{
			display_section($appname,$title,$file);
		}
	}
	else
	{
		if (($this_ver)
		&& ($GLOBALS['phpgw']->common->cmp_version_long($this_ver, $pre_xslt_ver)))
		{
			// this is the xslt template era
			display_section($appname,$file);
		}
		else
		{
			display_section($appname,$title,$file);
		}
	}
	*/
}
?>
