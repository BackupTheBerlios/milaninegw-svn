<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: core.inc,v 1.9 2004/03/17 16:51:54 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

$FORUM_VERSION = "2.6.0 (FUDeGW)";

ignore_user_abort(true);
set_magic_quotes_runtime(0);

function rls_db_lck()
{
	if (connection_status() && defined('fud_sql_lnk') && db_locked()) {
		db_unlock();
	}
	return;
}

register_shutdown_function('rls_db_lck');

$GLOBALS['TITLE_EXTRA'] = '';

if (get_magic_quotes_gpc()) {
	function strip_quotes(&$var)
	{
        	if (is_array($var)) {
			array_walk($var, 'strip_quotes');
		} else {
			$var = stripslashes($var);
		}
	}

	if (@count($_GET)) {
		array_walk($_GET, 'strip_quotes');
	}
        if (@count($_POST)) {
		array_walk($_POST, 'strip_quotes');
	}
        if (@count($_COOKIES)) {
		array_walk($_COOKIES, 'strip_quotes');
	}
	if (@count($_FILES)) {
		array_walk($_FILES, 'strip_quotes');
	}
}

$GLOBALS['PAGE_TIME'] = gettimeofday();
define('__request_timestamp__', $GLOBALS['PAGE_TIME']['sec']);
mt_srand($GLOBALS['PAGE_TIME']['usec']);

function fud_use($file, $static=false)
{
	if ($static) {
		include_once $GLOBALS['INCLUDE'] . $file;
		return;
	}
	if (!defined('fud_theme')) {
		define('fud_theme', 'theme/default/');
	}
	include_once $GLOBALS['INCLUDE'] . fud_theme . $file;
}

function get_random_value($bitlength=32)
{
	$n = round($bitlength/32);
	$v='';
	while (--$n > -1) {
		$v .= mt_rand();
	}
	return $v;
}

function get_ip()
{
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = strtok($_SERVER['HTTP_X_FORWARDED_FOR'], ',');
		do {
			if (($lip = ip2long($ip)) === -1) {
				continue;
			}
			/* some disallowed ip ranges, that are normally used for private networks
			 * 10.0.0.0 - 10.255.255.255
			 * 172.16.0.0 - 172.31.255.255
			 * 192.168.0.0 - 192.168.255.255
			 * 127.0.0.1, 255.255.255.255, 0.0.0.0
			*/
			if (!$lip || (($lip >= 167772160) && ($lip <= 184549375)) || (($lip >= -1408237568) && ($lip <= -1407188993)) || (($lip >= -1062731776) && ($lip <= -1062666241)) || ($lip == 2130706433)) {
				continue;
			}
			return long2ip(ip2long($ip));
		} while (($ip = strtok(',')));
	}
	return (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0');
}

/* Common DB functions */
if (!function_exists('intnull')) {
function ssn($str)
{
	if (is_null($str) || $str === '') {
		return 'NULL';
	} else {
		return "'" . addslashes($str) . "'";
	}
}

function in($str)
{
	return (!(int)$str) ? 'NULL' : (int)$str;
}

function iz($str)
{
	return (int)$str;
}

function intnull($val)
{
	return (!is_null($val) ? $val : 'NULL');
}

function intzero($val)
{
	return (!empty($val) ? $val : '0');
}

function ifnull($val, $alt)
{
	return (!is_null($val) ? "'".$val."'" : $alt);
}

function strnull($val)
{
	return (!is_null($val) ? "'".$val."'" : 'NULL');
}
}
/* END: Common DB Functions */

function __ffilesize($fp)
{
	$st = fstat($fp);
	return (isset($st['size']) ? $st['size'] : $st[7]);
}

if (!function_exists('file_get_contents')) {
	function file_get_contents($fname)
	{
		if (!($fp = @fopen($fname, 'rb'))) {
			return false;
		}
		/* this handles urls */
		if (!($size = @__ffilesize($fp))) {
			$size = 2 * 1024 * 1024;
		}
		$data = fread($fp, $size);
		fclose($fp);
		return $data;
	}
}

