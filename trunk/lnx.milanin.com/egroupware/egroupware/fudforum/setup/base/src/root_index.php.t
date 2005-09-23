<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: root_index.php.t,v 1.5 2004/06/02 16:22:05 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

	require('./GLOBALS.php');
	
	/* before we go on, we need to do some very basic activation checks */
	if (!($FUD_OPT_1 & 1)) {
		fud_egw();
		fud_use('errmsg.inc');
		exit($DISABLED_REASON . __fud_ecore_adm_login_msg);
	}

	if (isset($_GET['t'])) {
		$t = $_GET['t'];
	} else if (isset($_POST['t'])) {
		$t = $_POST['t'];
	} else {
		$t = 'index';
	}
	if (preg_match('/[^A-Za-z0-9_]/', $t)) {
		$t = 'index';
	}

	if ($FUD_OPT_2 & 16384 && $t != 'getfile') {
		ob_start(array('ob_gzhandler', (int)$PHP_COMPRESSION_LEVEL));
	}

	fud_egw($t, 0);

/*{PRE_HTML_PHP}*/
/*{POST_HTML_PHP}*/

	if ($t == 'rview') {
		if (isset($_GET['th']) || isset($_GET['goto'])) {
			$t = $_GET['t'] = d_thread_view;
		} else if (isset($_GET['frm_id'])) {
			$t = $_GET['t'] = t_thread_view;
		} else {
			$t = $_GET['t'] = 'index';
		}
	}

	fud_use('err.inc');

	define('__index_page_start__', true);
	if (isset($GLOBALS['fud_egw_hdr'])) {
		$GLOBALS['fud_egw_hdr'] = str_replace('/lib.js" type="text/javascript"></script>', '/lib.js" type="text/javascript"></script><link href="'.$GLOBALS['WWW_ROOT'].fud_theme.'/forum.css" type="text/css" rel="StyleSheet" /></HEAD>', $GLOBALS['fud_egw_hdr']);
	}
	require($WWW_ROOT_DISK . fud_theme . $t . '.php');
?>