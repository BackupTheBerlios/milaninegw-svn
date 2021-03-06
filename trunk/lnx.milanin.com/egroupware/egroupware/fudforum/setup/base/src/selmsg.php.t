<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: selmsg.php.t,v 1.1.1.1 2003/10/17 21:11:30 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

/*{PRE_HTML_PHP}*/

function ifstr($opt1, $opt2, $str)
{
	return (strlen($str) ? $opt1 : $opt2);
}

function valstat($a)
{
	return ($a ? '{TEMPLATE: status_indicator_on}' : '{TEMPLATE: status_indicator_off}');
}

function path_info_lnk($var, $val)
{
	$a = $_GET;
	unset($a['rid'], $a['S'], $a['t']);
	if (isset($a[$var])) {
		unset($a[$var]);
		$rm = 1;
	}
	$url = '/sel';

	foreach ($a as $k => $v) {
		$url .= '/' . $k . '/' . $v;
	}
	if (!isset($rm)) {
		$url .= '/' . $var . '/' . $val;
	}

	return $url . '/' . _rsid;
}

	ses_update_status($usr->sid, '{TEMPLATE: selmsg_update}');

	$count = $usr->posts_ppg ? $usr->posts_ppg : $POSTS_PER_PAGE;
	if (!isset($_GET['start']) || !($start = (int)$_GET['start'])) {
		$start = 0;
	}

	/* limited to today */
	if (isset($_GET['date'])) {
		if ($_GET['date'] != 'today') {
			$tm = __request_timestamp__ - ((int)$_GET['date'] - 1) * 86400;
		} else {
			$tm = __request_timestamp__;
		}
		list($day, $month, $year) = explode(' ', strftime('%d %m %Y', $tm));
		$tm_today_start = mktime(0, 0, 0, $month, $day, $year);
		$tm_today_end = $tm_today_start + 86400;
		$date_limit = ' AND m.post_stamp>'.$tm_today_start.' AND m.post_stamp<'.$tm_today_end . ' ';
	} else {
		$date_limit = '';
	}
	if (!_uid) { /* these options are restricted to registered users */
		unset($_GET['sub_forum_limit'], $_GET['sub_th_limit'], $_GET['unread']);
	}

	$unread_limit = (isset($_GET['unread']) && _uid) ? ' AND m.post_stamp > '.$usr->last_read.' AND (r.id IS NULL OR r.last_view < m.post_stamp) ' : '';
	$th = isset($_GET['th']) ? (int)$_GET['th'] : 0;
	$frm_id = isset($_GET['frm_id']) ? (int)$_GET['frm_id'] : 0;
	$perm_limit = $usr->users_opt & 1048576 ? '' : ' AND (mm.id IS NOT NULL OR ' . (_uid ? '((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END)' : '(g1.group_cache_opt') . ' & 2) > 0)';

	/* mark messages read for registered users */
	if (_uid && isset($_GET['mr']) && !empty($usr->data) && count($usr->data)) {
		foreach ($usr->data as $ti => $mi) {
			if (!(int)$ti || !(int)$mi) {
				break;
			}
			user_register_thread_view($ti, __request_timestamp__, $mi);
		}
	}
	ses_putvar((int)$usr->sid, null);

	/* no other limiters are present, assume 'today' limit */
	if (!$unread_limit && !isset($_GET['date']) && !isset($_GET['reply_count'])) {
		$_GET['date'] = 1;
	}

	/* date limit */
	$dt_opt = isset($_GET['date']) ? str_replace('&date='.$_GET['date'], '', $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . '&amp;date=1';
	$rp_opt = isset($_GET['reply_count']) ? str_replace('&reply_count='.$_GET['reply_count'], '', $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . '&amp;reply_count=0';

	$s_today = valstat(isset($_GET['date']));
	/* reply limit */
	$s_unu = valstat(isset($_GET['reply_count']));

	if (_uid) {
		$un_opt = isset($_GET['unread']) ? str_replace('&unread='.$_GET['unread'], '', $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . '&amp;unread=1';
		$frm_opt = isset($_GET['sub_forum_limit']) ? str_replace('&sub_forum_limit='.$_GET['sub_forum_limit'], '', $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . '&amp;sub_forum_limit=1';
		$th_opt = isset($_GET['sub_th_limit']) ? str_replace('&sub_th_limit='.$_GET['sub_th_limit'], '', $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . '&amp;sub_th_limit=1';

		$s_unread = valstat(isset($_GET['unread']));
		$s_subf = valstat(isset($_GET['sub_forum_limit']));
		$s_subt = valstat(isset($_GET['sub_th_limit']));

		$subscribed_thr = '{TEMPLATE: subscribed_thr}';
		$subscribed_frm = '{TEMPLATE: subscribed_frm}';
		$unread_messages = '{TEMPLATE: unread_messages}';
	} else {
		$subscribed_thr = $subscribed_frm = $unread_messages = '';
	}

	$todays_posts = '{TEMPLATE: todays_posts}';
	$unanswered = '{TEMPLATE: unanswered}';

	make_perms_query($fields, $join);

	if (!$unread_limit) {
		$total = (int) q_singleval('SELECT count(*) FROM {SQL_TABLE_PREFIX}msg m INNER JOIN {SQL_TABLE_PREFIX}thread t ON m.thread_id=t.id INNER JOIN {SQL_TABLE_PREFIX}forum f ON t.forum_id=f.id INNER JOIN {SQL_TABLE_PREFIX}cat c ON f.cat_id=c.id '.(isset($_GET['sub_forum_limit']) ? 'INNER JOIN {SQL_TABLE_PREFIX}forum_notify fn ON fn.forum_id=f.id AND fn.user_id='._uid : '').' '.(isset($_GET['sub_th_limit']) ? 'INNER JOIN {SQL_TABLE_PREFIX}thread_notify tn ON tn.thread_id=t.id AND tn.user_id='._uid : '').' '.$join.' LEFT JOIN {SQL_TABLE_PREFIX}mod mm ON mm.forum_id=f.id AND mm.user_id='._uid.' WHERE m.apr=1 '.$date_limit.' '.($frm_id ? ' AND f.id='.$frm_id : '').' '.($th ? ' AND t.id='.$th : '').' '.(isset($_GET['reply_count']) ? ' AND t.replies='.(int)$_GET['reply_count'] : '').' '.$perm_limit);
	}

/*{POST_HTML_PHP}*/

	if ($unread_limit || $total) {
		/* figure out the query */
		$c = $query_type('SELECT
			m.*,
			t.thread_opt, t.root_msg_id, t.last_post_id, t.forum_id,
			f.message_threshold, f.name,
			u.id AS user_id, u.alias AS login, u.avatar_loc, u.email, u.posted_msg_count, u.join_date, u.location,
			u.sig, u.custom_status, u.icq, u.jabber, u.affero, u.aim, u.msnm, u.yahoo, u.last_visit AS time_sec, u.users_opt,
			l.name AS level_name, l.level_opt, l.img AS level_img,
			p.max_votes, p.expiry_date, p.creation_date, p.name AS poll_name, p.total_votes,
			pot.id AS cant_vote,
			r.last_view,
			mm.id AS md,
			m2.subject AS thr_subject,
			'.$fields.'
		FROM
			{SQL_TABLE_PREFIX}msg m
			INNER JOIN {SQL_TABLE_PREFIX}thread t ON m.thread_id=t.id
			INNER JOIN {SQL_TABLE_PREFIX}msg m2 ON m2.id=t.root_msg_id
			INNER JOIN {SQL_TABLE_PREFIX}forum f ON t.forum_id=f.id
			INNER JOIN {SQL_TABLE_PREFIX}cat c ON f.cat_id=c.id
			'.(isset($_GET['sub_forum_limit']) ? 'INNER JOIN {SQL_TABLE_PREFIX}forum_notify fn ON fn.forum_id=f.id AND fn.user_id='._uid : '').'
			'.(isset($_GET['sub_th_limit']) ? 'INNER JOIN {SQL_TABLE_PREFIX}thread_notify tn ON tn.thread_id=t.id AND tn.user_id='._uid : '').'
			'.$join.'
			LEFT JOIN {SQL_TABLE_PREFIX}read r ON r.thread_id=t.id AND r.user_id='._uid.'
			LEFT JOIN {SQL_TABLE_PREFIX}users u ON m.poster_id=u.id
			LEFT JOIN {SQL_TABLE_PREFIX}level l ON u.level_id=l.id
			LEFT JOIN {SQL_TABLE_PREFIX}poll p ON m.poll_id=p.id
			LEFT JOIN {SQL_TABLE_PREFIX}poll_opt_track pot ON pot.poll_id=p.id AND pot.user_id='._uid.'
			LEFT JOIN {SQL_TABLE_PREFIX}mod mm ON mm.forum_id=f.id AND mm.user_id='._uid.'
		WHERE
			m.apr=1
			'.$date_limit.'
			'.($frm_id ? ' AND f.id='.$frm_id : '').'
			'.($th ? ' AND t.id='.$th : '').'
			'.(isset($_GET['reply_count']) ? ' AND t.replies='.(int)$_GET['reply_count'] : '').'
			'.$unread_limit.'
			'.$perm_limit.'
		ORDER BY
			f.last_post_id, t.last_post_date, m.post_stamp
		LIMIT '.qry_limit($count, $start));

		/* message drawing code */
		$message_data = '';
		$n = $prev_frm = $prev_th = '';
		while ($r = db_rowobj($c)) {
			if ($prev_frm != $r->forum_id) {
				$prev_frm = $r->forum_id;
				$message_data .= '{TEMPLATE: forum_row}';
				$perms = perms_from_obj($r, ($usr->users_opt & 1048576));
			}
			if ($prev_th != $r->thread_id) {
				$thl[] = $r->thread_id;
				$prev_th = $r->thread_id;
				$message_data .= '{TEMPLATE: thread_row}';
			}
			if (_uid && $r->last_view < $r->post_stamp && $r->post_stamp > $usr->last_read && !isset($mark_read[$r->thread_id])) {
				$mark_read[$r->thread_id] = $r->id;
			}
			$usr->md = $r->md;
			$message_data .= tmpl_drawmsg($r, $usr, $perms, false, $n, '');
		}
		un_register_fps();
		unset($c);
	} else {
		$message_data = '';
	}

	if (_uid && isset($mark_read)) {
		ses_putvar((int)$usr->sid, $mark_read);
	}
	if (isset($thl)) {
		q('UPDATE {SQL_TABLE_PREFIX}thread SET views=views+1 WHERE id IN('.implode(',', $thl).')');
	}

	if (!$message_data) {
		if (isset($_GET['unread'])) {
			$message_data = '{TEMPLATE: no_unread_messages}';
			if (!$frm_id && !$th) {
				user_mark_all_read(_uid);
			} else if ($frm_id) {
				user_mark_forum_read(_uid, $frm_id, $usr->last_read);
			}
		} else {
			$message_data = '{TEMPLATE: no_result}';
		}
	}

	if (!$unread_limit && $total > $count) {
		if (!isset($_GET['mr'])) {
			$_SERVER['QUERY_STRING'] .= '&mr=1';
		}
		$pager = tmpl_create_pager($start, $count, $total, '{ROOT}?' . str_replace('&start='.$start, '', $_SERVER['QUERY_STRING']));
	} else if ($unread_limit) {
		if (!isset($_GET['mark_page_read'])) {
			$_SERVER['QUERY_STRING'] .= '&amp;mark_page_read=1&amp;mr=1';
		}
		$pager = '{TEMPLATE: more_unread_messages}';
	} else {
		$pager = '';
	}

/*{POST_PAGE_PHP_CODE}*/
?>
{TEMPLATE: SELMSG_PAGE}