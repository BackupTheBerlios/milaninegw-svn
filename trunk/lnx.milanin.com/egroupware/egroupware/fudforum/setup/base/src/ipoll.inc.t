<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: ipoll.inc.t,v 1.1.1.1 2003/10/17 21:11:26 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

function poll_delete($id)
{
	if (!$id) {
		return;
	}

	q('UPDATE {SQL_TABLE_PREFIX}msg SET poll_id=0 WHERE poll_id='.$id);
	q('DELETE FROM {SQL_TABLE_PREFIX}poll_opt WHERE poll_id='.$id);
	q('DELETE FROM {SQL_TABLE_PREFIX}poll_opt_track WHERE poll_id='.$id);
	q('DELETE FROM {SQL_TABLE_PREFIX}poll WHERE id='.$id);
}

function poll_fetch_opts($id)
{
	$c = uq('SELECT id,name FROM {SQL_TABLE_PREFIX}poll_opt WHERE poll_id='.$id);
	while ($r = db_rowarr($c)) {
		$a[$r[0]] = $r[1];
	}

	return (isset($a) ? $a : null);
}

function poll_del_opt($id, $poll_id)
{
	q('DELETE FROM {SQL_TABLE_PREFIX}poll_opt WHERE poll_id='.$poll_id.' AND id='.$id);
	q('DELETE FROM {SQL_TABLE_PREFIX}poll_opt_track WHERE poll_id='.$poll_id.' AND poll_opt='.$id);
	$ttl_votes = (int) q_singleval('SELECT SUM(count) FROM {SQL_TABLE_PREFIX}poll_opt WHERE id='.$id);
	q('UPDATE {SQL_TABLE_PREFIX}poll SET total_votes='.$ttl_votes.' WHERE id='.$poll_id);
}

function poll_activate($poll_id, $frm_id)
{
	q('UPDATE {SQL_TABLE_PREFIX}poll SET forum_id='.$frm_id.' WHERE id='.$poll_id);
}

function poll_sync($poll_id, $name, $max_votes, $expiry)
{
	q("UPDATE {SQL_TABLE_PREFIX}poll SET name='".addslashes(htmlspecialchars($name))."', expiry_date=".intzero($expiry).", max_votes=".intzero($max_votes)." WHERE id=".$poll_id);
}

function poll_add($name, $max_votes, $expiry)
{
	return db_qid("INSERT INTO {SQL_TABLE_PREFIX}poll (name, owner, creation_date, expiry_date, max_votes) VALUES ('".addslashes(htmlspecialchars($name))."', "._uid.", ".__request_timestamp__.", ".intzero($expiry).", ".intzero($max_votes).")");
}

function poll_opt_sync($id, $name)
{
	q("UPDATE {SQL_TABLE_PREFIX}poll_opt SET name='".addslashes($name)."' WHERE id=".$id);
}

function poll_opt_add($name, $poll_id)
{
	return db_qid("INSERT INTO {SQL_TABLE_PREFIX}poll_opt (poll_id,name) VALUES(".$poll_id.", '".addslashes($name)."')");
}

function poll_validate($poll_id, $msg_id)
{
	if (($mid = (int) q_singleval('SELECT id FROM {SQL_TABLE_PREFIX}msg WHERE poll_id='.$poll_id)) && $mid != $msg_id) {
		return 0;
	} else {
		return $poll_id;
	}
}
?>