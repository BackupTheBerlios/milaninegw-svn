<?php
{
	/**************************************************************************\
	* phpGroupWare - Calendar's Sidebox-Menu for idots-template                *
	* http://www.phpgroupware.org                                              *
	* Written by Pim Snel <pim@lingewoud.nl>                                   *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	
	/* $Id: hook_sidebox_menu.inc.php,v 1.8 2004/04/12 15:01:09 lkneschke Exp $ */

 /*
	This hookfile is for generating an app-specific side menu used in the idots 
	template set.

	$menu_title speaks for itself
	$file is the array with link to app functions

	display_sidebox can be called as much as you like
 */

	$menu_title = $GLOBALS['phpgw_info']['apps'][$appname]['title'] . ' '. lang('Menu');
	$preferences = ExecMethod('felamimail.bopreferences.getPreferences');
	$linkData = array
	(
		'menuaction'    => 'felamimail.uicompose.compose'
	);
	if($preferences['messageNewWindow'] == 1)
	{
		$file = Array(
			'Compose'   => "javascript:displayMessage('".$GLOBALS['phpgw']->link('/index.php',$linkData)."');"
		);
	}
	else
	{
		$file = Array(
			'Compose'   => $GLOBALS['phpgw']->link('/index.php',$linkData)
			#'_NewLine_'=>'', // give a newline
			#'INBOX'=>$GLOBALS['phpgw']->link('/index.php','menuaction=felamimail.uifelamimail.viewMainScreen')
		);
	}
	display_sidebox($appname,$menu_title,$file);

	if ($GLOBALS['phpgw_info']['user']['apps']['preferences'])
	{
		$mailPreferences = ExecMethod('felamimail.bopreferences.getPreferences');
		#_debug_array($mailPreferences);
		$menu_title = lang('Preferences');
		$file = array(
			'Preferences'       	  => $GLOBALS['phpgw']->link('/preferences/preferences.php','appname=felamimail'),
			'Manage Folders'	  => $GLOBALS['phpgw']->link('/index.php','menuaction=felamimail.uipreferences.listFolder')	
		);
		
		if($mailPreferences['imapEnableSieve'] == true)
		{
			$linkData = array
			(
				'menuaction'	=> 'felamimail.uisieve.editScript',
				'editmode'	=> 'filter'
			);
			$file['EMailfilter']	= $GLOBALS['phpgw']->link('/index.php',$linkData);

			$linkData = array
			(
				'menuaction'	=> 'felamimail.uisieve.editScript',
				'editmode'	=> 'vacation'
			);
			$file['Vacation']	= $GLOBALS['phpgw']->link('/index.php',$linkData);
		}
		
		display_sidebox($appname,$menu_title,$file);
	}

	if ($GLOBALS['phpgw_info']['user']['apps']['admin'])
	{
		$menu_title = lang('Administration');
		$file = Array(
			'Configuration' => $GLOBALS['phpgw']->link('/index.php','menuaction=felamimail.uifelamimail.hookAdmin')
		);
		display_sidebox($appname,$menu_title,$file);
	}
}
?>
