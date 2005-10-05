<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: avatarsel.php.t,v 1.1.1.1 2003/10/17 21:11:31 ralfbecker Exp $
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


	$TITLE_EXTRA = ': Modulo di selezione degli avatar';

	/* here we draw the avatar control */
	$icons_per_row = 5;
	$c = uq('SELECT id, descr, img FROM phpgw_fud_avatar ORDER BY id');
	$avatars_data = '';
	$col = 0;
	while ($r = db_rowarr($c)) {
		if (!($col++ % $icons_per_row)) {
			$avatars_data .= '</tr><tr>';
		}
		$avatars_data .= '<td class="'.alt_var('avatarsel_cl','Av1','Av2').'">
<a class="GenLink" href="javascript: window.opener.document.fud_register.reg_avatar.value=\''.$r[0].'\'; window.opener.document.reg_avatar_img.src=\'images/avatars/'.$r[2].'\'; window.close();"><img src="images/avatars/'.$r[2].'" alt="" /><br /><font class="SmallText">'.$r[1].'</font></a></td>';
	}

	if (!$avatars_data) {
		$avatars_data = '<td class="NoAvatar">Nessun avatar disponibile</td>';
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
<table border=0 cellspacing=1 cellpadding=2><tr>
<?php echo $avatars_data; ?>
</tr></table>
</td></tr></table></body></html>