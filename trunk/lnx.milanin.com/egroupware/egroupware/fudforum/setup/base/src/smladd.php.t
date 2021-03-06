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

/*{PRE_HTML_PHP}*/
/*{POST_HTML_PHP}*/

	$col_count = '{TEMPLATE: sml_per_row}' - 2;
	$col_pos = -1;

	$sml_smiley_entry = $sml_smiley_row = '';
	$c = uq('SELECT code,img,descr FROM {SQL_TABLE_PREFIX}smiley ORDER BY vieworder');
	while ($r = db_rowarr($c)) {
		if ($col_pos++ > $col_count) {
			$sml_smiley_row .= '{TEMPLATE: sml_smiley_row}';
			$sml_smiley_entry = '';
			$col_pos = 0;
		}
		$r[0] = ($a = strpos($r[0], '~')) ? substr($r[0], 0, $a) : $r[0];
		$sml_smiley_entry .= '{TEMPLATE: sml_smiley_entry}';
	}
	if ($col_pos > -1) {
		$sml_smiley_row .= '{TEMPLATE: sml_smiley_row}';
	} else if ($col_pos == -1) {
		$sml_smiley_row = '{TEMPLATE: sml_no_smilies}';
	}

/*{POST_PAGE_PHP_CODE}*/
?>
{TEMPLATE: SMLLIST_PAGE}
