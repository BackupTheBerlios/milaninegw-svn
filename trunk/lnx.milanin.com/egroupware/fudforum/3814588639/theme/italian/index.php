<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: index.php.t,v 1.2 2003/12/18 18:20:49 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function draw_user_link($login, $type, $custom_color='')
{
	if ($custom_color) {
		return '<font style="color: '.$custom_color.'">'.$login.'</font>';
	}

	if (!($type & 1572864)) {
		return $login;
	} else if ($type & 1048576) {
		return '<font class="adminColor">'.$login.'</font>';
	} else if ($type & 524288) {
		return '<font class="modsColor">'.$login.'</font>';
	}
}

function reload_collapse($str)
{
	if (!($tok = strtok($str, '_'))) {
		return;
	}
	do {
		@list($key, $val) = explode(':', $tok);
		if ((int) $key) {
			$GLOBALS['collapse'][(int) $key] = (int) $val;
		}
	} while (($tok = strtok('_')));
}

function url_tog_collapse($id, $c)
{
	if (!isset($GLOBALS['collapse'][$id])) {
		return;
	}

	if (!$c) {
		return $id . ':'.(empty($GLOBALS['collapse'][$id]) ? '1' : '0');
	} else {
		$c_status = (empty($GLOBALS['collapse'][$id]) ? 1 : 0);

		if (isset($GLOBALS['collapse'][$id]) && ($p = strpos('_' . $c, '_' . $id . ':' . (int)!$c_status)) !== false) {
			$c[$p + strlen($id) + 1] = $c_status;
			return $c;
		} else {
			return $c . '_' . $id . ':' . $c_status;
		}
	}
}

	if (isset($_GET['c'])) {
		$cs = $_GET['c'];
		if (_uid && $cs != $usr->cat_collapse_status) {
			q("UPDATE phpgw_fud_users SET cat_collapse_status='".addslashes($cs)."' WHERE id="._uid);
		}
		reload_collapse($cs);
	} else if (_uid && $usr->cat_collapse_status) {
		$cs = $usr->cat_collapse_status;
		reload_collapse($cs);
	} else {
		$cs = '';
	}

	if (!_uid) {
		$mark_all_read = $welcome_message = '';
	} else {
		$welcome_message = '<font class="GenText">Benvenuto <b>'.$usr->alias.'</b>, l&#39;ultima volta sei stato con noi '.strftime("%A, %d %B %Y alle %H:%M", $usr->last_visit).'</font><br />';
		$mark_all_read = '<div align=right><font class="SmallText">[<a href="/egroupware/fudforum/3814588639/index.php?t=markread&amp;'._rsid.'" class="GenLink" title="Tutti i tuoi messaggi non letti verranno marcati come letti">segna tutti i messaggi come letti</a>]</font></div>';
	}

	ses_update_status($usr->sid, 'Sfoglia l&#39;<a href="/egroupware/fudforum/3814588639/index.php?t=index" class="GenLink">elenco dei forum</a>');

if (!isset($th)) {
	$th = 0;
}
if (!isset($frm->id)) {
	$frm->id = 0;
}

