<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: thread.php.t,v 1.1.1.1 2003/10/17 21:11:30 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function pager_replace(&$str, $s, $c)
{
	$str = str_replace('%s', $s, $str);
	$str = str_replace('%c', $c, $str);
}

function tmpl_create_pager($start, $count, $total, $arg, $suf='', $append=1, $js_pager=false)
{
	if (!$count) {
		$count =& $GLOBALS['POSTS_PER_PAGE'];
	}
	if ($total <= $count) {
		return;
	}

	$cur_pg = ceil($start / $count);
	$ttl_pg = ceil($total / $count);

	$page_pager_data = '';

	if (($page_start = $start - $count) > -1) {
		if ($append) {
			$page_first_url = $arg.'&amp;start=0'.$suf;
			$page_prev_url = $arg.'&amp;start='.$page_start.$suf;
		} else {
			$page_first_url = $page_prev_url = $arg;
			pager_replace($page_first_url, 0, $count);
			pager_replace($page_prev_url, $page_start, $count);
		}

		$page_pager_data .= !$js_pager ? '&nbsp;<a href="'.$page_first_url.'" class="PagerLink">&laquo;</a>&nbsp;&nbsp;<a href="'.$page_prev_url.'" class="PagerLink">&lt;</a>&nbsp;&nbsp;' : '&nbsp;<a href="javascript://" onClick="'.$page_first_url.'" class="PagerLink">&laquo;</a>&nbsp;&nbsp;<a href="javascript://" onClick="'.$page_prev_url.'" class="PagerLink">&lt;</a>&nbsp;&nbsp;';
	}

	$mid = ceil($GLOBALS['GENERAL_PAGER_COUNT'] / 2);

	if ($ttl_pg > $GLOBALS['GENERAL_PAGER_COUNT']) {
		if (($mid + $cur_pg) >= $ttl_pg) {
			$end = $ttl_pg;
			$mid += $mid + $cur_pg - $ttl_pg;
			$st = $cur_pg - $mid;
		} else if (($cur_pg - $mid) <= 0) {
			$st = 0;
			$mid += $mid - $cur_pg;
			$end = $mid + $cur_pg;
		} else {
			$st = $cur_pg - $mid;
			$end = $mid + $cur_pg;
		}

		if ($st < 0) {
			$start = 0;
		}
		if ($end > $ttl_pg) {
			$end = $ttl_pg;
		}
	} else {
		$end = $ttl_pg;
		$st = 0;
	}

	while ($st < $end) {
		if ($st != $cur_pg) {
			$page_start = $st * $count;
			if ($append) {
				$page_page_url = $arg.'&amp;start='.$page_start.$suf;
			} else {
				$page_page_url = $arg;
				pager_replace($page_page_url, $page_start, $count);
			}
			$st++;
			$page_pager_data .= !$js_pager ? '<a href="'.$page_page_url.'" class="PagerLink">'.$st.'</a>&nbsp;&nbsp;' : '<a href="javascript://" onClick="'.$page_page_url.'" class="PagerLink">'.$st.'</a>&nbsp;&nbsp;';
		} else {
			$st++;
			$page_pager_data .= !$js_pager ? $st.'&nbsp;&nbsp;' : $st.'&nbsp;&nbsp;';
		}
	}

	$page_pager_data = substr($page_pager_data, 0 , strlen((!$js_pager ? '&nbsp;&nbsp;' : '&nbsp;&nbsp;')) * -1);

	if (($page_start = $start + $count) < $total) {
		$page_start_2 = ($st - 1) * $count;
		if ($append) {
			$page_next_url = $arg.'&amp;start='.$page_start.$suf;
			$page_last_url = $arg.'&amp;start='.$page_start_2.$suf;
		} else {
			$page_next_url = $page_last_url = $arg;
			pager_replace($page_next_url, $page_start, $count);
			pager_replace($page_last_url, $page_start_2, $count);
		}
		$page_pager_data .= !$js_pager ? '&nbsp;&nbsp;<a href="'.$page_next_url.'" class="PagerLink">&gt;</a>&nbsp;&nbsp;<a href="'.$page_last_url.'" class="PagerLink">&raquo;</a>' : '&nbsp;&nbsp;<a href="javascript://" onClick="'.$page_next_url.'" class="PagerLink">&gt;</a>&nbsp;&nbsp;<a href="javascript://" onClick="'.$page_last_url.'" class="PagerLink">&raquo;</a>';
	}

	return !$js_pager ? '<font class="SmallText"><b>Pages ('.$ttl_pg.'): 
['.$page_pager_data.']
</b></font>' : '<font class="SmallText"><b>Pages ('.$ttl_pg.'): 
['.$page_pager_data.']
</b></font>';
}function is_forum_notified($user_id, $forum_id)
{
	return q_singleval('SELECT id FROM phpgw_fud_forum_notify WHERE forum_id='.$forum_id.' AND user_id='.$user_id);
}

