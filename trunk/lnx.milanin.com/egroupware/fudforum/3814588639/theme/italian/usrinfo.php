<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: usrinfo.php.t,v 1.2 2003/12/18 18:20:49 iliaa Exp $
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

function convert_bdate($val, $month_fmt)
{
	$ret['year']	= substr($val, 0, 4);
	$ret['day']	= substr($val, 6, 2);
	$ret['month']	= strftime($month_fmt, mktime(1, 1, 1, substr($val, 4, 2), 11, 2000));

	return $ret;
}

	if (!isset($_GET['id']) || !(int)$_GET['id']) {
		invl_inp_err();
	}

	if (!($u = db_sab('SELECT u.*, l.name AS level_name, l.level_opt, l.img AS level_img FROM phpgw_fud_users u LEFT JOIN phpgw_fud_level l ON l.id=u.level_id WHERE u.id='.(int)$_GET['id']))) {
		std_error('user');
	}

	$avatar = ($FUD_OPT_1 & 28 && $u->users_opt & 8388608 && !($u->level_opt & 2)) ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td nowrap valign="top" class="GenText">Avatar:</td><td class="GenText">'.$u->avatar_loc.'</td></tr>' : '';

	if ($avatar && $u->level_opt & 1) {
		$level_name = $level_image = '';
	} else {
		$level_name = $u->level_name ? ''.$u->level_name.'<br />' : '';
		$level_image = $u->level_img ? '<img src="images/'.$u->level_img.'" /><br />' : '';
	}

	$custom_tags = $u->custom_status ? ''.$u->custom_status.'<br />' : '';

	if (!($usr->users_opt & 1048576)) {
		$frm_perms = get_all_read_perms(_uid, ($usr->users_opt & 524288));
	}

	$moderation = '';
	if ($u->users_opt & 524288) {
		$c = uq('SELECT f.id, f.name FROM phpgw_fud_mod mm INNER JOIN phpgw_fud_forum f ON mm.forum_id=f.id INNER JOIN phpgw_fud_cat c ON f.cat_id=c.id WHERE '.($usr->users_opt & 1048576 ? '' : 'f.id IN('.implode(',', array_keys($frm_perms)).') AND ').'mm.user_id='.$u->id);
		while ($r = db_rowarr($c)) {
			$moderation .= '<a href="/egroupware/fudforum/3814588639/index.php?t='.t_thread_view.'&amp;frm_id='.$r[0].'&amp;'._rsid.'" class="GenLink">'.htmlspecialchars($r[1]).'</a>&nbsp;';
		}
		if ($moderation) {
			$moderation = 'Moderatore di:&nbsp;'.$moderation;
		}
	}



	$TITLE_EXTRA = ': Informazioni utente '.$user_info;

	ses_update_status($usr->sid, 'Guarda il profilo di <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&id='.$u->id.'">'.$user_info.'</a>');

	$status = (!empty($level_name) || !empty($moderation) || !empty($level_image) || !empty($custom_tags)) ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td nowrap valign="top" class="GenText">Status:</td><td class="GenText">
