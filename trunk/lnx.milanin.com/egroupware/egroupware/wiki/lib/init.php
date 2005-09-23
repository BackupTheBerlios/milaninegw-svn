<?php
	/**************************************************************************\
	* eGroupWare Wiki - general initialization of the old tavi code            *
	* http://www.egroupware.org                                                *
	* -------------------------------------------------                        *
	* Originaly from tavi, modified by RalfBecker@outdoor-training.de for eGW  *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	// $Id: init.php,v 1.17.2.2 2005/02/09 17:29:05 ralfbecker Exp $

	require('lib/defaults.php');

	$sessionid = isset($_GET['sessionid']) ? $_GET['sessionid'] : (isset($_COOKIE['sessionid']) ? $_COOKIE['sessionid'] : '');

	if (!$sessionid)
	{
		// uncomment the next line if wiki should use a eGW domain different from the first one defined in your header.inc.php
		// and of cause change the name accordingly ;-)
		// $GLOBALS['phpgw_info']['server']['default_domain'] = 'developers';

		$GLOBALS['phpgw_info']['flags'] = array(
			'disable_Template_class' => True,
			'login' => True,
			'currentapp' => 'login',
			'noheader'  => True,
		);
		include('../header.inc.php');
		$GLOBALS['phpgw_info']['flags']['currentapp'] = 'wiki';

		$c = CreateObject('phpgwapi.config','wiki');
		$c->read_repository();
		$config = $c->config_data;
		unset($c);

		if ($config['allow_anonymous'] && $config['anonymous_username'])
		{
			$sessionid = $GLOBALS['phpgw']->session->create($config['anonymous_username'],$config['anonymous_password'], 'text');
		}
		if (!$sessionid)
		{
			$GLOBALS['phpgw']->redirect('../login.php'.
				(isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']) ?
				'?phpgw_forward='.urlencode('/wiki/index.php?'.$_SERVER['QUERY_STRING']):''));
			$GLOBALS['phpgw']->phpgw_exit();
		}
		// we redirect to the same page again, as we cant reset some of the defines in the API
		if ($_GET['action'] != 'xml')	// for the xml-export, we cant do that and dont care for these settings
		{
			$GLOBALS['phpgw']->redirect_link('/wiki/index.php',urldecode($_SERVER['QUERY_STRING']));
		}
	}
	else
	{
		include('../header.inc.php');

		$c = CreateObject('phpgwapi.config','wiki');
		$c->read_repository();
		$config = $c->config_data;
	}
	// if we get here, we have a sessionid

	// anonymous sessions have no navbar !!!
	$GLOBALS['phpgw_info']['flags']['nonavbar'] = $config['allow_anonymous'] &&
		$config['anonymous_username'] == $GLOBALS['phpgw_info']['user']['account_lid'];

	$HomePage = (isset($config[wikihome])?$config[wikihome]:'eGroupWare');
	$InterWikiPrefix = (isset($config[InterWikiPrefix])?$config[InterWikiPrefix]:'EGroupWare');
	$EnableFreeLinks = (isset($config[Enable_Free_Links])?$config[Enable_Free_Links]:1);
	$EnableWikiLinks = (isset($config[Enable_Wiki_Links])?$config[Enable_Wiki_Links]:1);
	$EditWithPreview = (isset($config[Edit_With_Preview])?$config[Edit_With_Preview]:1);

	$UserName = $GLOBALS['phpgw_info']['user']['account_lid'];
	if (!($action == 'save' && !$Preview) && $action != 'admin' && !($action == 'prefs' && $Save) && $action != 'xml')
	{
		$GLOBALS['phpgw']->common->phpgw_header();
	}

	define('TemplateDir', 'template');

	$Charset = $GLOBALS['phpgw']->translation->charset();
	if (strtolower($Charset) == 'iso-8859-1')	// allow all iso-8859-1 extra-chars
	{
		$UpperPtn = "[A-Z\xc0-\xde]";
		$LowerPtn = "[a-z\xdf-\xff]";
		$AlphaPtn = "[A-Za-z\xc0-\xff]";
		$LinkPtn = $UpperPtn . $AlphaPtn . '*' . $LowerPtn . '+' .
			$UpperPtn . $AlphaPtn . '*(\\/' . $UpperPtn . $AlphaPtn . '*)?';
	}

	$WikiLogo = $GLOBALS['phpgw_info']['server']['webserver_url'] . '/phpgwapi/templates/default/images/logo.png';
	// use eGW's temp dir
	$TempDir = $GLOBALS['phpgw_info']['server']['temp_dir'];

	require('lib/url.php');
	require('lib/messages.php');

	$pagestore = CreateObject('wiki.sowiki');

	$FlgChr = chr(255);                     // Flag character for parse engine.

	$Entity = array();                      // Global parser entity list.

	// Strip slashes from incoming variables.

	if(get_magic_quotes_gpc())
	{
		$document = stripslashes($document);
		$categories = stripslashes($categories);
		$comment = stripslashes($comment);
		if (is_array($page))
		{
			$page['name'] = stripslashes($page['name']);
		}
		else
		{
			$page = stripslashes($page);
		}
	}

	// Read user preferences from cookie.

	$prefstr = isset($_COOKIE[$CookieName])
	? $_COOKIE[$CookieName] : '';

	if(!empty($prefstr))
	{
		if(ereg("rows=([[:digit:]]+)", $prefstr, $result))
		{ $EditRows = $result[1]; }
		if(ereg("cols=([[:digit:]]+)", $prefstr, $result))
		{ $EditCols = $result[1]; }
		if(ereg("user=([^&]*)", $prefstr, $result))
		{ $UserName = urldecode($result[1]); }
		if(ereg("days=([[:digit:]]+)", $prefstr, $result))
		{ $DayLimit = $result[1]; }
		if(ereg("auth=([[:digit:]]+)", $prefstr, $result))
		{ $AuthorDiff = $result[1]; }
		if(ereg("min=([[:digit:]]+)", $prefstr, $result))
		{ $MinEntries = $result[1]; }
		if(ereg("hist=([[:digit:]]+)", $prefstr, $result))
		{ $HistMax = $result[1]; }
		if(ereg("tzoff=([[:digit:]]+)", $prefstr, $result))
		{ $TimeZoneOff = $result[1]; }
	}
