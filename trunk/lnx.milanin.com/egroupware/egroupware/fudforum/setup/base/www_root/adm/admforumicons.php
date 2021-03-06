<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: admforumicons.php,v 1.3 2004/07/08 14:25:47 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

	require('./GLOBALS.php'); fud_egw();
	fud_use('adm.inc', true);
	fud_use('widgets.inc', true);

	/*
	 * The presense of the which_dir variable tells us whether we are editing
	 * forum icons or message icons.
	 */
	if (!empty($_GET['which_dir']) || !empty($_POST['which_dir'])) {
		$which_dir = '1';
		$ICONS_DIR = 'images/message_icons';
		$form_descr = 'Message Icons';
	} else {
		$which_dir = '';
		$ICONS_DIR = 'images/forum_icons';
		$form_descr = 'Forum Icons';
	}

	if (isset($_FILES['iconfile']) && $_FILES['iconfile']['size'] && preg_match('!\.(gif|png|jpg|jpeg)$!i', $_FILES['iconfile']['name'])) {
		move_uploaded_file($_FILES['iconfile']['tmp_name'], $GLOBALS['WWW_ROOT_DISK'] . $ICONS_DIR . '/' . $_FILES['iconfile']['name']);
	}
	if (isset($_GET['del'])) {
		@unlink($GLOBALS['WWW_ROOT_DISK'] . $ICONS_DIR . '/' . basename($_GET['del']));
	}

	require($WWW_ROOT_DISK . 'adm/admpanel.php');
?>
<h2><?php echo $form_descr; ?> Administration System</h2>
<?php
	if (@is_writeable($GLOBALS['WWW_ROOT_DISK'] . $ICONS_DIR)) {
?>
<form method="post" enctype="multipart/form-data" action="admforumicons.php">
<input type="hidden" name="which_dir" value="<?php echo $which_dir; ?>">
<?php echo _hs; ?>
<table class="datatable solidtable">
	<tr class="field">
		<td>Upload Icon:<br><font size="-1">Only (*.gif, *.jpg, *.png) files are supported</font></td>
		<td><input type="file" name="iconfile"></td>
	</tr>

	<tr class="fieldaction"><td align=right colspan=2><input type="submit" name="btn_upload" value="Add"></td></tr>
</table>
</form>
<?php
	} else {
?>
<table border=0 cellspacing=1 cellpadding=3>
	<tr class="field">
		<td align=center><font color="red"><?php echo $GLOBALS['WWW_ROOT_DISK'] . $ICONS_DIR; ?> is not writeable by the web server, file upload disabled.</td>
	</tr>
</table>
<?php
	}
?>
<table class="resulttable">
<tr class="resulttopic"><td>Icon</td><td>Action</td></tr>
<?php
	$i = 1;
	$dp = opendir($GLOBALS['WWW_ROOT_DISK'] . $ICONS_DIR);
	while ($de = readdir($dp)) {
		if ($de == '.' || $de == '..') continue;
		if (!preg_match('!\.(gif|png|jpg|jpeg)$!i', $de)) {
			continue;
		}
		$bgcolor = ($i++%2) ? ' class="resultrow2"' : ' class="resultrow1"';
		echo '<tr'.$bgcolor.'><td><img src="'.$GLOBALS['WWW_ROOT'] . $ICONS_DIR . '/' . $de.'"></td><td><a href="admforumicons.php?del='.urlencode($de).'&'._rsidl.'&which_dir='.$which_dir.'">Delete</a></td></tr>';
	}
	closedir($dp);
?>
</table>
<?php require($WWW_ROOT_DISK . 'adm/admclose.php'); ?>
