<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: polllist.php.t,v 1.1.1.1 2003/10/17 21:11:25 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
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
}

	ses_update_status($usr->sid, '<a href="/egroupware/fudforum/3814588639/index.php?t=polllist">Reviewing Available Polls</a>');



	if (!isset($_GET['oby'])) {
		$_GET['oby'] = 'DESC';
	}
	if (!isset($_GET['start']) || !($start = (int)$_GET['start'])) {
		$start = 0;
	}
	if (isset($_GET['uid']) && ($uid = (int)$_GET['uid'])) {
		$usr_lmt = ' p.owner='.$uid.' AND ';
	} else {
		$uid = $usr_lmt = '';
	}

	if ($_GET['oby'] == 'ASC') {
		$oby = 'ASC';
		$oby_rev_val = 'DESC';
	} else {
                $oby = 'DESC';
		$oby_rev_val = 'ASC';
	}

	$ttl = (int) q_singleval('SELECT count(*)
				FROM phpgw_fud_poll p
				INNER JOIN phpgw_fud_forum f ON p.forum_id=f.id
				INNER JOIN phpgw_fud_cat c ON c.id=f.cat_id
				LEFT JOIN phpgw_fud_mod mm ON mm.forum_id=p.forum_id AND mm.user_id='._uid.'
				INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id='.(_uid ? '2147483647' : '0').' AND g1.resource_id=p.forum_id
				LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=p.forum_id
				WHERE
					'.$usr_lmt.($usr->users_opt & 1048576 ? ' 1=1' : ' (mm.id IS NOT NULL OR ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 2) > 0)'));
	$poll_entries = $pager = '';
	if ($ttl) {
		$c = uq('SELECT
				p.owner, p.name, (CASE WHEN expiry_date = 0 THEN 0 ELSE (p.creation_date + p.expiry_date) END) AS poll_expiry_date, p.creation_date, p.id AS poid, p.max_votes, p.total_votes,
				u.alias, u.alias AS login, (u.last_visit + '.($LOGEDIN_TIMEOUT * 60).') AS last_visit, u.users_opt,
				m.id,
				t.thread_opt,
				'.($usr->users_opt & 1048576 ? '1' : 'mm.id').' AS md,
				pot.id AS cant_vote,
				(CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) AS gco
				FROM phpgw_fud_poll p
				INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id='.(_uid ? '2147483647' : '0').' AND g1.resource_id=p.forum_id
				INNER JOIN phpgw_fud_forum f ON p.forum_id=f.id
				INNER JOIN phpgw_fud_cat c ON c.id=f.cat_id
				INNER JOIN phpgw_fud_msg m ON m.poll_id=p.id
				INNER JOIN phpgw_fud_thread t ON m.thread_id=t.id
				LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=p.forum_id
				LEFT JOIN phpgw_fud_mod mm ON mm.forum_id=p.forum_id AND mm.user_id='._uid.'
				LEFT JOIN phpgw_fud_users u ON u.id=m.poster_id
				LEFT JOIN phpgw_fud_poll_opt_track pot ON pot.poll_id=p.id AND pot.user_id='._uid.'
				WHERE
					'.$usr_lmt.' '.($usr->users_opt & 1048576 ? '1=1' : '(mm.id IS NOT NULL OR ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 2) > 0)').' ORDER BY p.creation_date '.$oby.' LIMIT '.qry_limit($POLLS_PER_PAGE, $start));

		while ($obj = db_rowobj($c)) {
			if (!$obj->total_votes) {
				$obj->total_votes = '0';
			}
			$vote_lnk = '';
			if(!$obj->cant_vote && (!$obj->poll_expiry_date || $obj->poll_expiry_date < __request_timestamp__)) {
				if ($obj->md || ($obj->gco & 512 && (!($obj->thread_opt & 1) || $obj->gco & 4096))) {
					if (!$obj->max_votes || $obj->total_votes < $obj->max_votes) {
						$vote_lnk = '&nbsp;<b>::</b>&nbsp;<a href="/egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&amp;goto='.$obj->id.'&amp;'._rsid.'">Vote</a>&nbsp;<b>::</b>&nbsp;';
					}
				}
			}
			$view_res_lnk = $obj->total_votes ? '<a href="/egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&amp;goto='.$obj->id.'&amp;pl_view='.$obj->poid.'&amp;'._rsid.'">View Results</a>&nbsp;<b>::</b>&nbsp;' : '';

			if ($obj->owner && (!($obj->users_opt & 32768) || $usr->users_opt & 1048576) && $FUD_OPT_2 & 32) {
				$online_indicator = $obj->last_visit > __request_timestamp__ ? '<img src="/egroupware/fudforum/3814588639/theme/default/images/online.gif" title="'.$obj->login.' is currently online" alt="'.$obj->login.' is currently online" />&nbsp;' : '<img src="/egroupware/fudforum/3814588639/theme/default/images/offline.gif" title="'.$obj->login.'  is currently offline" alt="'.$obj->login.'  is currently offline" />&nbsp;';
			} else {
				$online_indicator = '';
			}
			$poll_entries .= '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'">
	<td width="100%">'.$obj->name.'</td>
	<td nowrap>'.strftime("%a, %d %B %Y %H:%M", $obj->creation_date).'</td>
	<td nowrap>'.$online_indicator.'<a href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$obj->owner.'&amp;'._rsid.'">'.$obj->alias.'</a></td>
	<td align="center" nowrap>'.$obj->total_votes.'</td>
	<td align="center" nowrap>'.$vote_lnk.$view_res_lnk.'</td>
</tr>';
		}

		if ($ttl > $POLLS_PER_PAGE) {
			$pager = tmpl_create_pager($start, $POLLS_PER_PAGE, $ttl, '/egroupware/fudforum/3814588639/index.php?t=polllist&amp;oby='.$oby.'&amp;uid='.$uid);
		}
	} else {
		$poll_entries = '<tr><td colspan="5" align="center">There are no accessible polls.</td></tr>';
	}

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
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr>
	<th>Poll Name</th>
	<th nowrap><a class="thLnk" href="/egroupware/fudforum/3814588639/index.php?t=polllist&amp;start=<?php echo $start; ?>&amp;oby=<?php echo $oby_rev_val; ?>&amp;<?php echo _rsid; ?>">Created On</a></th>
	<th nowrap>Created By</th>
	<th nowrap align="center">Total Votes</th>
	<th nowrap><div align="center">Action</div></th>
</tr>
<?php echo $poll_entries; ?>
</table>
<p>
<?php echo $pager; ?>
<?php echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>