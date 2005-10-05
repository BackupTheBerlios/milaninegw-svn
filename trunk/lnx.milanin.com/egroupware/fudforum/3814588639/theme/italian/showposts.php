<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: showposts.php.t,v 1.1.1.1 2003/10/17 21:11:28 ralfbecker Exp $
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

	return !$js_pager ? '<font class="SmallText"><b>Pagine ('.$ttl_pg.'): 
['.$page_pager_data.']
</b></font>' : '<font class="SmallText"><b>Pagine ('.$ttl_pg.'): 
['.$page_pager_data.']
</b></font>';
}function &get_all_read_perms($uid, $mod)
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

	if (!isset($_GET['id']) || !($tmp = db_saq('SELECT id, alias, posted_msg_count, join_date FROM phpgw_fud_users WHERE id='.(int)$_GET['id']))) {
		invl_inp_err();
	} else {
		$uid = $tmp[0];
		$u_alias = $tmp[1];
		$u_pcount = $tmp[2];
		$u_reg_date = $tmp[3];
	}



	$TITLE_EXTRA = ': Mostra i messaggi di: '.htmlspecialchars($u->login);

	ses_update_status($usr->sid, 'Visualizza messaggi per: <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&id='.$uid.'">'.htmlspecialchars($u->login).'</a>');

	if (!isset($_GET['start']) || !($start = (int)$_GET['start'])) {
		$start = 0;
	}

	if (!($usr->users_opt & 1048576)) {
		$fids = implode(',', array_keys(get_all_read_perms(_uid, ($usr->users_opt & 524288))));
	}

	if (isset($_GET['so']) && !strcasecmp($_GET['so'], 'asc')) {
		$SORT_ORDER = 'ASC';
		$SORT_ORDER_R = 'DESC';
	} else {
		$SORT_ORDER = 'DESC';
		$SORT_ORDER_R = 'ASC';
	}

	$post_entry = '';
	if ($usr->users_opt & 1048576 || $fids) {
		$qry_limit = $usr->users_opt & 1048576 ? '' : 'f.id IN ('.$fids.') AND ';

		/* we need the total for the pager & we don't trust the user to pass it via GET or POST */
		$total = q_singleval("SELECT count(*)
					FROM phpgw_fud_msg m
					INNER JOIN phpgw_fud_thread t ON m.thread_id=t.id
					INNER JOIN phpgw_fud_forum f ON t.forum_id=f.id
					INNER JOIN phpgw_fud_cat c ON c.id=f.cat_id
					WHERE ".$qry_limit." m.apr=1 AND m.poster_id=".$uid);

		$c = uq("SELECT f.name, f.id, m.subject, m.id, m.post_stamp
			FROM phpgw_fud_msg m
			INNER JOIN phpgw_fud_thread t ON m.thread_id=t.id
			INNER JOIN phpgw_fud_forum f ON t.forum_id=f.id
			INNER JOIN phpgw_fud_cat c ON c.id=f.cat_id
			WHERE ".$qry_limit." m.apr=1 AND m.poster_id=".$uid."
			ORDER BY m.post_stamp ".$SORT_ORDER." LIMIT ".qry_limit($THREADS_PER_PAGE, $start));

		while ($r = db_rowarr($c)) {
			$post_entry .= '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="GenText"><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&amp;goto='.$r[3].'&amp;'._rsid.'">'.$r[2].'</a></td><td class="GenText" nowrap><a href="/egroupware/fudforum/3814588639/index.php?t='.t_thread_view.'&amp;frm_id='.$r[1].'&amp;'._rsid.'" class="GenLink">'.htmlspecialchars($r[0]).'</a></td><td class="DateText" nowrap>'.strftime("%a, %d %B %Y %H:%M", $r[4]).'</td></tr>';
		}

		$pager = tmpl_create_pager($start, $THREADS_PER_PAGE, $total, '/egroupware/fudforum/3814588639/index.php?t=showposts&amp;id='.$uid.'&amp;'._rsid);
	}

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
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th colspan=3>Informazioni utente</th></tr>
<tr class="RowStyleA"><td class="GenText" width="100%">Login: <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id=<?php echo $uid; ?>&amp;<?php echo _rsid; ?>"><?php echo $u_alias; ?></a></td>
<td class="GenText" nowrap>Registrato il <?php echo strftime("%a, %d %B %Y", $u_reg_date); ?></td><td class="GenText" nowrap>Numero di messaggi <?php echo $u_pcount; ?></td></tr>
<tr><th width="100%">Oggetto</th><th nowrap>Forum:</th><th nowrap><a class="thLnk" href="/egroupware/fudforum/3814588639/index.php?t=showposts&amp;so=<?php echo $SORT_ORDER_R; ?>&amp;id=<?php echo $uid; ?>&amp;<?php echo _rsid; ?>">Data del messaggio</a></th></tr>
<?php echo $post_entry; ?>
</table>
<?php echo $pager; ?>
<?php echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>