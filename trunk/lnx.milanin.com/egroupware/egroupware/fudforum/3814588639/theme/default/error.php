<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: error.php.t,v 1.1.1.1 2003/10/17 21:11:26 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function check_return($returnto)
{
	if (!$returnto || !strncmp($returnto, 't=error', 7)) {
		header('Location: /egroupware/fudforum/3814588639/index.php?t=index&'._rsidl);
	} else {
		if (strpos($returnto, 'S=') === false && $GLOBALS['FUD_OPT_1'] & 128) {
			header('Location: /egroupware/fudforum/3814588639/index.php?'.$returnto.'&S='.s);
		} else {
			header('Location: /egroupware/fudforum/3814588639/index.php?'.$returnto);
		}
	}
	exit;
}

	if (isset($_POST['ok'])) {
		check_return($usr->returnto);
	}
	$TITLE_EXTRA = ': Error Form';



	q('UPDATE phpgw_fud_ses SET returnto=NULL WHERE id='.$usr->sid);

	if (isset($usr->data['er_msg'], $usr->data['err_t'])) {
		$error_message	= $usr->data['er_msg'];
		$error_title	= $usr->data['err_t'];
		ses_putvar((int)$usr->sid, null);
	} else {
		$error_message	= 'Invalid URL';
		$error_title	= 'Error';
	}


?>
<?php echo $GLOBALS['fud_egw_hdr']; ?>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">
<div align="center">
<table border="0" cellspacing="1" cellpadding="2" class="DialogTable">
<tr><th><?php echo $error_title; ?></th></tr>
<tr class="RowStyleA" align="center"><td class="GenText"><?php echo $error_message; ?>
<br /><br /><form action="/egroupware/fudforum/3814588639/index.php?t=error" name="error_frm" method="post"><?php echo _hs; ?><input type="submit" class="button" name="ok" value="OK"></form>
</td></tr>
</table></div>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>