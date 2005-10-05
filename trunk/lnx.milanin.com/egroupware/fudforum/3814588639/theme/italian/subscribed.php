<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: subscribed.php.t,v 1.1.1.1 2003/10/17 21:11:30 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
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
}function is_notified($user_id, $thread_id)
{
	return q_singleval('SELECT * FROM phpgw_fud_thread_notify WHERE thread_id='.$thread_id.' AND user_id='.$user_id);
}

function thread_notify_add($user_id, $thread_id)
{
	if (!is_notified($user_id, $thread_id)) {
		q('INSERT INTO phpgw_fud_thread_notify(user_id, thread_id) VALUES ('.$user_id.', '.$thread_id.')');
	}
}

function thread_notify_del($user_id, $thread_id)
{
	q('DELETE FROM phpgw_fud_thread_notify WHERE thread_id='.$thread_id.' AND user_id='.$user_id);
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

	return !$js_pager ? '<font class="SmallText"><b>Pagine ('.$ttl_pg.'): 
['.$page_pager_data.']
</b></font>' : '<font class="SmallText"><b>Pagine ('.$ttl_pg.'): 
['.$page_pager_data.']
</b></font>';
}function alt_var($key)
{
	if (!isset($GLOBALS['_ALTERNATOR_'][$key])) {
		$args = func_get_args(); array_shift($args);
		$GLOBALS['_ALTERNATOR_'][$key] = array('p' => 1, 't' => count($args), 'v' => $args);
		return $args[0];
	}
	$k =& $GLOBALS['_ALTERNATOR_'][$key];
	if ($k['p'] == $k['t']) {
		$k['p'] = 0;
	}
	return $k['v'][$k['p']++];
}

	if (!_uid) {
		std_error('login');
	}

	/* delete forum subscription */
	if (isset($_GET['frm_id']) && ($_GET['frm_id'] = (int)$_GET['frm_id'])) {
		forum_notify_del(_uid, $_GET['frm_id']);
	}

	/* delete thread subscription */
	if (isset($_GET['th']) && ($_GET['th'] = (int)$_GET['th'])) {
		thread_notify_del(_uid, $_GET['th']);
	}

	ses_update_status($usr->sid, 'Sfoglia le proprie sottoscrizioni');

