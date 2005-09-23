<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: mnav.php.t,v 1.1.1.1 2003/10/17 21:11:26 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function reverse_fmt(&$data)
{
	$data = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $data);
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
}function tmpl_draw_select_opt($values, $names, $selected, $normal_tmpl, $selected_tmpl)
{
	$vls = explode("\n", $values);
	$nms = explode("\n", $names);

	if (($a = count($vls)) != count($nms)) {
		exit("FATAL ERROR: inconsistent number of values inside a select<br>\n");
	}

	$options = '';
	for ($i = 0; $i < $a; $i++) {
		$options .= $vls[$i] != $selected ? '<option value="'.$vls[$i].'" '.$normal_tmpl.'>'.$nms[$i].'</option>' : '<option value="'.$vls[$i].'" selected '.$selected_tmpl.'>'.$nms[$i].'</option>';
	}

	return $options;
}function register_fp($id)
{
	if (!isset($GLOBALS['__MSG_FP__'][$id])) {
		$GLOBALS['__MSG_FP__'][$id] = fopen($GLOBALS['MSG_STORE_DIR'].'msg_'.$id, 'rb');
	}

	return $GLOBALS['__MSG_FP__'][$id];
}

function un_register_fps()
{
	if (!isset($GLOBALS['__MSG_FP__'])) {
		return;
	}
	unset($GLOBALS['__MSG_FP__']);
}

