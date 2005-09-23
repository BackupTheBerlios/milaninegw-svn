<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: ignore_list.php.t,v 1.1.1.1 2003/10/17 21:11:26 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function ignore_add($user_id, $ignore_id)
{
	q('INSERT INTO phpgw_fud_user_ignore (ignore_id, user_id) VALUES ('.$ignore_id.', '.$user_id.')');
	return ignore_rebuild_cache($user_id);
}

function ignore_delete($user_id, $ignore_id)
{
	q('DELETE FROM phpgw_fud_user_ignore WHERE user_id='.$user_id.' AND ignore_id='.$ignore_id);
	return ignore_rebuild_cache($user_id);
}

function ignore_rebuild_cache($uid)
{
	$q = uq('SELECT ignore_id FROM phpgw_fud_user_ignore WHERE user_id='.$uid);
	while ($ent = db_rowarr($q)) {
		$arr[$ent[0]] = 1;
	}

	if (isset($arr)) {
		q('UPDATE phpgw_fud_users SET ignore_list=\''.addslashes(serialize($arr)).'\' WHERE id='.$uid);
		return $arr;
	} else {
		q('UPDATE phpgw_fud_users SET ignore_list=NULL WHERE id='.$uid);
		return;
	}
}function check_return($returnto)
{
	if (!$returnto || !strncmp($returnto, 't=error', 7)) {
		header('Location: /egroupware/fudforum/3814588639/index.php?t=index&'._rsidl);
	} else {
		if (strpos($returnto, 'S=') === false && $GLOBALS['FUD_OPT_1'] & 128) {
			header('Location: /egroupware/fudforum/3814588639/index.php?'.$returnto.'&S='.s);
		} else {
			header('Location: /egroupware/fudforum/3814588639/index.php?'.$returnto);
		}
	}
	exit;
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

function ignore_alias_fetch($al, &$is_mod)
{
	if (!($tmp = db_saq("SELECT id, (users_opt & 1048576) FROM phpgw_fud_users WHERE alias='".addslashes(htmlspecialchars($al))."'"))) {
		return;
	}
	$is_mod = $tmp[1];

	return $tmp[0];
}

	if (isset($_POST['add_login'])) {
		if (!($ignore_id = ignore_alias_fetch($_POST['add_login'], $is_mod))) {
			error_dialog('Utente non trovato', 'L&#39;utente che hai cercato di inserire nella ignore list non risulta iscritto al forum. Attenzione: il motore di inserimento degli utenti distingue tra MAIUSCOLE e minuscole: controlla con attenzione!');
		}
		if ($is_mod) {
			error_dialog('Informazioni', 'Non puoi ignorare questo utente');
		}
		if (!empty($usr->ignore_list)) {
			$usr->ignore_list = @unserialize($usr->ignore_list);
		}
		if (!isset($usr->ignore_list[$ignore_id])) {
			ignore_add(_uid, $ignore_id);
		} else {
			error_dialog('Informazioni', 'Hai già inserito questo utente nella tua ignore list');
		}
	}

	/* incomming from message display page (ignore link) */
	if (isset($_GET['add']) && ($_GET['add'] = (int)$_GET['add'])) {
		if (!empty($usr->ignore_list)) {
			$usr->ignore_list = @unserialize($usr->ignore_list);
		}

		if (($ignore_id = q_singleval('SELECT id FROM phpgw_fud_users WHERE id='.$_GET['add'].' AND (users_opt & 1048576)=0')) && !isset($usr->ignore_list[$ignore_id])) {
			ignore_add(_uid, $ignore_id);
		}
		check_return($usr->returnto);
	}

	if (isset($_GET['del']) && ($_GET['del'] = (int)$_GET['del'])) {
		ignore_delete(_uid, $_GET['del']);
		/* needed for external links to this form */
		if (isset($_GET['redr'])) {
			check_return($usr->returnto);
		}
	}

	ses_update_status($usr->sid, 'Sfoglia la propria ignore list');

	$ignore_member_search = ($FUD_OPT_1 & (8388608|4194304) ? '<br>Or use the <a href="javascript://" class="GenLink" onClick="javascript: window_open(\'/egroupware/fudforum/3814588639/index.php?t=pmuserloc&amp;'._rsid.'&amp;js_redr=buddy_add.add_login&amp;overwrite=1\', \'user_list\', 250,250);">Find User</a> feature to find a person.' : '');

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

	$c = uq('SELECT ui.ignore_id, ui.id as ignoreent_id,
			u.id, u.alias AS login, u.join_date, u.posted_msg_count, u.home_page
		FROM phpgw_fud_user_ignore ui
		LEFT JOIN phpgw_fud_users u ON ui.ignore_id=u.id
		WHERE ui.user_id='._uid);

	$ignore_list = '';
	if (($r = @db_rowarr($c))) {
		do {
			if ($r[0]) {
				$homepage_link = $r[6] ? '<a class="GenLink" href="'.$r[6].'" target="_blank"><img src="/egroupware/fudforum/3814588639/theme/italian/images/homepage.gif" alt="" /></a>' : '';
				$email_link = $FUD_OPT_2 & 1073741824 ? '<a href="/egroupware/fudforum/3814588639/index.php?t=email&amp;toi='.$r[2].'&amp;'._rsid.'" class="GenLink"><img src="/egroupware/fudforum/3814588639/theme/italian/images/msg_email.gif" alt="" /></a>' : '';
				$ignore_list .= '<tr class="'.alt_var('ignore_alt','RowStyleA','RowStyleB').'">
	<td width="100%" class="GenText"><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$r[2].'&amp;'._rsid.'">'.$r[3].'</a>&nbsp;<font class="SmallText">(<a href="/egroupware/fudforum/3814588639/index.php?t=ignore_list&amp;del='.$r[0].'&amp;'._rsid.'" class="GenLink">rimuovi</a>)</font></td>
	<td align="center">'.$r[5].'</td>
	<td align="center" nowrap>'.strftime("%a, %d %B %Y %H:%M", $r[4]).'</td>
	<td class="GenText" nowrap><a href="/egroupware/fudforum/3814588639/index.php?t=showposts&amp;'._rsid.'&amp;id='.$r[2].'" class="GenLink"><img src="/egroupware/fudforum/3814588639/theme/italian/images/show_posts.gif" alt="" /></a> '.$email_link.' '.$homepage_link.'</td>
</tr>';
			} else {
				$ignore_list .=	'<tr class="'.alt_var('ignore_alt','RowStyleA','RowStyleB').'">
	<td width="100%" colspan=4 class="GenText"><font class="anon">'.$GLOBALS['ANON_NICK'].'</font>&nbsp;<font class="SmallText">(<a href="/egroupware/fudforum/3814588639/index.php?t=ignore_list&amp;del='.$r[1].'&amp;'._rsid.'" class="GenLink">rimuovi</a>)</font></td>
</tr>';
			}
		} while (($r = db_rowarr($c)));
		$ignore_list = '<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th>Utenti ignorati</th><th nowrap align="center">Numero di messaggi</th><th nowrap align="center">Registrato</th><th nowrap align="center">Azione</th></tr>
'.$ignore_list.'
</table>';
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
<?php echo $tabs; ?>
<?php echo $ignore_list; ?>
<br /><br />
<form name="buddy_add" action="/egroupware/fudforum/3814588639/index.php?t=ignore_list" method="post"><?php echo _hs; ?><div align="center">
<table border="0" cellspacing="1" cellpadding="2" class="MiniTable">
<tr><th nowrap>Aggiungi Ignoramus</th></tr>
<tr class="RowStyleA">
<td nowrap class="GenText"><font class="Smalltext">Enter the login of the user you wish to add.<?php echo $ignore_member_search; ?></font><p>
<input type="text" name="add_login" value="" maxlength=100 size=25> <input type="submit" class="button" name="submit" value="Aggiungi"></td></tr>
</table></div></form>
<?php echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>