<font class="LevelText">
'.$level_name.'
'.$level_image.'
'.$custom_tags.'
</font>
'.$moderation.'
</td></tr>' : '';

	$avg = sprintf('%.2f', $u->posted_msg_count / ((__request_timestamp__ - $u->join_date) / 86400));
	if ($avg > $u->posted_msg_count) {
		$avg = $u->posted_msg_count;
	}

	$last_post = '';
	if ($u->u_last_post_id) {
		$r = db_saq('SELECT m.subject, m.id, m.post_stamp, t.forum_id FROM phpgw_fud_msg m INNER JOIN phpgw_fud_thread t ON m.thread_id=t.id WHERE m.id='.$u->u_last_post_id);
		if ($usr->users_opt & 1048576 || !empty($frm_perms[$r[3]])) {
			$last_post = '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td valign="top" nowrap class="GenText">Ultimo messaggio:</td><td class="GenText"><font class="DateText">'.strftime("%a, %d %B %Y %H:%M", $r[2]).'</font><br /><a href="/egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&amp;goto='.$r[1].'&amp;'._rsid.'" class="GenLink">'.$r[0].'</a></td></tr>';
		}
	}

	$user_image = ($FUD_OPT_2 & 65536 && $u->user_image && strpos($u->user_image, '://')) ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td nowrap valign="top" class="GenText">Immagine:</td><td class="GenText"><img src="'.$u->user_image.'" /></td></tr>' : '';


	if ($u->users_opt & 1) {
		$email_link = '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td nowrap class="GenText">Email:</td><td class="GenText"><a class="GenLink" href="mailto:'.$u->email.'">'.$u->email.'</a></td></tr>';
	} else if ($FUD_OPT_2 & 1073741824) {
		$encoded_login = urlencode($u->alias);
		$email_link = '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td nowrap class="GenText">Email:</td><td class="GenText">[<a href="/egroupware/fudforum/3814588639/index.php?t=email&amp;toi='.$u->id.'&amp;'._rsid.'">Clicca qui per spedire un&#39;email all&#39;utente</a>]</td></tr>';
	} else {
		$email_link = '';
	}

	if (($referals = q_singleval('SELECT count(*) FROM phpgw_fud_users WHERE referer_id='.$u->id))) {
		$referals = '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td nowrap class="GenText">Utenti referenziati:</td><td class="GenText"><a href="/egroupware/fudforum/3814588639/index.php?t=list_referers&amp;'._rsid.'">'.$referals.' Utenti</a></td></tr>';
	} else {
		$referals = '';
	}

	if (_uid && _uid != $u->id && !q_singleval("SELECT id FROM phpgw_fud_buddy WHERE user_id="._uid." AND bud_id=".$u->id)) {
		$buddy = '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td nowrap class="GenText">Buddy:</td><td class="GenText"><a href="/egroupware/fudforum/3814588639/index.php?t=buddy_list&amp;add='.$u->id.'&amp;'._rsid.'" class="GenLink">aggiungi alla buddy list</a></td></tr>';
	} else {
		$buddy = '';
	}

	if (($polls = q_singleval('SELECT count(*) FROM phpgw_fud_poll p INNER JOIN phpgw_fud_forum f ON p.forum_id=f.id WHERE p.owner='.$u->id.' AND f.cat_id>0 '.($usr->users_opt & 1048576 ? '' : ' AND f.id IN('.implode(',', array_keys($frm_perms)).')')))) {
		$polls = '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td nowrap class="GenText">Sondaggi:</td><td class="GenText"><a href="/egroupware/fudforum/3814588639/index.php?t=polllist&amp;uid='.$u->id.'&amp;'._rsid.'">'.$polls.'</a></td></tr>';
	} else {
		$polls = '';
	}

	$usrinfo_private_msg = ($FUD_OPT_1 & 1024 && _uid) ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td nowrap class="GenText">Messaggio privato:</td><td class="GenText"><a href="/egroupware/fudforum/3814588639/index.php?t=ppost&amp;'._rsid.'&amp;toi='.$u->id.'" class="GenLink"><img src="/egroupware/fudforum/3814588639/theme/italian/images/msg_pm.gif" /></a></td></tr>' : '';

	if ($u->users_opt & 1024) {
		$gender = '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td nowrap class="GenText">Genere:</td><td class="GenText">Maschio</td></tr>';
	} else if (!($u->users_opt & 512)) {
		$gender = '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td nowrap class="GenText">Genere:</td><td class="GenText">Femmina</td></tr>';
	} else {
		$gender = '';
	}

	$location	= $u->location ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td nowrap class="GenText">Località:</td><td class="GenText">'.$u->location.'</td></tr>' : '';
	$occupation	= $u->occupation ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td nowrap class="GenText">Occupazione:</td><td class="GenText">'.$u->occupation.'</td></tr>' : '';
	$interests	= $u->interests ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td nowrap class="GenText">Interessi:</td><td class="GenText">'.$u->interests.'</td></tr>' : '';
	$bio		= $u->bio ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td nowrap class="GenText">Bio:</td><td class="GenText">'.$u->bio.'</td></tr>' : '';
	$home_page	= $u->home_page ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td nowrap class="GenText">Homepage:</td><td class="GenText"><a href="'.$u->home_page.'" target="_blank">'.$u->home_page.'</a></td></tr>' : '';
	$im_icq		= $u->icq ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td valign="top" nowrap class="GenText"><a name="icq_msg">ICQ Message Form:</a></td><td class="GenText">
		'.$u->icq.' <img src="http://web.icq.com/whitepages/online?icq='.$u->icq.'&amp;img=5" /><br />
			<table border="0">
			<tr><td colspan=2>
				<form action="http://wwp.icq.com/scripts/WWPMsg.dll" method="post" target=_blank>
				<font face="Arial, Helvetica" size="1"><b>ICQ Online-Message Panel</b></font>
			</td></tr>
			<tr>
				<td>
					<font face="Arial, Helvetica" size="1">Nome del mittente:</font><br />
					<input type="text" name="from" value="" size="15" maxlength="40" onfocus="this.select()">
				</td>
				<td>
					<font face="Arial, Helvetica" size="1">Mittente:</font><br />
					<input type="text" name="fromemail" value="" size="15" maxlength="40" onfocus="this.select()">
				</td>
			</tr>
			<tr>
				<td colspan=2>
					<font face="Arial, Helvetica" size="1">Oggetto</font><br />
					<input type="text" name="subject" value="" size="32"><br />
					<font face="Arial, Helvetica" size="1">Messaggio</font><br />
					<textarea name="body" rows="3" cols="32" wrap="Virtual"></textarea>
					<input type="hidden" name="to" value="'.$u->icq.'"><br />
				</td>
			</tr>
			<tr><td colspan=2 align=right><input type="submit" class="button" name="Send" value="Invia"></td></tr>
			</form>
			</table>
			</td></tr>' : '';
	$im_jabber	= $u->jabber ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td nowrap class="GenText">Jabber:</td><td class="GenText">'.$u->jabber.'</td></tr>' : '';
	$im_aim		= $u->aim ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td nowrap class="GenText">AIM Handle:</td><td class="GenText"><a href="aim:goim?screenname='.$u->aim.'&amp;message=Hello+Are+you+there?" class="GenLink">'.$u->aim.'</a></td></tr>' : '';
	$im_yahoo	= $u->yahoo ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td nowrap class="GenText">Yahoo Messenger:</td><td class="GenText"><a href="http://edit.yahoo.com/config/send_webmesg?.target='.$u->yahoo.'&amp;.src=pg" class="GenLink">'.$u->yahoo.'</a></td></tr>' : '';
	$im_msnm	= $u->msnm ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td nowrap class="GenText">MSN Messenger:</td><td class="GenText">'.$u->msnm.'</td></tr>' : '';

	if ($u->bday) {
		$bday = convert_bdate($u->bday, '%B');
		$birth_date = '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td nowrap class="GenText">Data di nascita:</td><td class="GenText">'.$bday['month'].' '.$bday['day'].', '.$bday['year'].'</td></tr>';
	} else {
		$birth_date = '';
	}

	if ($FUD_OPT_2 & 2048 && $u->affero) {
		$im_affero = '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td nowrap class="GenText">Affero Username</td><td class="GenText"><a href="http://svcs.affero.net/user-history.php?u='.$u->affero.'" target="_blank">'.htmlspecialchars(urldecode($u->affero)).'</a></td></tr>';
	} else {
		$im_affero = '';
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
<tr><th colspan=2 width="100%">Profilo <?php echo $user_info; ?></th></tr>
<tr class="RowStyleA"><td nowrap class="GenText">Data di registrazione:</td><td width="100%" class="DateText"><?php echo strftime("%B %d, %Y", $u->join_date); ?></td></tr>
<tr class="RowStyleB"><td valign="top" nowrap class="GenText">Numero di messaggi:</td><td class="GenText"><?php echo $u->posted_msg_count; ?> Post (<?php echo $avg; ?> media di post giornalieri)<br /><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=showposts&amp;id=<?php echo $u->id; ?>&amp;<?php echo _rsid; ?>">Mostra tutti i post di <?php echo $user_info; ?></a></td></tr>
<?php echo $status; ?>
<?php echo $avatar; ?>
<?php echo $last_post; ?>
<?php echo $polls; ?>
<?php echo $user_image; ?>
<?php echo $email_link; ?>
<?php echo $usrinfo_private_msg; ?>
<?php echo $referals; ?>
<?php echo $home_page; ?>
<?php echo $gender; ?>
<?php echo $location; ?>
<?php echo $occupation; ?>
<?php echo $interests; ?>
<?php echo $bio; ?>
<?php echo $birth_date; ?>
<?php echo $im_icq; ?>
<?php echo $im_aim; ?>
<?php echo $im_yahoo; ?>
<?php echo $im_msnm; ?>
<?php echo $im_jabber; ?>
<?php echo $im_affero; ?>
<tr class="RowStyleC"><td nowrap align="right" class="GenText" colspan=2><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=showposts&amp;id=<?php echo $u->id; ?>&amp;<?php echo _rsid; ?>">Mostra tutti i post di <?php echo $user_info; ?></a></td></tr>
</table>
<?php echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>