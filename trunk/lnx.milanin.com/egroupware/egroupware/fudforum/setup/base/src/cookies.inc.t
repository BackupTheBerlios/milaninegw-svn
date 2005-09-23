<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: cookies.inc.t,v 1.3 2003/12/18 18:20:49 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

function ses_update_status($ses_id, $str=null, $forum_id=0, $ret='')
{
	q('UPDATE {SQL_TABLE_PREFIX}ses SET forum_id='.$forum_id.', time_sec='.__request_timestamp__.', action='.($str ? "'".addslashes($str)."'" : 'NULL').', returnto='.(!is_int($ret) ? strnull(addslashes($_SERVER['QUERY_STRING'])) : 'returnto').' WHERE id='.$ses_id);
}

function ses_putvar($ses_id, $data)
{
	$cond = is_int($ses_id) ? 'id='.(int)$ses_id : "ses_id='".$ses_id."'";

	if (empty($data)) {
		q('UPDATE {SQL_TABLE_PREFIX}ses SET data=NULL WHERE '.$cond);
	} else {
		q("UPDATE {SQL_TABLE_PREFIX}ses SET data='".addslashes(serialize($data))."' WHERE ".$cond);
	}
}
?>