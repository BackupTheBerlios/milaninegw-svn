<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: ratethread.php.t,v 1.1.1.1 2003/10/17 21:11:29 ralfbecker Exp $
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


	if (isset($_POST['rate_thread_id'], $_POST['sel_vote'])) {
		$th = (int) $_POST['rate_thread_id'];
		$rt = (int) $_POST['sel_vote'];

		/* determine if the user has permission to rate the thread */
		if (!q_singleval('SELECT t.id
				FROM phpgw_fud_thread t
				LEFT JOIN phpgw_fud_mod m ON t.forum_id=m.forum_id AND m.user_id='._uid.'
				INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id='.(_uid ? 2147483647 : 0).' AND g1.resource_id=t.forum_id
				'.(_uid ? ' LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=t.forum_id ' : '').'
				WHERE t.id='.$th.($usr->users_opt & 1048576 ? '' : ' AND (m.id IS NOT NULL OR ((CASE WHEN g1.id IS NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 1024) > 0)')  . ' LIMIT 1')) {
			std_error('access');
		}

		if (db_li('INSERT INTO phpgw_fud_thread_rate_track (thread_id, user_id, stamp, rating) VALUES('.$th.', '._uid.', '.__request_timestamp__.', '.$rt.')', $ef)) {
			$rt = db_saq('SELECT count(*), ROUND(AVG(rating)) FROM phpgw_fud_thread_rate_track WHERE thread_id='.$th);
			q('UPDATE phpgw_fud_thread SET rating='.(int)$rt[1].', n_rating='.(int)$rt[0].' WHERE id='.$th);
		}
	}
	check_return($usr->returnto);
?>