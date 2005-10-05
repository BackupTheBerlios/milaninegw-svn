<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: pmsg.php.t,v 1.2 2003/12/18 18:20:49 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
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
}$folders = array(1=>'Inbox', 2=>'Saved', 4=>'Draft', 3=>'Sent', 5=>'Trash');

function tmpl_cur_ppage($folder_id, $folders, $msg_subject='')
{
	if (!$folder_id || (!$msg_subject && $_GET['t'] == 'ppost')) {
		$user_action = 'Writing a Private Message';
	} else {
		$user_action = $msg_subject ? '<a href="/egroupware/fudforum/3814588639/index.php?t=pmsg&amp;folder_id='.$folder_id.'&amp;'._rsid.'" class="GenLink">'.$folders[$folder_id].'</a> &raquo; '.$msg_subject : 'Browsing <b>'.$folders[$folder_id].'</b> folder';
	}

	return '<font class="SmallText"><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=pmsg&amp;'._rsid.'">Private Messaging</a>&nbsp;&raquo;&nbsp;'.$user_action.'</font><br /><img src="blank.gif" alt="" height=4 width=1 /><br />';
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

	/* moving or deleting a message */
	if (isset($_POST['sel']) || isset($_GET['sel'])) {
		$sel = isset($_POST['sel']) ? $_POST['sel'] : $_GET['sel'];
		if (!is_array($sel)) {
			$sel = array($sel);
		}
		$move_to = (!isset($_POST['btn_delete']) && isset($_POST['moveto'], $folders[$_POST['moveto']])) ? (int) $_POST['moveto'] : 0;
		foreach ($sel as $m) {
			if ($move_to) {
				pmsg_move((int)$m, $move_to, false);
			} else {
				pmsg_del((int)$m);
			}
		}
	}

	if (isset($_GET['folder_id']) && isset($folders[$_GET['folder_id']])) {
		$folder_id = $_GET['folder_id'];
	} else if (isset($_POST['folder_id']) && isset($folders[$_POST['folder_id']])) {
		$folder_id = $_POST['folder_id'];
	} else {
		$folder_id = 1;
	}

	ses_update_status($usr->sid, 'Using private messaging');

	$cur_ppage = tmpl_cur_ppage($folder_id, $folders);

	$lnk = $folder_id == 4 ? '/egroupware/fudforum/3814588639/index.php?t=pmsg&amp;msg_id' : '';
	$author_dest_col = $folder_id == 3 ? 'Recipient' : 'Author';

	$select_options_cur_folder = tmpl_draw_select_opt(implode("\n", array_keys($folders)), implode("\n", $folders), $folder_id, '', '');

	$disk_usage = q_singleval('SELECT SUM(length) FROM phpgw_fud_pmsg WHERE duser_id='._uid);
	$percent_full = ceil($disk_usage / $MAX_PMSG_FLDR_SIZE * 100);
	$full_indicator = ceil($percent_full * 1.69);

	if ($percent_full < 90) {
		$full_indicator = '<td class="pmSn"><img src="blank.gif" alt="" width='.$full_indicator.' height="8" /></td>';
	} else if ($percent_full >= 90 && $percent_full < 100) {
		$full_indicator = '<td class="pmSa"><img src="blank.gif" alt="" width='.$full_indicator.' height="8" /></td>';
	} else {
		$full_indicator = '<td class="pmSf"><img src="blank.gif" alt="" width='.$full_indicator.' height="8" /></td>';
	}

	if (($all_v = empty($_GET['all']))) {
		$desc = 'all';
	} else {
		$desc = 'none';
	}

	$ttl = q_singleval("SELECT count(*) FROM phpgw_fud_pmsg WHERE duser_id="._uid." AND fldr=".$folder_id);
	$count = $usr->posts_ppg ? $usr->posts_ppg : $POSTS_PER_PAGE;
	$start = (empty($_GET['start']) || $_GET['start'] >= $ttl) ? 0 : (int) $_GET['start'];

	$c = uq('SELECT p.id, p.read_stamp, p.post_stamp, p.duser_id, p.ouser_id, p.subject, p.pmsg_opt, p.fldr, p.pdest,
			u.users_opt, u.alias, u.last_visit AS time_sec,
			u2.users_opt AS users_opt2, u2.alias AS alias2, u2.last_visit AS time_sec2
		FROM phpgw_fud_pmsg p
		INNER JOIN phpgw_fud_users u ON p.ouser_id=u.id
		LEFT JOIN phpgw_fud_users u2 ON p.pdest=u2.id
		WHERE duser_id='._uid.' AND fldr='.$folder_id.' ORDER BY post_stamp DESC LIMIT '.qry_limit($count, $start));

	$private_msg_entry = '';
	while ($obj = db_rowobj($c)) {
		switch ($obj->fldr) {
			case 1:
			case 2:
				$action = '<a href="/egroupware/fudforum/3814588639/index.php?t=ppost&amp;'._rsid.'&amp;reply='.$obj->id.'" class="GenLink"><img src="/egroupware/fudforum/3814588639/theme/default/images/msg_reply.gif" alt="" /></a>&nbsp;<a href="/egroupware/fudforum/3814588639/index.php?t=ppost&amp;quote='.$obj->id.'&amp;'._rsid.'" class="GenLink"><img src="/egroupware/fudforum/3814588639/theme/default/images/msg_quote.gif" alt="" /></a>&nbsp;<a href="/egroupware/fudforum/3814588639/index.php?t=ppost&amp;forward='.$obj->id.'&amp;'._rsid.'" class="GenLink"><img src="/egroupware/fudforum/3814588639/theme/default/images/msg_forward.gif" alt="" /></a>';
				break;
			case 3:
				$obj->users_opt = $obj->users_opt2;
				$obj->alias = $obj->alias2;
				$obj->time_sec = $obj->time_sec2;
				$obj->ouser_id = $obj->pdest;
				$action = '';
				break;
			case 5:
				$action = '<a href="/egroupware/fudforum/3814588639/index.php?t=ppost&amp;'._rsid.'&amp;forward='.$obj->id.'" class="GenLink"><img src="/egroupware/fudforum/3814588639/theme/default/images/msg_forward.gif" alt="" /></a>';
				break;
			case 4:
				$action = '<a href="/egroupware/fudforum/3814588639/index.php?t=ppost&amp;'._rsid.'&amp;msg_id='.$obj->id.'" class="GenLink"><img src="/egroupware/fudforum/3814588639/theme/default/images/msg_edit.gif" alt="" /></a>';
				break;
		}
		$goto = $folder_id != 4 ? '/egroupware/fudforum/3814588639/index.php?t=pmsg_view&amp;'._rsid.'&amp;id='.$obj->id : '/egroupware/fudforum/3814588639/index.php?t=ppost&amp;'._rsid.'&amp;msg_id='.$obj->id;

		$pmsg_status = $obj->read_stamp ? '<img src="/egroupware/fudforum/3814588639/theme/default/images/pmsg_read.png" width=32 height=32 alt="Read private message" title="Read private message" />' : '<img src="/egroupware/fudforum/3814588639/theme/default/images/pmsg_unread.png" width=32 height=32 alt="Unread private message" title="Unread private message" />';
		if ($obj->pmsg_opt & 4 && $obj->pmsg_opt & 16 && $obj->duser_id == _uid && $obj->ouser_id != _uid) {
			$deny_recipt = '<font class="SmallText">&nbsp;&nbsp;[<a href="/egroupware/fudforum/3814588639/index.php?t=pmsg_view&amp;'._rsid.'&amp;dr=1&amp;id='.$obj->id.'" class="GenLink" title="Do not send a confirmation that you&#39;ve read this message">deny receipt</a>]</font>';
		} else {
			$deny_recipt = '';
		}

		if ($FUD_OPT_2 & 32 && (!($obj->users_opt & 32768) || $usr->users_opt & 1048576)) {
			$obj->login =& $obj->alias;
			if (($obj->time_sec + $LOGEDIN_TIMEOUT * 60) > __request_timestamp__) {
				$online_indicator = '<img src="/egroupware/fudforum/3814588639/theme/default/images/online.gif" alt="'.$obj->login.' is currently online" title="'.$obj->login.' is currently online" />&nbsp;';
			} else {
				$online_indicator = '<img src="/egroupware/fudforum/3814588639/theme/default/images/offline.gif" alt="'.$obj->login.'  is currently offline" title="'.$obj->login.'  is currently offline" />&nbsp;';
			}
		} else {
			$online_indicator = '';
		}

		if ($obj->pmsg_opt & 64) {
			$msg_type ='<font class="SmallText">(replied)</font>&nbsp;';
		} else if ($obj->pmsg_opt & 32) {
			$msg_type = '';
		} else {
			$msg_type ='<font class="SmallText">(forwarded)</font>&nbsp;';
		}

		$checked = !$all_v ? ' checked' : '';

		$private_msg_entry .= '<tr class="RowStyleB"><td>'.$pmsg_status.'</td><td width="100%" class="GenText">'.$msg_type.'<a href="'.$goto.'" class="GenLink">'.$obj->subject.'</a>'.$deny_recipt.'</td>
<td nowrap class="GenText">'.$online_indicator.'<a href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;'._rsid.'&amp;id='.$obj->ouser_id.'" class="GenLink">'.$obj->alias.'</a></td>
<td nowrap class="DateText">'.strftime("%a, %d %B %Y %H:%M", $obj->post_stamp).'</td>
<td nowrap align="center" class="GenText">'.$action.'</td>
<td align="center" class="GenText"><input type="checkbox" name="sel[]" value="'.$obj->id.'"'.$checked.'></td></tr>';
	}

	if (!$private_msg_entry) {
		$private_msg_entry = '<tr class="RowStyleC"><td colspan="6" align="center">There are no messages inside this folder</td></tr>';
		$private_tools = '';
	} else {
		$btn_action = $folder_id == 5 ? 'Restore To:' : 'Move To:';
		unset($folders[$folder_id]);
		$moveto_list = tmpl_draw_select_opt(implode("\n", array_keys($folders)), implode("\n", $folders), '', '', '');
		$private_tools = '<tr class="RowStyleB"><td colspan=6 class="GenText" align=right>
<input type="submit" class="button" name="btn_move" value="'.$btn_action.'">
<select name="moveto">'.$moveto_list.'</select>
&nbsp;&nbsp;&nbsp;<input type="submit" class="button" name="btn_delete" value="Delete"></td></tr>';
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
<table cellspacing="1" cellpadding="2" border="0" class="pmDu">
<tr>
	<td colspan="3" width="100%" class="RowStyleA" nowrap><span class="SmallText">Your PMs folders are <?php echo $percent_full; ?>% full.</span></td>
</tr>
<tr>
	<td colspan="3" width="100%" class="RowStyleB"><table cellspacing="0" cellpadding="1" border="0"><tr><?php echo $full_indicator; ?></tr></table></td>
</tr>
<tr class="RowStyleA">
	<td class="SmallText" width="58" >0%</td>
	<td class="SmallText" width="58" align="center">50%</td>
	<td width="58" align="right"><table cellspacing=0 cellpadding=0 border=0><tr><td width=58 class="SmallText" align="right">100%</td></tr></table></td>
</tr>
</table>
<div align="right"><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=ppost&amp;<?php echo _rsid; ?>"><img src="/egroupware/fudforum/3814588639/theme/default/images/new_pm.gif" alt="" /></a></div><img src="blank.gif" alt="" width=1 height=2 />
<?php echo $tabs; ?>
<form action="/egroupware/fudforum/3814588639/index.php?t=pmsg" method="post" name="priv_frm"><?php echo _hs; ?>
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr class="RowStyleB"><td colspan=6 class="GenText" align="right">
Folder: <select name="folder_id" onChange="javascript: document.priv_frm.submit();">
<?php echo $select_options_cur_folder; ?>
</select> <input type="submit" class="button" name="sel_folder" value="Go">
</td></tr>
<tr>
	<th>&nbsp;</th>
	<th width="100%">Subject</th>
	<th align="center"><?php echo $author_dest_col; ?></th>
	<th align="center">Time</th>
	<th align="center">Action</th>
	<th nowrap>Selected [<a href="/egroupware/fudforum/3814588639/index.php?t=pmsg&amp;folder_id=<?php echo $folder_id; ?>&amp;<?php echo _rsid; ?>&amp;all=<?php echo $all_v; ?>" class="thLnk"><?php echo $desc; ?></a>]</th>
</tr>
<?php echo $private_msg_entry; ?>
<?php echo $private_tools; ?>
</table></form>
<?php echo $page_pager; ?>
<div style="padding-top: 2px;" align="right"><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=ppost&amp;<?php echo _rsid; ?>"><img src="/egroupware/fudforum/3814588639/theme/default/images/new_pm.gif" alt="" /></a></div>
<?php echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>