$unread_posts = _uid ? '<b>::</b> <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=selmsg&amp;unread=1&amp;'._rsid.'&amp;frm_id='.$frm->id.'" title="Mostra tutti i messaggi non letti">Messaggi non letti</a>&nbsp;' : '';
$unanswered_posts = !$th ? '<b>::</b> <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=selmsg&amp;reply_count=0&amp;'._rsid.'&amp;frm_id='.$frm->id.'" title="Mostra tutti i messaggi che non hanno risposta">Messaggi senza risposta</a>' : '';
	$TITLE_EXTRA = ': Benvenuto nel forum di TGM Online!';

	$forum_list_table_data = '';

	/* List of fetched fields & their ids
	  0	msg.subject,
	  1	msg.id AS msg_id,
	  2	msg.post_stamp,
	  3	users.id AS user_id,
	  4	users.alias
	  5	cat.description,
	  6	cat.name,
	  7	cat.cat_opt,
	  8	forum.cat_id,
	  9	forum.forum_icon
	  10	forum.id
	  11	forum.last_post_id
	  12	forum.moderators
	  13	forum.name
	  14	forum.descr
	  15	forum.post_count
	  16	forum.thread_count
	  17	forum_read.last_view
	  18	is_moderator
	  19	read perm
	*/
	$frmres = uq('SELECT
				m.subject, m.id, m.post_stamp,
				u.id, u.alias,
				c.description, c.name, c.cat_opt,
				f.cat_id, f.forum_icon, f.id, f.last_post_id, f.moderators, f.name, f.descr, f.post_count, f.thread_count,
				fr.last_view,
				mo.id AS md,
				'.(_uid ? 'CASE WHEN g2.group_cache_opt IS NULL THEN g1.group_cache_opt ELSE g2.group_cache_opt END AS group_cache_opt' : 'g1.group_cache_opt').'
			FROM phpgw_fud_fc_view v
			INNER JOIN phpgw_fud_forum f ON f.id=v.f
			INNER JOIN phpgw_fud_cat c ON c.id=v.c
			INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id='.(_uid ? 2147483647 : 0).' AND g1.resource_id=f.id
			LEFT JOIN phpgw_fud_msg m ON f.last_post_id=m.id
			LEFT JOIN phpgw_fud_users u ON u.id=m.poster_id
			LEFT JOIN phpgw_fud_forum_read fr ON fr.forum_id=f.id AND fr.user_id='._uid.'
			LEFT JOIN phpgw_fud_mod mo ON mo.user_id='._uid.' AND mo.forum_id=f.id
			'.(_uid ? 'LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id' : '').'
			'.($usr->users_opt & 1048576 ? '' : 'WHERE mo.id IS NOT NULL OR ('.(_uid ? 'CASE WHEN g2.group_cache_opt IS NULL THEN g1.group_cache_opt ELSE g2.group_cache_opt END' : 'g1.group_cache_opt').' & 1)>0').' ORDER BY v.id');

	$post_count = $thread_count = $last_msg_id = $cat = 0;
	$anonimouse =0;
	while ($r = db_rowarr($frmres)) {
		if ($cat != $r[8]) {
			$r[7] = (int) $r[7];

			if ($r[7] & 1) {
				if (!isset($GLOBALS['collapse'][$r[8]])) {
					$GLOBALS['collapse'][$r[8]] = ($r[7] & 2 ? 0 : 1);
				}

				if (!empty($GLOBALS['collapse'][$r[8]])) {
					$collapse_status = 'Espandi la categoria';
					$collapse_indicator = '+';
				} else {
					$collapse_status = 'Riduci categoria';
					$collapse_indicator = '-';
				}

				$forum_list_table_data .= '<tr class="CatDesc"><td colspan="7">
&nbsp;<a href="/egroupware/fudforum/3814588639/index.php?t=index&amp;c='.url_tog_collapse($r[8], $cs).'&amp;'._rsid.'" class="CatLink" title="'.$collapse_status.'">'.$collapse_indicator.' '.$r[6].'</a>
'.$r[5].'
</td></tr>';
			} else {
				$forum_list_table_data .= '<tr class="CatDesc"><td colspan="7">* <font class="CatLockedName">'.$r[6].'</font>'.$r[5].'</td></tr>';
			}
			$cat = $r[8];
		}

		if (!($r[19] & 2) && !($usr->users_opt & 1048576) && !$r[18]) { /* visible forum with no 'read' permission */
			$anonimouse = 1;
			$forum_list_table_data .= '<tr>
	<td class="RowStyleA" colspan=6><b>'.htmlspecialchars($r[13]).'</b><br />'.$r[14].'</td>
</tr>';
			continue;
		}

		/* increase thread & post count */
		$post_count += $r[15];
		$thread_count += $r[16];

		/* code to determine the last post id for 'latest' forum message */
		if ($r[11] > $last_msg_id) {
			$last_msg_id = $r[11];
		}

		if (!empty($GLOBALS['collapse'][$r[8]])) {
			continue;
		}

		$forum_icon = $r[9] ? '<img src="'.$r[9].'" alt="Icona del forum" />' : '&nbsp;';
		$forum_descr = $r[14] ? '<br />'.$r[14].'' : '';

		if (_uid && $r[17] < $r[2] && $usr->last_read < $r[2]) {
			$forum_read_indicator = '<img title="Nuovi messaggi" src="/egroupware/fudforum/3814588639/theme/italian/images/new_content.gif" alt="Nuovi messaggi" />';
		} else if (_uid) {
			$forum_read_indicator = '<img title="Nessun nuovo messaggio" src="/egroupware/fudforum/3814588639/theme/italian/images/existing_content.gif" alt="Nessun nuovo messaggio" />';
		} else {
			$forum_read_indicator = '<img title="Solo gli utenti registrati del forum possono tener traccia dei messaggi letti e non letti" src="/egroupware/fudforum/3814588639/theme/italian/images/existing_content.gif" alt="Solo gli utenti registrati del forum possono tener traccia dei messaggi letti e non letti" />';
		}

		if ($r[11]) {
			if ($r[3]) {
				$last_poster_profile = '<a href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$r[3].'&amp;'._rsid.'" class="GenLink">'.$r[4].'</a>';
			} else {
				$last_poster_profile = ''.$GLOBALS['ANON_NICK'];
			}
			$last_post = '<font class="DateText">'.strftime("%a, %d %B %Y", $r[2]).'</font><br />Da: '.$last_poster_profile.' <a href="/egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&amp;goto='.$r[11].'&amp;'._rsid.'" class="GenLink"><img title="'.$r[0].'" src="/egroupware/fudforum/3814588639/theme/italian/images/goto.gif" alt="'.$r[0].'" /></a>';
		} else {
			$last_post = 'n.d.';
		}

		if ($r[12] && ($mods = @unserialize($r[12]))) {
			$moderators = '';
			foreach($mods as $k => $v) {
				$moderators .= '<a href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$k.'&amp;'._rsid.'" class="GenLink">'.$v.'</a> &nbsp;';
			}
			$moderators = '<div class="TopBy"><b>Moderatori:</b> '.$moderators.'</div>';
		} else {
			$moderators = '&nbsp;';
		}

		$forum_list_table_data .= '<tr>
	<td class="RowStyleA" width=1>'.$forum_icon.'</td>
	<td class="RowStyleB" width=1>'.$forum_read_indicator.'</td>
	<td class="RowStyleA" width="100%"><a href="/egroupware/fudforum/3814588639/index.php?t='.t_thread_view.'&amp;frm_id='.$r[10].'&amp;'._rsid.'" class="big">'.htmlspecialchars($r[13]).'</a>'.$forum_descr.$moderators.'</td>
	<td class="RowStyleB" align="center">'.$r[15].'</td>
	<td class="RowStyleB" align="center">'.$r[16].'</td>
	<td class="RowStyleA" nowrap align="center">'.$last_post.'</td>
</tr>';
	}

function rebuild_stats_cache($last_msg_id)
{
	$tm_expire = __request_timestamp__ - ($GLOBALS['LOGEDIN_TIMEOUT'] * 60);

	list($obj->last_user_id, $obj->user_count) = db_saq('SELECT MAX(id), count(*)-1 FROM phpgw_fud_users');

	$obj->online_users_anon	= q_singleval('SELECT count(*) FROM phpgw_fud_ses s WHERE time_sec>'.$tm_expire.' AND user_id>2000000000');
	$obj->online_users_hidden = q_singleval('SELECT count(*) FROM phpgw_fud_ses s INNER JOIN phpgw_fud_users u ON u.id=s.user_id WHERE s.time_sec>'.$tm_expire.' AND (u.users_opt & 32768) > 0');
	$obj->online_users_reg = q_singleval('SELECT count(*) FROM phpgw_fud_ses s INNER JOIN phpgw_fud_users u ON u.id=s.user_id WHERE s.time_sec>'.$tm_expire.' AND (u.users_opt & 32768)=0');
	$c = uq('SELECT u.id, u.alias, u.users_opt, u.custom_color FROM phpgw_fud_ses s INNER JOIN phpgw_fud_users u ON u.id=s.user_id WHERE s.time_sec>'.$tm_expire.' AND (u.users_opt & 32768)=0 ORDER BY s.time_sec DESC LIMIT '.$GLOBALS['MAX_LOGGEDIN_USERS']);
	while ($r = db_rowarr($c)) {
		$obj->online_users_text[$r[0]] = draw_user_link($r[1], $r[2], $r[3]);
	}

	q('UPDATE phpgw_fud_stats_cache SET
		cache_age='.__request_timestamp__.',
		last_user_id='.(int)$obj->last_user_id.',
		user_count='.(int)$obj->user_count.',
		online_users_anon='.(int)$obj->online_users_anon.',
		online_users_hidden='.(int)$obj->online_users_hidden.',
		online_users_reg='.(int)$obj->online_users_reg.',
		online_users_text='.strnull(addslashes(@serialize($obj->online_users_text))));

	$obj->last_user_alias = q_singleval('SELECT alias FROM phpgw_fud_users WHERE id='.$obj->last_user_id);
	$obj->last_msg_subject = q_singleval('SELECT subject FROM phpgw_fud_msg WHERE id='.$last_msg_id);

	return $obj;
}

$logedin = $forum_info = '';

if ($FUD_OPT_1 & 1073741824 || $FUD_OPT_2 & 16) {
	if (!($st_obj = db_sab('SELECT sc.*,m.subject AS last_msg_subject, u.alias AS last_user_alias FROM phpgw_fud_stats_cache sc INNER JOIN phpgw_fud_users u ON u.id=sc.last_user_id INNER JOIN phpgw_fud_msg m ON m.id='.$last_msg_id.' WHERE sc.cache_age>'.(__request_timestamp__ - $STATS_CACHE_AGE)))) {
		$st_obj =& rebuild_stats_cache($last_msg_id);
	} else if ($st_obj->online_users_text) {
		$st_obj->online_users_text = @unserialize($st_obj->online_users_text);
	}

	$i_spy = $FUD_OPT_1 & 536870912 ? '[<a href="/egroupware/fudforum/3814588639/index.php?t=actions&amp;'._rsid.'" class="thLnk">mostra quello che fa la gente</a>] [<a href="/egroupware/fudforum/3814588639/index.php?t=online_today&amp;'._rsid.'" class="thLnk">Visitatori di oggi</a>]' : '';

	if ($FUD_OPT_1 & 1073741824) {
		if (@count($st_obj->online_users_text)) {
			foreach($st_obj->online_users_text as $k => $v) {
				$logedin .= '<a href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$k.'&amp;'._rsid.'" class="GenLink">'.$v.'</a>' . ' ';
			}
		} else {
			$logedin = '';
		}
		$logedin = '<tr><th width="100%">Utenti collegati '.$i_spy.'</th></tr>
<tr><td class="RowStyleA">
<font class="SmallText">Ci sono <b>'.$st_obj->online_users_reg.'</b> utenti, <b>'.$st_obj->online_users_hidden.'</b> utenti invisibili e <b>'.$st_obj->online_users_anon.'</b> guest che frequentano questo forum.&nbsp;&nbsp;&nbsp;<font class="adminColor">[Amministratore]</font>&nbsp;&nbsp;<font class="modsColor">[Moderatore]</font></font><br />
'.$logedin.'
</td></tr>';
	}
	if ($FUD_OPT_2 & 16) {
		$last_msg = $last_msg_id ? '<br />Ultimo messaggio del forum: <a href="/egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&amp;goto='.$last_msg_id.'&amp;'._rsid.'"><b>'.$st_obj->last_msg_subject.'</b></a>' : '';
		$forum_info = '<tr><td class="RowStyleB"><font class="SmallText">
I nostri utenti hanno postato un totale di <b>'.$post_count.'</b> messaggi all&#39;interno di <b>'.$thread_count.'</b> topic.<br />
Abbiamo <b>'.$st_obj->user_count.'</b> utenti registrati.<br />
L&#39;ultimo utente ad essersi registrato è <a href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$st_obj->last_user_id.'&amp;'._rsid.'" class="GenLink"><b>'.$st_obj->last_user_alias.'</b></a>'.$last_msg.'
</font></td></tr>';
	}
}

$loged_in_list = ($logedin || $forum_info) ? '<br /><img src="blank.gif" alt="" height=2 width=1 />
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
'.$logedin.'
'.$forum_info.'
</table>' : '';if ($FUD_OPT_2 & 2) {
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
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr>
	<th colspan=3 width="100%">Forum</th>
	<th nowrap>Messaggi</th>
	<th nowrap>Topic</th>
	<th nowrap>Ultimo messaggio</th>
</tr>
<?php echo $forum_list_table_data; ?>
</table>
<?php echo $mark_all_read; ?>
<?php if (!$anonimouse) echo $loged_in_list; ?>
<br />
<div class="RowStyleB"><font class="SmallText">
<div class="LegendH"><b>Legenda:</b></div>
<img src="/egroupware/fudforum/3814588639/theme/italian/images/new_content.gif" alt="Nuovi messaggi dall&#39;ultima visita" /> Nuovi messaggi dall&#39;ultima visita&nbsp;&nbsp;
<img src="/egroupware/fudforum/3814588639/theme/italian/images/existing_content.gif" alt="Nessun nuovo messaggio dall&#39;ultima visita" /> Nessun nuovo messaggio dall&#39;ultima visita
</font></div>
<?php if (!$anonimouse)	echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>
