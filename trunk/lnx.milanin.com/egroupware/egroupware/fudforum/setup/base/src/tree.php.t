<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: tree.php.t,v 1.3 2003/12/18 18:20:49 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

/*{PRE_HTML_PHP}*/

	if ($FUD_OPT_3 & 2) {
		std_error('disabled');
	}

	if (!isset($_GET['th']) || !($th = (int)$_GET['th'])) {
		$th = 0;
	}
	if (!isset($_GET['mid']) || !($mid = (int)$_GET['mid'])) {
		$mid = 0;
	}

	if (isset($_GET['goto'])) {
		if (($mid = (int)$_GET['goto']) && !$th) {
			$th = q_singleval('SELECT thread_id FROM {SQL_TABLE_PREFIX}msg WHERE id='.$mid);
		} else if ($_GET['goto'] == 'end' && $th) {
			$mid = q_singleval('SELECT last_post_id FROM {SQL_TABLE_PREFIX}thread WHERE id='.$th);
		} else if ($th) {
			$mid = (int)$_GET['goto'];
		} else {
			invl_inp_err();
		}
	}
	if (!$th) {
		invl_inp_err();
	}
	if (!$mid && isset($_GET['unread']) && _uid) {
		$mid = q_singleval('SELECT m.id FROM {SQL_TABLE_PREFIX}msg m LEFT JOIN {SQL_TABLE_PREFIX}read r ON r.thread_id=m.thread_id AND r.user_id='._uid.' WHERE m.thread_id='.$th.' AND m.apr=1 AND m.post_stamp > r.last_view AND m.post_stamp > '.$usr->last_read.' ORDER BY m.post_stamp DESC LIMIT 1');
		if (!$mid) {
			$mid = q_singleval('SELECT last_post_id FROM {SQL_TABLE_PREFIX}thread WHERE id='.$th);		
		}
	}

	/* we create a BIG object frm, which contains data about forum,
	 * category, current thread, subscriptions, permissions, moderation status,
	 * rating possibilites and if we will need to update last_view field for registered user
	 */
	make_perms_query($fields, $join);

	$frm = db_sab('SELECT
			c.name AS cat_name,
			f.name AS frm_name,
			m.subject,
			t.id, t.forum_id, t.replies, t.rating, t.n_rating, t.root_msg_id, t.moved_to, t.thread_opt, t.root_msg_id,
			tn.thread_id AS subscribed,
			mo.forum_id AS md,
			tr.thread_id AS cant_rate,
			r.last_view,
			r2.last_view AS last_forum_view,
			r.msg_id,
			tv.pos AS th_pos, tv.page AS th_page,
			m2.thread_id AS last_thread,
			'.$fields.'
		FROM {SQL_TABLE_PREFIX}thread t
			INNER JOIN {SQL_TABLE_PREFIX}msg		m ON m.id=t.root_msg_id
			INNER JOIN {SQL_TABLE_PREFIX}forum		f ON f.id=t.forum_id
			INNER JOIN {SQL_TABLE_PREFIX}cat		c ON f.cat_id=c.id
			INNER JOIN {SQL_TABLE_PREFIX}thread_view	tv ON tv.forum_id=t.forum_id AND tv.thread_id=t.id
			INNER JOIN {SQL_TABLE_PREFIX}msg 		m2 ON f.last_post_id=m2.id
			LEFT  JOIN {SQL_TABLE_PREFIX}thread_notify 	tn ON tn.user_id='._uid.' AND tn.thread_id='.$th.'
			LEFT  JOIN {SQL_TABLE_PREFIX}mod 		mo ON mo.user_id='._uid.' AND mo.forum_id=t.forum_id
			LEFT  JOIN {SQL_TABLE_PREFIX}thread_rate_track 	tr ON tr.thread_id=t.id AND tr.user_id='._uid.'
			LEFT  JOIN {SQL_TABLE_PREFIX}read 		r ON r.thread_id=t.id AND r.user_id='._uid.'
			LEFT  JOIN {SQL_TABLE_PREFIX}forum_read 	r2 ON r2.forum_id=t.forum_id AND r2.user_id='._uid.'
			'.$join.'
		WHERE t.id='.$th);

	if (!$frm) { /* bad thread, terminate request */
		invl_inp_err();
	}

	if ($frm->moved_to) { /* moved thread, we could handle it, but this case is rather rare, so it's cleaner to redirect */
		header('Location: {ROOT}?t=tree&goto='.$frm->root_msg_id.'&'._rsidl);
		exit();
	}

	$perms = perms_from_obj($frm, ($usr->users_opt & 1048576));

	if (!($perms & 2)) {
		if (!isset($_GET['logoff'])) {
			std_error('perms');
		} else {
			header('Location: {ROOT}?t=index&' . _rsidl);
			exit;
		}
	}

	$msg_forum_path = '{TEMPLATE: msg_forum_path}';

	if (_uid) {
		/* Deal with thread subscriptions */
		if (isset($_GET['notify'], $_GET['opt'])) {
			if ($_GET['opt'] == 'on') {
				thread_notify_add(_uid, $_GET['th']);
				$frm->subscribed = 1;
			} else {
				thread_notify_del(_uid, $_GET['th']);
				$frm->subscribed = 0;
			}
		}
	}

	if (!$mid) {
		$mid = $frm->root_msg_id;
	}

	$msg_obj = db_sab('SELECT
		m.*,
		t.thread_opt, t.root_msg_id, t.last_post_id, t.forum_id,
		f.message_threshold,
		u.id AS user_id, u.alias AS login, u.avatar_loc, u.email, u.posted_msg_count, u.join_date, u.location,
		u.sig, u.custom_status, u.icq, u.jabber, u.affero, u.aim, u.msnm, u.yahoo, u.last_visit AS time_sec, u.users_opt,
		l.name AS level_name, l.level_opt, l.img AS level_img,
		p.max_votes, p.expiry_date, p.creation_date, p.name AS poll_name, p.total_votes,
		pot.id AS cant_vote
	FROM
		{SQL_TABLE_PREFIX}msg m
		INNER JOIN {SQL_TABLE_PREFIX}thread t ON m.thread_id=t.id
		INNER JOIN {SQL_TABLE_PREFIX}forum f ON t.forum_id=f.id
		LEFT JOIN {SQL_TABLE_PREFIX}users u ON m.poster_id=u.id
		LEFT JOIN {SQL_TABLE_PREFIX}level l ON u.level_id=l.id
		LEFT JOIN {SQL_TABLE_PREFIX}poll p ON m.poll_id=p.id
		LEFT JOIN {SQL_TABLE_PREFIX}poll_opt_track pot ON pot.poll_id=p.id AND pot.user_id='._uid.'
	WHERE
		m.id='.$mid.' AND m.apr=1');

	if (!isset($_GET['prevloaded'])) {
		th_inc_view_count($th);
		if (_uid) {
			if ($frm->last_view < $msg_obj->post_stamp) {
				user_register_thread_view($th, $msg_obj->post_stamp, $mid);
			}
			if ($frm->last_forum_view < $msg_obj->post_stamp) {
				user_register_forum_view($frm->forum_id);
			}
		}
		$subscribe_status = $frm->subscribed ? '{TEMPLATE: unsub_to_thread}' : '{TEMPLATE: sub_from_thread}';
	} else {
		$subscribe_status = '';
	}
	ses_update_status($usr->sid, '{TEMPLATE: tree_update}', $frm->id);

/*{POST_HTML_PHP}*/

	$TITLE_EXTRA = ': {TEMPLATE: tree_title}';

	if ($FUD_OPT_2 & 4096) {
		$thread_rating = $frm->rating ? '{TEMPLATE: thread_rating}' : '{TEMPLATE: no_thread_rating}';
		if ($perms & 1024 && !$frm->cant_rate) {
			$rate_thread = '{TEMPLATE: rate_thread}';
		} else {
			$rate_thread = '';
		}
	} else {
		$rate_thread = $thread_rating = '';
	}

	if ($perms & 4096) {
		$lock_thread = !($frm->thread_opt & 1) ? '{TEMPLATE: mod_lock_thread}' : '{TEMPLATE: mod_unlock_thread}';
	} else {
		$lock_thread = '';
	}

	$split_thread = ($frm->replies && $perms & 2048) ? '{TEMPLATE: split_thread}' : '';
	$post_reply = (!($frm->thread_opt & 1) || $perms & 4096) ? '{TEMPLATE: post_reply}' : '';
	$email_page_to_friend = $FUD_OPT_2 & 1073741824 ? '{TEMPLATE: email_page_to_friend}' : '';

	$arr = array();
	$c = uq('SELECT m.poster_id, m.subject, m.reply_to, m.id, m.poll_id, m.attach_cnt, m.post_stamp, u.alias, u.last_visit FROM {SQL_TABLE_PREFIX}msg m INNER JOIN {SQL_TABLE_PREFIX}thread t ON m.thread_id=t.id LEFT JOIN {SQL_TABLE_PREFIX}users u ON m.poster_id=u.id WHERE m.thread_id='.$th.' AND m.apr=1 ORDER BY m.id');
	while ($r = db_rowobj($c)) {
		$arr[$r->id] = $r;
		@$arr[$r->reply_to]->kiddie_count++;
		@$arr[$r->reply_to]->kiddies[] = &$arr[$r->id];

		if ($r->reply_to == 0) {
			@$tree->kiddie_count++;
			@$tree->kiddies[] = &$arr[$r->id];
		}
	}

	$prev_msg = $next_msg = 0;
	$rev = isset($_GET['rev']) ? $_GET['rev'] : '';
	$reveal = isset($_GET['reveal']) ? $_GET['reveal'] : '';
	$tree_data = '';

	if($arr) {
		reset($tree->kiddies);
		$stack[0] = &$tree;
		$stack_cnt = $tree->kiddie_count;
		$j = 0;
		$lev = 0;
		$prev_id = 0;

		while ($stack_cnt > 0) {
			$cur = &$stack[$stack_cnt-1];

			if (isset($cur->subject) && empty($cur->sub_shown)) {
				$user_login = $cur->poster_id ? '{TEMPLATE: reg_user_link}' : '{TEMPLATE: anon_user}';
				$width = '{TEMPLATE: tree_tab_width}' * ($lev - 1);

				if (_uid && $cur->post_stamp > $usr->last_read && $cur->post_stamp > $frm->last_view) {
					$read_indicator = '{TEMPLATE: tree_unread_message}';
				} else {
					$read_indicator = '{TEMPLATE: tree_read_message}';
				}

				if (isset($cur->kiddies) && $cur->kiddie_count) {
					$tree_data .= $cur->id == $mid ? '{TEMPLATE: tree_branch_selected}' : '{TEMPLATE: tree_branch}';
				} else {
					$tree_data .= $cur->id == $mid ? '{TEMPLATE: tree_entry_selected}' : '{TEMPLATE: tree_entry}';
				}
				$cur->sub_shown = 1;

				if ($cur->id == $mid) {
					$prev_msg = $prev_id;
				}
				if ($prev_id == $mid) {
					$next_msg = $cur->id;
				}

				$prev_id = $cur->id;
			}

			if (!isset($cur->kiddie_count)) {
				$cur->kiddie_count = 0;
			}

			if ($cur->kiddie_count && isset($cur->kiddie_pos)) {
				++$cur->kiddie_pos;
			} else {
				$cur->kiddie_pos = 0;
			}

			if ($cur->kiddie_pos < $cur->kiddie_count) {
				++$lev;
				$stack[$stack_cnt++] = &$cur->kiddies[$cur->kiddie_pos];
			} else { // unwind the stack if needed
				unset($stack[--$stack_cnt]);
				--$lev;
			}

			unset($cur);
		}
	}
	$n = 0; $_GET['start'] = '';
	$usr->md = $frm->md;
	$message_data = tmpl_drawmsg($msg_obj, $usr, $perms, false, $n, array($prev_msg, $next_msg));
	un_register_fps();

	get_prev_next_th_id($frm, $prev_thread_link, $next_thread_link);

	$pdf_link = $FUD_OPT_2 & 2097152 ? '{TEMPLATE: tree_pdf_link}' : '';
	$xml_link = $FUD_OPT_2 & 1048576 ? '{TEMPLATE: tree_xml_link}' : '';

/*{POST_PAGE_PHP_CODE}*/
?>
{TEMPLATE: TREE_PAGE}