<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: thread_notify.inc.t,v 1.1.1.1 2003/10/17 21:11:26 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

function is_notified($user_id, $thread_id)
{
	return q_singleval('SELECT * FROM {SQL_TABLE_PREFIX}thread_notify WHERE thread_id='.$thread_id.' AND user_id='.$user_id);
}

function thread_notify_add($user_id, $thread_id)
{
	if (!is_notified($user_id, $thread_id)) {
		q('INSERT INTO {SQL_TABLE_PREFIX}thread_notify(user_id, thread_id) VALUES ('.$user_id.', '.$thread_id.')');
	}
}

function thread_notify_del($user_id, $thread_id)
{
	q('DELETE FROM {SQL_TABLE_PREFIX}thread_notify WHERE thread_id='.$thread_id.' AND user_id='.$user_id);
}
?>