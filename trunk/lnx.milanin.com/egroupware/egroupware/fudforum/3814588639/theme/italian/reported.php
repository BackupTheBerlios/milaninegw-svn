<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: reported.php.t,v 1.1.1.1 2003/10/17 21:11:26 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}/* Handle poll votes if any are present */
function register_vote(&$options, $poll_id, $opt_id, $mid)
{
	/* invalid option or previously voted */
	if (!isset($options[$opt_id]) || q_singleval('SELECT id FROM phpgw_fud_poll_opt_track WHERE poll_id='.$poll_id.' AND user_id='._uid)) {
		return;
	}

	if (db_li('INSERT INTO phpgw_fud_poll_opt_track(poll_id, user_id, poll_opt) VALUES('.$poll_id.', '._uid.', '.$opt_id.')', $a)) {
		q('UPDATE phpgw_fud_poll_opt SET count=count+1 WHERE id='.$opt_id);
		q('UPDATE phpgw_fud_poll SET total_votes=total_votes+1 WHERE id='.$poll_id);
		poll_cache_rebuild($opt_id, $options);
		q('UPDATE phpgw_fud_msg SET poll_cache='.strnull(addslashes(@serialize($options))).' WHERE id='.$mid);
	}

	return 1;
}

$query_type = (empty($_POST['poll_opt']) || !($_POST['poll_opt'] = (int)$_POST['poll_opt']) ? 'uq' : 'q');

/* needed for message threshold & reveling messages */
if (isset($_GET['rev'])) {
	$tmp = explode(':', $_GET['rev']);
	foreach ($tmp as $v) {
		$GLOBALS['__FMDSP__'][$v] = 1;
	}
	define('reveal_lnk', '&amp;rev=' . $_GET['rev']);
} else {
	define('reveal_lnk', '');
}

/* initialize buddy & ignore list for registered users */
if (_uid) {
	if ($usr->buddy_list) {
		$usr->buddy_list = @unserialize($usr->buddy_list);
	}
	if ($usr->ignore_list) {
		$usr->ignore_list = @unserialize($usr->ignore_list);
	}

	/* handle temporarily un-hidden users */
	if (isset($_GET['reveal'])) {
		$tmp = explode(':', $_GET['reveal']);
		foreach($tmp as $v) {
			if (isset($usr->ignore_list[$v])) {
				$usr->ignore_list[$v] = 0;
			}
		}
		define('unignore_tmp', '&amp;reveal='.$_GET['reveal']);
	} else {
		define('unignore_tmp', '');
	}
} else {
	define('unignore_tmp', '');
}

if ($GLOBALS['FUD_OPT_2'] & 2048) {
	$GLOBALS['affero_domain'] = parse_url($WWW_ROOT);
	$GLOBALS['affero_domain'] = $GLOBALS['affero_domain']['host'];
}

$_SERVER['QUERY_STRING_ENC'] = str_replace('&', '&amp;', $_SERVER['QUERY_STRING']);

function make_tmp_unignore_lnk($id)
{
	if (!isset($_GET['reveal'])) {
		return $_SERVER['QUERY_STRING_ENC'] . '&amp;reveal='.$id;
	} else {
		return str_replace('&amp;reveal='.$_GET['reveal'], unignore_tmp . ':' . $id, $_SERVER['QUERY_STRING_ENC']);
	}
}

function make_reveal_link($id)
{
	if (!isset($GLOBALS['__FMDSP__'])) {
		return $_SERVER['QUERY_STRING_ENC'] . '&amp;rev='.$id;
	} else {
		return str_replace('&amp;rev='.$_GET['rev'], reveal_lnk . ':' . $id, $_SERVER['QUERY_STRING_ENC']);
	}
}

/* Draws a message, needs a message object, user object, permissions array,
 * flag indicating wether or not to show controls and a variable indicating
 * the number of the current message (needed for cross message pager)
 * last argument can be anything, allowing forms to specify various vars they
 * need to.
 */
