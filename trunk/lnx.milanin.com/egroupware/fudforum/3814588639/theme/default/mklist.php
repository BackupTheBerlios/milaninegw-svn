<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: mklist.php.t,v 1.2 2003/12/18 18:20:49 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

	define('plain_form', 1);

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function tmpl_draw_select_opt($values, $names, $selected, $normal_tmpl, $selected_tmpl)
{
	$vls = explode("\n", $values);
	$nms = explode("\n", $names);

	if (($a = count($vls)) != count($nms)) {
		exit("FATAL ERROR: inconsistent number of values inside a select<br>\n");
	}

	$options = '';
	for ($i = 0; $i < $a; $i++) {
		$options .= $vls[$i] != $selected ? '<option value="'.$vls[$i].'" '.$normal_tmpl.'>'.$nms[$i].'</option>' : '<option value="'.$vls[$i].'" selected '.$selected_tmpl.'>'.$nms[$i].'</option>';
	}

	return $options;
}

	/* decode list if one exists */
	if (!empty($_POST['opt_list'])) {
		$_POST['opt_list'] = @unserialize(base64_decode($_POST['opt_list']));
	}

	/* remove list entry */
	if (isset($_POST['del'])) {
		unset($_POST['opt_list'][$_POST['del']]);
	}

	/* append list entry */
	if (isset($_POST['btn_submit'], $_POST['opt'])) {
		$_POST['opt_list'][] = $_POST['opt'];
	}

	if (isset($_POST['go'])) {
		if (empty($_POST['opt_list'])) {
			exit('<html><script>window.close();</script></html>');
		}
		list($list_tag, $list_type) = explode(':', trim($_POST['tp']), 2);

		$tag = '[LIST TYPE='.$list_type.']\n';
		foreach ($_POST['opt_list'] as $o) {
			$tag .= '[*]'.addslashes($o).'\n';
		}
		$tag .= '[/LIST]';

		echo '<html><script>';
		readfile('lib.js');
		echo "\n\n".'insertParentTag(\''.$tag.'\', \' \'); window.close();</script></html>';

		exit();
	}



	$tp_select_data = tmpl_draw_select_opt("OL:1\nOL:a\nUL:square\nUL:disc\nUL:circle", "Numerical\nAlpha\nSquare\nDisc\nCircle", (isset($_POST['tp']) ? $_POST['tp'] : (isset($_GET['tp']) ? $_GET['tp'] : '')), '', '');
	if (!empty($_POST['opt_list'])) {
		list($list_tag, $list_type) = explode(':', trim($_POST['tp']), 2);
		$list_entry_data = '';
		foreach ($_POST['opt_list'] as $k => $op) {
			$list_entry_data .= '<li>'.htmlspecialchars($op).'&nbsp;&nbsp;&nbsp;<font size="-1"><a href="javascript:document.list.del.value=\''.$k.'\'; document.list.submit();">Delete</a></font>';
		}
		$list_sample = '<tr>
<td colspan=2>
<'.$list_tag.' type="'.$list_type.'">
'.$list_entry_data.'
</'.$list_tag.'>
</td>
</tr>';
		$_POST['opt_list'] = base64_encode(@serialize($_POST['opt_list']));
	} else {
		$list_sample = $_POST['opt_list'] = '';
	}


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
<form name="list" method="post" action="/egroupware/fudforum/3814588639/index.php?t=mklist">
<table border=0 cellspacing=2 cellpadding=0 width="99%" class="dashed">
<tr>
	<td>Type:</td>
	<td><select name="tp" onChange="document.list.submit();"><?php echo $tp_select_data; ?></select></td>
</tr>
<tr>
	<td>Option:</td>
	<td nowrap>
		<input type="text" name="opt" size=20>
		<input type="submit" class="button" name="btn_submit" value="Add Item">
	</td>
</tr>
<?php echo $list_sample; ?>
<tr>
<td colspan=2 align=right>
<input type="submit" class="button" name="go" value="Apply">
<input type="button" class="button" name="close" value="Close" onClick="javascript: window.close();">
</td></tr>
</table>
<input type="hidden" name="opt_list" value="<?php echo $_POST['opt_list']; ?>"><input type="hidden" name="del" value=""><?php echo _hs; ?>
</form>
</td></tr></table></body></html>