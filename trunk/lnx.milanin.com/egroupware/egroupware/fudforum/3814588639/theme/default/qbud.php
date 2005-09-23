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

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function alt_var($key)
{
	if (!isset($GLOBALS['_ALTERNATOR_'][$key])) {
		$args = func_get_args(); array_shift($args);
		$GLOBALS['_ALTERNATOR_'][$key] = array('p' => 1, 't' => count($args), 'v' => $args);
		return $args[0];
	}
	$k =& $GLOBALS['_ALTERNATOR_'][$key];
	if ($k['p'] == $k['t']) {
		$k['p'] = 0;
	}
	return $k['v'][$k['p']++];
}

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



	$buddies = '';
	if ($all) {
		$all_v = '';
		$all_d = 'none';
	} else {
		$all_v = '1';
		$all_d = 'all';
	}
	$c = uq('SELECT u.alias FROM phpgw_fud_buddy b INNER JOIN phpgw_fud_users u ON b.bud_id=u.id WHERE b.user_id='._uid.' AND b.user_id>1');
	while ($r = db_rowarr($c)) {
		$checked = $all ? ' checked' : '';
		$buddies .= '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="GenText">'.$r[0].'</td><td align="center"><input type="checkbox" name="names[]" value="'.$r[0].'"'.$checked.'></td></tr>';
	}
	$qbud_data = $buddies ? '<tr><th width="100%">Nick Name</th><th nowrap>Selected [<a class="thLnk" href="/egroupware/fudforum/3814588639/index.php?t=qbud&amp;'._rsid.'&amp;all='.$all_v.'">'.$all_d.'</a>]</th></tr>
'.$buddies.'
<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td colspan=2 class="GenText" align="right"><input type="submit" class="button" name="submit" value="Add Selected"></td></tr>' : '<tr class="RowStyleA"><td class="GenText" align="center">No buddies to choose from</td></tr>';


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-15">
<title><?php echo $GLOBALS['FORUM_TITLE'].$TITLE_EXTRA; ?></title>
<script language="JavaScript" src="<?php echo $GLOBALS['WWW_ROOT']; ?>/lib.js" type="text/javascript"></script>
<link rel="StyleSheet" href="/egroupware/fudforum/3814588639/theme/default/forum.css" type="text/css">
</head>
<body>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">
<form action="/egroupware/fudforum/3814588639/index.php?t=qbud" name="qbud" method="post"><?php echo _hs; ?>
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<?php echo $qbud_data; ?>
</table>
</form>
</td></tr></table></body></html>