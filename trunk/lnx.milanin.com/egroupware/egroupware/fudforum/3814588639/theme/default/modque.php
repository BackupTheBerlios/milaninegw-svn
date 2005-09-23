<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: modque.php.t,v 1.2 2004/06/09 15:14:22 iliaa Exp $
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
			$prev_message = $misc[0] ? '<a href="/egroupware/fudforum/3814588639/index.php?t='.$_GET['t'].'&amp;'._rsid.'&amp;th='.$obj->thread_id.'&amp;mid='.$misc[0].'" class="GenLink"><img src="/egroupware/fudforum/3814588639/theme/default/images/up.png" title="Go to previous message" alt="Go to previous message" width=16 height=11 /></a>' : '';
			$next_message = $misc[1] ? '<a href="/egroupware/fudforum/3814588639/index.php?t='.$_GET['t'].'&amp;'._rsid.'&amp;th='.$obj->thread_id.'&amp;mid='.$misc[1].'" class="GenLink"><img alt="Go to previous message" title="Go to next message" src="/egroupware/fudforum/3814588639/theme/default/images/down.png" width=16 height=11 /></a>' : '';
			$next_page = '';
		} else {
			/* handle previous link */
			if (!$m_num && $obj->id > $obj->root_msg_id) { /* prev link on different page */
				$msg_start = $misc[0] - $misc[1];
				$prev_message = '<a href="/egroupware/fudforum/3814588639/index.php?t='.$_GET['t'].'&amp;'._rsid.'&amp;prevloaded=1&amp;th='.$obj->thread_id.'&amp;start='.$msg_start.reveal_lnk.unignore_tmp.'" class="GenLink"><img src="/egroupware/fudforum/3814588639/theme/default/images/up.png" title="Go to previous message" alt="Go to previous message" width=16 height=11 /></a>';
			} else if ($m_num) { /* inline link, same page */
				$msg_num = $m_num;
				$prev_message = '<a href="#msg_num_'.$msg_num.'" class="GenLink"><img alt="Go to previous message" title="Go to previous message" src="/egroupware/fudforum/3814588639/theme/default/images/up.png" width=16 height=11 /></a>';
			} else {
				$prev_message = '';
			}

			/* handle next link */
			if ($obj->id < $obj->last_post_id) {
				if ($m_num && !($misc[1] - $m_num - 1)) { /* next page link */
					$msg_start = $misc[0] + $misc[1];
					$next_message = '<a href="/egroupware/fudforum/3814588639/index.php?t='.$_GET['t'].'&amp;'._rsid.'&amp;prevloaded=1&amp;th='.$obj->thread_id.'&amp;start='.$msg_start.reveal_lnk.unignore_tmp.'" class="GenLink"><img alt="Go to previous message" title="Go to next message" src="/egroupware/fudforum/3814588639/theme/default/images/down.png" width=16 height=11 /></a>';
					$next_page = '<a href="/egroupware/fudforum/3814588639/index.php?t='.$_GET['t'].'&amp;'._rsid.'&amp;prevloaded=1&amp;th='.$obj->thread_id.'&amp;start='.$msg_start.reveal_lnk.unignore_tmp.'" class="GenLink">Next Page <img src="/egroupware/fudforum/3814588639/theme/default/images/goto.gif" alt="" /></a>';
				} else {
					$msg_num = $m_num + 2;
					$next_message = '<a href="#msg_num_'.$msg_num.'" class="GenLink"><img alt="Go to next message" title="Go to next message" src="/egroupware/fudforum/3814588639/theme/default/images/down.png" width=16 height=11 /></a>';
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
		$user_login_td = $GLOBALS['ANON_NICK'].' is ignored&nbsp;';
	} else {
		$user_login =& $obj->login;
		$user_login_td = 'Post by <a href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;'._rsid.'&amp;id='.$obj->user_id.'" class="GenLink">'.$obj->login.'</a> is ignored&nbsp;';
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
[<a href="/egroupware/fudforum/3814588639/index.php?'.$rev_url.'" class="GenLink">reveal post</a>]&nbsp;
[<a href="/egroupware/fudforum/3814588639/index.php?'.$un_ignore_url.'" class="GenLink">reveal all posts by '.$user_login.'</a>]&nbsp;
[<a href="/egroupware/fudforum/3814588639/index.php?t=ignore_list&amp;del='.$obj->poster_id.'&amp;redr=1&amp;'._rsid.'" class="GenLink">stop ignoring this user</a>]</td>
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
				$online_indicator = (($obj->time_sec + $GLOBALS['LOGEDIN_TIMEOUT'] * 60) > __request_timestamp__) ? '<img src="/egroupware/fudforum/3814588639/theme/default/images/online.gif" alt="'.$obj->login.' is currently online" title="'.$obj->login.' is currently online" />&nbsp;' : '<img src="/egroupware/fudforum/3814588639/theme/default/images/offline.gif" alt="'.$obj->login.'  is currently offline" title="'.$obj->login.'  is currently offline" />&nbsp;';
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
				$location = '<br /><b>Location: </b>'.$location;
			} else {
				$location = '';
			}

			if (_uid && _uid != $obj->user_id) {
				$buddy_link	= !isset($usr->buddy_list[$obj->user_id]) ? '<a href="/egroupware/fudforum/3814588639/index.php?t=buddy_list&amp;add='.$obj->user_id.'&amp;'._rsid.'" class="GenLink">add to buddy list</a><br />' : '<a href="/egroupware/fudforum/3814588639/index.php?t=buddy_list&amp;del='.$obj->user_id.'&amp;redr=1&amp;'._rsid.'" class="GenLink">remove from buddy list</a><br />';
				$ignore_link	= !isset($usr->ignore_list[$obj->user_id]) ? '<a href="/egroupware/fudforum/3814588639/index.php?t=ignore_list&amp;add='.$obj->user_id.'&amp;'._rsid.'" class="GenLink">ignore all posts by this user</a>' : '<a href="/egroupware/fudforum/3814588639/index.php?t=ignore_list&amp;del='.$obj->user_id.'&amp;redr=1&amp;'._rsid.'" class="GenLink">stop ignoring posts by this user</a>';
				$dmsg_bd_il	= $buddy_link.$ignore_link.'<br />';
			} else {
				$dmsg_bd_il = '';
			}

			/* show im buttons if need be */
			if ($b & 16384) {
				$im_icq		= $obj->icq ? '<a href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$obj->poster_id.'&amp;'._rsid.'#icq_msg"><img title="'.$obj->icq.'" src="/egroupware/fudforum/3814588639/theme/default/images/icq.gif" alt="" /></a>' : '';
				$im_aim		= $obj->aim ? '<a href="aim:goim?screenname='.$obj->aim.'&amp;message=Hi.+Are+you+there?" target="_blank"><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/aim.gif" title="'.$obj->aim.'" /></a>' : '';
				$im_yahoo	= $obj->yahoo ? '<a target="_blank" href="http://edit.yahoo.com/config/send_webmesg?.target='.$obj->yahoo.'&amp;.src=pg"><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/yahoo.gif" title="'.$obj->yahoo.'" /></a>' : '';
				$im_msnm	= $obj->msnm ? '<a href="mailto: '.$obj->msnm.'"><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/msnm.gif" title="'.$obj->msnm.'" /></a>' : '';
				$im_jabber	= $obj->jabber ? '<img src="/egroupware/fudforum/3814588639/theme/default/images/jabber.gif" title="'.$obj->jabber.'" alt="" />' : '';
				if ($o2 & 2048) {
					$im_affero = $obj->affero ? '<a href="http://svcs.affero.net/rm.php?r='.$obj->affero.'&amp;ll='.$obj->forum_id.'.'.$GLOBALS['affero_domain'].'&amp;lp='.$obj->forum_id.'.'.urlencode($GLOBALS['affero_domain']['host']).'&amp;ls='.urlencode($obj->subject).'" target=_blank><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/affero_reg.gif" /></a>' : '<a href="http://svcs.affero.net/rm.php?m='.urlencode($obj->email).'&amp;ll='.$obj->forum_id.'.'.$GLOBALS['affero_domain'].'&amp;lp='.$obj->forum_id.'.'.urlencode($GLOBALS['affero_domain']['host']).'&amp;ls='.urlencode($obj->subject).'" target=_blank><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/affero_noreg.gif" /></a>';
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
<br /><div align="center">[<a href="/egroupware/fudforum/3814588639/index.php?'.$rev_url.'" class="GenLink">Show the rest of the message</a>]</div>';
	} else if ($obj->length) {
		$msg_body = read_msg_body($obj->foff, $obj->length, $obj->file_id);
		$msg_body = '<font class="MsgBodyText">'.$msg_body.'</font>';
	} else {
		$msg_body = 'No Message Body';
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
				$poll_data .= '<tr class="'.alt_var('msg_poll_alt_clr','RowStyleB','RowStyleA').'"><td>'.$i.'.</td><td>'.$v[0].'</td><td><img src="/egroupware/fudforum/3814588639/theme/default/images/poll_pix.gif" alt="" height="10" width="'.$length.'" /> '.$v[1].' / '.$length.'%</td></tr>';
			} else {
				$poll_data .= '<tr class="'.alt_var('msg_poll_alt_clr','RowStyleB','RowStyleA').'"><td>'.$i.'.</td><td colspan=2><input type="radio" name="poll_opt" value="'.$k.'">&nbsp;&nbsp;'.$v[0].'</td></tr>';
			}
		}

		if (!$show_res) {
			$view_poll_results_button = $obj->total_votes ? '<input type="submit" class="button" name="pl_res" value="View Results">' : '';
			$poll_buttons = '<tr class="'.alt_var('msg_poll_alt_clr','RowStyleB','RowStyleA').'"><td colspan=3 align="right"><input type="submit" class="button" name="pl_vote" value="Vote">&nbsp;'.$view_poll_results_button.'</td></tr>';
			$poll = '<p>
<form action="/egroupware/fudforum/3814588639/index.php?'.$_SERVER['QUERY_STRING'].'#msg_'.$obj->id.'" method="post">'._hs.'
<table border=0 cellspacing=1 cellpadding=2 class="PollTable">
<tr><th nowrap colspan=3>'.$obj->poll_name.'<img src="blank.gif" alt="" height=1 width=10 /><font size="-1">[ '.$obj->total_votes.' vote(s) ]</font></th></tr>
'.$poll_data.'
'.$poll_buttons.'
</table><input type="hidden" name="pl_view" value="'.$obj->poll_id.'"></form><p>';
		} else {
			$poll = '<p><table border=0 cellspacing=1 cellpadding=2 class="PollTable">
<tr><th nowrap colspan=3>'.$obj->poll_name.'<img src="blank.gif" alt="" height=1 width=10 /><font size="-1">[ '.$obj->total_votes.' vote(s) ]</font></th></tr>
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
<font class="SmallText">(Size: '.$sz.', Downloaded '.$v[3].' time(s))</font></td></tr>';
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
			$modified_message = '<p>[Updated on: '.strftime("%a, %d %B %Y %H:%M", $obj->update_stamp).'] by Moderator';
		} else if ($obj->updated_by == $obj->poster_id && $o1 & 33554432) {
			$modified_message = '<p>[Updated on: '.strftime("%a, %d %B %Y %H:%M", $obj->update_stamp).']';
		} else {
			$modified_message = '';
		}
	} else {
		$modified_message = '';
	}

	$rpl = '';
	if (!$hide_controls) {
		$ip_address = ($b & 1048576 || $usr->md || $o1 & 134217728) ? '<b>IP:</b> <a href="http://www.nic.com/cgi-bin/whois.cgi?query='.$obj->ip_addr.'" target="_blank">'.$obj->ip_addr.'</a>' : '';
		$host_name = ($obj->host_name && $o1 & 268435456) ? '<b>From:</b> '.$obj->host_name.'<br />' : '';
		$msg_icon = !$obj->icon ? '' : '<img src="images/message_icons/'.$obj->icon.'" alt="'.$obj->icon.'" />&nbsp;&nbsp;';
		$signature = ($obj->sig && $o1 & 32768 && $obj->msg_opt & 1 && $b & 4096) ? '<p><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br />'.$obj->sig : '';

		$report_to_mod_link = '<div align="right"><font class="SmallText">[<a class="SmallText" href="/egroupware/fudforum/3814588639/index.php?t=report&amp;msg_id='.$obj->id.'&amp;'._rsid.'">Report message to a moderator</a>]</font></div>';

		if ($obj->reply_to && $obj->reply_to != $obj->id && $o2 & 536870912) {
			if ($_GET['t'] != 'tree' && $_GET['t'] != 'msg') {
				$lnk = d_thread_view;
			} else {
				$lnk =& $_GET['t'];
			}
			$rpl = '<font class="small"> [ <a href="/egroupware/fudforum/3814588639/index.php?t='.$lnk.'&amp;'._rsid.'&amp;th='.$obj->thread_id.'&amp;goto='.$obj->reply_to.'" class="small">is a reply to message #'.$obj->reply_to.'</a> ]</font>';
		}

		if ($obj->user_id) {
			$user_profile = '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$obj->user_id.'&amp;'._rsid.'"><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/msg_about.gif" /></a>';
			$email_link = ($o1 & 4194304 && $a & 16) ? '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=email&amp;toi='.$obj->user_id.'&amp;'._rsid.'"><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/msg_email.gif" /></a>' : '';
			$private_msg_link = $o1 & 1024 ? '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=ppost&amp;toi='.$obj->user_id.'&amp;'._rsid.'"><img alt="Send a private message to this user" title="Send a private message to this user" src="/egroupware/fudforum/3814588639/theme/default/images/msg_pm.gif" /></a>' : '';
			$dmsg_user_info = '<br /><b>Posts:</b> '.$obj->posted_msg_count.'<br />
<b>Registered:</b> '.strftime("%B %Y", $obj->join_date).'
'.$location;
		} else {
			$user_profile = $email_link = $private_msg_link = '';
			$dmsg_user_info = ($host_name || $ip_address) ? '' : '';
		}

		/* little trick, this variable will only be avaliable if we have a next link leading to another page */
		if (isset($next_page)) {
			$next_page = '&nbsp;';
		}

		$delete_link = $perms & 32 ? '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=mmod&amp;del='.$obj->id.'&amp;'._rsid.'"><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/msg_delete.gif" /></a>&nbsp;' : '';

		if ($perms & 16 || (_uid == $obj->poster_id && (!$GLOBALS['EDIT_TIME_LIMIT'] || __request_timestamp__ - $obj->post_stamp < $GLOBALS['EDIT_TIME_LIMIT'] * 60))) {
			$edit_link = '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=post&amp;msg_id='.$obj->id.'&amp;'._rsid.'"><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/msg_edit.gif" /></a>&nbsp;&nbsp;&nbsp;&nbsp;';
		} else {
			$edit_link = '';
		}

		if (!($obj->thread_opt & 1) || $perms & 4096) {
			$reply_link = '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=post&amp;reply_to='.$obj->id.'&amp;'._rsid.'"><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/msg_reply.gif" /></a>&nbsp;';
			$quote_link = '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=post&amp;reply_to='.$obj->id.'&amp;quote=true&amp;'._rsid.'"><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/msg_quote.gif" /></a>';
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
}function msg_get($id)
{
	if (($r = db_sab('SELECT * FROM phpgw_fud_msg WHERE id='.$id))) {
		$r->body = read_msg_body($r->foff, $r->length, $r->file_id);
		un_register_fps();
		return $r;
	}
	error_dialog('Invalid Message', 'The message you are trying to view does not exist.');
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
}class fud_msg
{
	var $id, $thread_id, $poster_id, $reply_to, $ip_addr, $host_name, $post_stamp, $subject, $attach_cnt, $poll_id,
	    $update_stamp, $icon, $apr, $updated_by, $login, $length, $foff, $file_id, $msg_opt,
	    $file_id_preview, $length_preview, $offset_preview, $body, $mlist_msg_id;
}

class fud_msg_edit extends fud_msg
{
	function add_reply($reply_to, $th_id=null, $perm, $autoapprove=true)
	{
		if ($reply_to) {
			$this->reply_to = $reply_to;
			$fd = db_saq('SELECT t.forum_id, f.message_threshold, f.forum_opt FROM phpgw_fud_msg m INNER JOIN phpgw_fud_thread t ON m.thread_id=t.id INNER JOIN phpgw_fud_forum f ON f.id=t.forum_id WHERE m.id='.$reply_to);
		} else {
			$fd = db_saq('SELECT t.forum_id, f.message_threshold, f.forum_opt FROM phpgw_fud_thread t INNER JOIN phpgw_fud_forum f ON f.id=t.forum_id WHERE t.id='.$th_id);
		}

		return $this->add($fd[0], $fd[1], $fd[2], $perm, $autoapprove);
	}

	function add($forum_id, $message_threshold, $forum_opt, $perm, $autoapprove=true)
	{
		if (!$this->post_stamp) {
			$this->post_stamp = __request_timestamp__;
		}

		if (!isset($this->ip_addr)) {
			$this->ip_addr = get_ip();
		}
		$this->host_name = $GLOBALS['FUD_OPT_1'] & 268435456 ? "'".addslashes(get_host($this->ip_addr))."'" : 'NULL';
		$this->thread_id = isset($this->thread_id) ? $this->thread_id : 0;
		$this->reply_to = isset($this->reply_to) ? $this->reply_to : 0;

		$file_id = write_body($this->body, $length, $offset);

		/* determine if preview needs building */
		if ($message_threshold && $message_threshold < strlen($this->body)) {
			$thres_body = trim_html($this->body, $message_threshold);
			$file_id_preview = write_body($thres_body, $length_preview, $offset_preview);
		} else {
			$file_id_preview = $offset_preview = $length_preview = 0;
		}

		poll_cache_rebuild($this->poll_id, $poll_cache);
		$poll_cache = ($poll_cache ? @serialize($poll_cache) : null);

		$this->id = db_qid("INSERT INTO phpgw_fud_msg (
			thread_id,
			poster_id,
			reply_to,
			ip_addr,
			host_name,
			post_stamp,
			subject,
			attach_cnt,
			poll_id,
			icon,
			msg_opt,
			file_id,
			foff,
			length,
			file_id_preview,
			offset_preview,
			length_preview,
			mlist_msg_id,
			poll_cache
		) VALUES(
			".$this->thread_id.",
			".$this->poster_id.",
			".(int)$this->reply_to.",
			'".$this->ip_addr."',
			".$this->host_name.",
			".$this->post_stamp.",
			".strnull(addslashes($this->subject)).",
			".(int)$this->attach_cnt.",
			".(int)$this->poll_id.",
			".strnull(addslashes($this->icon)).",
			".$this->msg_opt.",
			".$file_id.",
			".(int)$offset.",
			".(int)$length.",
			".$file_id_preview.",
			".$offset_preview.",
			".$length_preview.",
			".strnull($this->mlist_msg_id).",
			".strnull(addslashes($poll_cache))."
		)");

		$thread_opt = (int) ($perm & 4096 && isset($_POST['thr_locked']));

		if (!$this->thread_id) { /* new thread */
			if ($perm & 64 && isset($_POST['thr_ordertype'], $_POST['thr_orderexpiry'])) {
				if ((int)$_POST['thr_ordertype']) {
					$thread_opt |= (int) $_POST['thr_ordertype'];
					$thr_orderexpiry = (int) $_POST['thr_orderexpiry'];
				}
			}

			$this->thread_id = th_add($this->id, $forum_id, $this->post_stamp, $thread_opt, (isset($thr_orderexpiry) ? $thr_orderexpiry : 0));

			q('UPDATE phpgw_fud_msg SET thread_id='.$this->thread_id.' WHERE id='.$this->id);
		} else {
			th_lock($this->thread_id, $thread_opt & 1);
		}

		if ($autoapprove && $forum_opt & 2) {
			$this->approve($this->id, true);
		}

		return $this->id;
	}

	function sync($id, $frm_id, $message_threshold, $perm)
	{
		if (!db_locked()) {
			db_lock('phpgw_fud_poll_opt WRITE, phpgw_fud_forum WRITE, phpgw_fud_msg WRITE, phpgw_fud_thread WRITE, phpgw_fud_thread_view WRITE');
			$ll=1;
		}
		$file_id = write_body($this->body, $length, $offset);

		/* determine if preview needs building */
		if ($message_threshold && $message_threshold < strlen($this->body)) {
			$thres_body = trim_html($this->body, $message_threshold);
			$file_id_preview = write_body($thres_body, $length_preview, $offset_preview);
		} else {
			$file_id_preview = $offset_preview = $length_preview = 0;
		}

		poll_cache_rebuild($this->poll_id, $poll_cache);
		$poll_cache = ($poll_cache ? @serialize($poll_cache) : null);

		q("UPDATE phpgw_fud_msg SET
			file_id=".$file_id.",
			foff=".(int)$offset.",
			length=".(int)$length.",
			mlist_msg_id=".strnull(addslashes($this->mlist_msg_id)).",
			file_id_preview=".$file_id_preview.",
			offset_preview=".$offset_preview.",
			length_preview=".$length_preview.",
			updated_by=".$id.",
			msg_opt=".$this->msg_opt.",
			attach_cnt=".(int)$this->attach_cnt.",
			poll_id=".(int)$this->poll_id.",
			update_stamp=".__request_timestamp__.",
			icon=".strnull(addslashes($this->icon))." ,
			poll_cache=".strnull(addslashes($poll_cache)).",
			subject=".strnull(addslashes($this->subject))."
		WHERE id=".$this->id);

		/* determine wether or not we should deal with locked & sticky stuff
		 * current approach may seem a little redundant, but for (most) users who
		 * do not have access to locking & sticky this eliminated a query.
		 */
		$th_data = db_saq('SELECT orderexpiry, thread_opt, root_msg_id FROM phpgw_fud_thread WHERE id='.$this->thread_id);
		$locked = (int) isset($_POST['thr_locked']);
		if (isset($_POST['thr_ordertype'], $_POST['thr_orderexpiry']) || (($th_data[1] ^ $locked) & 1)) {
			$thread_opt = (int) $th_data[1];
			$orderexpiry = isset($_POST['thr_orderexpiry']) ? (int) $_POST['thr_orderexpiry'] : 0;

			/* confirm that user has ability to change lock status of the thread */
			if ($perm & 4096) {
				if ($locked && !($thread_opt & $locked)) {
					$thread_opt |= 1;
				} else if (!$locked && $thread_opt & 1) {
					$thread_opt &= ~1;
				}
			}

			/* confirm that user has ability to change sticky status of the thread */
			if ($th_data[2] == $this->id && isset($_POST['thr_ordertype'], $_POST['thr_orderexpiry']) && $perm & 64) {
				if (!$_POST['thr_ordertype'] && $thread_opt>1) {
					$orderexpiry = 0;
					$thread_opt &= ~6;
				} else if ($thread_opt < 2 && (int) $_POST['thr_ordertype']) {
					$thread_opt |= $_POST['thr_ordertype'];
				} else if (!($thread_opt & (int) $_POST['thr_ordertype'])) {
					$thread_opt = $_POST['thr_ordertype'] | ($thread_opt & 1);
				}
			}

			/* Determine if any work needs to be done */
			if ($thread_opt != $th_data[1] || $orderexpiry != $th_data[0]) {
				q("UPDATE phpgw_fud_thread SET thread_opt=".$thread_opt.", orderexpiry=".$orderexpiry." WHERE id=".$this->thread_id);
				/* Avoid rebuilding the forum view whenever possible, since it's a rather slow process
				 * Only rebuild if expiry time has changed or message gained/lost sticky status
				 */
				$diff = $thread_opt ^ $th_data[1];
				if (($diff > 1 && !($diff & 6)) || $orderexpiry != $th_data[0]) {
					rebuild_forum_view($frm_id);
				}
			}
		}

		if (isset($ll)) {
			db_unlock();
		}

		if ($GLOBALS['FUD_OPT_1'] & 16777216) {
			delete_msg_index($this->id);
			index_text((preg_match('!^Re: !i', $this->subject) ? '': $this->subject), $this->body, $this->id);
		}
	}

	function delete($rebuild_view=true, $mid=0, $th_rm=0)
	{
		if (!$mid) {
			$mid = $this->id;
		}

		if (!db_locked()) {
			db_lock('phpgw_fud_thr_exchange WRITE, phpgw_fud_thread_view WRITE, phpgw_fud_level WRITE, phpgw_fud_forum WRITE, phpgw_fud_forum_read WRITE, phpgw_fud_thread WRITE, phpgw_fud_msg WRITE, phpgw_fud_attach WRITE, phpgw_fud_poll WRITE, phpgw_fud_poll_opt WRITE, phpgw_fud_poll_opt_track WRITE, phpgw_fud_users WRITE, phpgw_fud_thread_notify WRITE, phpgw_fud_msg_report WRITE, phpgw_fud_thread_rate_track WRITE');
			$ll = 1;
		}

		if (!($del = db_sab('SELECT
				phpgw_fud_msg.id, phpgw_fud_msg.attach_cnt, phpgw_fud_msg.poll_id, phpgw_fud_msg.thread_id, phpgw_fud_msg.reply_to, phpgw_fud_msg.apr, phpgw_fud_msg.poster_id,
				phpgw_fud_thread.replies, phpgw_fud_thread.root_msg_id AS root_msg_id, phpgw_fud_thread.last_post_id AS thread_lip, phpgw_fud_thread.forum_id,
				phpgw_fud_forum.last_post_id AS forum_lip FROM phpgw_fud_msg LEFT JOIN phpgw_fud_thread ON phpgw_fud_msg.thread_id=phpgw_fud_thread.id LEFT JOIN phpgw_fud_forum ON phpgw_fud_thread.forum_id=phpgw_fud_forum.id WHERE phpgw_fud_msg.id='.$mid))) {
			if (isset($ll)) {
				db_unlock();
			}
			return;
		}

		/* attachments */
		if ($del->attach_cnt) {
			$res = q('SELECT location FROM phpgw_fud_attach WHERE message_id='.$mid." AND attach_opt=0");
			while ($loc = db_rowarr($res)) {
				@unlink($loc[0]);
			}
			unset($res);
			q('DELETE FROM phpgw_fud_attach WHERE message_id='.$mid." AND attach_opt=0");
		}

		q('DELETE FROM phpgw_fud_msg_report WHERE msg_id='.$mid);

		if ($del->poll_id) {
			poll_delete($del->poll_id);
		}

		/* check if thread */
		if ($del->root_msg_id == $del->id) {
			$th_rm = 1;
			/* delete all messages in the thread if there is more then 1 message */
			if ($del->replies) {
				$rmsg = q('SELECT id FROM phpgw_fud_msg WHERE thread_id='.$del->thread_id.' AND id != '.$del->id);
				while ($dim = db_rowarr($rmsg)) {
					fud_msg_edit::delete(false, $dim[0], 1);
				}
				unset($rmsg);
			}

			q('DELETE FROM phpgw_fud_thread_notify WHERE thread_id='.$del->thread_id);
			q('DELETE FROM phpgw_fud_thread WHERE id='.$del->thread_id);
			q('DELETE FROM phpgw_fud_thread_rate_track WHERE thread_id='.$del->thread_id);
			q('DELETE FROM phpgw_fud_thr_exchange WHERE th='.$del->thread_id);

			if ($del->apr) {
				/* we need to determine the last post id for the forum, it can be null */
				$lpi = (int) q_singleval('SELECT phpgw_fud_thread.last_post_id FROM phpgw_fud_thread INNER JOIN phpgw_fud_msg ON phpgw_fud_thread.last_post_id=phpgw_fud_msg.id AND phpgw_fud_msg.apr=1 WHERE forum_id='.$del->forum_id.' AND moved_to=0 ORDER BY phpgw_fud_msg.post_stamp DESC LIMIT 1');
				q('UPDATE phpgw_fud_forum SET last_post_id='.$lpi.', thread_count=thread_count-1, post_count=post_count-'.$del->replies.'-1 WHERE id='.$del->forum_id);
			}
		} else if (!$th_rm  && $del->apr) {
			q('UPDATE phpgw_fud_msg SET reply_to='.$del->reply_to.' WHERE thread_id='.$del->thread_id.' AND reply_to='.$mid);

			/* check if the message is the last in thread */
			if ($del->thread_lip == $del->id) {
				list($lpi, $lpd) = db_saq('SELECT id, post_stamp FROM phpgw_fud_msg WHERE thread_id='.$del->thread_id.' AND apr=1 AND id!='.$del->id.' ORDER BY post_stamp DESC LIMIT 1');
				q('UPDATE phpgw_fud_thread SET last_post_id='.$lpi.', last_post_date='.$lpd.', replies=replies-1 WHERE id='.$del->thread_id);
			} else {
				q('UPDATE phpgw_fud_thread SET replies=replies-1 WHERE id='.$del->thread_id);
			}

			/* check if the message is the last in the forum */
			if ($del->forum_lip == $del->id) {
				$lp = db_saq('SELECT phpgw_fud_thread.last_post_id, phpgw_fud_thread.last_post_date FROM phpgw_fud_thread_view INNER JOIN phpgw_fud_thread ON phpgw_fud_thread_view.forum_id=phpgw_fud_thread.forum_id AND phpgw_fud_thread_view.thread_id=phpgw_fud_thread.id WHERE phpgw_fud_thread_view.forum_id='.$del->forum_id.' AND phpgw_fud_thread_view.page=1 AND phpgw_fud_thread.moved_to=0 ORDER BY phpgw_fud_thread.last_post_date DESC LIMIT 1');
				if (!isset($lpd) || $lp[1] > $lpd) {
					$lpi = $lp[0];
				}
				q('UPDATE phpgw_fud_forum SET post_count=post_count-1, last_post_id='.$lpi.' WHERE id='.$del->forum_id);
			} else {
				q('UPDATE phpgw_fud_forum SET post_count=post_count-1 WHERE id='.$del->forum_id);
			}
		}

		q('DELETE FROM phpgw_fud_msg WHERE id='.$mid);

		if ($del->apr) {
			if ($del->poster_id) {
				user_set_post_count($del->poster_id);
			}

			if ($rebuild_view) {
				rebuild_forum_view($del->forum_id);

				/* needed for moved thread pointers */
				$r = q('SELECT forum_id, id FROM phpgw_fud_thread WHERE root_msg_id='.$del->root_msg_id);
				while (($res = db_rowarr($r))) {
					if ($th_rm) {
						q('DELETE FROM phpgw_fud_thread WHERE id='.$res[1]);
					}
					rebuild_forum_view($res[0]);
				}
				unset($r);
			}
		}

		if (isset($ll)) {
			db_unlock();
		}
	}

	function approve($id, $unlock_safe=false)
	{
		/* fetch info about the message, poll (if one exists), thread & forum */
		$mtf = db_sab('SELECT
					m.id, m.poster_id, m.apr, m.subject, m.foff, m.length, m.file_id, m.thread_id, m.poll_id, m.attach_cnt,
					m.post_stamp, m.reply_to, m.mlist_msg_id,
					t.forum_id, t.last_post_id, t.root_msg_id, t.last_post_date,
					m2.post_stamp AS frm_last_post_date,
					f.name AS frm_name,
					u.alias, u.email, u.sig,
					n.id AS nntp_id, ml.id AS mlist_id
				FROM phpgw_fud_msg m
				INNER JOIN phpgw_fud_thread t ON m.thread_id=t.id
				INNER JOIN phpgw_fud_forum f ON t.forum_id=f.id
				LEFT JOIN phpgw_fud_msg m2 ON f.last_post_id=m2.id
				LEFT JOIN phpgw_fud_users u ON m.poster_id=u.id
				LEFT JOIN phpgw_fud_mlist ml ON ml.forum_id=f.id
				LEFT JOIN phpgw_fud_nntp n ON n.forum_id=f.id
				WHERE m.id='.$id.' AND m.apr=0');

		/* nothing to do or bad message id */
		if (!$mtf) {
			return;
		}

		if ($mtf->alias) {
			reverse_fmt($mtf->alias);
		} else {
			$mtf->alias = $GLOBALS['ANON_NICK'];
		}

		if (!db_locked()) {
			db_lock('phpgw_fud_thread_view WRITE, phpgw_fud_level WRITE, phpgw_fud_users WRITE, phpgw_fud_forum WRITE, phpgw_fud_thread WRITE, phpgw_fud_msg WRITE');
			$ll = 1;
		}

		q("UPDATE phpgw_fud_msg SET apr=1 WHERE id=".$mtf->id);

		if ($mtf->poster_id) {
			user_set_post_count($mtf->poster_id);
		}

		$last_post_id = $mtf->post_stamp > $mtf->frm_last_post_date ? $mtf->id : 0;

		if ($mtf->root_msg_id == $mtf->id) {	/* new thread */
			rebuild_forum_view($mtf->forum_id);
			$threads = 1;
		} else {				/* reply to thread */
			if ($mtf->post_stamp > $mtf->last_post_date) {
				th_inc_post_count($mtf->thread_id, 1, $mtf->id, $mtf->post_stamp);
			} else {
				th_inc_post_count($mtf->thread_id, 1);
			}
			rebuild_forum_view($mtf->forum_id, q_singleval('SELECT page FROM phpgw_fud_thread_view WHERE forum_id='.$mtf->forum_id.' AND thread_id='.$mtf->thread_id));
			$threads = 0;
		}

		/* update forum thread & post count as well as last_post_id field */
		frm_updt_counts($mtf->forum_id, 1, $threads, $last_post_id);

		if ($unlock_safe || isset($ll)) {
			db_unlock();
		}

		if ($mtf->poll_id) {
			poll_activate($mtf->poll_id, $mtf->forum_id);
		}

		$mtf->body = read_msg_body($mtf->foff, $mtf->length, $mtf->file_id);

		if ($GLOBALS['FUD_OPT_1'] & 16777216) {
			index_text((preg_match('!Re: !i', $mtf->subject) ? '': $mtf->subject), $mtf->body, $mtf->id);
		}

		/* handle notifications */
		if ($mtf->root_msg_id == $mtf->id) {
			if (empty($mtf->frm_last_post_date)) {
				$mtf->frm_last_post_date = 0;
			}

			/* send new thread notifications to forum subscribers */
			$c = uq('SELECT u.email, u.icq, u.users_opt
					FROM phpgw_fud_forum_notify fn
					INNER JOIN phpgw_fud_users u ON fn.user_id=u.id
					LEFT JOIN phpgw_fud_forum_read r ON r.forum_id=fn.forum_id AND r.user_id=fn.user_id
					INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id=2147483647 AND g1.resource_id='.$mtf->forum_id.'
					LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id=fn.user_id AND g2.resource_id='.$mtf->forum_id.'
				WHERE
					fn.forum_id='.$mtf->forum_id.' AND fn.user_id!='.(int)$mtf->poster_id.'
					AND (CASE WHEN (r.last_view IS NULL AND (u.last_read=0 OR u.last_read >= '.$mtf->frm_last_post_date.')) OR r.last_view > '.$mtf->frm_last_post_date.' THEN 1 ELSE 0 END)=1
					AND ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 2) > 0');
			$notify_type = 'frm';
		} else {
			/* send new reply notifications to thread subscribers */
			$c = uq('SELECT u.email, u.icq, u.users_opt, r.msg_id, u.id
					FROM phpgw_fud_thread_notify tn
					INNER JOIN phpgw_fud_users u ON tn.user_id=u.id
					LEFT JOIN phpgw_fud_read r ON r.thread_id=tn.thread_id AND r.user_id=tn.user_id
					INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id=2147483647 AND g1.resource_id='.$mtf->forum_id.'
					LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id=tn.user_id AND g2.resource_id='.$mtf->forum_id.'
				WHERE
					tn.thread_id='.$mtf->thread_id.' AND tn.user_id!='.(int)$mtf->poster_id.'
					AND (r.msg_id='.$mtf->last_post_id.' OR (r.msg_id IS NULL AND '.$mtf->post_stamp.' > u.last_read))
					AND ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 2) > 0');
			$notify_type = 'thr';
		}
		while ($r = db_rowarr($c)) {
			if ($r[2] & 16) {
				$to['EMAIL'] = $r[0];
			} else {
				$to['ICQ'] = $r[1].'@pager.icq.com';
			}
			if (isset($r[4]) && is_null($r[3])) {
				$tl[] = $r[4];
			}
		}
		unset($c);
		if (isset($tl)) {
			/* this allows us to mark the message we are sending notification about as read, so that we do not re-notify the user
			 * until this message is read.
			 */
			q('INSERT INTO phpgw_fud_read (thread_id, msg_id, last_view, user_id) SELECT '.$mtf->thread_id.', 0, 0, id FROM phpgw_fud_users WHERE id IN('.implode(',', $tl).')');
		}
		if (isset($to)) {
			send_notifications($to, $mtf->id, $mtf->subject, $mtf->alias, $notify_type, ($notify_type == 'thr' ? $mtf->thread_id : $mtf->forum_id), $mtf->frm_name, $mtf->forum_id);
		}

		// Handle Mailing List and/or Newsgroup syncronization.
		if (($mtf->nntp_id || $mtf->mlist_id) && !$mtf->mlist_msg_id) {
			fud_use('email_msg_format.inc', true);

			reverse_fmt($mtf->alias);
			$from = $mtf->poster_id ? $mtf->alias.' <'.$mtf->email.'>' : $GLOBALS['ANON_NICK'].' <'.$GLOBALS['NOTIFY_FROM'].'>';
			$body = $mtf->body . (($mtf->msg_opt & 1 && $mtf->sig) ? "\n--\n" . $mtf->sig : '');
			plain_text($body);
			plain_text($subject);

			if ($mtf->reply_to) {
				$replyto_id = q_singleval('SELECT mlist_msg_id FROM phpgw_fud_msg WHERE id='.$mtf->reply_to);
			} else {
				$replyto_id = 0;
			}

			if ($mtf->attach_cnt) {
				$r = uq("SELECT a.id, a.original_name,
						CASE WHEN m.mime_hdr IS NULL THEN 'application/octet-stream' ELSE m.mime_hdr END
						FROM phpgw_fud_attach a
						LEFT JOIN phpgw_fud_mime m ON a.mime_type=m.id
						WHERE a.message_id=".$mtf->id." AND a.attach_opt=0");
				while ($ent = db_rowarr($r)) {
					$attach[$ent[1]] = file_get_contents($GLOBALS['FILE_STORE'].$ent[0].'.atch');
					if ($mtf->mlist_id) {
						$attach_mime[$ent[1]] = $ent[2];
					}
				}
			} else {
				$attach_mime = $attach = null;
			}

			if ($mtf->nntp_id) {
				fud_use('nntp.inc', true);

				$nntp_adm = db_sab('SELECT * FROM phpgw_fud_nntp WHERE id='.$mtf->nntp_id);
				$nntp = new fud_nntp;

				$nntp->server = $nntp_adm->server;
				$nntp->newsgroup = $nntp_adm->newsgroup;
				$nntp->port = $nntp_adm->port;
				$nntp->timeout = $nntp_adm->timeout;
				$nntp->nntp_opt = $nntp_adm->nntp_opt;
				$nntp->login = $nntp_adm->login;
				$nntp->pass = $nntp_adm->pass;

				define('sql_p', 'phpgw_fud_');

				$lock = $nntp->get_lock();
				$nntp->post_message($mtf->subject, $body, $from, $mtf->id, $replyto_id, $attach);
				$nntp->close_connection();
				$nntp->release_lock($lock);
			} else {
				fud_use('mlist_post.inc', true);
				$GLOBALS['CHARSET'] = 'ISO-8859-15';
				$r = db_saq('SELECT name, additional_headers FROM phpgw_fud_mlist WHERE id='.$mtf->mlist_id);
				mail_list_post($r[0], $from, $mtf->subject, $body, $mtf->id, $replyto_id, $attach, $attach_mime, $r[1]);
			}
		}
	}
}

function write_body($data, &$len, &$offset)
{
	$MAX_FILE_SIZE = 2147483647;

	$len = strlen($data);
	$i = 1;
	while ($i < 100) {
		$fp = fopen($GLOBALS['MSG_STORE_DIR'].'msg_'.$i, 'ab');
		fseek($fp, 0, SEEK_END);
		if (!($off = ftell($fp))) {
			$off = __ffilesize($fp);
		}
		if (!$off || ($off + $len) < $MAX_FILE_SIZE) {
			break;
		}
		fclose($fp);
		$i++;
	}

	if (fwrite($fp, $data) !== $len) {
		exit("FATAL ERROR: system has ran out of disk space<br>\n");
	}
	fclose($fp);

	if (!$off) {
		@chmod('msg_'.$i, ($GLOBALS['FUD_OPT_2'] & 8388608 ? 0600 : 0666));
	}
	$offset = $off;

	return $i;
}

function trim_html($str, $maxlen)
{
	$n = strlen($str);
	$ln = 0;
	for ($i = 0; $i < $n; $i++) {
		if ($str[$i] != '<') {
			$ln++;
			if ($ln > $maxlen) {
				break;
			}
			continue;
		}

		if (($p = strpos($str, '>', $i)) === false) {
			break;
		}

		for ($k = $i; $k < $p; $k++) {
			switch ($str[$k]) {
				case ' ':
				case "\r":
				case "\n":
				case "\t":
				case ">":
					break 2;
			}
		}

		if ($str[$i+1] == '/') {
			$tagname = strtolower(substr($str, $i+2, $k-$i-2));
			if (@end($tagindex[$tagname])) {
				$k = key($tagindex[$tagname]);
				unset($tagindex[$tagname][$k], $tree[$k]);
			}
		} else {
			$tagname = strtolower(substr($str, $i+1, $k-$i-1));
			switch ($tagname) {
				case 'br':
				case 'img':
				case 'meta':
					break;
				default:
					$tree[] = $tagname;
					end($tree);
					$tagindex[$tagname][key($tree)] = 1;
			}
		}
		$i = $p;
	}

	$data = substr($str, 0, $i);
	if (isset($tree) && is_array($tree)) {
		$tree = array_reverse($tree);
		foreach ($tree as $v) {
			$data .= '</'.$v.'>';
		}
	}

	return $data;
}

function make_email_message(&$body, &$obj, $iemail_unsub)
{
	$TITLE_EXTRA = $iemail_poll = $iemail_attach = '';
	if ($obj->poll_cache) {
		$pl = @unserialize($obj->poll_cache);
		if (is_array($pl) && count($pl)) {
			foreach ($pl as $k => $v) {
				$length = ($v[1] && $obj->total_votes) ? round($v[1] / $obj->total_votes * 100) : 0;
				$iemail_poll .= '<tr class="'.alt_var('msg_poll_alt_clr','RowStyleB','RowStyleA').'"><td>'.$i.'.</td><td>'.$v[0].'</td><td><img src="/egroupware/fudforum/3814588639/theme/default/images/poll_pix.gif" alt="" height="10" width="'.$length.'" /> '.$v[1].' / '.$length.'%</td></tr>';
			}
			$iemail_poll = '<table border=0 cellspacing=1 cellpadding=2 class="PollTable">
<tr><th nowrap colspan=3>'.$obj->poll_name.'<img src="blank.gif" alt="" height=1 width=10 /><font size="-1">[ '.$obj->total_votes.' vote(s) ]</font></th></tr>
'.$iemail_poll.'
</table><p>';
		}
	}
	if ($obj->attach_cnt && $obj->attach_cache) {
		$atch = @unserialize($obj->attach_cache);
		if (is_array($atch) && count($atch)) {
			foreach ($atch as $v) {
				$sz = $v[2] / 1024;
				$sz = $sz < 1000 ? number_format($sz, 2).'KB' : number_format($sz/1024, 2).'MB';
				$iemail_attach .= '<tr>
<td valign="middle"><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=getfile&amp;id='.$v[0].'&amp;'._rsid.'"><img alt="" src="'.$GLOBALS['WWW_ROOT'].'images/mime/'.$v[4].'" /></a></td>
<td><font class="GenText"><b>Attachment:</b></font> <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=getfile&amp;id='.$v[0].'&amp;'._rsid.'">'.$v[1].'</a><br />
<font class="SmallText">(Size: '.$sz.', Downloaded '.$v[3].' time(s))</font></td></tr>';
			}
			$iemail_attach = '<p>
<table border=0 cellspacing=0 cellpadding=2>
'.$iemail_attach.'
</table>';
		}
	}

	return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-15">
<title>'.$GLOBALS['FORUM_TITLE'].$TITLE_EXTRA.'</title>
<script language="JavaScript" src="'.$GLOBALS['WWW_ROOT'].'/lib.js" type="text/javascript"></script>
<link rel="StyleSheet" href="/egroupware/fudforum/3814588639/theme/default/forum.css" type="text/css">
</head>
<body>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr class="RowStyleB">
	<td width="33%"><b>Subject:</b> '.$obj->subject.'</td>
	<td width="33%"><b>Author:</b> '.$obj->alias.'</td>
	<td width="33%"><b>Date:</b> '.strftime("%a, %d %B %Y %H:%M", $obj->post_stamp).'</td>
</tr>
<tr class="RowStyleA">
	<td colspan="3">
	'.$iemail_poll.'
	'.$body.'
	'.$iemail_attach.'
	</td>
</tr>
<tr class="RowStyleB">
	<td colspan="3">
	[ <a href="/egroupware/fudforum/3814588639/index.php?t=post&reply_to='.$obj->id.'">Reply</a> ][ <a href="/egroupware/fudforum/3814588639/index.php?t=post&reply_to='.$obj->id.'&quote=true">Quote</a> ][ <a href="/egroupware/fudforum/3814588639/index.php?t=rview&goto='.$obj->id.'">View Topic/Message</a> ]'.$iemail_unsub.'
	</td>
</tr>
</table>
</td></tr></table></body></html>';
}

function send_notifications($to, $msg_id, $thr_subject, $poster_login, $id_type, $id, $frm_name, $frm_id)
{
	if (isset($to['EMAIL']) && (is_string($to['EMAIL']) || (is_array($to['EMAIL']) && count($to['EMAIL'])))) {
		$do_email = 1;
		$goto_url['email'] = '/egroupware/fudforum/3814588639/index.php?t=rview&goto='.$msg_id;
		if ($GLOBALS['FUD_OPT_2'] & 64) {

			$obj = db_sab("SELECT p.total_votes, p.name AS poll_name, m.reply_to, m.subject, m.id, m.post_stamp, m.poster_id, m.foff, m.length, m.file_id, u.alias, m.attach_cnt, m.attach_cache, m.poll_cache FROM phpgw_fud_msg m LEFT JOIN phpgw_fud_users u ON m.poster_id=u.id LEFT JOIN phpgw_fud_poll p ON m.poll_id=p.id WHERE m.id=".$msg_id." AND m.apr=1");

			$headers  = "MIME-Version: 1.0\r\n";
			if ($obj->reply_to) {
				$headers .= "In-Reply-To: ".$obj->reply_to."\r\n";
			}
			$headers .= "List-Id: ".$frm_id.".".$_SERVER['SERVER_NAME']."\r\n";
			$split = get_random_value(128)                                                                            ;
			$headers .= "Content-Type: multipart/alternative; boundary=\"------------" . $split . "\"\r\n";
			$boundry = "\r\n--------------" . $split . "\r\n";

			$CHARSET = 'ISO-8859-15';

			$pfx = '';

			$plain_text = read_msg_body($obj->foff, $obj->length, $obj->file_id);
			$iemail_unsub = $id_type == 'thr' ? '[ <a href="/egroupware/fudforum/3814588639/index.php?t=rview&th='.$id.'&notify=1&opt=off">Unsubscribe from this topic</a> ]' : '[ <a href="/egroupware/fudforum/3814588639/index.php?t=rview&frm_id='.$id.'&unsub=1">Unsubscribe from this forum</a> ]';

			$body_email = $boundry . "Content-Type: text/plain; charset=" . $CHARSET . "; format=flowed\r\nContent-Transfer-Encoding: 7bit\r\n\r\n" . strip_tags($plain_text) . "\r\n\r\n" . 'To participate in the discussion, go here:' . ' ' . '/egroupware/fudforum/3814588639/index.php?t=rview&th=' . $id . "&notify=1&opt=off\r\n" .
			$boundry . "Content-Type: text/html; charset=" . $CHARSET . "\r\nContent-Transfer-Encoding: 7bit\r\n\r\n" . make_email_message($plain_text, $obj, $iemail_unsub) . "\r\n" . substr($boundry, 0, -2) . "--\r\n";
		}
	}
	if (isset($to['ICQ']) && (is_string($to['ICQ']) || (is_array($to['ICQ']) && count($to['ICQ'])))) {
		$do_icq = 1;
		$icq = str_replace('http://', "http'+'://'+'", $GLOBALS['WWW_ROOT']);
		$icq = str_replace('www.', "www'+'.", $icq);
		$goto_url['icq'] = "javascript:window.location='".$icq."/egroupware/fudforum/3814588639/index.php?t=rview&goto=".$msg_id."';";
	} else if (!isset($do_email)) {
		/* nothing to do */
		return;
	}

	reverse_fmt($thr_subject);
	reverse_fmt($poster_login);

	if ($id_type == 'thr') {
		$subj = 'New reply to '.$thr_subject.' by '.$poster_login;

		if (!isset($body_email) && isset($do_email)) {
			$unsub_url['email'] = '/egroupware/fudforum/3814588639/index.php?t=rview&th='.$id.'&notify=1&opt=off';
			$body_email = 'To view unread replies goto '.$goto_url['email'].'\n\nIf you do not wish to receive further notifications about replies in this topic, please go here: '.$unsub_url['email'];
		}

		if (isset($do_icq)) {
			$unsub_url['icq'] = "javascript:window.location='".$icq."/egroupware/fudforum/3814588639/index.php?t=rview&th=".$id."&notify=1&opt=off';";
			$body_icq = 'To view unread replies goto\n'.$goto_url['icq'].'\n\nIf you do not wish to receive further notifications about replies in this topic, please go here:\n'.$unsub_url['icq'];
		}
	} else if ($id_type == 'frm') {
		reverse_fmt($frm_name);

		$subj = 'New topic in forum '.$frm_name.' called '.$thr_subject.' by '.$poster_login;

		if (isset($do_icq)) {
			$unsub_url['icq'] = "javascript:window.location='".$icq."/egroupware/fudforum/3814588639/index.php?t=rview&unsub=1&frm_id=".$id."';";
			$body_icq = 'To view the topic goto:\n'.$goto_url['icq'].'\n\nTo stop receiving notifications about new topics in this forum, please go here:\n'.$unsub_url['icq'];
		}
		if (!isset($body_email) && isset($do_email)) {
			$unsub_url['email'] = '/egroupware/fudforum/3814588639/index.php?t=rview&unsub=1&frm_id='.$id;
			$body_email = 'To view the topic goto:\n'.$goto_url['email'].'\n\nTo stop receiving notifications about new topics in this forum, please go here: '.$unsub_url['email'];
		}
	}

	if (isset($do_email)) {
		send_email($GLOBALS['NOTIFY_FROM'], $to['EMAIL'], $subj, $body_email, (isset($headers) ? $headers : ''));
	}
	if (isset($do_icq)) {
		send_email($GLOBALS['NOTIFY_FROM'], $to['ICQ'], $subj, $body_icq);
	}
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
}function th_lock($id, $lck)
{
	q("UPDATE phpgw_fud_thread SET thread_opt=(thread_opt|1)".(!$lck ? '& ~ 1' : '')." WHERE id=".$id);
}

function th_inc_view_count($id)
{
	q('UPDATE phpgw_fud_thread SET views=views+1 WHERE id='.$id);
}

function th_inc_post_count($id, $r, $lpi=0, $lpd=0)
{
	if ($lpi && $lpd) {
		q('UPDATE phpgw_fud_thread SET replies=replies+'.$r.', last_post_id='.$lpi.', last_post_date='.$lpd.' WHERE id='.$id);
	} else {
		q('UPDATE phpgw_fud_thread SET replies=replies+'.$r.' WHERE id='.$id);
	}
}

function th_frm_last_post_id($id, $th)
{
	return (int) q_singleval('SELECT phpgw_fud_thread.last_post_id FROM phpgw_fud_thread INNER JOIN phpgw_fud_msg ON phpgw_fud_thread.root_msg_id=phpgw_fud_msg.id WHERE phpgw_fud_thread.forum_id='.$id.' AND phpgw_fud_thread.id!='.$th.' AND phpgw_fud_thread.moved_to=0 AND phpgw_fud_msg.apr=1 ORDER BY phpgw_fud_thread.last_post_date DESC LIMIT 1');
}$GLOBALS['__SML_CHR_CHK__'] = array("\n"=>1, "\r"=>1, "\t"=>1, " "=>1, "]"=>1, "["=>1, "<"=>1, ">"=>1, "'"=>1, '"'=>1, "("=>1, ")"=>1, "."=>1, ","=>1, "!"=>1, "?"=>1);

function smiley_to_post($text)
{
	$text_l = strtolower($text);

        $c = uq('SELECT code, '.__FUD_SQL_CONCAT__.'(\'images/smiley_icons/\', img), descr FROM phpgw_fud_smiley');
        while ($r = db_rowarr($c)) {
        	$codes = (strpos($r[0], '~') !== false) ? explode('~', strtolower($r[0])) : array(strtolower($r[0]));

		foreach ($codes as $v) {
			$a = 0;
			$len = strlen($v);
			while (($a = strpos($text_l, $v, $a)) !== false) {
				if ((!$a || isset($GLOBALS['__SML_CHR_CHK__'][$text_l[$a - 1]])) && ((@$ch = $text_l[$a + $len]) == "" || isset($GLOBALS['__SML_CHR_CHK__'][$ch]))) {
					$rep = '<img src="'.$r[1].'" border=0 alt="'.$r[2].'">';
					$text = substr_replace($text, $rep, $a, $len);
					$text_l = substr_replace($text_l, $rep, $a, $len);
					$a += strlen($rep);
				} else {
					$a += $len;
				}
			}
		}
	}

	return $text;
}

function post_to_smiley($text)
{
	$c = uq('SELECT code, '.__FUD_SQL_CONCAT__.'(\'images/smiley_icons/\', img), descr FROM phpgw_fud_smiley');
	while ($r = db_rowarr($c)) {
		$im = '<img src="'.$r[1].'" border=0 alt="'.$r[2].'">';
		$re[$im] = (($p = strpos($r[0], '~')) !== false) ? substr($r[0], 0, $p) : $r[0];
	}

	return (isset($re) ? strtr($text, $re) : $text);
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
}function poll_delete($id)
{
	if (!$id) {
		return;
	}

	q('UPDATE phpgw_fud_msg SET poll_id=0 WHERE poll_id='.$id);
	q('DELETE FROM phpgw_fud_poll_opt WHERE poll_id='.$id);
	q('DELETE FROM phpgw_fud_poll_opt_track WHERE poll_id='.$id);
	q('DELETE FROM phpgw_fud_poll WHERE id='.$id);
}

function poll_fetch_opts($id)
{
	$c = uq('SELECT id,name FROM phpgw_fud_poll_opt WHERE poll_id='.$id);
	while ($r = db_rowarr($c)) {
		$a[$r[0]] = $r[1];
	}

	return (isset($a) ? $a : null);
}

function poll_del_opt($id, $poll_id)
{
	q('DELETE FROM phpgw_fud_poll_opt WHERE poll_id='.$poll_id.' AND id='.$id);
	q('DELETE FROM phpgw_fud_poll_opt_track WHERE poll_id='.$poll_id.' AND poll_opt='.$id);
	$ttl_votes = (int) q_singleval('SELECT SUM(count) FROM phpgw_fud_poll_opt WHERE id='.$id);
	q('UPDATE phpgw_fud_poll SET total_votes='.$ttl_votes.' WHERE id='.$poll_id);
}

function poll_activate($poll_id, $frm_id)
{
	q('UPDATE phpgw_fud_poll SET forum_id='.$frm_id.' WHERE id='.$poll_id);
}

function poll_sync($poll_id, $name, $max_votes, $expiry)
{
	q("UPDATE phpgw_fud_poll SET name='".addslashes(htmlspecialchars($name))."', expiry_date=".intzero($expiry).", max_votes=".intzero($max_votes)." WHERE id=".$poll_id);
}

function poll_add($name, $max_votes, $expiry)
{
	return db_qid("INSERT INTO phpgw_fud_poll (name, owner, creation_date, expiry_date, max_votes) VALUES ('".addslashes(htmlspecialchars($name))."', "._uid.", ".__request_timestamp__.", ".intzero($expiry).", ".intzero($max_votes).")");
}

function poll_opt_sync($id, $name)
{
	q("UPDATE phpgw_fud_poll_opt SET name='".addslashes($name)."' WHERE id=".$id);
}

function poll_opt_add($name, $poll_id)
{
	return db_qid("INSERT INTO phpgw_fud_poll_opt (poll_id,name) VALUES(".$poll_id.", '".addslashes($name)."')");
}

function poll_validate($poll_id, $msg_id)
{
	if (($mid = (int) q_singleval('SELECT id FROM phpgw_fud_msg WHERE poll_id='.$poll_id)) && $mid != $msg_id) {
		return 0;
	} else {
		return $poll_id;
	}
}function safe_attachment_copy($source, $id, $ext)
{
	$loc = $GLOBALS['FILE_STORE'] . $id . '.atch';
	if (!$ext && !move_uploaded_file($source, $loc)) {
		std_out('unable to move uploaded file', 'ERR');
	} else if ($ext && !copy($source, $loc)) {
		std_out('unable to handle file attachment', 'ERR');
	}
	@unlink($source);

	@chmod($loc, ($GLOBALS['FUD_OPT_2'] & 8388608 ? 0600 : 0666));

	return $loc;
}

function attach_add($at, $owner, $attach_opt=0, $ext=0)
{
	$mime_type = (int) q_singleval("SELECT id FROM phpgw_fud_mime WHERE fl_ext='".addslashes(substr(strrchr($at['name'], '.'), 1))."'");

	$id = db_qid("INSERT INTO phpgw_fud_attach (location,message_id,original_name,owner,attach_opt,mime_type,fsize) VALUES('',0,'".addslashes($at['name'])."', ".$owner.", ".$attach_opt.", ".$mime_type.", ".$at['size'].")");

	safe_attachment_copy($at['tmp_name'], $id, $ext);

	return $id;
}

function attach_finalize($attach_list, $mid, $attach_opt=0)
{
	$id_list = '';
	$attach_count = 0;

	$tbl = !$attach_opt ? 'msg' : 'pmsg';

	foreach ($attach_list as $key => $val) {
		if (empty($val)) {
			$del[] = (int)$key;
			@unlink($GLOBALS['FILE_STORE'].(int)$key.'.atch');
		} else {
			$attach_count++;
			$id_list .= (int)$key.',';
		}
	}

	if (!empty($id_list)) {
		$id_list = substr($id_list, 0, -1);
		$cc = __FUD_SQL_CONCAT__.'('.__FUD_SQL_CONCAT__."('".$GLOBALS['FILE_STORE']."', id), '.atch')";
		q("UPDATE phpgw_fud_attach SET location=".$cc.", message_id=".$mid." WHERE id IN(".$id_list.") AND attach_opt=".$attach_opt);
		$id_list = ' AND id NOT IN('.$id_list.')';
	} else {
		$id_list = '';
	}

	/* delete any temp attachments created during message creation */
	if (isset($del)) {
		q('DELETE FROM phpgw_fud_attach WHERE id IN('.implode(',', $del).') AND message_id='.$mid.' AND attach_opt='.$attach_opt);
	}

	/* delete any prior (removed) attachments if there are any */
	q("DELETE FROM phpgw_fud_attach WHERE message_id=".$mid." AND attach_opt=".$attach_opt.$id_list);

	if (!$attach_opt && ($atl = attach_rebuild_cache($mid))) {
		q('UPDATE phpgw_fud_msg SET attach_cnt='.$attach_count.', attach_cache=\''.addslashes(@serialize($atl)).'\' WHERE id='.$mid);
	}
}

function attach_rebuild_cache($id)
{
	$c = uq('SELECT a.id, a.original_name, a.fsize, a.dlcount, CASE WHEN m.icon IS NULL THEN \'unknown.gif\' ELSE m.icon END FROM phpgw_fud_attach a LEFT JOIN phpgw_fud_mime m ON a.mime_type=m.id WHERE message_id='.$id.' AND attach_opt=0');
	while ($r = db_rowarr($c)) {
		$ret[] = $r;
	}

	return (isset($ret) ? $ret : null);
}

function attach_inc_dl_count($id, $mid)
{
	q('UPDATE phpgw_fud_attach SET dlcount=dlcount+1 WHERE id='.$id);
	if (($a = attach_rebuild_cache($mid))) {
		q('UPDATE phpgw_fud_msg SET attach_cache=\''.addslashes(@serialize($a)).'\' WHERE id='.$mid);
	}
}function validate_email($email)
{
        return !preg_match('!([-_A-Za-z0-9\.]+)\@([-_A-Za-z0-9\.]+)\.([A-Za-z0-9]{2,4})$!', $email);
}

function send_email($from, $to, $subj, $body, $header='')
{
	if (empty($to) || !count($to)) {
		return;
	}
	$body = str_replace('\n', "\n", $body);

	if ($GLOBALS['FUD_OPT_1'] & 512) {
		if (!class_exists('fud_smtp')) {
			fud_use('smtp.inc');
		}
		$smtp = new fud_smtp;
		$smtp->msg = str_replace("\n.", "\n..", $body);
		$smtp->subject = $subj;
		$smtp->to = $to;
		$smtp->from = $from;
		$smtp->headers = $header;
		$smtp->send_smtp_email();
	} else {
		$bcc = '';

		if (is_array($to)) {
			$to = $to[0];
			if (count($to) > 1) {
				unset($to[0]);
				$bcc = 'Bcc: ' . implode(', ', $to);
			}
		}
		if ($header) {
			$header = "\n" . str_replace("\r", "", $header);
		} else if ($bcc) {
			$bcc = "\n" . $bcc;
		}

		if (version_compare("4.3.3RC2", phpversion(), ">")) {
			$body = str_replace("\n.", "\n..", $body);
		}

		mail($to, $subj, str_replace("\r", "", $body), "From: ".$from."\nErrors-To: ".$from."\nReturn-Path: ".$from."\nX-Mailer: FUDforum v".$GLOBALS['FORUM_VERSION'].$header.$bcc);
	}
}function reverse_fmt(&$data)
{
	$data = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $data);
}function get_host($ip)
{
	if (!$ip || $ip == '0.0.0.0') {
		return;
	}

	$name = gethostbyaddr($ip);

	if ($name == $ip) {
		$name = substr($name, 0, strrpos($name, '.')) . '*';
	} else if (substr_count($name, '.') > 2) {
		$name = '*' . substr($name, strpos($name, '.')+1);
	}

	return $name;
}function delete_msg_index($msg_id)
{
	q('DELETE FROM phpgw_fud_index WHERE msg_id='.$msg_id);
	q('DELETE FROM phpgw_fud_title_index WHERE msg_id='.$msg_id);
}

function mb_word_split($str)
{
	$m = array();
	$lang = $GLOBALS['usr']->lang == 'chinese' ? 'EUC-CN' : 'BIG-5';

	if (function_exists('iconv')) {
		preg_match_all('!(\W)!u', @iconv($lang, 'UTF-8', $str), $m);
	} else if (function_exists('mb_convert_encoding')) {
		preg_match_all('!(\W)!u', @mb_convert_encoding($str, 'UTF-8', $lang), $m);
	}

	if (!$m) {
		return array();
	}

	$m = array_unique($m[0]);
	foreach ($m as $v) {
		if (isset($v[1])) {
			$m2[] = "'".addslashes($v)."'";
		}
	}

	return isset($m2) ? $m2 : array();
}

function index_text($subj, $body, $msg_id)
{
	/* Remove Stuff In Quotes */
	while (preg_match('!<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>(.*?)</b></td></tr><tr><td class="quote"><br>(.*?)<br></td></tr></table>!is', $body)) {
		$body = preg_replace('!<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>(.*?)</b></td></tr><tr><td class="quote"><br>(.*?)<br></td></tr></table>!is', '', $body);
	}

	/* this is mostly a hack for php verison < 4.3 because isset(string[bad offset]) returns a warning */
	error_reporting(0);

	if (strncmp($GLOBALS['usr']->lang, 'chinese', 7)) {
		$cs = array('!\W!', '!\s+!');
		$cd = array(' ', ' ');

		reverse_fmt($subj);
		$subj = trim(preg_replace($cs, $cd, strip_tags(strtolower($subj))));
		reverse_fmt($body);
		$body = trim(preg_replace($cs, $cd, strip_tags(strtolower($body))));

		/* build full text index */
		$t1 = array_unique(explode(' ', $subj));
		$t2 = array_unique(explode(' ', $body));

		foreach ($t1 as $v) {
			if (isset($v[51]) || !isset($v[3])) continue;
			$w1[] = "'".addslashes($v)."'";
		}

		if (isset($w1)) {
			$w2 = $w1;
		}

		foreach ($t2 as $v) {
			if (isset($v[51]) || !isset($v[3])) continue;
			$w2[] = "'".addslashes($v)."'";
		}
	} else { /* handling for multibyte languages */
		$w1 = mb_word_split($subj);
		if ($w1) {
			$w2 = array_merge($w1, mb_word_split($body));
		} else {
			unset($w1);
		}
	}

	if (!$w2) {
		return;
	}

	$w2 = array_unique($w2);
	if (__dbtype__ == 'mysql') {
		ins_m('phpgw_fud_search', 'word', $w2);
	} else {
		ins_m('phpgw_fud_search', 'word', $w2, 'text');
	}

	/* This allows us to return right away, meaning we don't need to wait
	 * for any locks to be released etc... */
	if (__dbtype__ == 'mysql') {
		$del = 'DELAYED';
	} else {
		$del = '';
	}

	if (isset($w1)) {
		db_li('INSERT '.$del.' INTO phpgw_fud_title_index (word_id, msg_id) SELECT id, '.$msg_id.' FROM phpgw_fud_search WHERE word IN('.implode(',', $w1).')', $ef);
	}
	db_li('INSERT '.$del.' INTO phpgw_fud_index (word_id, msg_id) SELECT id, '.$msg_id.' FROM phpgw_fud_search WHERE word IN('.implode(',', $w2).')', $ef);
}function is_moderator($frm_id, $user_id)
{
	return q_singleval('SELECT id FROM phpgw_fud_mod WHERE user_id='.$user_id.' AND forum_id='.$frm_id);
}

function frm_updt_counts($frm_id, $replies, $threads, $last_post_id)
{
	$threads	= !$threads ? '' : ', thread_count=thread_count+'.$threads;
	$last_post_id	= !$last_post_id ? '' : ', last_post_id='.$last_post_id;

	q('UPDATE phpgw_fud_forum SET post_count=post_count+'.$replies.$threads.$last_post_id.' WHERE id='.$frm_id);
}function th_add($root, $forum_id, $last_post_date, $thread_opt, $orderexpiry, $replies=0, $lpi=0)
{
	if (!$lpi) {
		$lpi = $root;
	}

	return db_qid("INSERT INTO
		phpgw_fud_thread
			(forum_id, root_msg_id, last_post_date, replies, views, rating, last_post_id, thread_opt, orderexpiry)
		VALUES
			(".$forum_id.", ".$root.", ".$last_post_date.", ".$replies.", 0, 0, ".$lpi.", ".$thread_opt.", ".$orderexpiry.")");
}

function th_move($id, $to_forum, $root_msg_id, $forum_id, $last_post_date, $last_post_id)
{
	if (!db_locked()) {
		db_lock('phpgw_fud_poll WRITE, phpgw_fud_thread_view WRITE, phpgw_fud_thread WRITE, phpgw_fud_forum WRITE, phpgw_fud_msg WRITE');
		$ll = 1;
	}
	$msg_count = q_singleval("SELECT count(*) FROM phpgw_fud_thread LEFT JOIN phpgw_fud_msg ON phpgw_fud_msg.thread_id=phpgw_fud_thread.id WHERE phpgw_fud_msg.apr=1 AND phpgw_fud_thread.id=".$id);

	q('UPDATE phpgw_fud_thread SET forum_id='.$to_forum.' WHERE id='.$id);
	q('UPDATE phpgw_fud_forum SET post_count=post_count-'.$msg_count.' WHERE id='.$forum_id);
	q('UPDATE phpgw_fud_forum SET thread_count=thread_count+1,post_count=post_count+'.$msg_count.' WHERE id='.$to_forum);
	q('DELETE FROM phpgw_fud_thread WHERE forum_id='.$to_forum.' AND root_msg_id='.$root_msg_id.' AND moved_to='.$forum_id);
	if (($aff_rows = db_affected())) {
		q('UPDATE phpgw_fud_forum SET thread_count=thread_count-'.$aff_rows.' WHERE id='.$to_forum);
	}
	q('UPDATE phpgw_fud_thread SET moved_to='.$to_forum.' WHERE id!='.$id.' AND root_msg_id='.$root_msg_id);

	q('INSERT INTO phpgw_fud_thread
		(forum_id, root_msg_id, last_post_date, last_post_id, moved_to)
	VALUES
		('.$forum_id.', '.$root_msg_id.', '.$last_post_date.', '.$last_post_id.', '.$to_forum.')');

	rebuild_forum_view($forum_id);
	rebuild_forum_view($to_forum);

	$c = q('SELECT poll_id FROM phpgw_fud_msg WHERE thread_id='.$id.' AND apr=1 AND poll_id>0');
	while ($r = db_rowarr($c)) {
		$p[] = $r[0];
	}
	unset($c);
	if (isset($p)) {
		q('UPDATE phpgw_fud_poll SET forum_id='.$to_forum.' WHERE id IN('.implode(',', $p).')');
	}

	if (isset($ll)) {
		db_unlock();
	}
}

function rebuild_forum_view($forum_id, $page=0)
{
	if (!db_locked()) {
		$ll = 1;
	        db_lock('phpgw_fud_thread_view WRITE, phpgw_fud_thread WRITE, phpgw_fud_msg WRITE, phpgw_fud_forum WRITE');
	}

	$tm = __request_timestamp__;

	/* Remove expired moved thread pointers */
	q('DELETE FROM phpgw_fud_thread WHERE forum_id='.$forum_id.' AND last_post_date<'.($tm-86400*$GLOBALS['MOVED_THR_PTR_EXPIRY']).' AND moved_to!=0');
	if (($aff_rows = db_affected())) {
		q('UPDATE phpgw_fud_forum SET thread_count=thread_count-'.$aff_rows.' WHERE id='.$forum_id);
		$page = 0;
	}

	/* De-announce expired announcments and sticky messages */
	$r = q("SELECT phpgw_fud_thread.id FROM phpgw_fud_thread INNER JOIN phpgw_fud_msg ON phpgw_fud_thread.root_msg_id=phpgw_fud_msg.id WHERE phpgw_fud_thread.forum_id=".$forum_id." AND thread_opt>=2 AND (phpgw_fud_msg.post_stamp+phpgw_fud_thread.orderexpiry)<=".$tm);
	while ($tid = db_rowarr($r)) {
		q("UPDATE phpgw_fud_thread SET orderexpiry=0, thread_opt=thread_opt & ~ (2|4) WHERE id=".$tid[0]);
	}
	unset($r);

	if (__dbtype__ == 'pgsql') {
		$tmp_tbl_name = "phpgw_fud_ftvt_".get_random_value();
		q("CREATE TEMP TABLE ".$tmp_tbl_name." ( forum_id INT NOT NULL, page INT NOT NULL, thread_id INT NOT NULL, pos SERIAL, tmp INT )");

		if ($page) {
			q("DELETE FROM phpgw_fud_thread_view WHERE forum_id=".$forum_id." AND page<".($page+1));
			q("INSERT INTO ".$tmp_tbl_name." (thread_id,forum_id,page,tmp) SELECT phpgw_fud_thread.id, phpgw_fud_thread.forum_id, 2147483647, CASE WHEN thread_opt>=2 AND (phpgw_fud_msg.post_stamp+phpgw_fud_thread.orderexpiry>".$tm." OR phpgw_fud_thread.orderexpiry=0) THEN 2147483647 ELSE phpgw_fud_thread.last_post_date END AS sort_order_fld  FROM phpgw_fud_thread INNER JOIN phpgw_fud_msg ON phpgw_fud_thread.root_msg_id=phpgw_fud_msg.id WHERE forum_id=".$forum_id." AND phpgw_fud_msg.apr=1 ORDER BY sort_order_fld DESC, phpgw_fud_thread.last_post_id DESC LIMIT ".($GLOBALS['THREADS_PER_PAGE']*$page));
		} else {
			q("DELETE FROM phpgw_fud_thread_view WHERE forum_id=".$forum_id);
			q("INSERT INTO ".$tmp_tbl_name." (thread_id,forum_id,page,tmp) SELECT phpgw_fud_thread.id, phpgw_fud_thread.forum_id, 2147483647, CASE WHEN thread_opt>=2 AND (phpgw_fud_msg.post_stamp+phpgw_fud_thread.orderexpiry>".$tm." OR phpgw_fud_thread.orderexpiry=0) THEN 2147483647 ELSE phpgw_fud_thread.last_post_date END AS sort_order_fld  FROM phpgw_fud_thread INNER JOIN phpgw_fud_msg ON phpgw_fud_thread.root_msg_id=phpgw_fud_msg.id WHERE forum_id=".$forum_id." AND phpgw_fud_msg.apr=1 ORDER BY sort_order_fld DESC, phpgw_fud_thread.last_post_id DESC");
		}

		q("INSERT INTO phpgw_fud_thread_view (thread_id,forum_id,page,pos) SELECT thread_id,forum_id,CEIL(pos/".$GLOBALS['THREADS_PER_PAGE'].".0),(pos-(CEIL(pos/".$GLOBALS['THREADS_PER_PAGE'].".0)-1)*".$GLOBALS['THREADS_PER_PAGE'].") FROM ".$tmp_tbl_name);
		q("DROP TABLE ".$tmp_tbl_name);
		return;
	} else if (__dbtype__ == 'mysql') {
		if ($page) {
			q('DELETE FROM phpgw_fud_thread_view WHERE forum_id='.$forum_id.' AND page<'.($page+1));
			q("INSERT INTO phpgw_fud_thread_view (thread_id,forum_id,page,tmp) SELECT phpgw_fud_thread.id, phpgw_fud_thread.forum_id, 2147483645, CASE WHEN thread_opt>=2 AND (phpgw_fud_msg.post_stamp+phpgw_fud_thread.orderexpiry>".$tm." OR phpgw_fud_thread.orderexpiry=0) THEN 4294967294 ELSE phpgw_fud_thread.last_post_date END AS sort_order_fld  FROM phpgw_fud_thread INNER JOIN phpgw_fud_msg ON phpgw_fud_thread.root_msg_id=phpgw_fud_msg.id WHERE forum_id=".$forum_id." AND phpgw_fud_msg.apr=1 ORDER BY sort_order_fld DESC, phpgw_fud_thread.last_post_id DESC LIMIT 0, ".($GLOBALS['THREADS_PER_PAGE']*$page));
			q('UPDATE phpgw_fud_thread_view SET page=CEILING(pos/'.$GLOBALS['THREADS_PER_PAGE'].'), pos=pos-(CEILING(pos/'.$GLOBALS['THREADS_PER_PAGE'].')-1)*'.$GLOBALS['THREADS_PER_PAGE'].' WHERE forum_id='.$forum_id.' AND page=2147483645');
		} else {
			q('DELETE FROM phpgw_fud_thread_view WHERE forum_id='.$forum_id);
			q("INSERT INTO phpgw_fud_thread_view (thread_id,forum_id,page,tmp) SELECT phpgw_fud_thread.id, phpgw_fud_thread.forum_id, 2147483645, CASE WHEN thread_opt>=2 AND (phpgw_fud_msg.post_stamp+phpgw_fud_thread.orderexpiry>".$tm." OR phpgw_fud_thread.orderexpiry=0) THEN 4294967294 ELSE phpgw_fud_thread.last_post_date END AS sort_order_fld  FROM phpgw_fud_thread INNER JOIN phpgw_fud_msg ON phpgw_fud_thread.root_msg_id=phpgw_fud_msg.id WHERE forum_id=".$forum_id." AND phpgw_fud_msg.apr=1 ORDER BY sort_order_fld DESC, phpgw_fud_thread.last_post_id DESC");
			q('UPDATE phpgw_fud_thread_view SET page=CEILING(pos/'.$GLOBALS['THREADS_PER_PAGE'].'), pos=pos-(CEILING(pos/'.$GLOBALS['THREADS_PER_PAGE'].')-1)*'.$GLOBALS['THREADS_PER_PAGE'].' WHERE forum_id='.$forum_id);
		}
	}

	if (isset($ll)) {
		db_unlock();
	}
}class fud_smtp
{
	var $fs, $last_ret, $msg, $subject, $to, $from, $headers;

	function get_return_code($cmp_code='250')
	{
		if (!($this->last_ret = fgets($this->fs, 1024))) {
			return;
		}
		if (substr($this->last_ret, 0, 3) == $cmp_code) {
			return 1;
		}

		return;
	}

	function wts($string)
	{
		fwrite($this->fs, $string . "\r\n");
	}

	function open_smtp_connex()
	{
		if( !($this->fs = fsockopen($GLOBALS['FUD_SMTP_SERVER'], 25, $errno, $errstr, $GLOBALS['FUD_SMTP_TIMEOUT'])) ) {
			exit("ERROR: stmp server at ".$GLOBALS['FUD_SMTP_SERVER']." is not avaliable<br>\nAdditional Problem Info: $errno -> $errstr <br>\n");
		}
		if (!$this->get_return_code(220)) {
			return;
		}
		$this->wts("HELO ".$GLOBALS['FUD_SMTP_SERVER']);
		if (!$this->get_return_code()) {
			return;
		}

		/* Do SMTP Auth if needed */
		if ($GLOBALS['FUD_SMTP_LOGIN']) {
			$this->wts('AUTH LOGIN');
			if (!$this->get_return_code(334)) {
				return;
			}
			$this->wts(base64_encode($GLOBALS['FUD_SMTP_LOGIN']));
			if (!$this->get_return_code(334)) {
				return;
			}
			$this->wts(base64_encode($GLOBALS['FUD_SMTP_PASS']));
			if (!$this->get_return_code(235)) {
				return;
			}
		}

		return 1;
	}

	function send_from_hdr()
	{
		$this->wts('MAIL FROM: <'.$GLOBALS['NOTIFY_FROM'].'>');
		return $this->get_return_code();
	}

	function send_to_hdr()
	{
		if (!@is_array($this->to)) {
			$this->to = array($this->to);
		}

		foreach ($this->to as $to_addr) {
			$this->wts('RCPT TO: <'.$to_addr.'>');
			if (!$this->get_return_code()) {
				return;
			}
		}
		return 1;
	}

	function send_data()
	{
		$this->wts('DATA');
		if (!$this->get_return_code(354)) {
			return;
		}

		/* This is done to ensure what we comply with RFC requiring each line to end with \r\n */
		$this->msg = preg_replace("!(\r)?\n!si", "\r\n", $this->msg);

		if( empty($this->from) ) $this->from = $GLOBALS['NOTIFY_FROM'];

		$this->wts('Subject: '.$this->subject);
		$this->wts('Date: '.date("r"));
		$this->wts('To: '.(count($this->to) == 1 ? $this->to[0] : $GLOBALS['NOTIFY_FROM']));
		$this->wts('From: '.$this->from);
		$this->wts('X-Mailer: FUDforum v'.$GLOBALS['FORUM_VERSION']);
		$this->wts($this->headers."\r\n");
		$this->wts($this->msg);
		$this->wts('.');

		return $this->get_return_code();
	}

	function close_connex()
	{
		$this->wts('quit');
		fclose($this->fs);
	}

	function send_smtp_email()
	{
		if (!$this->open_smtp_connex()) {
			exit("Invalid STMP return code: ".$this->last_ret."<br>\n");
		}
		if (!$this->send_from_hdr()) {
			$this->close_connex();
			exit("Invalid STMP return code: ".$this->last_ret."<br>\n");
		}
		if (!$this->send_to_hdr()) {
			$this->close_connex();
			exit("Invalid STMP return code: ".$this->last_ret."<br>\n");
		}
		if (!$this->send_data()) {
			$this->close_connex();
			exit("Invalid STMP return code: ".$this->last_ret."<br>\n");
		}

		$this->close_connex();
	}
}

	/* only admins & moderators have access to this control panel */
	if (!_uid || !($usr->users_opt & (1048576|524288))) {
		std_error('perms');
	}

	$appr = isset($_GET['appr']) ? (int) $_GET['appr'] : 0;
	$del = isset($_GET['del']) ? (int) $_GET['del'] : 0;

	/* we need to determine wether or not the message exists & if the user has access to approve/delete it */
	if ($appr || $del) {
		if (!q_singleval('SELECT CASE WHEN (('.$usr->users_opt.' & 1048576) = 0) THEN mm.id ELSE 1 END FROM phpgw_fud_msg m INNER JOIN phpgw_fud_thread t ON m.thread_id=t.id LEFT JOIN phpgw_fud_mod mm ON t.forum_id=mm.forum_id AND mm.user_id='._uid.' WHERE m.id='.($appr ? $appr : $del))) {
			if (db_affected()) {
				std_error('perms');
			} else {
				$del = $appr = 0;
			}
		}
	}

	if ($appr) {
		fud_msg_edit::approve($appr, true);
	} else if ($del) {
		fud_msg_edit::delete(false, $del);
	}

	ses_update_status($usr->sid, '', 0);

	

	/* for sanity sake, we only select up to POSTS_PER_PAGE messages, simply because otherwise the form will
	 * become unmanageable.
	 */
	$r = $query_type("SELECT
		m.*,
		t.thread_opt, t.root_msg_id, t.last_post_id, t.forum_id,
		f.message_threshold, f.name AS frm_name,
		c.name AS cat_name,
		u.id AS user_id, u.alias AS login, u.avatar_loc, u.email, u.posted_msg_count, u.join_date, u.location,
		u.sig, u.custom_status, u.icq, u.jabber, u.affero, u.aim, u.msnm, u.yahoo, u.last_visit AS time_sec, u.users_opt,
		l.name AS level_name, l.level_opt, l.img AS level_img,
		p.max_votes, p.expiry_date, p.creation_date, p.name AS poll_name, p.total_votes,
		pot.id AS cant_vote
	FROM
		phpgw_fud_msg m
	INNER JOIN phpgw_fud_thread t ON m.thread_id=t.id
	INNER JOIN phpgw_fud_forum f ON t.forum_id=f.id
	INNER JOIN phpgw_fud_fc_view v ON v.f=f.id
	".($usr->users_opt & 1048576 ? '' : ' INNER JOIN phpgw_fud_mod mm ON f.id=mm.forum_id AND mm.user_id='._uid.' ')."
	INNER JOIN phpgw_fud_cat c ON f.cat_id=c.id
	LEFT JOIN phpgw_fud_users u ON m.poster_id=u.id
	LEFT JOIN phpgw_fud_level l ON u.level_id=l.id
	LEFT JOIN phpgw_fud_poll p ON m.poll_id=p.id
	LEFT JOIN phpgw_fud_poll_opt_track pot ON pot.poll_id=p.id AND pot.user_id="._uid."
	WHERE (f.forum_opt>=2 AND (f.forum_opt & 2) > 0) AND m.apr=0
	ORDER BY v.id, m.post_stamp DESC LIMIT ".$POSTS_PER_PAGE);

	$prev_thread_id = $modque_message = '';
	$m_num = 0;

	/* quick cheat to give us full access to the messages ;) */
	$perms = 2147483647;
	$_GET['start'] = 0;

	$usr->md = 1;
	while ($obj = db_rowobj($r)) {
		if (!$prev_thread_id || $prev_thread_id != $obj->thread_id) {
			$prev_thread_id = $obj->thread_id;
		}

		$message = tmpl_drawmsg($obj, $usr, $perms, false, $m_num, null);
		$modque_message .= '<tr class="RowStyleB"><td>
<b>Forum:</b> '.$obj->cat_name.' :: <b>Topic:</b> '.$obj->frm_name.' [<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=modque&amp;appr='.$obj->id.'&amp;'._rsid.'">Approve</a>]&nbsp;&nbsp;[<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=modque&amp;del='.$obj->id.'&amp;'._rsid.'">Delete</a>]
</td></tr>
'.$message;
	}
	unset($r);

	if (empty($modque_message)) {
		$modque_message = '<tr><th>There are no messages pending approval</th></tr>';
	}

	un_register_fps();

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
<table border="0" cellspacing="0" cellpadding="0" class="ContentTable">
<tr><th>Messages pending approval</th></tr>
<?php echo $modque_message; ?>
</table>
<?php echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>