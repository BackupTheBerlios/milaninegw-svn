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

/*{PRE_HTML_PHP}*/

	if (isset($_POST['ok'])) {
		check_return($usr->returnto);
	}
	$TITLE_EXTRA = ': {TEMPLATE: error_title}';

/*{POST_HTML_PHP}*/

	q('UPDATE {SQL_TABLE_PREFIX}ses SET returnto=NULL WHERE id='.$usr->sid);

	if (isset($usr->data['er_msg'], $usr->data['err_t'])) {
		$error_message	= $usr->data['er_msg'];
		$error_title	= $usr->data['err_t'];
		ses_putvar((int)$usr->sid, null);
	} else {
		$error_message	= '{TEMPLATE: error_invalidurl}';
		$error_title	= '{TEMPLATE: error_error}';
	}

/*{POST_PAGE_PHP_CODE}*/
?>
{TEMPLATE: ERROR_PAGE}