$tabs = '';
if (_uid) {
	$tablist = array(
'Impostazioni'=>'register',
'Iscrizioni'=>'subscribed',
'Referrals'=>'referals',
'Buddy List'=>'buddy_list',
'Ignore List'=>'ignore_list'
);
	if (isset($_POST['mod_id'])) {
		$mod_id_chk = $_POST['mod_id'];
	} else if (isset($_GET['mod_id'])) {
		$mod_id_chk = $_GET['mod_id'];
	} else {
		$mod_id_chk = null;
	}

	if (!$mod_id_chk) {
		if ($FUD_OPT_1 & 1024) {
			$tablist['Messaggi privati'] = 'pmsg';
		}
		$pg = ($_GET['t'] == 'pmsg_view' || $_GET['t'] == 'ppost') ? 'pmsg' : $_GET['t'];

		foreach($tablist as $tab_name => $tab) {
			$tab_url = '/egroupware/fudforum/3814588639/index.php?t='.$tab.'&amp;'._rsid;
			if ($tab == 'referals') {
				if (!($FUD_OPT_2 & 8192)) {
					continue;
				}
				$tab_url .= '&amp;id='._uid;
			}
			$tabs .= $pg == $tab ? '<td class="tabA"><div class="tabT"><a href="'.$tab_url.'">'.$tab_name.'</a></div></td>' : '<td class="tabI"><div class="tabT"><a href="'.$tab_url.'">'.$tab_name.'</a></div></td>';
		}

		$tabs = '<table border=0 cellspacing=1 cellpadding=0 class="tab">
<tr class="tab">'.$tabs.'</tr>
</table>';
	}
}

	/* fetch a list of all the accessible forums */
	$lmt = '';
	if (!($usr->users_opt & 1048576)) {
		$c = uq('SELECT g1.resource_id
				FROM phpgw_fud_group_cache g1
				LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id='._uid.' AND g1.resource_id=g2.resource_id
				LEFT JOIN phpgw_fud_mod m ON m.forum_id=g1.resource_id AND m.user_id='._uid.'
				WHERE g1.user_id=2147483647 AND (m.id IS NULL AND ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 2)=0)');
		while ($r = db_rowarr($c)) {
			$lmt .= $r[0] . ',';
		}
		if ($lmt) {
			$lmt[strlen($lmt) - 1] = ' ';
			$lmt = ' AND forum_id NOT IN('.$lmt.') ';
		} else {
			$lmt = ' AND forum_id NOT IN(0) ';
		}
	}

	$c = uq('SELECT f.id, f.name FROM phpgw_fud_forum_notify fn LEFT JOIN phpgw_fud_forum f ON fn.forum_id=f.id WHERE fn.user_id='._uid.' '.$lmt.' ORDER BY f.last_post_id DESC');

	$subscribed_forum_data = '';
	while (($r = db_rowarr($c))) {
		$subscribed_forum_data .= '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td width="100%"><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t='.t_thread_view.'&amp;frm_id='.$r[0].'&amp;'._rsid.'">'.htmlspecialchars($r[1]).'</a></td><td nowrap><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=subscribed&amp;frm_id='.$r[0].'&amp;'._rsid.'">Cancella iscrizione</a> | <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t='.t_thread_view.'&amp;frm_id='.$r[0].'&amp;'._rsid.'" target="_blank">Visualizza forum</a></td></tr>';
	}
	if (!$subscribed_forum_data) {
		$subscribed_forum_data = '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td colspan=2>Non sei iscritto ad alcun forum</td></tr>';
	}

	/* Since a person can have MANY subscribed threads, we need a pager & for the pager we need a entry count */
	$total = q_singleval('SELECT count(*) FROM phpgw_fud_thread_notify tn LEFT JOIN phpgw_fud_thread t ON tn.thread_id=t.id INNER JOIN phpgw_fud_msg m ON t.root_msg_id=m.id WHERE tn.user_id='._uid.' '.$lmt);
	if (!isset($_GET['start']) || !($start = (int)$_GET['start'])) {
		$start = 0;
	}

	$subscribed_thread_data = '';
	$c = uq('SELECT t.id, m.subject FROM phpgw_fud_thread_notify tn INNER JOIN phpgw_fud_thread t ON tn.thread_id=t.id INNER JOIN phpgw_fud_msg m ON t.root_msg_id=m.id WHERE tn.user_id='._uid.' '.$lmt.' ORDER BY t.last_post_id DESC LIMIT '.qry_limit($THREADS_PER_PAGE, $start));

	while (($r = db_rowarr($c))) {
		$subscribed_thread_data .= '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td width="100%"><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&amp;th='.$r[0].'&amp;'._rsid.'">'.$r[1].'</a></td><td nowrap><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=subscribed&amp;th='.$r[0].'&amp;'._rsid.'">Cancella iscrizione</a> | <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&amp;th='.$r[0].'&amp;'._rsid.'" target="_blank">Visualizza topic</a></td></tr>';
	}

	if (!$subscribed_thread_data) {
		$subscribed_thread_data = '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td colspan=2>Non sei iscritto ad alcun topic</td></tr>';
	}

	$pager = tmpl_create_pager($start, $THREADS_PER_PAGE, $total, '/egroupware/fudforum/3814588639/index.php?t=subscribed&a=1&'._rsid, '#fff');

if ($FUD_OPT_2 & 2) {
	$page_gen_end = gettimeofday();
	$page_gen_time = sprintf('%.5f', ($page_gen_end['sec'] - $PAGE_TIME['sec'] + (($page_gen_end['usec'] - $PAGE_TIME['usec'])/1000000)));
	$page_stats = '<br /><div align="left" class="SmallText">Tempo totale richiesto per generare la pagina: '.$page_gen_time.' secondi</div>';
} else {
	$page_stats = '';
}
?>
<?php echo $GLOBALS['fud_egw_hdr']; ?>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">
<?php echo $tabs; ?>
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th colspan=2>Forum cui sei iscritto<a name="fff"></a></th></tr>
<?php echo $subscribed_forum_data; ?>
<tr><th colspan=2>Topic sottoscritti</th></tr>
<?php echo $subscribed_thread_data; ?>
</table>
<?php echo $pager; ?>
<?php echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>