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

	/* $Id: functions.inc.php,v 1.10 2004/04/14 13:56:17 ralfbecker Exp $ */

	/*******************************************************\
	* This file is for global functions needed by the       *
	* sitemgr-site program.  This includes:                 *
	*    - phpgw_link($url, $extravars)                     *
	*    - sitemgr_link2($url, $extravars)                  *
	\*******************************************************/

	function phpgw_link($url, $extravars = '')
	{
		return $GLOBALS['phpgw']->session->link($url, $extravars);
	} 

	function sitemgr_link2($url, $extravars = '')
	{
		//I remove the URL argument for sitemgr_link,since it should always be index.php
		//which shouldn't be needed since the webserver interprets '/' as '/index.php'.
		return sitemgr_link($extravars);
	}

	function sitemgr_link($extravars = '')
	{
		// Change http://xyz/index.php?page_name=page1 to
		// http://xyz/page1/ if the htaccess stuff is enabled
		if (!is_array($extravars))
		{
			parse_str($extravars,$extravarsnew);
			$extravars = $extravarsnew;
		}
		if ($extravars['page_name'] != '' && $GLOBALS['sitemgr_info']['htaccess_rewrite'])
		{
			$url = '/'.$extravars['page_name'];
			unset($extravars['page_name']);
		}

		// In certain instances (wouldn't it be better to fix these instances? MT)
		// a url may look like this: 'http://xyz//hi.php' or
		// like this: '//index.php?blahblahblah' -- so the code below will remove
		// the inappropriate double slashes and leave appropriate ones
		$url = $GLOBALS['sitemgr_info']['site_url'] . $url;
		$url = substr(ereg_replace('([^:])//','\1/','s'.$url),1);

		if (!isset($GLOBALS['phpgw_info']['server']['usecookies']) || !$GLOBALS['phpgw_info']['server']['usecookies'])
		{
			$extravars['sessionid'] = @$GLOBALS['phpgw_info']['user']['sessionid'];
			$extravars['kp3']       = $_GET['kp3'] ? $_GET['kp3'] : $GLOBALS['phpgw_info']['user']['kp3'];
			$extravars['domain']    = @$GLOBALS['phpgw_info']['user']['domain'];
		}
		// build the extravars string from a array
		$vars = array();
		foreach($extravars as $key => $value)
		{
			$vars[] = urlencode($key).'='.urlencode($value);
		}
		return $url . (count($vars) ? '?'.implode('&',$vars) : '');
	}
?>
