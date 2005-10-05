<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: pmuserloc.php.t,v 1.1.1.1 2003/10/17 21:11:31 ralfbecker Exp $
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

	if (empty($_GET['js_redr'])) {
		exit;
	}

	if (!($FUD_OPT_1 & (8388608|4194304)) || !_uid) {
		std_error('disabled');
	}



	$usr_login = isset($_GET['usr_login']) ? trim($_GET['usr_login']) : '';
	$usr_email = isset($_GET['usr_email']) ? trim($_GET['usr_email']) : '';
	$overwrite = isset($_GET['overwrite']) ? (int)$_GET['overwrite'] : 0;
	$js_redr = $_GET['js_redr'];

	if ($usr_login || $usr_email) {
		if ($usr_login) {
			$qry = "WHERE alias LIKE '".addslashes(str_replace('\\', '\\\\', $usr_login))."%'";
		} else {
			$qry = "WHERE email LIKE '".addslashes(str_replace('\\', '\\\\', $usr_email))."%'";
		}
		$find_user_data = '';
		$c = uq('SELECT alias FROM phpgw_fud_users '.$qry.' AND id>1');
		$i = 0;
		while ($r = db_rowarr($c)) {
			if ($overwrite) {
				$retlink = 'javascript: window.opener.document.'.$js_redr.'.value=\''.addcslashes($r[0], "'\\").'\'; window.close();';
			} else {
				$retlink = 'javascript:
						if (!window.opener.document.'.$js_redr.'.value) {
							window.opener.document.'.$js_redr.'.value = \''.addcslashes($r[0], "'\\").'\';
						} else {
							window.opener.document.'.$js_redr.'.value = window.opener.document.'.$js_redr.'.value + \'; \' + \''.addcslashes($r[0], "'\\").'; \';
						}
					window.close();';
			}
			$find_user_data .= '<tr class="'.alt_var('pmuserloc_alt','RowStyleA','RowStyleB').'"><td><a href="'.$retlink.'">'.$r[0].'</a></td></tr>';
			$i++;
		}
		if (!$find_user_data) {
			$find_user_data = '<tr><td colspan=2>Nessun risultato</td>';
		}
	} else {
		$find_user_data = '';
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
<form action="/egroupware/fudforum/3814588639/index.php" method="get"><?php echo _hs; ?>
<table border=0 width="100%" cellspacing=0 cellpadding=3 class="dashed">
<tr>
	<td>Login:</td>
	<td><input type="text" name="usr_login" value="<?php echo htmlspecialchars($usr_login); ?>"></td>
	
</tr>
<tr>
	<td>Email:</td>
	<td><input type="text" name="usr_email" value="<?php echo htmlspecialchars($usr_email); ?>"></td>
</tr>
<tr>
<td colspan=2 align=right><input type="submit" class="button" name="btn_submit" value="Invia"></td>
</tr>
</table>
<input type="hidden" name="js_redr" value="<?php echo $js_redr; ?>">
<input type="hidden" name="overwrite" value="<?php echo $overwrite; ?>">
<input type="hidden" name="t" value="pmuserloc">
</form>
<br />
<table border=0 width="100%" cellspacing=0 cellpadding=3 class="dashed">
<tr><td class="pmH">Utente</td></tr>
<?php echo $find_user_data; ?>
</table>

</td></tr></table></body></html>