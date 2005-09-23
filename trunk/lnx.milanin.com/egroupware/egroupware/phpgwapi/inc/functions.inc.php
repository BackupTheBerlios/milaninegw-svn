<?php
	 /**************************************************************************\
	 * eGroupWare API - phpgwapi loader                                         *
	 * This file written by Dan Kuykendall <seek3r@phpgroupware.org>            *
	 * and Joseph Engo <jengo@phpgroupware.org>                                 *
	 * Has a few functions, but primary role is to load the phpgwapi            *
	 * Copyright (C) 2000, 2001 Dan Kuykendall                                  *
	 * -------------------------------------------------------------------------*
	 * This library is part of the eGroupWare API                               *
	 * http://www.egroupware.org/api                                            *
	 * ------------------------------------------------------------------------ *
	 * This library is free software; you can redistribute it and/or modify it  *
	 * under the terms of the GNU Lesser General Public License as published by *
	 * the Free Software Foundation; either version 2.1 of the License,         *
	 * or any later version.                                                    *
	 * This library is distributed in the hope that it will be useful, but      *
	 * WITHOUT ANY WARRANTY; without even the implied warranty of               *
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                     *
	 * See the GNU Lesser General Public License for more details.              *
	 * You should have received a copy of the GNU Lesser General Public License *
	 * along with this library; if not, write to the Free Software Foundation,  *
	 * Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA            *
	 \**************************************************************************/
	
	/* $Id: functions.inc.php,v 1.191.2.1 2004/11/03 05:02:25 shrykedude Exp $ */
	
	/***************************************************************************\
	* If running in PHP3, then force admin to upgrade                           *
	\***************************************************************************/

	error_reporting(error_reporting() & ~E_NOTICE);

	if (!function_exists('version_compare'))//version_compare() is only available in PHP4.1+
	{
		echo 'eGroupWare requires PHP 4.1 or greater.<br>';
		echo 'Please contact your System Administrator';
		exit;
	}

	include(PHPGW_API_INC.'/common_functions.inc.php');
	
	/*!
	 @function lang
	 @abstract function to handle multilanguage support
	*/
	function lang($key,$m1='',$m2='',$m3='',$m4='',$m5='',$m6='',$m7='',$m8='',$m9='',$m10='')
	{
		if(is_array($m1))
		{
			$vars = $m1;
		}
		else
		{
			$vars = array($m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8,$m9,$m10);
		}
		$value = $GLOBALS['phpgw']->translation->translate("$key",$vars);
		return $value;
	}

	/* Make sure the header.inc.php is current. */
	if ($GLOBALS['phpgw_info']['server']['versions']['header'] < $GLOBALS['phpgw_info']['server']['versions']['current_header'])
	{
		echo '<center><b>You need to port your settings to the new header.inc.php version by running <a href="setup/manageheader.php">setup/headeradmin</a>.</b></center>';
		exit;
	}

	/* Make sure the developer is following the rules. */
	if (!isset($GLOBALS['phpgw_info']['flags']['currentapp']))
	{
		/* This object does not exist yet. */
	/*	$GLOBALS['phpgw']->log->write(array('text'=>'W-MissingFlags, currentapp flag not set'));*/

		echo '<b>!!! YOU DO NOT HAVE YOUR $GLOBALS[\'phpgw_info\'][\'flags\'][\'currentapp\'] SET !!!';
		echo '<br>!!! PLEASE CORRECT THIS SITUATION !!!</b>';
	}

	magic_quotes_runtime(false);
	print_debug('sane environment','messageonly','api');

	/****************************************************************************\
	* Multi-Domain support                                                       *
	\****************************************************************************/
	
	/* make them fix their header */
	if (!isset($GLOBALS['phpgw_domain']))
	{
		echo '<center><b>The administrator must upgrade the header.inc.php file before you can continue.</b></center>';
		exit;
	}
	if (!isset($GLOBALS['phpgw_info']['server']['default_domain']) ||	// allow to overwrite the default domain
		!isset($GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['server']['default_domain']]))
	{
		reset($GLOBALS['phpgw_domain']);
		list($GLOBALS['phpgw_info']['server']['default_domain']) = each($GLOBALS['phpgw_domain']);
	}
	if (isset($_POST['login']))	// on login
	{
		$GLOBALS['login'] = $_POST['login'];
		if (strstr($GLOBALS['login'],'@') === False || count($GLOBALS['phpgw_domain']) == 1)
		{
			$GLOBALS['login'] .= '@' . get_var('logindomain',array('POST'),$GLOBALS['phpgw_info']['server']['default_domain']);
		}
		$parts = explode('@',$GLOBALS['login']);
		$GLOBALS['phpgw_info']['user']['domain'] = array_pop($parts);
	}
	else	// on "normal" pageview
	{
		$GLOBALS['phpgw_info']['user']['domain'] = get_var('domain', array('GET', 'COOKIE'), FALSE);
	}

	if (@isset($GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['user']['domain']]))
	{
		$GLOBALS['phpgw_info']['server']['db_host'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['user']['domain']]['db_host'];
		$GLOBALS['phpgw_info']['server']['db_port'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['user']['domain']]['db_port'];
		$GLOBALS['phpgw_info']['server']['db_name'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['user']['domain']]['db_name'];
		$GLOBALS['phpgw_info']['server']['db_user'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['user']['domain']]['db_user'];
		$GLOBALS['phpgw_info']['server']['db_pass'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['user']['domain']]['db_pass'];
		$GLOBALS['phpgw_info']['server']['db_type'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['user']['domain']]['db_type'];
	}
	else
	{
		$GLOBALS['phpgw_info']['server']['db_host'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['server']['default_domain']]['db_host'];
		$GLOBALS['phpgw_info']['server']['db_port'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['server']['default_domain']]['db_port'];
		$GLOBALS['phpgw_info']['server']['db_name'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['server']['default_domain']]['db_name'];
		$GLOBALS['phpgw_info']['server']['db_user'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['server']['default_domain']]['db_user'];
		$GLOBALS['phpgw_info']['server']['db_pass'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['server']['default_domain']]['db_pass'];
		$GLOBALS['phpgw_info']['server']['db_type'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['server']['default_domain']]['db_type'];
	}

	if ($GLOBALS['phpgw_info']['flags']['currentapp'] != 'login' && ! $GLOBALS['phpgw_info']['server']['show_domain_selectbox'])
	{
		unset ($GLOBALS['phpgw_domain']); // we kill this for security reasons
	}

	print_debug('domain',@$GLOBALS['phpgw_info']['user']['domain'],'api');

	 /****************************************************************************\
	 * These lines load up the API, fill up the $phpgw_info array, etc            *
	 \****************************************************************************/
	 /* Load main class */
	$GLOBALS['phpgw'] = CreateObject('phpgwapi.phpgw');
	 /************************************************************************\
	 * Load up the main instance of the db class.                             *
	 \************************************************************************/
	$GLOBALS['phpgw']->db           = CreateObject('phpgwapi.db');
	if ($GLOBALS['phpgw']->debug)
	{
		$GLOBALS['phpgw']->db->Debug = 1;
	}
	$GLOBALS['phpgw']->db->Halt_On_Error = 'no';
	$GLOBALS['phpgw']->db->connect(
		$GLOBALS['phpgw_info']['server']['db_name'],
		$GLOBALS['phpgw_info']['server']['db_host'],
		$GLOBALS['phpgw_info']['server']['db_port'],
		$GLOBALS['phpgw_info']['server']['db_user'],
		$GLOBALS['phpgw_info']['server']['db_pass'],
		$GLOBALS['phpgw_info']['server']['db_type']
	);
	@$GLOBALS['phpgw']->db->query("SELECT COUNT(config_name) FROM phpgw_config");
	if(!@$GLOBALS['phpgw']->db->next_record())
	{
		$setup_dir = str_replace($_SERVER['PHP_SELF'],'index.php','setup/');
		echo '<center><b>Fatal Error:</b> It appears that you have not created the database tables for '
			.'eGroupWare.  Click <a href="' . $setup_dir . '">here</a> to run setup.</center>';
		exit;
	}
	$GLOBALS['phpgw']->db->Halt_On_Error = 'yes';

	/* Fill phpgw_info["server"] array */
	// An Attempt to speed things up using cache premise
	$GLOBALS['phpgw']->db->query("select config_value from phpgw_config WHERE config_app='phpgwapi' and config_name='cache_phpgw_info'",__LINE__,__FILE__);
	if ($GLOBALS['phpgw']->db->num_rows())
	{
		$GLOBALS['phpgw']->db->next_record();
		$GLOBALS['phpgw_info']['server']['cache_phpgw_info'] = stripslashes($GLOBALS['phpgw']->db->f('config_value'));
	}

	$cache_query = "select content from phpgw_app_sessions where"
		." sessionid = '0' and loginid = '0' and app = 'phpgwapi' and location = 'config'";

	$GLOBALS['phpgw']->db->query($cache_query,__LINE__,__FILE__);
	$server_info_cache = $GLOBALS['phpgw']->db->num_rows();

	if(@$GLOBALS['phpgw_info']['server']['cache_phpgw_info'] && $server_info_cache)
	{
		$GLOBALS['phpgw']->db->next_record();
		$GLOBALS['phpgw_info']['server'] = unserialize(stripslashes($GLOBALS['phpgw']->db->f('content')));
	}
	else
	{
		$GLOBALS['phpgw']->db->query("select * from phpgw_config WHERE config_app='phpgwapi'",__LINE__,__FILE__);
		while ($GLOBALS['phpgw']->db->next_record())
		{
			$GLOBALS['phpgw_info']['server'][$GLOBALS['phpgw']->db->f('config_name')] = stripslashes($GLOBALS['phpgw']->db->f('config_value'));
		}

		if(@isset($GLOBALS['phpgw_info']['server']['cache_phpgw_info']))
		{
			if($server_info_cache)
			{
				$cache_query = "DELETE FROM phpgw_app_sessions WHERE sessionid='0' and loginid='0' and app='phpgwapi' and location='config'";
				$GLOBALS['phpgw']->db->query($cache_query,__LINE__,__FILE__);
			}
			$cache_query = 'INSERT INTO phpgw_app_sessions(sessionid,loginid,app,location,content) VALUES('
				. "'0','0','phpgwapi','config','".addslashes(serialize($GLOBALS['phpgw_info']['server']))."')";
			$GLOBALS['phpgw']->db->query($cache_query,__LINE__,__FILE__);
		}
	}
	unset($cache_query);
	unset($server_info_cache);
	if(@isset($GLOBALS['phpgw_info']['server']['enforce_ssl']) && !$_SERVER['HTTPS'])
	{
		Header('Location: https://' . $GLOBALS['phpgw_info']['server']['hostname'] . $GLOBALS['phpgw_info']['server']['webserver_url'] . $_SERVER['REQUEST_URI']);
		exit;
	}

	/****************************************************************************\
	* This is a global constant that should be used                              *
	* instead of / or \ in file paths                                            *
	\****************************************************************************/
	define('SEP',filesystem_separator());

	/************************************************************************\
	* Required classes                                                       *
	\************************************************************************/
	$GLOBALS['phpgw']->log          = CreateObject('phpgwapi.errorlog');
	$GLOBALS['phpgw']->translation  = CreateObject('phpgwapi.translation');
	$GLOBALS['phpgw']->common       = CreateObject('phpgwapi.common');
	$GLOBALS['phpgw']->hooks        = CreateObject('phpgwapi.hooks');
	$GLOBALS['phpgw']->auth         = CreateObject('phpgwapi.auth');
	$GLOBALS['phpgw']->accounts     = CreateObject('phpgwapi.accounts');
	$GLOBALS['phpgw']->acl          = CreateObject('phpgwapi.acl');
	$GLOBALS['phpgw']->session      = CreateObject('phpgwapi.sessions');
	$GLOBALS['phpgw']->preferences  = CreateObject('phpgwapi.preferences');
	$GLOBALS['phpgw']->applications = CreateObject('phpgwapi.applications');
	print_debug('main class loaded', 'messageonly','api');
	if (! isset($GLOBALS['phpgw_info']['flags']['included_classes']['error']) ||
		! $GLOBALS['phpgw_info']['flags']['included_classes']['error'])
	{
		include(PHPGW_INCLUDE_ROOT.'/phpgwapi/inc/class.error.inc.php');
		$GLOBALS['phpgw_info']['flags']['included_classes']['error'] = True;
	}

	/*****************************************************************************\
	* ACL defines - moved here to work for xml-rpc/soap, also                     *
	\*****************************************************************************/
	define('PHPGW_ACL_READ',1);
	define('PHPGW_ACL_ADD',2);
	define('PHPGW_ACL_EDIT',4);
	define('PHPGW_ACL_DELETE',8);
	define('PHPGW_ACL_PRIVATE',16);
	define('PHPGW_ACL_GROUP_MANAGERS',32);
	define('PHPGW_ACL_CUSTOM_1',64);
	define('PHPGW_ACL_CUSTOM_2',128);
	define('PHPGW_ACL_CUSTOM_3',256);

	/****************************************************************************\
	* Forcing the footer to run when the rest of the script is done.             *
	\****************************************************************************/
	register_shutdown_function(array($GLOBALS['phpgw']->common, 'phpgw_final'));

	/****************************************************************************\
	* Stuff to use if logging in or logging out                                  *
	\****************************************************************************/
	if ($GLOBALS['phpgw_info']['flags']['currentapp'] == 'login' || $GLOBALS['phpgw_info']['flags']['currentapp'] == 'logout')
	{
		if ($GLOBALS['phpgw_info']['flags']['currentapp'] == 'login')
		{
			if (@$_POST['login'] != '')
			{
				if (count($GLOBALS['phpgw_domain']) > 1)
				{
					list($login) = explode('@',$_POST['login']);
				}
				else
				{
					$login = $_POST['login'];
				}
				print_debug('LID',$login,'app');
				$login_id = $GLOBALS['phpgw']->accounts->name2id($login);
				print_debug('User ID',$login_id,'app');
				$GLOBALS['phpgw']->accounts->accounts($login_id);
				$GLOBALS['phpgw']->preferences->preferences($login_id);
				$GLOBALS['phpgw']->datetime = CreateObject('phpgwapi.datetime');
			}
		}
	/**************************************************************************\
	* Everything from this point on will ONLY happen if                        *
	* the currentapp is not login or logout                                    *
	\**************************************************************************/
	}
	else
	{
		if (! $GLOBALS['phpgw']->session->verify())
		{
			// we forward to the same place after the re-login
			if ($GLOBALS['phpgw_info']['server']['webserver_url'] && $GLOBALS['phpgw_info']['server']['webserver_url'] != '/')
			{
				list(,$relpath) = explode($GLOBALS['phpgw_info']['server']['webserver_url'],$_SERVER['PHP_SELF'],2);
			}
			else	// the webserver-url is empty or just a slash '/' (eGW is installed in the docroot and no domain given)
			{
				if (preg_match('/^https?:\/\/[^\/]*\/(.*)$/',$relpath=$_SERVER['PHP_SELF'],$matches))
				{
					$relpath = $matches[1];
				}
			}
			// this removes the sessiondata if its saved in the URL
			$query = preg_replace('/[&]?sessionid(=|%3D)[^&]+&kp3(=|%3D)[^&]+&domain=.*$/','',$_SERVER['QUERY_STRING']);
			Header('Location: '.$GLOBALS['phpgw_info']['server']['webserver_url'].'/login.php?cd=10&phpgw_forward='.urlencode($relpath.(!empty($query) ? '?'.$query : '')));
			exit;
		}

		$GLOBALS['phpgw']->datetime = CreateObject('phpgwapi.datetime');

		/* A few hacker resistant constants that will be used throught the program */
		define('PHPGW_TEMPLATE_DIR', ExecMethod('phpgwapi.phpgw.common.get_tpl_dir', 'phpgwapi'));
		define('PHPGW_IMAGES_DIR', ExecMethod('phpgwapi.phpgw.common.get_image_path', 'phpgwapi'));
		define('PHPGW_IMAGES_FILEDIR', ExecMethod('phpgwapi.phpgw.common.get_image_dir', 'phpgwapi'));
		define('PHPGW_APP_ROOT', ExecMethod('phpgwapi.phpgw.common.get_app_dir'));
		define('PHPGW_APP_INC', ExecMethod('phpgwapi.phpgw.common.get_inc_dir'));
		define('PHPGW_APP_TPL', ExecMethod('phpgwapi.phpgw.common.get_tpl_dir'));
		define('PHPGW_IMAGES', ExecMethod('phpgwapi.phpgw.common.get_image_path'));
		define('PHPGW_APP_IMAGES_DIR', ExecMethod('phpgwapi.phpgw.common.get_image_dir'));

		/*	define('PHPGW_APP_IMAGES_DIR', $GLOBALS['phpgw']->common->get_image_dir()); */

		/* Moved outside of this logic
		define('PHPGW_ACL_READ',1);
		define('PHPGW_ACL_ADD',2);
		define('PHPGW_ACL_EDIT',4);
		define('PHPGW_ACL_DELETE',8);
		define('PHPGW_ACL_PRIVATE',16);
		*/

		/********* This sets the user variables *********/
		$GLOBALS['phpgw_info']['user']['private_dir'] = $GLOBALS['phpgw_info']['server']['files_dir']
			. '/users/'.$GLOBALS['phpgw_info']['user']['userid'];

		/* This will make sure that a user has the basic default prefs. If not it will add them */
		$GLOBALS['phpgw']->preferences->verify_basic_settings();

		/********* Optional classes, which can be disabled for performance increases *********/
		while ($phpgw_class_name = each($GLOBALS['phpgw_info']['flags']))
		{
			if (ereg('enable_',$phpgw_class_name[0]))
			{
				$enable_class = str_replace('enable_','',$phpgw_class_name[0]);
				$enable_class = str_replace('_class','',$enable_class);
				eval('$GLOBALS["phpgw"]->' . $enable_class . ' = createobject(\'phpgwapi.' . $enable_class . '\');');
			}
		}
		unset($enable_class);
		reset($GLOBALS['phpgw_info']['flags']);

		/*************************************************************************\
		* These lines load up the templates class                                 *
		\*************************************************************************/
		if(!@$GLOBALS['phpgw_info']['flags']['disable_Template_class'])
		{
			$GLOBALS['phpgw']->template = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
		}

		/*************************************************************************\
		* These lines load up the themes                                          *
		\*************************************************************************/
		if (! $GLOBALS['phpgw_info']['user']['preferences']['common']['theme'])
		{
			if (@$GLOBALS['phpgw_info']['server']['template_set'] == 'user_choice')
			{
				$GLOBALS['phpgw_info']['user']['preferences']['common']['theme'] = 'default';
			}
			else
			{
				$GLOBALS['phpgw_info']['user']['preferences']['common']['theme'] = $GLOBALS['phpgw_info']['server']['template_set'];
			}
		}
		if (@$GLOBALS['phpgw_info']['server']['force_theme'] == 'user_choice')
		{
			if (!isset($GLOBALS['phpgw_info']['user']['preferences']['common']['theme']))
			{
				$GLOBALS['phpgw_info']['user']['preferences']['common']['theme'] = 'default';
			}
		}
		else
		{
			if (isset($GLOBALS['phpgw_info']['server']['force_theme']))
			{
				$GLOBALS['phpgw_info']['user']['preferences']['common']['theme'] = $GLOBALS['phpgw_info']['server']['force_theme'];
			}
		}

		if(@file_exists(PHPGW_SERVER_ROOT . '/phpgwapi/themes/' . $GLOBALS['phpgw_info']['user']['preferences']['common']['theme'] . '.theme'))
		{
			include(PHPGW_SERVER_ROOT . '/phpgwapi/themes/' . $GLOBALS['phpgw_info']['user']['preferences']['common']['theme'] . '.theme');
		}
		elseif(@file_exists(PHPGW_SERVER_ROOT . '/phpgwapi/themes/default.theme'))
		{
			include(PHPGW_SERVER_ROOT . '/phpgwapi/themes/default.theme');
		}
		else
		{
			/* Hope we don't get to this point.  Better then the user seeing a */
			/* complety back screen and not know whats going on                */
			echo '<body bgcolor="FFFFFF">';
			$GLOBALS['phpgw']->log->write(array('text'=>'F-Abort, No themes found'));

			exit;
		}
		unset($theme_to_load);

		/*************************************************************************\
		* If they are using frames, we need to set some variables                 *
		\*************************************************************************/
		if (((isset($GLOBALS['phpgw_info']['user']['preferences']['common']['useframes']) &&
			$GLOBALS['phpgw_info']['user']['preferences']['common']['useframes']) &&
			$GLOBALS['phpgw_info']['server']['useframes'] == 'allowed') ||
			($GLOBALS['phpgw_info']['server']['useframes'] == 'always'))
		{
			$GLOBALS['phpgw_info']['flags']['navbar_target'] = 'phpgw_body';
		}

		/*************************************************************************\
		* Verify that the users session is still active otherwise kick them out   *
		\*************************************************************************/
		if ($GLOBALS['phpgw_info']['flags']['currentapp'] != 'home' &&
			$GLOBALS['phpgw_info']['flags']['currentapp'] != 'about')
		{
			// This will need to use ACL in the future
			if (! $GLOBALS['phpgw_info']['user']['apps'][$GLOBALS['phpgw_info']['flags']['currentapp']] ||
				(@$GLOBALS['phpgw_info']['flags']['admin_only'] &&
				! $GLOBALS['phpgw_info']['user']['apps']['admin']))
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				if ($GLOBALS['phpgw_info']['flags']['noheader'])
				{
					echo parse_navbar();
				}

				$GLOBALS['phpgw']->log->write(array('text'=>'W-Permissions, Attempted to access %1','p1'=>$GLOBALS['phpgw_info']['flags']['currentapp']));

				echo '<p><center><b>'.lang('Access not permitted').'</b></center>';
				$GLOBALS['phpgw']->common->phpgw_exit(True);
			}
		}

		if(!is_object($GLOBALS['phpgw']->datetime))
		{
			$GLOBALS['phpgw']->datetime = CreateObject('phpgwapi.datetime');
		}
		$GLOBALS['phpgw']->applications->read_installed_apps();	// to get translated app-titles
		
		/*************************************************************************\
		* Load the header unless the developer turns it off                       *
		\*************************************************************************/
		if (!@$GLOBALS['phpgw_info']['flags']['noheader'])
		{
			$GLOBALS['phpgw']->common->phpgw_header();
		}

		/*************************************************************************\
		* Load the app include files if the exists                                *
		\*************************************************************************/
		/* Then the include file */
		if (PHPGW_APP_INC != "" &&
                   ! preg_match ("/phpgwapi/i", PHPGW_APP_INC) &&
                   file_exists(PHPGW_APP_INC . '/functions.inc.php') &&
                   !isset($_GET['menuaction']))
		{
			include(PHPGW_APP_INC . '/functions.inc.php');
		}
		if (!@$GLOBALS['phpgw_info']['flags']['noheader'] &&
			!@$GLOBALS['phpgw_info']['flags']['noappheader'] &&
			file_exists(PHPGW_APP_INC . '/header.inc.php') && !isset($_GET['menuaction']))
		{
			include(PHPGW_APP_INC . '/header.inc.php');
		}
	}
