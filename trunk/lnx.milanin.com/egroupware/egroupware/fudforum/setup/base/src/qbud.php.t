<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: qbud.php.t,v 1.1.1.1 2003/10/17 21:11:30 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

	define('plain_form', 1);

/*{PRE_HTML_PHP}*/

	if (!_uid) {
		std_error('login');
	}

	$all = !empty($_GET['all']) ? 1 : 0;

	if (!$all && isset($_POST['names']) && is_array($_POST['names'])) {
		$names = addcslashes(implode(';', $_POST['names']), '"\\');

		echo '<html><body><script language="Javascript"><!--

		if (window.opener.document.post_form.msg_to_list.value.length > 0) {
			window.opener.document.post_form.msg_to_list.value = window.opener.document.post_form.msg_to_list.value+\';\'+"'.$names.'";
		} else {
			window.opener.document.post_form.msg_to_list.value = window.opener.document.post_form.msg_to_list.value+"'.$names.'";
		}

		window.close();

		//--></script></body></html>';
		exit;
	}

/*{POST_HTML_PHP}*/

	$buddies = '';
	if ($all) {
		$all_v = '';
		$all_d = '{TEMPLATE: pmsg_none}';
	} else {
		$all_v = '1';
		$all_d = '{TEMPLATE: pmsg_all}';
	}
	$c = uq('SELECT u.alias FROM {SQL_TABLE_PREFIX}buddy b INNER JOIN {SQL_TABLE_PREFIX}users u ON b.bud_id=u.id WHERE b.user_id='._uid.' AND b.user_id>1');
	while ($r = db_rowarr($c)) {
		$checked = $all ? ' checked' : '';
		$buddies .= '{TEMPLATE: buddy_entry}';
	}
	$qbud_data = $buddies ? '{TEMPLATE: buddy_list}' : '{TEMPLATE: no_buddies}';

/*{POST_PAGE_PHP_CODE}*/
?>
{TEMPLATE: QBUD_PAGE}