function fud_egw($t='index', $plain=0)
{
	/* Import session id */
	$domain = $kp3 = $sessionid = null;
	if (isset($_GET['sessionid'])) {
		$sessionid =& $_GET['sessionid'];
		if (isset($_GET['domain'])) {
			$domain =& $_GET['domain'];
		}
		if (isset($_GET['kp3'])) {
			$kp3 =& $_GET['kp3'];
		}
	}
	if (isset($_POST['sessionid'])) {
		$sessionid =& $_POST['sessionid'];
		if (isset($_POST['domain'])) {
			$_GET['domain'] = $domain =& $_POST['domain'];
		}
		if (isset($_POST['kp3'])) {
			$_GET['kp3'] = $kp3 =& $_POST['kp3'];
		}
	}
	if (isset($_COOKIE['sessionid'])) {
		$sessionid =& $_COOKIE['sessionid'];
	}

        /* validate session id */
	if ($sessionid && preg_match('![^A-Za-z0-9]!', $sessionid)) {
		$sessionid = null;
	}

	$_COOKIE['sessionid'] = $_GET['sessionid'] = $sessionid;

	$GLOBALS['phpgw_info']['flags'] = array('currentapp' => 'fudforum', 'noappheader' => true, 'noappfooter' => true, 'nofooter' => false, 'noheader' => true, 'nonavbar' => true);
	$GLOBALS['phpgw_info']['flags']['java_script'] = '<script language="javascript" src="'.$GLOBALS['WWW_ROOT'].'lib.js" type="text/javascript"></script>';

	if (!$sessionid) {
		$GLOBALS['phpgw_info']['flags']['login'] = true;
		/* In order to create a session, currentapp must be 'login' */
		$GLOBALS['phpgw_info']['flags']['currentapp'] = 'login';
	}

	require_once($GLOBALS['WWW_ROOT_DISK'].'../../header.inc.php');

	/* anon user handling */
	if (!$GLOBALS['phpgw']->session->verify()) {
		if ($GLOBALS['ANON_NICK'] && $GLOBALS['ANON_PASS']) {
			$sessionid = $GLOBALS['phpgw']->session->create($GLOBALS['ANON_NICK'], $GLOBALS['ANON_PASS'], 'text');
			/* still no session, failed to create session for anon user */
			if (!$sessionid) {
				echo "<p>Can't create session for user '".$GLOBALS['ANON_NICK']."' !!!</p>\n";
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			if (isset($_SERVER['PHP_SELF'], $_SERVER['QUERY_STRING'])) {
				header("Location: ".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."&S=".$GLOBALS['phpgw']->session->sessionid);
			} else {
				header("Location: ".$GLOBALS['WWW_ROOT']."index.php?S=".$GLOBALS['phpgw']->session->sessionid);
			}
			exit;
		} else {
			$GLOBALS['phpgw']->redirect_link('/login.php', 'cd=10');
		}
	}

	/* define constants */
	define('s', $sessionid);
	/* likely cookie only situation */
	if (!$domain) {
		define('_hs', '');
		define('_rsid', '');
		define('_rsidl', '');
	} else {
		define('_hs', '<input type="hidden" name="sessionid" value="'.$sessionid.'"><input type="hidden" name="domain" value="'.$domain.'"><input type="hidden" name="kp3" value="'.$kp3.'">');
		define('_rsid', "sessionid={$sessionid}&amp;kp3={$kp3}&amp;domain={$domain}");
		define('_rsidl', str_replace('&amp;', '&', _rsid));
	}

	$db =& $GLOBALS['phpgw']->db;
	$server =& $GLOBALS['phpgw_info']['server'];

	if (!empty($server['use_adodb']) || empty($db->Link_ID) || !is_resource($db->Link_ID)) {
		// open your own connection, as ADOdb does not export the use Link_ID
		switch ($server['db_type']) {
			case 'mysql':
				$func = $server['db_persistent'] ? 'mysql_pconnect' : 'mysql_connect';
				define('fud_sql_lnk',$func($db->Host, $db->User, $db->Password));
				mysql_select_db($db->Database,fud_sql_lnk);
				break;

			case 'pgsql':
				$func = $server['db_persistent'] ? 'pg_pconnect' : 'pg_connect';
				define('fud_sql_lnk',$func('dbname='.$db->Database.' host='.$db->Host.' user='.$db->User.' password='.$db->Password));
				break;

			default:
				die('FUDforum only supports mysql or pgsql !!!');
		}
		unset($func);
	} else {
		define('fud_sql_lnk', $db->Link_ID);
	}

	if (!$plain) {
		ob_start();
		$GLOBALS['phpgw']->common->phpgw_header();
		/* nav bar is for actual users only */
		if ($GLOBALS['phpgw_info']['user']['account_lid'] && $GLOBALS['ANON_NICK'] != $GLOBALS['phpgw_info']['user']['account_lid']) {
			parse_navbar();
		}
		$GLOBALS['fud_egw_hdr'] = ob_get_contents();
		ob_end_clean();
	} else {
		$GLOBALS['fud_egw_hdr'] = null;
	}
}
?>
