<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: admthemesel.php,v 1.3 2004/07/08 14:25:47 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

	@set_time_limit(6000);

	require('./GLOBALS.php'); fud_egw();
	fud_use('adm.inc', true);

	if (isset($_POST['tname'], $_POST['tlang'], $_POST['ret'])) {
		header('Location: '.$_POST['ret'].'.php?tname='.$_POST['tname'].'&tlang='.$_POST['tlang'].'&'._rsidl);
		exit;
	}

	$ret = isset($_GET['ret']) ? $_GET['ret'] : 'tmpllist';

	require($WWW_ROOT_DISK . 'adm/admpanel.php');

	list($def_thm, $def_tmpl) = db_saq('SELECT name, lang FROM '.$GLOBALS['DBHOST_TBL_PREFIX'].'themes WHERE theme_opt=3');
?>
<h3>Template Set Selection</h3>
<form method="post" action="admthemesel.php">
<input type="hidden" name="ret" value="<?php echo $ret; ?>">
<table class="datatable solidtable">
<tr class="field">
<?php
	echo _hs;
	$path = $GLOBALS['DATA_DIR'].'/thm';

	$dp = opendir($path);
	echo '<td>Template Set:</td><td><select name="tname">';
	while ($de = readdir($dp)) {
		if ($de == '.' || $de == '..') continue;
		if ($de == 'CVS' || !@is_dir($path . '/' . $de)) {
			continue;
		}
		echo '<option value="'.$de.'"'.($de == $def_thm ? ' selected' : '').'>'.$de.'</option>';
	}
	echo '</select></td>';
?>
</tr>
<tr class="field">
<?php
	$path .= '/default/i18n';

	$dp = opendir($path);
	echo '<td>Language:</td><td><select name="tlang">';
	while ($de = readdir($dp)) {
		if ($de == '.' || $de == '..') continue;
		if ($de == 'CVS' || !@is_dir($path . '/' . $de)) {
			continue;
		}
		echo '<option value="'.$de.'"'.($de == $def_tmpl ? ' selected' : '').'>'.$de.'</option>';
	}
	echo '</select></td>';
?>
</tr>
<?php
	echo '<tr class="fieldaction" align=right><td colspan=2><input type="submit" name="btn_submit" value="Edit"></td></td>';
?>
</tr>
</table>
</form>
<?php require($WWW_ROOT_DISK . 'adm/admclose.php'); ?>
