<?php
	/**************************************************************************\
	* eGroupWare                                                               *
	* http://www.egroupware.org                                                *
	* This file was originaly written by Dan Kuykendall                        *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: header.inc.php.template,v 1.55.2.1 2004/08/03 14:05:35 reinerj Exp $ */

	/**************************************************************************\
	* !!!!!!! EDIT THESE LINES !!!!!!!!                                        *
	* This setting allows you to easily move the include directory and the     *
	* base of the eGroupWare install. Simple edit the following 2 lines with   *
	* the absolute path to fit your site, and you should be up and running.    *
	\**************************************************************************/

	define('PHPGW_SERVER_ROOT','/web/htdocs/www.milanin.com/home/egroupware');
	define('PHPGW_INCLUDE_ROOT','/web/htdocs/www.milanin.com/home/egroupware');
	$GLOBALS['phpgw_info']['server']['header_admin_user'] = 'admin';
	$GLOBALS['phpgw_info']['server']['header_admin_password'] = '200820e3227815ed1756a6b531e7e0d2';
	$GLOBALS['phpgw_info']['server']['setup_acl'] = '';

	/* eGroupWare domain-specific db settings */
	$GLOBALS['phpgw_domain']['default'] = array(
		'db_host' => '62.149.150.38',
		'db_port' => '3306',
		'db_name' => 'Sql73134_1',
		'db_user' => 'Sql73134',
		'db_pass' => '4e455633',
		// Look at the README file
		'db_type' => 'mysql',
		// This will limit who is allowed to make configuration modifications
		'config_user'   => 'configme',
		'config_passwd' => '200820e3227815ed1756a6b531e7e0d2'
	);

	/*
	** If you want to have your domains in a select box, change to True
	** If not, users will have to login as user@domain
	** Note: This is only for virtual domain support, default domain users can login only using
	** there loginid.
	*/
	$GLOBALS['phpgw_info']['server']['show_domain_selectbox'] = False;

	$GLOBALS['phpgw_info']['server']['db_persistent'] = True;

	/*
	** eGroupWare can handle session management using the database or 
	** the session support built into PHP4 which usually gives better
	** performance. 
	** Your choices are 'db' or 'php4'
	*/
	$GLOBALS['phpgw_info']['server']['sessions_type'] = 'php4';

	/* Select which login template set you want, most people will use default */
	$GLOBALS['phpgw_info']['login_template_set'] = 'idots';

	/* This is used to control mcrypt's use */
	$GLOBALS['phpgw_info']['server']['mcrypt_enabled'] = True;
	/* Set this to 'old' for versions < 2.4, otherwise the exact mcrypt version you use. */
	$GLOBALS['phpgw_info']['server']['versions']['mcrypt'] = '';

	/*
	** This is a random string used as the initialization vector for mcrypt
	** feel free to change it when setting up eGrouWare on a clean database,
	** but you must not change it after that point!
	** It should be around 30 bytes in length.
	*/
	$GLOBALS['phpgw_info']['server']['mcrypt_iv'] = 'yY1j85r4f1JAMuJf8JYVpyBvCukV5r';

	if(!isset($GLOBALS['phpgw_info']['flags']['nocachecontrol']) || !$GLOBALS['phpgw_info']['flags']['nocachecontrol'])
	{
		header('Cache-Control: no-cache, must-revalidate');  // HTTP/1.1
		header('Pragma: no-cache');                          // HTTP/1.0
	}
	else
	{
		// allow caching by browser
		session_cache_limiter(PHP_VERSION >= 4.2 ? 'private_no_expire' : 'private');
	}

	/* debugging settings */
	define('DEBUG_APP',  False);
	define('DEBUG_API',  False);
	define('DEBUG_DATATYPES',  True);
	define('DEBUG_LEVEL',  3);
	define('DEBUG_OUTPUT', 2); /* 1 = screen,  2 = DB. For both use 3. */
	define('DEBUG_TIMER', False);

	function perfgetmicrotime()
	{
		list($usec, $sec) = explode(' ',microtime());
		return ((float)$usec + (float)$sec);
	}

	if (DEBUG_TIMER)
	{
		$GLOBALS['debug_timer_start'] = perfgetmicrotime();
	}

	/**************************************************************************\
	* Do not edit these lines                                                  *
	\**************************************************************************/
	define('PHPGW_API_INC',PHPGW_INCLUDE_ROOT.'/phpgwapi/inc');
	include(PHPGW_SERVER_ROOT.'/phpgwapi/setup/setup.inc.php');
	$GLOBALS['phpgw_info']['server']['versions']['phpgwapi'] = $setup_info['phpgwapi']['version'];
	$GLOBALS['phpgw_info']['server']['versions']['current_header'] = $setup_info['phpgwapi']['versions']['current_header'];
	unset($setup_info);
	$GLOBALS['phpgw_info']['server']['versions']['header'] = '1.27';
	/* This is a fix for NT */
	if(!isset($GLOBALS['phpgw_info']['flags']['noapi']) || !$GLOBALS['phpgw_info']['flags']['noapi'] == True)
	{
		include(PHPGW_API_INC . '/functions.inc.php');
		include(PHPGW_API_INC . '/xml_functions.inc.php');
		include(PHPGW_API_INC . '/soap_functions.inc.php');
	}

	/*
	  Leave off the final php closing tag, some editors will add
	  a \n or space after which will mess up cookies later on
	*/
