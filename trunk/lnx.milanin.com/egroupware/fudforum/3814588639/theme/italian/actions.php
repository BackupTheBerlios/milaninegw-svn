<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: actions.php.t,v 1.1.1.1 2003/10/17 21:11:25 ralfbecker Exp $
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
}

	if (!($FUD_OPT_1 & 536870912)) {
		std_error('disabled');
	}

	ses_update_status($usr->sid, 'Quelli che si fanno gli affari degli altri, proprio come te');



	$rand_val = get_random_value();

	$limit = &get_all_read_perms(_uid, ($usr->users_opt & (524288|1048576)));

	$c = uq('SELECT
			s.action, s.user_id, s.forum_id,
			u.alias, u.custom_color, s.time_sec, u.users_opt,
			m.id, m.subject, m.post_stamp,
			t.forum_id,
			mm1.id, mm2.id
		FROM phpgw_fud_ses s
		LEFT JOIN phpgw_fud_users u ON s.user_id=u.id
		LEFT JOIN phpgw_fud_msg m ON u.u_last_post_id=m.id
		LEFT JOIN phpgw_fud_thread t ON m.thread_id=t.id
		LEFT JOIN phpgw_fud_mod mm1 ON mm1.forum_id=t.forum_id AND mm1.user_id='._uid.'
		LEFT JOIN phpgw_fud_mod mm2 ON mm2.forum_id=s.forum_id AND mm2.user_id='._uid.'
		WHERE s.time_sec>'.(__request_timestamp__ - ($LOGEDIN_TIMEOUT * 60)).' AND s.user_id!='._uid.' ORDER BY u.alias, s.time_sec DESC');

	$action_data = '';
	while ($r = db_rowarr($c)) {
		if ($r[6] & 32768 && !($usr->users_opt & 1048576)) {
			continue;
		}

		if ($r[3]) {
			$user_login = draw_user_link($r[3], $r[6], $r[4]);
			$user_login = '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&id='.$r[1].'&'._rsid.'">'.$user_login.'</a>';

			if (!$r[9]) {
				$last_post = 'n.d.';
			} else {
				$last_post = (!($usr->users_opt & 1048576) && !$r[11] && empty($limit[$r[10]])) ? 'Non disponi dei permessi necessari per visualizzare questo topic.' : ''.strftime("%a, %d %B %Y %H:%M", $r[9]).'<br />
<a href="/egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&goto='.$r[7].'&'._rsid.'">'.$r[8].'</a>';
			}
		} else {
			$user_login = ''.$GLOBALS['ANON_NICK'];
			$last_post = 'n.d.';
		}

		if (!$r[2] || ($usr->users_opt & 1048576 || !empty($limit[$r[2]]) || $r[12])) {
			if (($p = strpos($r[0], '?')) !== false) {
				$action = substr_replace($r[0], '?'._rsid.'&', $p, 1);
			} else if (($p = strpos($r[0], '.php')) !== false) {
				$action = substr_replace($r[0], '.php?'._rsid.'&', $p, 4);
			} else {
				$action = $r[0];
			}
		} else {
			$action = 'Non disponi dei permessi necessari per visualizzare questo topic.';
		}

		$action_data .= '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'">
	<td class="GenText">'.$user_login.'</td>
	<td class="GenText">'.$action.'</td>
	<td class="DateText">'.strftime("%H:%M:%S", $r[5]).'</td>
	<td class="SmallText">'.$last_post.'</td>
</tr>';
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
<div align="center" class="GenText">[<a href="/egroupware/fudforum/3814588639/index.php?t=actions&rand=<?php echo $rand_val; ?>&<?php echo _rsid; ?>" class="GenLink">Aggiorna l&#39;elenco</a>]</div>
<p>
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th>Utente</th><th>Azione</th><th>Ora</th><th>Ultimo post</th></tr>
<?php echo $action_data; ?>
</table>
<?php echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>