function tmpl_drawmsg($obj, $usr, $perms, $hide_controls, &$m_num, $misc)
{
	$o1 =& $GLOBALS['FUD_OPT_1'];
	$o2 =& $GLOBALS['FUD_OPT_2'];
	$a =& $obj->users_opt;
	$b =& $usr->users_opt;
	$c =& $obj->level_opt;

	/* draw next/prev message controls */
	if (!$hide_controls && $misc) {
		/* tree view is a special condition, we only show 1 message per page */
		if ($_GET['t'] == 'tree') {
			$prev_message = $misc[0] ? '<a href="/egroupware/fudforum/3814588639/index.php?t='.$_GET['t'].'&amp;'._rsid.'&amp;th='.$obj->thread_id.'&amp;mid='.$misc[0].'" class="GenLink"><img src="/egroupware/fudforum/3814588639/theme/italian/images/up.png" title="Messaggio precedente" alt="Messaggio precedente" width=16 height=11 /></a>' : '';
			$next_message = $misc[1] ? '<a href="/egroupware/fudforum/3814588639/index.php?t='.$_GET['t'].'&amp;'._rsid.'&amp;th='.$obj->thread_id.'&amp;mid='.$misc[1].'" class="GenLink"><img alt="Messaggio precedente" title="Messaggio successivo" src="/egroupware/fudforum/3814588639/theme/italian/images/down.png" width=16 height=11 /></a>' : '';
			$next_page = '';
		} else {
			/* handle previous link */
			if (!$m_num && $obj->id > $obj->root_msg_id) { /* prev link on different page */
				$msg_start = $misc[0] - $misc[1];
				$prev_message = '<a href="/egroupware/fudforum/3814588639/index.php?t='.$_GET['t'].'&amp;'._rsid.'&amp;prevloaded=1&amp;th='.$obj->thread_id.'&amp;start='.$msg_start.reveal_lnk.unignore_tmp.'" class="GenLink"><img src="/egroupware/fudforum/3814588639/theme/italian/images/up.png" title="Messaggio precedente" alt="Messaggio precedente" width=16 height=11 /></a>';
			} else if ($m_num) { /* inline link, same page */
				$msg_num = $m_num;
				$prev_message = '<a href="#msg_num_'.$msg_num.'" class="GenLink"><img alt="Messaggio precedente" title="Messaggio precedente" src="/egroupware/fudforum/3814588639/theme/italian/images/up.png" width=16 height=11 /></a>';
			} else {
				$prev_message = '';
			}

			/* handle next link */
			if ($obj->id < $obj->last_post_id) {
				if ($m_num && !($misc[1] - $m_num - 1)) { /* next page link */
					$msg_start = $misc[0] + $misc[1];
					$next_message = '<a href="/egroupware/fudforum/3814588639/index.php?t='.$_GET['t'].'&amp;'._rsid.'&amp;prevloaded=1&amp;th='.$obj->thread_id.'&amp;start='.$msg_start.reveal_lnk.unignore_tmp.'" class="GenLink"><img alt="Messaggio precedente" title="Messaggio successivo" src="/egroupware/fudforum/3814588639/theme/italian/images/down.png" width=16 height=11 /></a>';
					$next_page = '<a href="/egroupware/fudforum/3814588639/index.php?t='.$_GET['t'].'&amp;'._rsid.'&amp;prevloaded=1&amp;th='.$obj->thread_id.'&amp;start='.$msg_start.reveal_lnk.unignore_tmp.'" class="GenLink">Pagina successiva <img src="/egroupware/fudforum/3814588639/theme/italian/images/goto.gif" alt="" /></a>';
				} else {
					$msg_num = $m_num + 2;
					$next_message = '<a href="#msg_num_'.$msg_num.'" class="GenLink"><img alt="Messaggio successivo" title="Messaggio successivo" src="/egroupware/fudforum/3814588639/theme/italian/images/down.png" width=16 height=11 /></a>';
					$next_page = '';
				}
			} else {
				$next_page = $next_message = '';
			}
		}
		$m_num++;
	} else {
		$next_page = $next_message = $prev_message = '';
	}

	if (!$obj->user_id) {
		$user_login =& $GLOBALS['ANON_NICK'];
		$user_login_td = $GLOBALS['ANON_NICK'].' è ignorato&nbsp;';
	} else {
		$user_login =& $obj->login;
		$user_login_td = 'Il messaggio di <a href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&'._rsid.'&id='.$obj->user_id.'" class="GenLink">'.$obj->login.'</a> viene ignorato&nbsp;';
	}

	/* check if the message should be ignored and it is not temporarily revelead */
	if ($usr->ignore_list && !empty($usr->ignore_list[$obj->poster_id]) && !isset($GLOBALS['__FMDSP__'][$obj->id])) {
		$rev_url = make_reveal_link($obj->id);
		$un_ignore_url = make_tmp_unignore_lnk($obj->poster_id);
		return !$hide_controls ? '<tr><td>
<table border=0 cellspacing=0 cellpadding=0 class="MsgTable">
<tr>
<td align="left" class="MsgIg">
<a name="msg_num_'.$m_num.'"></a>
<a name="msg_'.$obj->id.'"></a>
'.$user_login_td.'
[<a href="/egroupware/fudforum/3814588639/index.php?'.$rev_url.'" class="GenLink">mostra messaggio</a>]&nbsp;
[<a href="/egroupware/fudforum/3814588639/index.php?'.$un_ignore_url.'" class="GenLink">mostra tutti i messaggi di '.$user_login.'</a>]&nbsp;
[<a href="/egroupware/fudforum/3814588639/index.php?t=ignore_list&amp;del='.$obj->poster_id.'&amp;redr=1&amp;'._rsid.'" class="GenLink">smetti di ignorare questo utente</a>]</td>
<td class="MsgIg" align="right">'.$prev_message.$next_message.'</td></tr>
</table></td></tr>' : '<tr class="MsgR1">
<td class="GenText"><a name="msg_num_'.$m_num.'"></a> <a name="msg_'.$obj->id.'"></a>Post by '.$user_login.' is ignored&nbsp;</td>
</tr>';
	}

	if ($obj->user_id) {
		if (!$hide_controls) {
			$custom_tag = $obj->custom_status ? '<br />'.$obj->custom_status : '';

			if ($obj->avatar_loc && $a & 8388608 && $b & 8192 && $o1 & 28 && !($c & 2)) {
				if (!($c & 1)) {
					$level_name =& $obj->level_name;
					$level_image = $obj->level_img ? '&nbsp;<img src="images/'.$obj->level_img.'" alt="" />' : '';
				} else {
					$level_name = $level_image = '';
				}
			} else {
				$level_image = $obj->level_img ? '&nbsp;<img src="images/'.$obj->level_img.'" alt="" />' : '';
				$obj->avatar_loc = '';
				$level_name =& $obj->level_name;
			}
			$avatar = ($obj->avatar_loc || $level_image) ? '<td class="avatarPad" width="1">'.$obj->avatar_loc.$level_image.'</td>' : '';
			$dmsg_tags = ($custom_tag || $level_name) ? '<div class="ctags">'.$level_name.$custom_tag.'</div>' : '';

			if (($o2 & 32 && !($a & 32768)) || $b & 1048576) {
				$online_indicator = (($obj->time_sec + $GLOBALS['LOGEDIN_TIMEOUT'] * 60) > __request_timestamp__) ? '<img src="/egroupware/fudforum/3814588639/theme/italian/images/online.gif" alt="'.$user_login.' è attualmente online" title="'.$user_login.' è attualmente online" />&nbsp;' : '<img src="/egroupware/fudforum/3814588639/theme/italian/images/offline.gif" alt="'.$user_login.'  è attualmente offline" title="'.$user_login.'  è attualmente offline" />&nbsp;';
			} else {
				$online_indicator = '';
			}

			$user_link = '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$obj->user_id.'&amp;'._rsid.'">'.$user_login.'</a>';

			if ($obj->location) {
				if (strlen($obj->location) > $GLOBALS['MAX_LOCATION_SHOW']) {
					$location = substr($obj->location, 0, $GLOBALS['MAX_LOCATION_SHOW']) . '...';
				} else {
					$location =& $obj->location;
				}
				$location = '<br /><b>Località: </b>'.$location;
			} else {
				$location = '';
			}

			if (_uid && _uid != $obj->user_id) {
				$buddy_link	= !isset($usr->buddy_list[$obj->user_id]) ? '<a href="/egroupware/fudforum/3814588639/index.php?t=buddy_list&amp;add='.$obj->user_id.'&amp;'._rsid.'" class="GenLink">aggiungi alla buddy list</a><br />' : '<a href="/egroupware/fudforum/3814588639/index.php?t=buddy_list&amp;del='.$obj->user_id.'&amp;redr=1&amp;'._rsid.'" class="GenLink">remove from buddy list</a><br />';
				$ignore_link	= !isset($usr->ignore_list[$obj->user_id]) ? '<a href="/egroupware/fudforum/3814588639/index.php?t=ignore_list&amp;add='.$obj->user_id.'&amp;'._rsid.'" class="GenLink">ignora tutti i messaggi<br>di questo utente</a>' : '<a href="/egroupware/fudforum/3814588639/index.php?t=ignore_list&amp;del='.$obj->user_id.'&amp;redr=1&amp;'._rsid.'" class="GenLink">smetti di ignorare i messaggi di questo utente</a>';
				$dmsg_bd_il	= $buddy_link.$ignore_link.'<br />';
			} else {
				$dmsg_bd_il = '';
			}

			/* show im buttons if need be */
			if ($b & 16384) {
				$im_icq		= $obj->icq ? '<a href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$obj->poster_id.'&amp;'._rsid.'#icq_msg"><img title="'.$obj->icq.'" src="/egroupware/fudforum/3814588639/theme/italian/images/icq.gif" alt="" /></a>' : '';
				$im_aim		= $obj->aim ? '<a href="aim:goim?screenname='.$obj->aim.'&amp;message=Hi.+Are+you+there?" target="_blank"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/aim.gif" title="'.$obj->aim.'" /></a>' : '';
				$im_yahoo	= $obj->yahoo ? '<a target="_blank" href="http://edit.yahoo.com/config/send_webmesg?.target='.$obj->yahoo.'&amp;.src=pg"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/yahoo.gif" title="'.$obj->yahoo.'" /></a>' : '';
				$im_msnm	= $obj->msnm ? '<a href="mailto: '.$obj->msnm.'"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/msnm.gif" title="'.$obj->msnm.'" /></a>' : '';
				$im_jabber	= $obj->jabber ? '<img src="/egroupware/fudforum/3814588639/theme/italian/images/jabber.gif" title="'.$obj->jabber.'" alt="" />' : '';
				if ($o2 & 2048) {
					$im_affero = $obj->affero ? '<a href="http://svcs.affero.net/rm.php?r='.$obj->affero.'&amp;ll='.$obj->forum_id.'.'.$GLOBALS['affero_domain'].'&amp;lp='.$obj->forum_id.'.'.urlencode($GLOBALS['affero_domain']['host']).'&amp;ls='.urlencode($obj->subject).'" target=_blank><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/affero_reg.gif" /></a>' : '<a href="http://svcs.affero.net/rm.php?m='.urlencode($obj->email).'&amp;ll='.$obj->forum_id.'.'.$GLOBALS['affero_domain'].'&amp;lp='.$obj->forum_id.'.'.urlencode($GLOBALS['affero_domain']['host']).'&amp;ls='.urlencode($obj->subject).'" target=_blank><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/affero_noreg.gif" /></a>';
				} else {
					$im_affero = '';
				}
				$dmsg_im_row = ($im_icq || $im_aim || $im_yahoo || $im_msnm || $im_jabber || $im_affero) ? $im_icq.' '.$im_aim.' '.$im_yahoo.' '.$im_msnm.' '.$im_jabber.' '.$im_affero.'<br />' : '';
			} else {
				$dmsg_im_row = '';
			}
		 } else {
		 	$user_link = $user_login;
		 	$dmsg_tags = $dmsg_im_row = $dmsg_bd_il = $location = $online_indicator = $avatar = '';
		 }
	} else {
		$user_link = $user_login;
		$dmsg_tags = $dmsg_im_row = $dmsg_bd_il = $location = $online_indicator = $avatar = '';
	}

	/* Display message body
	 * If we have message threshold & the entirity of the post has been revelead show a preview
	 * otherwise if the message body exists show an actual body
	 * if there is no body show a 'no-body' message
	 */
	if (!$hide_controls && $obj->message_threshold && $obj->length_preview && $obj->length > $obj->message_threshold && !isset($GLOBALS['__FMDSP__'][$obj->id])) {
		$rev_url = make_reveal_link($obj->id);
		$msg_body = read_msg_body($obj->offset_preview, $obj->length_preview, $obj->file_id_preview);
		$msg_body = '<font class="MsgBodyText">'.$msg_body.'</font>
<br /><div align="center">[<a href="/egroupware/fudforum/3814588639/index.php?'.$rev_url.'" class="GenLink">Mostra il resto del messaggio</a>]</div>';
	} else if ($obj->length) {
		$msg_body = read_msg_body($obj->foff, $obj->length, $obj->file_id);
		$msg_body = '<font class="MsgBodyText">'.$msg_body.'</font>';
	} else {
		$msg_body = 'Non c&#39;è il corpo del messaggio!';
	}

	if ($obj->poll_cache) {
		$obj->poll_cache = @unserialize($obj->poll_cache);
	}

	/* handle poll votes */
	if (!empty($_POST['poll_opt']) && ($_POST['poll_opt'] = (int)$_POST['poll_opt']) && !($obj->thread_opt & 1) && $perms & 512) {
		if (register_vote($obj->poll_cache, $obj->poll_id, $_POST['poll_opt'], $obj->id)) {
			$obj->total_votes += 1;
			$obj->cant_vote = 1;
		}
		unset($_GET['poll_opt']);
	}

	/* display poll if there is one */
	if ($obj->poll_id && $obj->poll_cache) {
		/* we need to determine if we allow the user to vote or see poll results */
		$show_res = 1;

		if (isset($_GET['pl_view']) && !isset($_POST['pl_view'])) {
			$_POST['pl_view'] = $_GET['pl_view'];
		}

		/* various conditions that may prevent poll voting */
		if (!$hide_controls && !$obj->cant_vote && (!isset($_POST['pl_view']) || $_POST['pl_view'] != $obj->poll_id)) {
			if ($perms & 512 && (!($obj->thread_opt & 1) || $perms & 4096)) {
				if (!$obj->expiry_date || ($obj->creation_date + $obj->expiry_date) > __request_timestamp__) {
					/* check if the max # of poll votes was reached */
					if (!$obj->max_votes || $obj->total_votes < $obj->max_votes) {
						$show_res = 0;
					}
				}
			}
		}

		$i = 0;

		$poll_data = '';
		foreach ($obj->poll_cache as $k => $v) {
			$i++;
			if ($show_res) {
				$length = ($v[1] && $obj->total_votes) ? round($v[1] / $obj->total_votes * 100) : 0;
				$poll_data .= '<tr class="'.alt_var('msg_poll_alt_clr','RowStyleB','RowStyleA').'"><td>'.$i.'.</td><td>'.$v[0].'</td><td><img src="/egroupware/fudforum/3814588639/theme/italian/images/poll_pix.gif" alt="" height="10" width="'.$length.'" /> '.$v[1].' / '.$length.'%</td></tr>';
			} else {
				$poll_data .= '<tr class="'.alt_var('msg_poll_alt_clr','RowStyleB','RowStyleA').'"><td>'.$i.'.</td><td colspan=2><input type="radio" name="poll_opt" value="'.$k.'">&nbsp;&nbsp;'.$v[0].'</td></tr>';
			}
		}

		if (!$show_res) {
			$view_poll_results_button = $obj->total_votes ? '<input type="submit" class="button" name="pl_res" value="Visualizza risultati">' : '';
			$poll_buttons = '<tr class="'.alt_var('msg_poll_alt_clr','RowStyleB','RowStyleA').'"><td colspan=3 align="right"><input type="submit" class="button" name="pl_vote" value="Vota">&nbsp;'.$view_poll_results_button.'</td></tr>';
			$poll = '<p>
<form action="/egroupware/fudforum/3814588639/index.php?'.$_SERVER['QUERY_STRING'].'#msg_'.$obj->id.'" method="post">'._hs.'
<table border=0 cellspacing=1 cellpadding=2 class="PollTable">
<tr><th nowrap colspan=3>'.$obj->poll_name.'<img src="blank.gif" alt="" height=1 width=10 /><font size="-1">[ '.$obj->total_votes.' voti ]</font></th></tr>
'.$poll_data.'
'.$poll_buttons.'
</table><input type="hidden" name="pl_view" value="'.$obj->poll_id.'"></form><p>';
		} else {
			$poll = '<p><table border=0 cellspacing=1 cellpadding=2 class="PollTable">
<tr><th nowrap colspan=3>'.$obj->poll_name.'<img src="blank.gif" alt="" height=1 width=10 /><font size="-1">[ '.$obj->total_votes.' voti ]</font></th></tr>
'.$poll_data.'
</table><p>';
		}
	} else {
		$poll = '';
	}

	/* draw file attachments if there are any */
	$drawmsg_file_attachments = '';
	if ($obj->attach_cnt && !empty($obj->attach_cache)) {
		$atch = @unserialize($obj->attach_cache);
		if (is_array($atch) && count($atch)) {
			foreach ($atch as $v) {
				$sz = $v[2] / 1024;
				$sz = $sz < 1000 ? number_format($sz, 2).'KB' : number_format($sz/1024, 2).'MB';
				$drawmsg_file_attachments .= '<tr>
<td valign="middle"><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=getfile&amp;id='.$v[0].'&amp;'._rsid.'"><img alt="" src="images/mime/'.$v[4].'" /></a></td>
<td><font class="GenText"><b>Attachment:</b></font> <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=getfile&amp;id='.$v[0].'&amp;'._rsid.'">'.$v[1].'</a><br />
<font class="SmallText">(Dimensione: '.$sz.', Scaricato '.$v[3].' volte)</font></td></tr>';
			}
			$drawmsg_file_attachments = '<p>
<table border=0 cellspacing=0 cellpadding=2>
'.$drawmsg_file_attachments.'
</table>';
		}
	}

	/* Determine if the message was updated and if this needs to be shown */
	if ($obj->update_stamp) {
		if ($obj->updated_by != $obj->poster_id && $o1 & 67108864) {
			$modified_message = '<p>[Aggiornato il : '.strftime("%a, %d %B %Y %H:%M", $obj->update_stamp).'] dal moderatore';
		} else if ($obj->updated_by == $obj->poster_id && $o1 & 33554432) {
			$modified_message = '<p>[Aggiornato il : '.strftime("%a, %d %B %Y %H:%M", $obj->update_stamp).']';
		} else {
			$modified_message = '';
		}
	} else {
		$modified_message = '';
	}

	$rpl = '';
	if (!$hide_controls) {
		$ip_address = ($b & 1048576 || $usr->md || $o1 & 134217728) ? '<b>IP:</b> <a href="http://www.nic.com/cgi-bin/whois.cgi?query='.$obj->ip_addr.'" target="_blank">'.$obj->ip_addr.'</a>' : '';
		$host_name = ($obj->host_name && $o1 & 268435456) ? '<b>Da:</b> '.$obj->host_name.'<br />' : '';
		$msg_icon = !$obj->icon ? '' : '<img src="images/message_icons/'.$obj->icon.'" alt="'.$obj->icon.'" />&nbsp;&nbsp;';
		$signature = ($obj->sig && $o1 & 32768 && $obj->msg_opt & 1 && $b & 4096) ? '<p><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br />'.$obj->sig : '';

		$report_to_mod_link = '<div align="right"><font class="SmallText">[<a class="SmallText" href="/egroupware/fudforum/3814588639/index.php?t=report&amp;msg_id='.$obj->id.'&amp;'._rsid.'">Segnala il messaggio al moderatore</a>]</font></div>';

		if ($obj->reply_to && $obj->reply_to != $obj->id && $o2 & 536870912) {
			if ($_GET['t'] != 'tree' && $_GET['t'] != 'msg') {
				$lnk = d_thread_view;
			} else {
				$lnk =& $_GET['t'];
			}
			$rpl = '<font class="small"> [ <a href="/egroupware/fudforum/3814588639/index.php?t='.$lnk.'&amp;'._rsid.'&amp;th='.$obj->thread_id.'&amp;goto='.$obj->reply_to.'" class="small">is a reply to message #'.$obj->reply_to.'</a> ]</font>';
		}

		if ($obj->user_id) {
			$user_profile = '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$obj->user_id.'&amp;'._rsid.'"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/msg_about.gif" /></a>';
			$email_link = ($o1 & 4194304 && $a & 16) ? '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=email&amp;toi='.$obj->user_id.'&amp;'._rsid.'"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/msg_email.gif" /></a>' : '';
			$private_msg_link = $o1 & 1024 ? '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=ppost&amp;toi='.$obj->user_id.'&amp;'._rsid.'"><img alt="Invia un messaggi privato a questo utente" title="Invia un messaggi privato a questo utente" src="/egroupware/fudforum/3814588639/theme/italian/images/msg_pm.gif" /></a>' : '';
			$dmsg_user_info = '<br /><b>Messaggi:</b> '.$obj->posted_msg_count.'<br />
<b>Registrato:</b> '.strftime("%B %Y", $obj->join_date).'
'.$location;
		} else {
			$user_profile = $email_link = $private_msg_link = '';
			$dmsg_user_info = ($host_name || $ip_address) ? '' : '';
		}

		/* little trick, this variable will only be avaliable if we have a next link leading to another page */
		if (isset($next_page)) {
			$next_page = '&nbsp;';
		}

		$delete_link = $perms & 32 ? '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=mmod&amp;del='.$obj->id.'&amp;'._rsid.'"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/msg_delete.gif" /></a>&nbsp;' : '';

		if ($perms & 16 || (_uid == $obj->poster_id && (!$GLOBALS['EDIT_TIME_LIMIT'] || __request_timestamp__ - $obj->post_stamp < $GLOBALS['EDIT_TIME_LIMIT'] * 60))) {
			$edit_link = '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=post&amp;msg_id='.$obj->id.'&amp;'._rsid.'"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/msg_edit.gif" /></a>&nbsp;&nbsp;&nbsp;&nbsp;';
		} else {
			$edit_link = '';
		}

		if (!($obj->thread_opt & 1) || $perms & 4096) {
			$reply_link = '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=post&amp;reply_to='.$obj->id.'&amp;'._rsid.'"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/msg_reply.gif" /></a>&nbsp;';
			$quote_link = '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=post&amp;reply_to='.$obj->id.'&amp;quote=true&amp;'._rsid.'"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/msg_quote.gif" /></a>';
		} else {
			$reply_link = $quote_link = '';
		}

		$message_toolbar = '<tr><td colspan="2" class="MsgToolBar"><table border=0 cellspacing=0 cellpadding=0 width="100%"><tr>
<td nowrap align="left">'.$user_profile.'&nbsp;'.$email_link.'&nbsp;'.$private_msg_link.'</td>
<td width="100%" align="center" class="GenText">'.$next_page.'</td>
<td nowrap align="right">'.$delete_link.$edit_link.$reply_link.$quote_link.'</td>
</tr></table></td></tr>';
	} else {
		$host_name = $ip_address = $dmsg_user_info = $msg_icon = $signature = $report_to_mod_link = $message_toolbar = '';
	}

	return '<tr><td class="MsgSpacer"><table cellspacing=0 cellpadding=0 class="MsgTable">
<tr>
<td valign="top" align="left" class="MsgR1"><font class="MsgSubText"><a name="msg_num_'.$m_num.'"></a><a name="msg_'.$obj->id.'"></a>'.$msg_icon.$obj->subject.$rpl.'</font></td>
<td valign="top" align="right" class="MsgR1"><font class="DateText">'.strftime("%a, %d %B %Y %H:%M", $obj->post_stamp).'</font> '.$prev_message.$next_message.'</td>
</tr>
<tr class="MsgR2"><td class="MsgR2" colspan=2><table border="0" cellspacing="0" cellpadding="0" class="ContentTable">
<tr class="MsgR2">

'.$avatar.'
<td class="msgud">'.$online_indicator.$user_link.$dmsg_user_info.'</td>
<td class="msgud">'.$dmsg_tags.'</td>
<td class="msgot">'.$dmsg_bd_il.$dmsg_im_row.$host_name.$ip_address.'</td>
</tr></table></td>
</tr>
<tr><td colspan="2" class="MsgR3">
'.$poll.$msg_body.$drawmsg_file_attachments.'
'.$modified_message.$signature.$report_to_mod_link.'
</td></tr>
'.$message_toolbar.'
</table></td></tr>';
}function logaction($user_id, $res, $res_id=0, $action=null)
{
	q('INSERT INTO phpgw_fud_action_log (logtime, logaction, user_id, a_res, a_res_id)
		VALUES('.__request_timestamp__.', '.strnull($action).', '.$user_id.', '.strnull($res).', '.(int)$res_id.')');
}

function clear_action_log()
{
	q('DELETE FROM phpgw_fud_action_log');
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
}

	if (isset($_GET['del']) && ($del = (int)$_GET['del'])) {
		if ($usr->users_opt & 1048576 || q_singleval('SELECT mr.id FROM phpgw_fud_msg_report mr INNER JOIN phpgw_fud_msg m ON m.id=mr.msg_id INNER JOIN phpgw_fud_thread t ON t.id=m.thread_id INNER JOIN phpgw_fud_mod mm ON mm.forum_id=t.forum_id AND mm.user_id='._uid.' WHERE mr.id='.$del)) {
			q('DELETE FROM phpgw_fud_msg_report WHERE id='.$del);
			if (db_affected()) {
				logaction(_uid, 'DELREPORT');
			}
		} else {
			std_error('access');
		}
	}



	$r = $query_type('SELECT
			m.*,
			t.thread_opt, t.root_msg_id, t.last_post_id, t.forum_id,
			f.message_threshold, f.name AS frm_name,
			u.id AS user_id, u.alias AS login, u.avatar_loc, u.email, u.posted_msg_count, u.join_date, u.location,
			u.sig, u.custom_status, u.icq, u.jabber, u.affero, u.aim, u.msnm, u.yahoo, u.users_opt, u.last_visit AS time_sec,
			l.name AS level_name, l.level_opt, l.img AS level_img,
			p.max_votes, p.expiry_date, p.creation_date, p.name AS poll_name, p.total_votes,
			mr.id AS report_id, mr.stamp AS report_stamp, mr.reason AS report_reason,
			u2.id AS report_user_id, u2.alias AS report_user_login, u2.last_visit AS time_sec_r,
			m2.subject AS thread_subject,
			pot.id AS cant_vote
		FROM phpgw_fud_msg_report mr
			INNER JOIN phpgw_fud_msg m ON mr.msg_id=m.id
			INNER JOIN phpgw_fud_thread t ON m.thread_id=t.id
			INNER JOIN phpgw_fud_msg m2 ON m2.id=t.root_msg_id
			INNER JOIN phpgw_fud_forum f ON t.forum_id=f.id
			'.($usr->users_opt & 1048576 ? '' : ' INNER JOIN phpgw_fud_mod mm ON mm.forum_id=t.forum_id AND mm.user_id='._uid).'
			LEFT JOIN phpgw_fud_users u ON m.poster_id=u.id
			LEFT JOIN phpgw_fud_users u2 ON mr.user_id=u2.id
			LEFT JOIN phpgw_fud_level l ON u.level_id=l.id
			LEFT JOIN phpgw_fud_poll p ON m.poll_id=p.id
			LEFT JOIN phpgw_fud_poll_opt_track pot ON pot.poll_id=p.id AND pot.user_id='._uid.'
		ORDER BY mr.id');

	$perms = perms_from_obj($r, ($usr->users_opt & 1048576));
	$MOD = 1;
	$reported_message = '';
	$n = 0;
	$_GET['start'] = 0;

	$usr->md = 1;
	while ($obj = db_rowobj($r)) {
		$user_login = $obj->report_user_id ? '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$obj->report_user_id.'&amp;'._rsid.'">'.$obj->report_user_login.'</a>' : ''.$GLOBALS['ANON_NICK'];
		if (empty($prev_thread_id) || $prev_thread_id != $obj->thread_id) {
			$prev_thread_id = $obj->thread_id;

		}
		$message = tmpl_drawmsg($obj, $usr, $perms, false, $n, null);

		$reported_message .= '<tr class="RowStyleC"><td>
<table border=0 cellspacing=0 cellpadding=3 width="100%">
	<tr>
		<td align=left nowrap valign=top>
			<font class="SmallText">
			<b>Riferito da:</b> '.$user_login.'<br />
			<b>Il:</b> '.strftime("%a, %d %B %Y %H:%M", $obj->report_stamp).'
			</font>
		</td>
		<td align=center width="100%" valign=top>
			<font class="SmallText"><b>Motivazione</b><br /></font>
			<table border=1 cellspacing=1 cellpadding=0><tr><td align="left">&nbsp;'.$obj->report_reason.' &nbsp;</td></tr></table>
			
		</td>
		<td nowrap>[<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=reported&amp;'._rsid.'&amp;del='.$obj->report_id.'">Cancella report</a>]</td>
	</tr>
	
	<tr>
		<td colspan=3><font class="SmallText"><b>Nel topic:</b></font> <a href="/egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&amp;goto='.$obj->id.'&amp;'._rsid.'" target="_blank">'.$obj->thread_subject.'</a></td>
	</tr>
</table>
</td></tr>
'.$message;
	}
	unset($r);
	un_register_fps();

	if (!$reported_message) {
		$reported_message = '<tr><td>There are no reported messages.</td></tr>';
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
<table border="0" cellspacing="1" cellpadding="2" class="MsgTable">
<tr><th>Messaggi segnalati</th></tr>
<?php echo $reported_message; ?>
</table>
<?php echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>