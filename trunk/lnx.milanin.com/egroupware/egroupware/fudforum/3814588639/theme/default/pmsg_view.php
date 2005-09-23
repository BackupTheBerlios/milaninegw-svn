<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: pmsg_view.php.t,v 1.1.1.1 2003/10/17 21:11:29 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}$folders = array(1=>'Inbox', 2=>'Saved', 4=>'Draft', 3=>'Sent', 5=>'Trash');

function tmpl_cur_ppage($folder_id, $folders, $msg_subject='')
{
	if (!$folder_id || (!$msg_subject && $_GET['t'] == 'ppost')) {
		$user_action = 'Writing a Private Message';
	} else {
		$user_action = $msg_subject ? '<a href="/egroupware/fudforum/3814588639/index.php?t=pmsg&amp;folder_id='.$folder_id.'&amp;'._rsid.'" class="GenLink">'.$folders[$folder_id].'</a> &raquo; '.$msg_subject : 'Browsing <b>'.$folders[$folder_id].'</b> folder';
	}

	return '<font class="SmallText"><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=pmsg&amp;'._rsid.'">Private Messaging</a>&nbsp;&raquo;&nbsp;'.$user_action.'</font><br /><img src="blank.gif" alt="" height=4 width=1 /><br />';
}$GLOBALS['affero_domain'] = parse_url($GLOBALS['WWW_ROOT']);

