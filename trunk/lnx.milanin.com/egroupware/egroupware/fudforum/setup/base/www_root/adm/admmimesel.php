<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: admmimesel.php,v 1.2 2004/07/08 14:25:47 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

	require('./GLOBALS.php'); fud_egw();
	fud_use('adm.inc', true);

	echo '<html><body bgcolor="#ffffff">';

	if ($dp = opendir($GLOBALS['WWW_ROOT_DISK'] . 'images/mime')) {
		$icons_per_row = 7;
		$col = $i = 0;
		echo '<table border=0 cellspacing=1 cellpadding=2><tr>';
		while ($de = readdir($dp)) {
			if ($de == '.' || $de == '..') continue;
			$ext = strtolower(substr($de, -4));
			if ($ext != '.gif' && $ext != '.jpg' && $ext != '.png' && $ext != 'jpeg') {
				continue;
			}
			if (!($col++%$icons_per_row)) {
				echo '</tr><tr>';
			}
			$bgcolor = (!($i++%2)) ? ' bgcolor="#f4f4f4"' : '';

			echo '<td '.$bgcolor.' nowrap valign=center align=center><a href="javascript: window.opener.document.prev_icon.src=\'../images/mime/'.$de.'\'; window.opener.document.frm_mime.mime_icon.value=\''.$de.'\'; window.close();"><img src="../images/mime/'.$de.'" border=0><br><font size=-2>'.$de.'</font></a></td>';
		}
		closedir($dp);
		echo '</tr></table>';
	}
	if (!$i) {
		echo 'There are no mime icons';
	}

	echo '</body></html>';
?>