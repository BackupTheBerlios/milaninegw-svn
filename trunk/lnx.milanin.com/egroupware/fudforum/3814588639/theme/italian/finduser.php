<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: finduser.php.t,v 1.2 2003/10/22 19:26:20 iliaa Exp $
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
}function msg_get($id)
{
	if (($r = db_sab('SELECT * FROM phpgw_fud_msg WHERE id='.$id))) {
		$r->body = read_msg_body($r->foff, $r->length, $r->file_id);
		un_register_fps();
		return $r;
	}
	error_dialog('Messaggio non valido', 'Il messaggio che stai cercando di visualizzare non esiste.');
}

function poll_cache_rebuild($poll_id, &$data)
{
	if (!$poll_id) {
		$data = null;
		return;
	}

	if (!$data) { /* rebuild from cratch */
		$c = uq('SELECT id, name, count FROM phpgw_fud_poll_opt WHERE poll_id='.$poll_id);
		while ($r = db_rowarr($c)) {
			$data[$r[0]] = array($r[1], $r[2]);
		}
		if (!$data) {
			$data = null;
		}
	} else { /* register single vote */
		$data[$poll_id][1] += 1;
	}
}function reverse_fmt(&$data)
{
	$data = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $data);
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
}

	$adm = $usr->users_opt & 1048576;

	if (!$adm && !($FUD_OPT_1 & 8388608) && (!($FUD_OPT_1 & 4194304) || !_uid)) {
		std_error('disabled');
	}

	if (isset($_GET['js_redr'])) {
		define('plain_form', 1);
		$adm = 0;
	}

	$TITLE_EXTRA = ': Trova utenti';

	ses_update_status($usr->sid, 'Cerca utenti');



	if (!isset($_GET['start']) || !($start = (int)$_GET['start'])) {
		$start = 0;
	}
	$count = $MEMBERS_PER_PAGE;

	if (isset($_GET['pc'])) {
		$ord = 'posted_msg_count DESC';
	} else if (isset($_GET['us'])) {
		$ord = 'alias';
	} else {
		$ord = 'id DESC';
	}
	$usr_login = !empty($_GET['usr_login']) ? trim($_GET['usr_login']) : '';
	$usr_email = !empty($_GET['usr_email']) ? trim($_GET['usr_email']) : '';

	if ($usr_login) {
		$qry = "alias LIKE '".addslashes(htmlspecialchars(str_replace('\\', '\\\\', $usr_login)))."%' AND";
	} else if ($usr_email) {
		$qry = "email LIKE '".addslashes($usr_email)."%' AND";
	} else {
		$qry = '';
	}
	$lmt = ' LIMIT '.qry_limit($count, $start);

	$admin_opts = $adm ? '<th>Admin Opts.</th>' : '';

	$find_user_data = '';
	$c = uq('SELECT home_page, users_opt, alias, join_date, posted_msg_count, id FROM phpgw_fud_users WHERE ' . $qry . ' id>1 ORDER BY ' . $ord . ' ' . $lmt);
	while ($r = db_rowobj($c)) {
		$pm_link = ($FUD_OPT_1 & 1024 && _uid) ? '<a href="/egroupware/fudforum/3814588639/index.php?t=ppost&amp;'._rsid.'&amp;toi='.$r->id.'" class="GenLink"><img src="/egroupware/fudforum/3814588639/theme/italian/images/msg_pm.gif" alt="" /></a>' : '';
		$homepage_link = $r->home_page ? '<a class="GenLink" href="'.$r->home_page.'" target="_blank"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/homepage.gif" /></a>' : '';
		$email_link = ($FUD_OPT_2 & 1073741824 && $r->users_opt & 16) ? '<a href="/egroupware/fudforum/3814588639/index.php?t=email&amp;toi='.$r->id.'&amp;'._rsid.'" class="GenLink"><img src="/egroupware/fudforum/3814588639/theme/italian/images/msg_email.gif" alt="" /></a>' : '';

		if ($adm) {
			$admi = $r->users_opt & 65536 ? '<a href="'.$GLOBALS['WWW_ROOT'].'adm/admuser.php?act=block&usr_id='.$r->id.'&'._rsid.'">UnBan</a>' : '<a href="'.$GLOBALS['WWW_ROOT'].'adm/admuser.php?act=block&usr_id='.$r->id.'&'._rsid.'">Ban</a>';
			$admi = '<td class="SmallText" nowrap><a href="'.$GLOBALS['WWW_ROOT'].'adm/admuser.php?usr_id='.$r->id.'&'._rsid.'&act=1">Edit</a> || '.$admi.'</td>';
		} else {
			$admi = '';
		}

		$find_user_data .= '<tr class="'.alt_var('finduser_alt','RowStyleA','RowStyleB').'"><td nowrap class="GenText"><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$r->id.'&amp;'._rsid.'">'.$r->alias.'</a></td><td align="center" nowrap>'.$r->posted_msg_count.'</td><td class="DateText" nowrap>'.strftime("%a, %d %B %Y", $r->join_date).'</td><td nowrap class="GenText"><a href="/egroupware/fudforum/3814588639/index.php?t=showposts&amp;id='.$r->id.'&amp;'._rsid.'" class="GenLink"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/show_posts.gif" /></a>
'.$email_link.'
'.$pm_link.'
'.$homepage_link.'</td>'.$admi.'</tr>';
	}
	if (!$find_user_data) {
		$colspan = $adm ? 5 : 4;
		$find_user_data = '<tr class="RowStyleA"><td colspan="'.$colspan.'" width="100%" class="GenText">Nessun utente trovato</td></tr>';
	}

	$pager = '';
	if (!$qry) {
		$total = q_singleval('SELECT count(*) FROM phpgw_fud_users ' . $qry);
		if ($total > $count) {
			$pg = '/egroupware/fudforum/3814588639/index.php?t=finduser&amp;' . _rsid . '&amp;';
			if ($usr_login) {
				$pg .= urlencode($usr_login) . '&amp;';
			}
			if ($usr_email) {
				$pg .= urlencode($usr_email) . '&amp;';
			}
			if (isset($_GET['pc'])) {
				$pg .= 'pc=1&amp;';
			}
			if (isset($_GET['us'])) {
				$pg .= 'us=1&amp;';
			}
			if (isset($_GET['js_redr'])) {
				$pg .= 'js_redr='.urlencode($_GET['js_redr']).'&amp;';
			}
			$pager = tmpl_create_pager($start, $count, $total, $pg);
		}
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
<form method="get" action="/egroupware/fudforum/3814588639/index.php"><?php echo _hs; ?>
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th colspan=2>Informazioni utente</th></tr>
<tr class="RowStyleA"><td class="GenText" nowrap>Per login:</td><td class="GenText" width="100%"><input type="text" name="usr_login" value="<?php echo htmlspecialchars($usr_login); ?>"></td></tr>
<tr class="RowStyleB"><td class="GenText" nowrap>Per email:</td><td width="100%"><input type="text" name="usr_email" value="<?php echo htmlspecialchars($usr_email); ?>"></td></tr>
<tr class="RowStyleA"><td class="GenText" align="right" colspan=2><font class="SmallText">The search engine will automatically add * mask to your query. ex. to search for all users who&#39;s login begins with an &#39;a&#39;, enter &#39;a&#39; into the search box.</font> <input type="submit" class="button" name="btn_submit" value="Trova"></td></tr>
</table><input type="hidden" name="t" value="finduser"></form>
<img src="blank.gif" alt="" height=2 width=1 /><br />
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr>
<th width="100%"><a class="thLnk" href="/egroupware/fudforum/3814588639/index.php?t=finduser&amp;usr_login=<?php echo urlencode($usr_login); ?>&amp;usr_email=<?php echo urlencode($usr_email); ?>&amp;us=1&amp;btn_submit=Find&amp;<?php echo _rsid; ?>">Utente</a></th><th nowrap><a href="/egroupware/fudforum/3814588639/index.php?t=finduser&amp;usr_login=<?php echo urlencode($usr_login); ?>&amp;<?php echo _rsid; ?>&amp;usr_email=<?php echo urlencode($usr_email); ?>&amp;pc=1&amp;btn_submit=Find" class="thLnk">Numero di messaggi</a></th><th nowrap><div align="center"><a href="/egroupware/fudforum/3814588639/index.php?t=finduser&amp;usr_login=<?php echo urlencode($usr_login); ?>&amp;<?php echo _rsid; ?>&amp;usr_email=<?php echo urlencode($usr_email); ?>&amp;btn_submit=Find" class="thLnk">Data di registrazione</a></div></th><th align="center">Azione</th><?php echo $admin_opts; ?>
</tr>
<?php echo $find_user_data; ?>
</table>
<?php echo $pager; ?>
<?php echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>