function forum_notify_add($user_id, $forum_id)
{
	if (!is_forum_notified($user_id, $forum_id)) {
		q('INSERT INTO phpgw_fud_forum_notify(user_id, forum_id) VALUES ('.$user_id.', '.$forum_id.')');
	}
}

function forum_notify_del($user_id, $forum_id)
{
	q('DELETE FROM phpgw_fud_forum_notify WHERE forum_id='.$forum_id.' AND user_id='.$user_id);
}/* make sure that we have what appears to be a valid forum id */
if (!isset($_GET['frm_id']) || (!($frm_id = (int)$_GET['frm_id']))) {
	invl_inp_err();
}

if (!isset($_GET['start']) || !($start = (int)$_GET['start'])) {
	$start = 0;
}

/* This query creates frm object that contains info about the current
 * forum, category & user's subscription status & permissions to the
 * forum.
 */

make_perms_query($fields, $join, $frm_id);

$frm = db_sab('SELECT
			f.id, f.name, f.thread_count,
			c.name AS cat_name,
			fn.forum_id AS subscribed,
			m.forum_id AS md,
			a.ann_id AS is_ann,
			'.$fields.'
		FROM phpgw_fud_forum f
		INNER JOIN phpgw_fud_cat c ON c.id=f.cat_id
		LEFT JOIN phpgw_fud_forum_notify fn ON fn.user_id='._uid.' AND fn.forum_id='.$frm_id.'
		LEFT JOIN phpgw_fud_mod m ON m.user_id='._uid.' AND m.forum_id='.$frm_id.'
		'.$join.'
		LEFT JOIN phpgw_fud_ann_forums a ON a.forum_id='.$frm_id.'
		WHERE f.id='.$frm_id.' LIMIT 1');

if (!$frm) {
	invl_inp_err();
}

$MOD = ($usr->users_opt & 1048576 || $frm->md);

/* check that the user has permissions to access this forum */
if (!($frm->group_cache_opt & 2) && !$MOD) {
	if (!isset($_GET['logoff'])) {
		std_error('perms');
	} else {
		header('Location: /egroupware/fudforum/3814588639/index.php?' . _rsidl);
		exit;
	}
}

if ($_GET['t'] == 'threadt') {
	$ann_cols = '5';
	$cur_frm_page = $start + 1;
} else {
	$ann_cols = '6';
	$cur_frm_page = floor($start / $THREADS_PER_PAGE) + 1;
}

$thread_printable_pdf = $FUD_OPT_2 & 2097152 ? '&nbsp;[ <a href="'.$GLOBALS['WWW_ROOT'].'pdf.php?frm='.$frm->id.'&amp;page='.$cur_frm_page.'" class="GenLink">Generate printable PDF</a> ]' : '';
$thread_syndicate = $FUD_OPT_2 & 1048576 ? '&nbsp;[ <a href="/egroupware/fudforum/3814588639/index.php?t=help_index&amp;section=boardusage#syndicate" class="GenLink">Syndicate this forum (XML)</a> ]' : '';

/* do various things for registered users */
if (_uid) {
	if (isset($_GET['sub'])) {
		forum_notify_add(_uid, $frm->id);
		$frm->subscribed = 1;
	} else if (isset($_GET['unsub'])) {
		forum_notify_del(_uid, $frm->id);
		$frm->subscribed = 0;
	}
	$subscribe = $frm->subscribed ? '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t='.$_GET['t'].'&amp;unsub=1&amp;frm_id='.$frm->id.'&amp;start='.$start.'&amp;'._rsid.'" title="Stop receiving notifications about new topics in the forum">Unsubscribe</a>' : '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t='.$_GET['t'].'&amp;sub=1&amp;frm_id='.$frm->id.'&amp;start='.$start.'&amp;'._rsid.'" title="Receive notifications when someone creates a new topic in this forum">Subscribe</a>';
	$mark_all_read = '<div align="right"><font class="SmallText">[<a href="/egroupware/fudforum/3814588639/index.php?t=markread&amp;'._rsid.'&id='.$frm->id.'" class="GenLink" title="All unread messages inside this forum will be marked read">mark all unread forum messages read</a>]'.$thread_printable_pdf.$thread_syndicate.'</font></div>';
	$merget = ($MOD || $frm->group_cache_opt & 2048) ? '&nbsp;<a href="/egroupware/fudforum/3814588639/index.php?t=merge_th&amp;frm='.$frm->id.'&amp;'._rsid.'" class="GenLink">Merge Topics</a>' : '';
} else {
	$merget = $subscribe = '';
	$mark_all_read = '<div align="right"><font class="SmallText">'.$thread_printable_pdf.$thread_syndicate.'</font></div>';
}

$ppg = $usr->posts_ppg ? $usr->posts_ppg : $POSTS_PER_PAGE;function &get_all_read_perms($uid, $mod)
{
	$limit = array(0);

	$r = uq('SELECT resource_id, group_cache_opt FROM phpgw_fud_group_cache WHERE user_id='._uid);
	while ($ent = db_rowarr($r)) {
		$limit[$ent[0]] = $ent[1] & 2;
	}

	if (_uid) {
		$r = uq("SELECT resource_id FROM phpgw_fud_group_cache WHERE resource_id NOT IN (".implode(',', array_keys($limit)).") AND user_id=2147483647 AND (group_cache_opt & 2) > 0");
		while ($ent = db_rowarr($r)) {
			if (!isset($limit[$ent[0]])) {
				$limit[$ent[0]] = 1;
			}
		}

		if ($mod) {
			$r = uq('SELECT forum_id FROM phpgw_fud_mod WHERE user_id='._uid);
			while ($ent = db_rowarr($r)) {
				$limit[$ent[0]] = 1;
			}
		}
	}

	return $limit;
}

function perms_from_obj($obj, $adm)
{
	$perms = 1|2|4|8|16|32|64|128|256|512|1024|2048|4096|8192|16384|32768;

	if ($adm || $obj->md) {
		return $perms;
	}

	return ($perms & $obj->group_cache_opt);
}

function make_perms_query(&$fields, &$join, $fid='')
{
	if (!$fid) {
		$fid = 'f.id';
	}

	if (_uid) {
		$join = ' INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id=2147483647 AND g1.resource_id='.$fid.' LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id='.$fid.' ';
		$fields = ' (CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) AS group_cache_opt ';
	} else {
		$join = ' INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id=0 AND g1.resource_id='.$fid.' ';
		$fields = ' g1.group_cache_opt ';
	}
}

	ses_update_status($usr->sid, 'Browsing forum <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=thread&amp;frm_id='.$frm->id.'">'.htmlspecialchars($frm->name).'</a>', $frm_id);

$announcements = '';
	if ($frm->is_ann) {
		$today = gmdate('Ymd', __request_timestamp__);
		$res = uq('SELECT a.subject, a.text FROM phpgw_fud_announce a INNER JOIN phpgw_fud_ann_forums af ON a.id=af.ann_id AND af.forum_id='.$frm->id.' WHERE a.date_started<='.$today.' AND a.date_ended>='.$today);
		while ($r = db_rowarr($res)) {
			$announcements .= '<tr><td class="AnnText" colspan="'.$ann_cols.'"><font class="AnnSubjText">'.$r[0].'</font><br />'.$r[1].'</td></tr>';
		}
	}function tmpl_create_forum_select($frm_id, $mod)
{
	$prev_cat_id = 0;
	$selection_options = '';

	if (!isset($_GET['t']) || ($_GET['t'] != 'thread' && $_GET['t'] != 'threadt')) {
		$dest = t_thread_view;
	} else {
		$dest = $_GET['t'];
	}

	if (!_uid) { /* anon user, we can optimize things quite a bit here */
		$c = q('SELECT f.id, f.name, c.name, c.id FROM phpgw_fud_group_cache g INNER JOIN phpgw_fud_fc_view v ON v.f=g.resource_id INNER JOIN phpgw_fud_forum f ON f.id=g.resource_id INNER JOIN phpgw_fud_cat c ON c.id=f.cat_id WHERE g.user_id=0 AND group_cache_opt>=1 AND (group_cache_opt & 1) > 0 ORDER BY v.id');
		while ($r = db_rowarr($c)) {
			if ($prev_cat_id != $r[3]) {
				$prev_cat_id = $r[3];
				$selection_options .= '<option value="0">'.$r[2].'</option>';
			}
			$selected = $frm_id == $r[0] ? ' selected' : '';
			$selection_options .= '<option value="'.$r[0].'"'.$selected.'>&nbsp;&nbsp;&nbsp;&nbsp;'.htmlspecialchars($r[1]).'</option>';
		}
		unset($c);

		return '<form action="/egroupware/fudforum/3814588639/index.php" name="frmquicksel" method="get" onSubmit="javascript: if (document.frmquicksel.frm_id.value < 1) document.frmquicksel.frm_id.value='.$frm_id.';">
<table border=0 cellspacing=0 cellpadding=1><tr><td class="GenText" valign="bottom">
<font class="SmallText"><b>Goto Forum:</b><br /></font>
<select class="SmallText" name="frm_id" onChange="javascript: if ( this.value==0 ) return false; document.frmquicksel.submit();">
'.$selection_options.'
</select>
<input type="hidden" name="t" value="'.$dest.'">'._hs.'<input type="hidden" name="forum_redr" value="1">
</td><td valign="bottom"><input type="submit" class="button" name="frm_goto" value="Go" ></td></tr></table></form>';
	} else {
		$c = q('SELECT f.id, f.name, c.name, c.id, CASE WHEN '.$GLOBALS['usr']->last_read.' < m.post_stamp AND (fr.last_view IS NULL OR m.post_stamp > fr.last_view) THEN 1 ELSE 0 END AS reads
			FROM phpgw_fud_fc_view v
			INNER JOIN phpgw_fud_forum f ON f.id=v.f
			INNER JOIN phpgw_fud_cat c ON c.id=v.c
			LEFT JOIN phpgw_fud_msg m ON m.id=f.last_post_id
			'.($mod ? '' : 'LEFT JOIN phpgw_fud_mod mm ON mm.user_id='._uid.' AND mm.forum_id=f.id INNER JOIN phpgw_fud_group_cache g1 ON g1.resource_id=f.id AND g1.user_id=2147483647 LEFT JOIN phpgw_fud_group_cache g2 ON g2.resource_id=f.id AND g2.user_id='._uid).'
			LEFT JOIN phpgw_fud_forum_read fr ON fr.forum_id=f.id AND fr.user_id='._uid.'
			'.($mod ? '' : ' WHERE mm.id IS NOT NULL OR ((CASE WHEN g2.id IS NULL THEN g1.group_cache_opt ELSE g2.group_cache_opt END) & 1) > 0').'
			ORDER BY v.id');

		while ($r = db_rowarr($c)) {
			if ($prev_cat_id != $r[3]) {
				$prev_cat_id = $r[3];
				$selection_options .= '<option value="0">'.$r[2].'</option>';
			}
			$selected = $frm_id == $r[0] ? ' selected' : '';
			$selection_options .= $r[4] ? '<option value="'.$r[0].'"'.$selected.'>&nbsp;&nbsp;&nbsp;&nbsp;*(UNREAD) '.htmlspecialchars($r[1]).'</option>' : '<option value="'.$r[0].'"'.$selected.'>&nbsp;&nbsp;&nbsp;&nbsp;'.htmlspecialchars($r[1]).'</option>';
		}
		unset($c);

		return '<form action="/egroupware/fudforum/3814588639/index.php" name="frmquicksel" method="get" onSubmit="javascript: if (document.frmquicksel.frm_id.value < 1) document.frmquicksel.frm_id.value='.$frm_id.';">
<table border=0 cellspacing=0 cellpadding=1><tr><td class="GenText" valign="bottom">
<font class="SmallText"><b>Goto Forum:</b><br /></font>
<select class="SmallText" name="frm_id" onChange="javascript: if ( this.value==0 ) return false; document.frmquicksel.submit();">
'.$selection_options.'
</select>
<input type="hidden" name="t" value="'.$dest.'">'._hs.'<input type="hidden" name="forum_redr" value="1">
</td><td valign="bottom"><input type="submit" class="button" name="frm_goto" value="Go" ></td></tr></table></form>';
	}
}

	$forum_select = tmpl_create_forum_select((isset($frm->forum_id) ? $frm->forum_id : $frm->id), $usr->users_opt & 1048576);if (!isset($th)) {
	$th = 0;
}
if (!isset($frm->id)) {
	$frm->id = 0;
}

$unread_posts = _uid ? '<b>::</b> <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=selmsg&amp;unread=1&amp;'._rsid.'&amp;frm_id='.$frm->id.'" title="Show all unread messages">Unread Posts</a>&nbsp;' : '';
$unanswered_posts = !$th ? '<b>::</b> <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=selmsg&amp;reply_count=0&amp;'._rsid.'&amp;frm_id='.$frm->id.'" title="Show all messages, which have no replies">Unanswered Posts</a>' : '';

	$TITLE_EXTRA = ': '.htmlspecialchars($frm->name);

	$result = uq('SELECT
		m.attach_cnt, m.poll_id, m.subject, m.icon, m.post_stamp,
		u.alias, u.id,
		u2.id, u2.alias,
		m2.id, m2.post_stamp,
		f.id, f.name,
		t.id, t.moved_to, t.root_msg_id, t.replies, t.rating, t.thread_opt, t.views,
		r.last_view
		FROM phpgw_fud_thread_view tv
			INNER JOIN phpgw_fud_thread	t	ON tv.thread_id=t.id
			INNER JOIN phpgw_fud_msg	m	ON t.root_msg_id=m.id
			INNER JOIN phpgw_fud_msg	m2	ON m2.id=t.last_post_id
			LEFT JOIN phpgw_fud_users	u	ON u.id=m.poster_id
			LEFT JOIN phpgw_fud_users	u2	ON u2.id=m2.poster_id
			LEFT JOIN phpgw_fud_forum	f	ON f.id=t.moved_to
			LEFT JOIN phpgw_fud_read 	r	ON t.id=r.thread_id AND r.user_id='._uid.'
			WHERE tv.forum_id='.$frm_id.' AND tv.page='.$cur_frm_page.' ORDER BY tv.pos ASC');
	/* Field Defenitions
	 * 0 msg.attach_cnt
	 * 1 msg.poll_id
	 * 2 msg.subject
	 * 3 msg.icon
	 * 4 msg.post_stamp
	 * 5 users.alias
	 * 6 users.id
	 * 7 fud_users_2.id
	 * 8 fud_users_2.alias
	 * 9 fud_msg_2.id
	 * 10 fud_msg_2.post_stamp
	 * 11 forum.id
	 * 12 forum.name
	 * 13 thread.id
	 * 14 thread.moved_to
	 * 15 thread.root_msg_id
	 * 16 thread.replies
	 * 17 thread.thread_opt
	 * 18 thread.rating
	 * 19 thread.views
	 * 20 read.last_view
	 */

	if (!($r = @db_rowarr($result))) {
		$thread_list_table_data = '<font class="GenText">There are no messages in this forum.</font>';
		$threaded_view = $admin_heading_row = '';
	} else {
		$admin_heading_row = ($MOD || $frm->group_cache_opt & 8224);
		$threaded_view = $FUD_OPT_2 & 512 ? '<a href="/egroupware/fudforum/3814588639/index.php?t=threadt&amp;frm_id='.$frm->id.'&amp;'._rsid.'" class="GenLink"><img title="Switch to threaded view of this topic" alt="Switch to threaded view of this topic" src="/egroupware/fudforum/3814588639/theme/default/images/tree_view.gif" /></a>&nbsp;' : '';
		$thread_list_table_data = '';

		do {
			$r[18] = (int) $r[18];

			if ($r[14]) {
				$thread_list_table_data .= '<tr>
<td width=1 class="RowStyleB"><img src="/egroupware/fudforum/3814588639/theme/default/images/moved.png" title="This topic has been moved to another forum" /></td>
<td class="RowStyleB" colspan="5" align="center" class="GenText"><a href="/egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&amp;goto='.$r[15].'&amp;'._rsid.'" class="GenLink">'.$r[2].'</a> has been moved to <a href="/egroupware/fudforum/3814588639/index.php?t='.t_thread_view.'&amp;frm_id='.$r[11].'&amp;'._rsid.'" class="GenLink">'.$r[12].'</a> by the moderator</td>
</tr>';
				continue;
			}
			$msg_count = $r[16] + 1;

			if ($msg_count > $ppg && $usr->users_opt & 256) {
				if ($THREAD_MSG_PAGER < ($pgcount = ceil($msg_count/$ppg))) {
					$i = $pgcount - $THREAD_MSG_PAGER;
					$mini_pager_data = '&nbsp;...';
				} else {
					$mini_pager_data = '';
					$i = 0;
				}

				for ($i; $i < $pgcount; $i++) {
					$st_pos = $i * $ppg;
					$pg_num = $i + 1;
					$mini_pager_data .= '&nbsp;<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&amp;th='.$r[13].'&amp;start='.$st_pos.'&amp;'._rsid.'">'.$pg_num.'</a>';
				}

				if ($mini_pager_data) {
					$mini_thread_pager = '<font class="SmallText">(<img src="/egroupware/fudforum/3814588639/theme/default/images/pager.gif" alt="" />'.$mini_pager_data.')</font>';
				} else {
					$mini_thread_pager = '';
				}
			} else {
				$mini_thread_pager = '';
			}

			$thread_poll_indicator = $r[1] ? 'Poll:&nbsp;' : '';
			$thread_attach_indicator = $r[0] ? '<img src="/egroupware/fudforum/3814588639/theme/default/images/attachment.gif" alt="" />' : '';
			$thread_icon = $r[3] ? '<img src="images/message_icons/'.$r[3].'" alt="'.$r[3].'" />' : '&nbsp;';
			if ($FUD_OPT_2 & 4096) {
				$rating = $r[17] ? '<img src="/egroupware/fudforum/3814588639/theme/default/images/'.$r[17].'stars.gif" alt="'.$r[17].'" />' : '';
			} else {
				$rating = '';
			}
			if ($r[18] > 1) {
				$stick_status = $r[18] & 4 ? '<font class="StClr">sticky:&nbsp;</font>' : '<font class="AnClr">Announcement:&nbsp;</font>';
			} else {
				$stick_status = '';
			}
			$user_link = $r[8] ? '<a href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$r[7].'&amp;'._rsid.'">'.$r[8].'</a>' : ''.$GLOBALS['ANON_NICK'];
			$first_post_login = $r[5] ? '<a href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$r[6].'&amp;'._rsid.'">'.$r[5].'</a>' : ''.$GLOBALS['ANON_NICK'];

			$thread_read_status = $first_unread_msg_link = '';
			if (_uid && $usr->last_read < $r[10] && $r[10] > $r[20]) {
				if ($r[18] & 1) {
					$thread_read_status = '<img src="/egroupware/fudforum/3814588639/theme/default/images/unreadlocked.png" width=32 height=32 title="Locked topic with unread messages" alt="" />';
				} else {
					$thread_read_status = '<img src="/egroupware/fudforum/3814588639/theme/default/images/unread.png" width=32 height=32 title="This topic contains messages you have not yet read" alt="" />';
				}
				/* do not show 1st unread message link if thread has no replies */
				if ($r[16]) {
					$first_unread_msg_link = '<a href="/egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&amp;th='.$r[13].'&amp;unread=1&amp;'._rsid.'"><img src="/egroupware/fudforum/3814588639/theme/default/images/newposts.gif" title="Click here to go the first unread message in this topic" alt="" /></a>&nbsp;';
				}
			}

			if (!$thread_read_status) {
				if ($r[18] & 1) {
					$thread_read_status = '<img src="/egroupware/fudforum/3814588639/theme/default/images/readlocked.png" width=32 height=32 title="This topic has been locked" alt="" />';
				} else if (!_uid) {
					$thread_read_status = '<img src="/egroupware/fudforum/3814588639/theme/default/images/read.png" width=32 height=32 title="The read &amp; unread messages are only tracked for registered users" alt="" />';
				} else {
					$thread_read_status = '<img src="/egroupware/fudforum/3814588639/theme/default/images/read.png" width=32 height=32 title="This topic has no unread messages" alt="" />';
				}
			}

			$thread_first_post = '<font class="DateText">'.strftime("%a, %d %B %Y", $r[4]).'</font>';

			if ($admin_heading_row) {
				if ($MOD || (($frm->group_cache_opt & 8224) == 8224)) {
					$admin_control_row = '<div class="ModOpt">Moderator Options: <a href="javascript://" onClick="javascript: window_open(\'/egroupware/fudforum/3814588639/index.php?t=mvthread&amp;'._rsid.'&amp;th='.$r[13].'\', \'th_move\', 300, 400);">Move</a> | <a href="/egroupware/fudforum/3814588639/index.php?t=mmod&amp;'._rsid.'&amp;th='.$r[13].'&amp;del='.$r[15].'">Delete</a></div>';
				} else if ($frm->group_cache_opt & 32) {
					$admin_control_row = '<div class="ModOpt">Moderator Options: <a href="/egroupware/fudforum/3814588639/index.php?t=mmod&amp;'._rsid.'&amp;th='.$r[13].'&amp;del='.$r[15].'">Delete</a></div>';
				} else {
					$admin_control_row = '<div class="ModOpt">Moderator Options: <a href="javascript://" onClick="javascript: window_open(\'/egroupware/fudforum/3814588639/index.php?t=mvthread&amp;'._rsid.'&amp;th='.$r[13].'\', \'th_move\', 300, 400);">Move</a></div>';
				}
			} else {
				$admin_control_row = '';
			}
			$thread_list_table_data .= '<tr>		
<td width=1 class="RowStyleB">'.$thread_read_status.'</td>
<td class="RowStyleB" width=1 align="center">'.$thread_icon.'</td>
<td class="RowStyleA">'.$stick_status.$first_unread_msg_link.$thread_poll_indicator.$thread_attach_indicator.'<a class="big" href="/egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&amp;th='.$r[13].'&amp;start=0&amp;'._rsid.'">'.$r[2].'</a> '.$rating.' '.$mini_thread_pager.'
<div class="TopBy">By: '.$first_post_login.' on <font class="DateText">'.strftime("%a, %d %B %Y", $r[4]).'</font></div>'.$admin_control_row.'</td>
<td class="RowStyleB" align="center">'.$r[16].'</td>
<td class="RowStyleB" align="center">'.$r[19].'</td>
<td class="RowStyleC" nowrap><font class="DateText">'.strftime("%a, %d %B %Y %H:%M", $r[10]).'</font><br />By: '.$user_link.' <a href="/egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&amp;goto='.$r[9].'&amp;'._rsid.'" class="GenLink"><img src="/egroupware/fudforum/3814588639/theme/default/images/goto.gif" title="Go to the last message in this topic" alt="" /></a></td>
</tr>';
		} while (($r = db_rowarr($result)));
	}

	$page_pager = tmpl_create_pager($start, $THREADS_PER_PAGE, $frm->thread_count, '/egroupware/fudforum/3814588639/index.php?t=thread&amp;frm_id='.$frm_id.'&amp;'._rsid);

if ($FUD_OPT_2 & 2) {
	$page_gen_end = gettimeofday();
	$page_gen_time = sprintf('%.5f', ($page_gen_end['sec'] - $PAGE_TIME['sec'] + (($page_gen_end['usec'] - $PAGE_TIME['usec'])/1000000)));
	$page_stats = '<br /><div align="left" class="SmallText">Total time taken to generate the page: '.$page_gen_time.' seconds</div>';
} else {
	$page_stats = '';
}
?>
<?php echo $GLOBALS['fud_egw_hdr']; ?>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">
<table width="100%" border=0 cellspacing=0 cellpadding=0><tr>
<td align="left" width="100%"><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=index&amp;<?php echo _rsid; ?>"><?php echo $frm->cat_name; ?></a><font class="GenText"> &raquo; <b><?php echo htmlspecialchars($frm->name); ?></b></font><br /><font class="GenText"><b>Show:</b></font> <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=selmsg&amp;date=today&amp;<?php echo _rsid; ?>&amp;frm_id=<?php echo $frm->id; ?>&amp;th=<?php echo $th; ?>" title="Show all messages that were posted today">Today's Posts</a>&nbsp;<?php echo $unread_posts.$unanswered_posts; ?> <b>::</b> <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=polllist&amp;<?php echo _rsid; ?>">Show Polls</a> <b>::</b> <a href="/egroupware/fudforum/3814588639/index.php?t=mnav&amp;<?php echo _rsid; ?>" class="GenLink">Message Navigator</a><br /><img src="blank.gif" alt="" height=2 /><br /><?php echo $subscribe.$merget; ?></td>
<td valign="bottom" align="right" class="GenText"><?php echo $threaded_view; ?><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=post&amp;frm_id=<?php echo $frm->id; ?>&amp;<?php echo _rsid; ?>"><img src="/egroupware/fudforum/3814588639/theme/default/images/new_thread.gif" alt="Create a new topic" /></a></td>
</tr></table>
<table border="0" cellspacing="1" cellpadding="2" class="pad">
<tr>
	<th>&nbsp;</th>
	<th>&nbsp;</th>
	<th width="100%">Topic</th>
	<th width=1>Replies</th>
	<th width=1>Views</th>
	<th nowrap>Last Post</th>
</tr>
<?php echo $announcements; ?>
<?php echo $thread_list_table_data; ?>
</table>
<table border=0 cellspacing=0 cellpadding=0 width="100%">
<tr>
<td valign="top"><?php echo $page_pager; ?>&nbsp;</td>
<td align="right" class="GenText" valign="bottom" nowrap><?php echo $threaded_view; ?><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=post&amp;frm_id=<?php echo $frm->id; ?>&amp;<?php echo _rsid; ?>"><img src="/egroupware/fudforum/3814588639/theme/default/images/new_thread.gif" alt="Create a new topic" /></a></td>
</tr>
</table>
<?php echo $forum_select; ?>
<?php echo $mark_all_read; ?>
<br />
<div class="RowStyleB"><font class="SmallText">
<div class="LegendH"><b>Legend:</b></div>
<img src="/egroupware/fudforum/3814588639/theme/default/images/unread.png" width=32 height=32 alt="New Posts" /> New Posts&nbsp;&nbsp;
<img src="/egroupware/fudforum/3814588639/theme/default/images/read.png" width=32 height=32 alt="No New posts" /> No New posts&nbsp;&nbsp;
<img src="/egroupware/fudforum/3814588639/theme/default/images/unreadlocked.png" width=32 height=32 alt="Locked (w/ unread posts)" /> Locked (w/ unread posts)&nbsp;&nbsp;
<img src="/egroupware/fudforum/3814588639/theme/default/images/readlocked.png" width=32 height=32 alt="Locked" /> Locked&nbsp;&nbsp;
<img src="/egroupware/fudforum/3814588639/theme/default/images/moved.png" width=32 height=32 alt="Moved to another forum" /> Moved to another forum
</font></div>
<?php echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>
<?php
	if (_uid) {
		user_register_forum_view($frm_id);
	}
?>