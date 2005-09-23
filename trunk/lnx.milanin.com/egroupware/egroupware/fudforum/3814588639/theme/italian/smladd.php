<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: smladd.php.t,v 1.2 2003/12/18 18:20:49 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}


	$col_count = '7' - 2;
	$col_pos = -1;

	$sml_smiley_entry = $sml_smiley_row = '';
	$c = uq('SELECT code,img,descr FROM phpgw_fud_smiley ORDER BY vieworder');
	while ($r = db_rowarr($c)) {
		if ($col_pos++ > $col_count) {
			$sml_smiley_row .= '<tr valign="bottom"><td>'.$sml_smiley_entry.'</td></tr>';
			$sml_smiley_entry = '';
			$col_pos = 0;
		}
		$r[0] = ($a = strpos($r[0], '~')) ? substr($r[0], 0, $a) : $r[0];
		$sml_smiley_entry .= '<a href="javascript: insertParentTag(\' '.$r[0].' \',\'\');"><img src="images/smiley_icons/'.$r[1].'" title="'.$r[2].'" alt="'.$r[2].'" /></a>&nbsp;&nbsp;';
	}
	if ($col_pos > -1) {
		$sml_smiley_row .= '<tr valign="bottom"><td>'.$sml_smiley_entry.'</td></tr>';
	} else if ($col_pos == -1) {
		$sml_smiley_row = 'No emoticons available.';
	}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-15">
<title><?php echo $GLOBALS['FORUM_TITLE'].$TITLE_EXTRA; ?></title>
<script language="JavaScript" src="<?php echo $GLOBALS['WWW_ROOT']; ?>/lib.js" type="text/javascript"></script>
<link rel="StyleSheet" href="/egroupware/fudforum/3814588639/theme/italian/forum.css" type="text/css">
</head>
<body>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">
<table border=0 cellspacing=1 cellpadding=0 width="100%" class="dashed">
<?php echo $sml_smiley_row; ?>
<tr><td align="center" colspan="<?php echo $col_count; ?>">[<a href="javascript://" onClick="javascript: window.close();">chiudi finestra</a>]</td></tr>
</table>
</td></tr></table></body></html>