function read_msg_body($off, $len, $file_id)
{
	$fp = register_fp($file_id);
	fseek($fp, $off);
	return fread($fp, $len);
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

	if (!isset($_GET['start']) || !($start = (int)$_GET['start'])) {
		$start = 0;
	}
	$forum_limiter = isset($_GET['forum_limiter']) ? $_GET['forum_limiter'] : '';
	$rng = isset($_GET['rng']) ? (float) $_GET['rng'] : 1;
	$unit = isset($_GET['u']) ? (int) $_GET['u'] : 86400;
	$ppg = $usr->posts_ppg ? $usr->posts_ppg : $POSTS_PER_PAGE;

if (!isset($th)) {
	$th = 0;
}
if (!isset($frm->id)) {
	$frm->id = 0;
}

$unread_posts = _uid ? '<b>::</b> <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=selmsg&amp;unread=1&amp;'._rsid.'&amp;frm_id='.$frm->id.'" title="Mostra tutti i messaggi non letti">Messaggi non letti</a>&nbsp;' : '';
$unanswered_posts = !$th ? '<b>::</b> <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=selmsg&amp;reply_count=0&amp;'._rsid.'&amp;frm_id='.$frm->id.'" title="Mostra tutti i messaggi che non hanno risposta">Messaggi senza risposta</a>' : '';/* draw search engine selection boxes */
if ($usr->users_opt & 1048576) {
	$c = uq('SELECT f.id, f.name, c.id, c.name FROM phpgw_fud_fc_view v INNER JOIN phpgw_fud_forum f ON f.id=v.f INNER JOIN phpgw_fud_cat c ON f.cat_id=c.id ORDER BY v.id');
} else {
	$c = uq('SELECT f.id,f.name, c.id, c.name AS cat_name
			FROM phpgw_fud_fc_view v
			INNER JOIN phpgw_fud_forum f ON f.id=v.f
			INNER JOIN phpgw_fud_cat c ON f.cat_id=c.id
			LEFT JOIN phpgw_fud_mod mm ON mm.forum_id=f.id AND mm.user_id='._uid.'
			INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id='.(_uid ? '2147483647' : '0').' AND g1.resource_id=f.id
			LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id
			WHERE mm.id IS NOT NULL OR ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 1) > 0
			ORDER BY v.id');
}
$old_cat = $forum_limit_data = '';
while ($r = db_rowarr($c)) {
	if ($old_cat != $r[2]) {
		$selected = ('c'.$r[2] == $forum_limiter) ? ' selected' : '';
		$forum_limit_data .= '<option value="c'.$r[2].'"'.$selected.'>'.htmlspecialchars($r[3]).'</option>';
		$old_cat = $r[2];
	}
	$selected = $r[0] == $forum_limiter ? ' selected' : '';
	$forum_limit_data .= '<option value="'.$r[0].'"'.$selected.'>&nbsp;&nbsp;&nbsp;'.htmlspecialchars($r[1]).'</option>';
}
/* user has no permissions to any forum, so as far as they are concerned the search is disabled */
if (!$forum_limit_data) {
	std_error('disabled');
}

function trim_body($body)
{
	/* remove stuff in quotes */
	while (($p = strpos($body, '<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>')) !== false) {
		$e = strpos($body, '<br></td></tr></table>', $p) + strlen('<br></td></tr></table>');
		$body = substr($body, 0, $p) . substr($body, $e);
	}

	$body = strip_tags($body);
	if (strlen($body) > $GLOBALS['MNAV_MAX_LEN']) {
		$body = substr($body, 0, $GLOBALS['MNAV_MAX_LEN']) . '...';
	}
	return $body;
}

	$TITLE_EXTRA = ': Message Navigator';

	ses_update_status($usr->sid, 'Browsing Messages using <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=mnav">Message Navigator</a>');

	if ($forum_limiter) {
		if ($forum_limiter[0] != 'c') {
			$qry_lmt = ' AND f.id=' . (int)$forum_limiter . ' ';
		} else {
			$qry_lmt = ' AND c.id=' . (int)substr($forum_limiter, 1) . ' ';
		}
	} else {
		$qry_lmt = '';
	}

	$mnav_time_unit = tmpl_draw_select_opt("60\n3600\n86400\n604800\n2635200", "Minute(s)\nHour(s)\nDay(s)\nWeek(s)\nMonth(s)", $unit, '', '');

	if (!$rng) {
		$mnav_pager = $rng = ''; $unit = 86400;
		$mnav_data = '<br />
<div align="center">
<table border="0" cellspacing="1" cellpadding="2" class="mnavWarnTbl">
<tr>
	<td class="GenTextRed">You must enter a valid date range. This value can be a float (0.12) but it must be greater then zero.</td>
</tr>
</table>
</div>';
	} else if ($unit <= 0) {
		$mnav_pager = $rng = ''; $unit = 86400;
		$mnav_data = '<br />
<div align="center">
<table border="0" cellspacing="1" cellpadding="2" class="mnavWarnTbl">
<tr>
	<td class="GenTextRed">You must specify a valid time unit.</td>
</tr>
</table>
</div>';
	} else if (($mage = round($rng * $unit)) > ($MNAV_MAX_DATE * 86400) && $MNAV_MAX_DATE > 0) {
		$mnav_data = '<br />
<div align="center">
<table border="0" cellspacing="1" cellpadding="2" class="mnavWarnTbl">
<tr>
	<td class="GenTextRed">The date range you&#39;ve specified is larger then the one allowed by the administrator, narrow down date range of your search.</td>
</tr>
</table>
</div>';
		$mnav_pager = '';
	} else if (isset($_GET['u'])) {
		$tm = __request_timestamp__ - $mage;

		$total = q_singleval('SELECT count(*) FROM phpgw_fud_msg m
					INNER JOIN phpgw_fud_thread t ON m.thread_id=t.id
					INNER JOIN phpgw_fud_forum f ON t.forum_id=f.id
					INNER JOIN phpgw_fud_cat c ON f.cat_id=c.id
					INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id='.(_uid ? '2147483647' : '0').' AND g1.resource_id=f.id
					LEFT JOIN phpgw_fud_mod mm ON mm.forum_id=f.id AND mm.user_id='._uid.'
					LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id
				WHERE
					m.post_stamp > '.$tm.' AND m.apr=1 '.$qry_lmt.'
					'.($usr->users_opt & 1048576 ? '' : ' AND (mm.id IS NOT NULL OR ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 2) > 0)'));
		if (!$total) {
			$mnav_pager = '';
			$mnav_data = '<div align="center" class="GenText" class="manvNoRes">There are no messages matching query.</div>';
		} else {
			$c = uq('SELECT u.alias, f.name AS forum_name, f.id AS forum_id,
					m.poster_id, m.id, m.thread_id, m.subject, m.poster_id, m.foff, m.length, m.post_stamp, m.file_id, m.icon
					FROM phpgw_fud_msg m
					INNER JOIN phpgw_fud_thread t ON m.thread_id=t.id
					INNER JOIN phpgw_fud_forum f ON t.forum_id=f.id
					INNER JOIN phpgw_fud_cat c ON f.cat_id=c.id
					INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id='.(_uid ? '2147483647' : '0').' AND g1.resource_id=f.id
					LEFT JOIN phpgw_fud_users u ON m.poster_id=u.id
					LEFT JOIN phpgw_fud_mod mm ON mm.forum_id=f.id AND mm.user_id='._uid.'
					LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id
				WHERE
					m.post_stamp > '.$tm.' AND m.apr=1 '.$qry_lmt.'
					'.($usr->users_opt & 1048576 ? '' : ' AND (mm.id IS NOT NULL OR ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 2) > 0)').'
					ORDER BY m.thread_id, t.forum_id, m.post_stamp DESC LIMIT '.qry_limit($ppg, $start));

			$oldf = $oldt = 0;
			$mnav_data = '<div align="center">
<table border="0" cellspacing="0" cellpadding="0" class="ContentTable">';
			while ($r = db_rowobj($c)) {
				if ($oldf != $r->forum_id) {
					$mnav_data .= '<tr><th colspan="3"> Forum: <a class="thLnk" href="/egroupware/fudforum/3814588639/index.php?t='.t_thread_view.'&amp;frm_id='.$r->forum_id.'&amp;'._rsid.'"><font clas="lg">'.htmlspecialchars($r->forum_name).'</font></a></th></tr>';
					$oldf = $r->forum_id;
				}
				if ($oldt != $r->thread_id) {
					$mnav_data .= '<tr><th class="RowStyleC">&nbsp;&nbsp;&nbsp;</th><th colspan="2"> Topic: <a class="thLnk" href="/egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&amp;goto='.$r->id.'&amp;'._rsid.'">'.$r->subject.'</a></th></tr>';
					$oldt = $r->thread_id;
				}

				$body = trim_body(read_msg_body($r->foff, $r->length, $r->file_id));

				$poster_info = !empty($r->poster_id) ? '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$r->poster_id.'&amp;'._rsid.'">'.$r->alias.'</a>' : ''.$GLOBALS['ANON_NICK'];

				$mnav_data .= '<tr><td class="RowStyleC">&nbsp;&nbsp;&nbsp;</td><td class="RowStyleC">&nbsp;&nbsp;&nbsp;</td><td>
<table cellspacing=0 cellpadding=2 border=0 class="mnavMsg">
<tr class="mnavH">
	<td align="left" class="SmallText"><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&amp;goto='.$r->id.'&amp;'._rsid.'">'.$r->subject.'</a></td>
	<td align="center" class="SmallText">Posted By: '.$poster_info.'</td>
	<td align="right" class="SmallText">'.strftime("%a, %d %B %Y %H:%M", $r->post_stamp).'</td>
</tr>
<tr class="mnavM">
	<td colspan="3" class="SmallText">'.$body.' <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&amp;goto='.$r->id.'&amp;'._rsid.'">More &raquo;&raquo;</a></td>
</tr>
</table>
</td></tr>';
			}
			un_register_fps();
			$mnav_data .= '</table>
</div>';

			/* handle pager if needed */
			if ($total > $ppg) {
				$mnav_pager = tmpl_create_pager($start, $ppg, $total, '/egroupware/fudforum/3814588639/index.php?t=mnav&amp;rng='.$rng.'&amp;u='.$unit.'&amp;'._rsid.'&amp;forum_limiter='.$forum_limiter);
			} else {
				$mnav_pager = '';
			}
		}
	} else {
		$mnav_pager = $mnav_data = '';
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
<font class="GenText"><b>Mostra:</b></font> <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=selmsg&amp;date=today&amp;<?php echo _rsid; ?>&amp;frm_id=<?php echo $frm->id; ?>&amp;th=<?php echo $th; ?>" title="Mostra tutti i messaggi postati oggi">Messaggi odierni</a>&nbsp;<?php echo $unread_posts.$unanswered_posts; ?> <b>::</b> <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=polllist&amp;<?php echo _rsid; ?>">Mostra i sondaggi</a> <b>::</b> <a href="/egroupware/fudforum/3814588639/index.php?t=mnav&amp;<?php echo _rsid; ?>" class="GenLink">Message Navigator</a><br /><img src="blank.gif" alt="" height=2 />

<form method="get" action="/egroupware/fudforum/3814588639/index.php"><?php echo _hs; ?><input type="hidden" name="t" value="mnav">
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th colspan=3 width="100%">Message Navigator</th></tr>
<tr class="<?php echo alt_var('color_alt','RowStyleA','RowStyleB'); ?>">
	<td class="GenText" width="30%" nowrap>Date range:</td>
	<td class="GenText"><input type="text" name="rng" value="<?php echo $rng; ?>" maxlength="10" size=11></td>
	<td align="left" width="60%"><select name="u"><?php echo $mnav_time_unit; ?></select></td></tr>
<tr class="<?php echo alt_var('color_alt','RowStyleA','RowStyleB'); ?>"><td class="GenText" nowrap>Only search In:</td>
	<td colspan=2>
		<select name="forum_limiter"><option value="">Search all forums</option>
		<?php echo $forum_limit_data; ?>
		</select>
	</td>
</tr>
<tr class="RowStyleC"><td class="GenText" align="right" colspan=3><input type="submit" class="button" name="btn_submit" value="Begin Search"></td></tr>
</table></form>
<br />
<?php echo $mnav_data; ?>
<div align="left"><?php echo $mnav_pager; ?></div>
<?php echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>