function tmpl_drawpmsg($obj, $usr, $mini)
{
	$o1 =& $GLOBALS['FUD_OPT_1'];
	$o2 =& $GLOBALS['FUD_OPT_2'];
	$a =& $obj->users_opt;
	$b =& $usr->users_opt;
	$c =& $obj->level_opt;

	if (!$mini) {
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
			$obj->login = $obj->alias;
			$online_indicator = (($obj->last_visit + $GLOBALS['LOGEDIN_TIMEOUT'] * 60) > __request_timestamp__) ? '<img src="/egroupware/fudforum/3814588639/theme/default/images/online.gif" alt="'.$obj->login.' is currently online" title="'.$obj->login.' is currently online" />' : '<img src="/egroupware/fudforum/3814588639/theme/default/images/offline.gif" alt="'.$obj->login.'  is currently offline" title="'.$obj->login.'  is currently offline" />';
		} else {
			$online_indicator = '';
		}

		$host_name = ($obj->host_name && $o1 & 268435456) ? '<b>From:</b> '.$obj->host_name.'<br />' : '';
		$ip_address = '';

		if ($obj->location) {
			if (strlen($obj->location) > $GLOBALS['MAX_LOCATION_SHOW']) {
				$location = substr($obj->location, 0, $GLOBALS['MAX_LOCATION_SHOW']) . '...';
			} else {
				$location = $obj->location;
			}
			$location = '<br /><b>Location:</b> '.$location;
		} else {
			$location = '';
		}
		$msg_icon = !$obj->icon ? '' : '<img src="images/message_icons/'.$obj->icon.'" alt="" />&nbsp;&nbsp;';
		$usr->buddy_list = @unserialize($usr->buddy_list);
		if ($obj->user_id != _uid && $obj->user_id > 0) {
			$buddy_link = !isset($usr->buddy_list[$obj->user_id]) ? '<a href="/egroupware/fudforum/3814588639/index.php?t=buddy_list&amp;'._rsid.'&amp;add='.$obj->user_id.'" class="GenLink">add to buddy list</a><br />' : '<br />[<a href="/egroupware/fudforum/3814588639/index.php?t=buddy_list&amp;del='.$obj->user_id.'&amp;redr=1&amp;'._rsid.'" class="GenLink">remove from buddy list</a>]';
		} else {
			$buddy_link = '';
		}
		/* show im buttons if need be */
		if ($b & 16384) {
			$im_icq		= $obj->icq ? '<a href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$obj->user_id.'&amp;'._rsid.'#icq_msg"><img src="/egroupware/fudforum/3814588639/theme/default/images/icq.gif" alt="" title="'.$obj->icq.'" /></a>&nbsp;' : '';
			$im_aim		= $obj->aim ? '<a href="aim:goim?screenname='.$obj->aim.'&amp;message=Hi.+Are+you+there?" target="_blank"><img src="/egroupware/fudforum/3814588639/theme/default/images/aim.gif" title="'.$obj->aim.'" alt="" /></a>&nbsp;' : '';
			$im_yahoo	= $obj->yahoo ? '<a target="_blank" href="http://edit.yahoo.com/config/send_webmesg?.target='.$obj->yahoo.'&amp;.src=pg"><img src="/egroupware/fudforum/3814588639/theme/default/images/yahoo.gif" alt="" title="'.$obj->yahoo.'" /></a>&nbsp;' : '';
			$im_msnm	= $obj->msnm ? '<a href="mailto:'.$obj->msnm.'"><img src="/egroupware/fudforum/3814588639/theme/default/images/msnm.gif" title="'.$obj->msnm.'" alt="" /></a>' : '';
			$im_jabber	= $obj->jabber ? '<img src="/egroupware/fudforum/3814588639/theme/default/images/jabber.gif" title="'.$obj->jabber.'" alt="" />' : '';
			if ($o2 & 2048) {
				$im_affero = $obj->affero ? '<a href="http://svcs.affero.net/rm.php?r='.$obj->affero.'&amp;ll=0.'.urlencode($GLOBALS['affero_domain']['host']).'&amp;lp=0.'.urlencode($GLOBALS['affero_domain']['host']).'&amp;ls='.urlencode($obj->subject).'" target=_blank><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/affero_reg.gif" /></a>' : '<a href="http://svcs.affero.net/rm.php?m='.urlencode($obj->email).'&amp;ll=0.'.urlencode($GLOBALS['affero_domain']['host']).'&amp;lp=0.'.urlencode($GLOBALS['affero_domain']['host']).'&amp;ls='.urlencode($obj->subject).'" target=_blank><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/affero_noreg.gif" /></a>';
			} else {
				$im_affero = '';
			}
			$dmsg_im_row = ($im_icq || $im_aim || $im_yahoo || $im_msnm || $im_jabber || $im_affero) ? $im_icq.' '.$im_aim.' '.$im_yahoo.' '.$im_msnm.' '.$im_jabber.' '.$im_affero.'<br />' : '';
		} else {
			$dmsg_im_row = '';
		}
		if ($obj->ouser_id != _uid) {
			$user_profile = '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$obj->user_id.'&amp;'._rsid.'"><img src="/egroupware/fudforum/3814588639/theme/default/images/msg_about.gif" alt="" /></a>';
			$email_link = ($o1 & 4194304 && $a & 16) ? '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=email&amp;toi='.$obj->user_id.'&amp;'._rsid.'"><img src="/egroupware/fudforum/3814588639/theme/default/images/msg_email.gif" alt="" /></a>' : '';
			$private_msg_link = '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=ppost&amp;toi='.$obj->user_id.'&amp;'._rsid.'"><img title="Send a private message to this user" src="/egroupware/fudforum/3814588639/theme/default/images/msg_pm.gif" alt="" /></a>';
		} else {
			$user_profile = $email_link = $private_msg_link = '';
		}
		$edit_link = $obj->fldr == 4 ? '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=ppost&amp;msg_id='.$obj->id.'&amp;'._rsid.'"><img src="/egroupware/fudforum/3814588639/theme/default/images/msg_edit.gif" alt="" /></a>&nbsp;&nbsp;&nbsp;&nbsp;' : '';
		if ($obj->fldr == 1) {
			$reply_link = '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=ppost&amp;reply='.$obj->id.'&amp;'._rsid.'"><img src="/egroupware/fudforum/3814588639/theme/default/images/msg_reply.gif" alt="" /></a>&nbsp;';
			$quote_link = '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=ppost&amp;quote='.$obj->id.'&amp;'._rsid.'"><img src="/egroupware/fudforum/3814588639/theme/default/images/msg_quote.gif" alt="" /></a>&nbsp;';
		} else {
			$reply_link = $quote_link = '';
		}
		$profile_link = '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$obj->user_id.'&amp;'._rsid.'">'.$obj->alias.'</a>';
		$dmsg_user_info = '<br /><b>Posts:</b> '.$obj->posted_msg_count.'<br />
<b>Registered:</b> '.strftime("%B %Y", $obj->join_date).'
'.$location;
		$msg_toolbar = '<tr><td colspan="2" class="MsgToolBar"><table border=0 cellspacing=0 cellpadding=0 width="100%"><tr>
<td nowrap align="left">'.$user_profile.'&nbsp;'.$email_link.'&nbsp;'.$private_msg_link.'</td>
<td nowrap align="right"><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=pmsg&amp;'._rsid.'&amp;btn_delete=1&amp;sel='.$obj->id.'"><img src="/egroupware/fudforum/3814588639/theme/default/images/msg_delete.gif" alt="" /></a>&nbsp;'.$edit_link.$reply_link.$quote_link.'<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=ppost&amp;forward='.$obj->id.'&amp;'._rsid.'"><img src="/egroupware/fudforum/3814588639/theme/default/images/msg_forward.gif" alt="" /></a></td>
</tr></table></td></tr>';
	} else {
		$dmsg_user_info = $dmsg_tags = $dmsg_im_row = $user_profile = $msg_toolbar = $buddy_link = $avatar = $online_indicator = $host_name = $location = $msg_icon = '';
		$profile_link = $obj->alias;
	}
	$msg_body = $obj->length ? read_pmsg_body($obj->foff, $obj->length) : 'No Message Body';

	$file_attachments = '';
	if ($obj->attach_cnt) {
		$c = uq('SELECT a.id, a.original_name, a.dlcount, m.icon, a.fsize FROM phpgw_fud_attach a LEFT JOIN phpgw_fud_mime m ON a.mime_type=m.id WHERE a.message_id='.$obj->id.' AND attach_opt=1');
		while ($r = db_rowobj($c)) {
			$sz = $r->fsize/1024;
			$sz = $sz<1000 ? number_format($sz, 2).'KB' : number_format($sz / 1024 ,2).'MB';
			if(!$r->icon) {
				$r->icon = 'unknown.gif';
			}
			$file_attachments .= '<tr>
<td valign=middle><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=getfile&amp;id='.$r->id.'&amp;'._rsid.'&amp;private=1"><img src="images/mime/'.$r->icon.'" alt="" /></a></td>
<td><font class="GenText"><b>Attachment:</b></font> <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=getfile&amp;id='.$r->id.'&amp;'._rsid.'&amp;private=1">'.$r->original_name.'</a><br />
<font class="SmallText">(Size: '.$sz.', Downloaded '.$r->dlcount.' time(s))</font></td></tr>';
		}
		if ($file_attachments) {
			$file_attachments = '<p>
<table border=0 cellspacing=0 cellpadding=2>
'.$file_attachments.'
</table>';
		}
	}

	$signature = ($obj->sig && $o1 & 32768 && $obj->pmsg_opt & 1 && $b & 4096) ? '<p><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br />'.$obj->sig : '';

	return '<tr><td><table cellspacing=0 cellpadding=0 class="MsgTable">
<tr>
<td valign="top" align="left" class="MsgR1"><font class="MsgSubText">'.$msg_icon.$obj->subject.'</font></td>
<td valign="top" align="right" class="MsgR1"><font class="DateText">'.strftime("%a, %d %B %Y %H:%M", $obj->post_stamp).'</font></td>
</tr>
<tr class="MsgR2"><td class="MsgR2" colspan=2><table border="0" cellspacing="0" cellpadding="0" class="ContentTable">
<tr class="MsgR2">
'.$avatar.'
<td class="msgud">'.$online_indicator.$profile_link.$dmsg_user_info.'</td>
<td class="msgud">'.$dmsg_tags.'</td>
<td class="msgot">'.$buddy_link.$dmsg_im_row.'</td>
</tr></table></td>
</tr>
<tr><td class="MsgR3" colspan=2>'.$msg_body.$file_attachments.$signature.'</td></tr>
'.$msg_toolbar.'
<tr><td class="MsgR2" align="center" colspan=2>'.$GLOBALS['dpmsg_prev_message'].' '.$GLOBALS['dpmsg_next_message'].'</td></tr>
</table></td></tr>';
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
}class fud_pmsg
{
	var	$id, $to_list, $ouser_id, $duser_id, $pdest, $ip_addr, $host_name, $post_stamp, $icon, $fldr,
		$subject, $attach_cnt, $pmsg_opt, $length, $foff, $login, $ref_msg_id, $body;

	function add($track='')
	{
		$this->post_stamp = __request_timestamp__;
		$this->ip_addr = get_ip();
		$this->host_name = $GLOBALS['FUD_OPT_1'] & 268435456 ? "'".addslashes(get_host($this->ip_addr))."'" : 'NULL';

		if ($this->fldr != 1) {
			$this->read_stamp = $this->post_stamp;
		}

		list($this->foff, $this->length) = write_pmsg_body($this->body);

		$this->id = db_qid("INSERT INTO phpgw_fud_pmsg (
			ouser_id,
			duser_id,
			pdest,
			to_list,
			ip_addr,
			host_name,
			post_stamp,
			icon,
			fldr,
			subject,
			attach_cnt,
			read_stamp,
			ref_msg_id,
			foff,
			length,
			pmsg_opt
			) VALUES(
				".$this->ouser_id.",
				".$this->ouser_id.",
				".(isset($GLOBALS['recv_user_id']) ? intzero($GLOBALS['recv_user_id'][0]) : '0').",
				".strnull(addslashes($this->to_list)).",
				'".$this->ip_addr."',
				".$this->host_name.",
				".$this->post_stamp.",
				".strnull($this->icon).",
				".$this->fldr.",
				'".addslashes($this->subject)."',
				".(int)$this->attach_cnt.",
				".$this->read_stamp.",
				".strnull($this->ref_msg_id).",
				".(int)$this->foff.",
				".(int)$this->length.",
				".$this->pmsg_opt."
			)");

		if ($this->fldr == 3 && !$track) {
			$this->send_pmsg();
		}
	}

	function send_pmsg()
	{
		$this->pmsg_opt |= 16|32;
		$this->pmsg_opt &= 16|32|1|2|4;

		foreach($GLOBALS['recv_user_id'] as $v) {
			$id = db_qid("INSERT INTO phpgw_fud_pmsg (
				to_list,
				ouser_id,
				ip_addr,
				host_name,
				post_stamp,
				icon,
				fldr,
				subject,
				attach_cnt,
				foff,
				length,
				duser_id,
				ref_msg_id,
				pmsg_opt
			) VALUES (
				".strnull(addslashes($this->to_list)).",
				".$this->ouser_id.",
				'".$this->ip_addr."',
				".$this->host_name.",
				".$this->post_stamp.",
				".strnull($this->icon).",
				1,
				'".addslashes($this->subject)."',
				".intzero($this->attach_cnt).",
				".$this->foff.",
				".$this->length.",
				".$v.",
				".strnull($this->ref_msg_id).",
				".$this->pmsg_opt.")");
			$GLOBALS['send_to_array'][] = array($v, $id);
			$um[$v] = $id;
		}
		$c =  uq('SELECT id, email, users_opt, icq FROM phpgw_fud_users WHERE id IN('.implode(',', $GLOBALS['recv_user_id']).') AND users_opt>=64 AND (users_opt & 64) > 0');

		$from = $GLOBALS['usr']->alias;
		reverse_fmt($from);
		$subject = $this->subject;
		reverse_fmt($subject);

		while ($r = db_rowarr($c)) {
			/* do not send notifications about messages sent to self */
			if ($r[0] == $this->ouser_id) {
				continue;
			}
			if (!($r[2] & 4)) {
				$r[1] = $r[3] . '@pager.icq.com';
			}
			send_pm_notification($r[1], $um[$r[0]], $subject, $from, $r[2]);
		}
	}

	function sync()
	{
		$this->post_stamp = __request_timestamp__;
		$this->ip_addr = get_ip();
		$this->host_name = $GLOBALS['FUD_OPT_1'] & 268435456 ? "'".addslashes(get_host($this->ip_addr))."'" : 'NULL';

		list($this->foff, $this->length) = write_pmsg_body($this->body);

		q("UPDATE phpgw_fud_pmsg SET
			to_list=".strnull(addslashes($this->to_list)).",
			icon=".strnull($this->icon).",
			ouser_id=".$this->ouser_id.",
			duser_id=".$this->ouser_id.",
			post_stamp=".$this->post_stamp.",
			subject='".addslashes($this->subject)."',
			ip_addr='".$this->ip_addr."',
			host_name=".$this->host_name.",
			attach_cnt=".(int)$this->attach_cnt.",
			fldr=".$this->fldr.",
			foff=".(int)$this->foff.",
			length=".(int)$this->length.",
			pmsg_opt=".$this->pmsg_opt."
		WHERE id=".$this->id);

		if ($this->fldr == 3) {
			$this->send_pmsg();
		}
	}
}

function set_nrf($nrf, $id)
{
	q("UPDATE phpgw_fud_pmsg SET pmsg_opt=(pmsg_opt & ~ 96) | ".$nrf." WHERE id=".$id);
}

function write_pmsg_body($text)
{
	if (!db_locked()) {
		$ll = 1;
		db_lock('phpgw_fud_pmsg WRITE');
	}

	$fp = fopen($GLOBALS['MSG_STORE_DIR'].'private', 'ab');

	fseek($fp, 0, SEEK_END);
	if (!($s = ftell($fp))) {
		$s = __ffilesize($fp);
	}

	if (($len = fwrite($fp, $text)) !== strlen($text)) {
		exit("FATAL ERROR: system has ran out of disk space<br>\n");
	}
	fclose($fp);

	if (isset($ll)) {
		db_unlock();
	}

	if (!$s) {
		chmod($GLOBALS['MSG_STORE_DIR'].'private', ($GLOBALS['FUD_OPT_2'] & 8388608 ? 0600 : 0666));
	}

	return array($s, $len);
}

function read_pmsg_body($offset, $length)
{
	if (!$length) {
		return;
	}

	$fp = fopen($GLOBALS['MSG_STORE_DIR'].'private', 'rb');
	fseek($fp, $offset, SEEK_SET);
	$str = fread($fp, $length);
	fclose($fp);

	return $str;
}

function pmsg_move($mid, $fid, $validate)
{
	if (!$validate && !q_singleval('SELECT id FROM phpgw_fud_pmsg WHERE duser_id='._uid.' AND id='.$mid)) {
		return;
	}

	q('UPDATE phpgw_fud_pmsg SET fldr='.$fid.' WHERE duser_id='._uid.' AND id='.$mid);
}

function pmsg_del($mid, $fldr=null)
{
	if (is_null($fldr) && is_null(($fldr = q_singleval('SELECT fldr FROM phpgw_fud_pmsg WHERE duser_id='._uid.' AND id='.$mid)))) {
		return;
	}
	if ($fldr != 5) {
		pmsg_move($mid, 5, false);
	} else {
		q('DELETE FROM phpgw_fud_pmsg WHERE id='.$mid);
		$c = uq('SELECT id FROM phpgw_fud_attach WHERE message_id='.$mid.' AND attach_opt=1');
		while ($r = db_rowarr($c)) {
			@unlink($GLOBALS[''] . $r[0] . '.atch');
		}
		q('DELETE FROM phpgw_fud_attach WHERE message_id='.$mid.' AND attach_opt=1');
	}
}

function send_pm_notification($email, $pid, $subject, $from, $not_mthd)
{
	$sub = '['.$GLOBALS['FORUM_TITLE'].'] New Private Message Notification';

	if ($not_mthd == 'EMAIL') {
		$pfx = '';
		$body = 'You have a new private message titled "'.$subject.'" from "'.$from.'" on the "'.$GLOBALS['FORUM_TITLE'].'" forum.\nTo view the message click here: /egroupware/fudforum/3814588639/index.php?t=pmsg_view&id='.$pid.'\n\nTo stop future notifications, disable "Private Message Notification" in your profile.';
	} else {
		$body = 'You have a new private message titled "'.$subject.'" from "'.$from.'" on the "'.$GLOBALS['FORUM_TITLE'].'" forum.\n\nTo stop future notifications, disable "Private Message Notification" in your profile.';
	}

	send_email($GLOBALS['NOTIFY_FROM'], $email, $sub, $body);
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

	if (!($FUD_OPT_1 & 1024)) {
		error_dialog('ERROR: Private Messaging Disabled', 'You cannot use the private messaging system, it has been disabled by the administrator.');
	}
	if (!_uid) {
		std_error('login');
	}

$tabs = '';
if (_uid) {
	$tablist = array(
'Settings'=>'register',
'Subscriptions'=>'subscribed',
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
			$tablist['Private Messaging'] = 'pmsg';
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

	if (!isset($_GET['id']) || !($id = (int)$_GET['id'])) {
		invl_inp_err();
	}

	$m = db_sab('SELECT
		p.*,
		u.id AS user_id, u.alias, u.users_opt, u.avatar_loc, u.email, u.posted_msg_count, u.join_date,
		u.location, u.sig, u.icq, u.aim, u.msnm, u.yahoo, u.jabber, u.affero, u.custom_status, u.last_visit,
		l.name AS level_name, l.level_opt, l.img AS level_img
	FROM
		phpgw_fud_pmsg p
		INNER JOIN phpgw_fud_users u ON p.ouser_id=u.id
		LEFT JOIN phpgw_fud_level l ON u.level_id=l.id
	WHERE p.duser_id='._uid.' AND p.id='.$id);

	if (!$m) {
		invl_inp_err();
	}

	ses_update_status($usr->sid, 'Using private messaging');

	$cur_ppage = tmpl_cur_ppage($m->fldr, $folders, $m->subject);

	/* Next Msg */
	if (($nid = q_singleval('SELECT p.id FROM phpgw_fud_pmsg p INNER JOIN phpgw_fud_users u ON u.id=p.ouser_id WHERE p.duser_id='._uid.' AND p.fldr='.$m->fldr.' AND post_stamp>'.$m->post_stamp.' ORDER BY p.post_stamp ASC LIMIT 1'))) {
		$dpmsg_next_message = '<a href="/egroupware/fudforum/3814588639/index.php?t=pmsg_view&amp;'._rsid.'&amp;id='.$nid.'" class="GenLink">Next message <img src="/egroupware/fudforum/3814588639/theme/default/images/goto.gif" alt="" /></a>';
	} else {
		$dpmsg_next_message = '';
	}

	/* Prev Msg */
	if (($pid = q_singleval('SELECT p.id FROM phpgw_fud_pmsg p INNER JOIN phpgw_fud_users u ON u.id=p.ouser_id WHERE p.duser_id='._uid.' AND p.fldr='.$m->fldr.' AND p.post_stamp<'.$m->post_stamp.' ORDER BY p.post_stamp DESC LIMIT 1'))) {
		$dpmsg_prev_message = '<a href="/egroupware/fudforum/3814588639/index.php?t=pmsg_view&amp;'._rsid.'&amp;id='.$pid.'" class="GenLink"><img src="/egroupware/fudforum/3814588639/theme/default/images/goback.gif" alt="" /> Previous message</a>';
	} else {
		$dpmsg_prev_message = '';
	}

	$private_message_entry = tmpl_drawpmsg($m, $usr, false);

	if (!$m->read_stamp && $m->pmsg_opt & 16) {
		q('UPDATE phpgw_fud_pmsg SET read_stamp='.__request_timestamp__.', pmsg_opt=(pmsg_opt & ~ 4) |8 WHERE id='.$m->id);
		if ($m->ouser_id != _uid && $m->pmsg_opt & 4 && !isset($_GET['dr'])) {
			$track_msg = new fud_pmsg;
			$track_msg->ouser_id = $track_msg->duser_id = $m->ouser_id;
			$track_msg->ip_addr = $track_msg->host_name = null;
			$track_msg->post_stamp = __request_timestamp__;
			$track_msg->read_stamp = 0;
			$track_msg->fldr = 1;
			$track_msg->pmsg_opt = 16|32;
			$track_msg->subject = 'Read Notification For: '.$m->subject;
			$track_msg->body = 'Hello,<br>'.$usr->login.' has read your private message, "'.$m->subject.'", on '.strftime("%a, %d %B %Y %H:%M", $m->post_stamp).'<br>';
			$track_msg->add(1);
		}
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
<?php echo $cur_ppage; ?>
<?php echo $tabs; ?>
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th colspan=2>Author</th>
<?php echo $private_message_entry; ?>
</table>
<?php echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>	