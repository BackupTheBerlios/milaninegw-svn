<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: thr_exch.php.t,v 1.1.1.1 2003/10/17 21:11:28 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function send_status_update($uid, $ulogin, $uemail, $title, $msg)
{
	if ($GLOBALS['FUD_OPT_1'] & 1024) {
		if (defined('no_inline')) {
			fud_use('private.inc');
			fud_use('iemail.inc');
			fud_use('rev_fmt.inc');
		}
		$GLOBALS['recv_user_id'][] = $uid;
		$pmsg = new fud_pmsg;
		$pmsg->to_list = addslashes($ulogin);
		$pmsg->ouser_id = _uid;
		$pmsg->post_stamp = __request_timestamp__;
		$pmsg->subject = addslashes($title);
		$pmsg->host_name = 'NULL';
		$pmsg->ip_addr = '0.0.0.0';
		list($pmsg->foff, $pmsg->length) = write_pmsg_body(nl2br($msg));
		$pmsg->send_pmsg();
	} else {
		if (defined('no_inline')) {
			fud_use('iemail.inc');
		}
		send_email($GLOBALS['NOTIFY_FROM'], $uemail, $title, $msg);
	}
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
}function logaction($user_id, $res, $res_id=0, $action=null)
{
	q('INSERT INTO phpgw_fud_action_log (logtime, logaction, user_id, a_res, a_res_id)
		VALUES('.__request_timestamp__.', '.strnull($action).', '.$user_id.', '.strnull($res).', '.(int)$res_id.')');
}

function clear_action_log()
{
	q('DELETE FROM phpgw_fud_action_log');
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
}

	/* only admins & moderators have access to this control panel */
	if (!_uid || !($usr->users_opt & (1048576|524288))) {
		std_error('access');
	}

	if (isset($_GET['appr']) || isset($_GET['decl']) || isset($_POST['decl'])) {
		fud_use('thrx_adm.inc', true);
	}

	/* verify that we got a valid thread-x-change approval */
	if (isset($_GET['appr']) && ($thrx = thx_get((int)$_GET['appr']))) {
		$data = db_sab('SELECT
					t.forum_id, t.last_post_id, t.root_msg_id, t.last_post_date, t.last_post_id,
					f1.id, f1.last_post_id as f1_lpi, f2.last_post_id AS f2_lpi,
					'.($usr->users_opt & 1048576 ? ' 1 ' : ' mm.id ').' AS md
				FROM phpgw_fud_thread t
				INNER JOIN phpgw_fud_forum f1 ON t.forum_id=f1.id
				INNER JOIN phpgw_fud_forum f2 ON f2.id='.$thrx->frm.'
				LEFT JOIN phpgw_fud_mod mm ON mm.forum_id=f2.id AND mm.user_id='._uid.'
				WHERE t.id='.$thrx->th);
		if (!$data) {
			invl_inp_err();
		}
		if (!$data->md) {
			std_error('access');
		}

		th_move($thrx->th, $thrx->frm, $data->root_msg_id, $data->forum_id, $data->last_post_date, $data->last_post_id);

		if ($data->f1_lpi == $data->last_post_id) {
			$mid = (int) q_singleval('SELECT MAX(last_post_id) FROM phpgw_fud_thread t INNER JOIN phpgw_fud_msg m ON t.root_msg_id=m.id WHERE t.forum_id='.$data->forum_id.' AND t.moved_to=0 AND m.apr=1');
			q('UPDATE phpgw_fud_forum SET last_post_id='.$mid.' WHERE id='.$data->forum_id);
		}

		if ($data->f2_lpi < $data->last_post_id) {
			q('UPDATE phpgw_fud_forum SET last_post_id='.$data->last_post_id.' WHERE id='.$thrx->frm);
		}

		thx_delete($thrx->id);
		logaction($usr->id, 'THRXAPPROVE', $thrx->th);
	} else if ((isset($_GET['decl']) || isset($_POST['decl'])) && ($thrx = thx_get(($decl = (int)(isset($_GET['decl']) ? $_GET['decl'] : $_POST['decl']))))) {
		$data = db_sab('SELECT u.email, u.login, u.id, m.subject, f1.name AS f1_name, f2.name AS f2_name, '.($usr->users_opt & 1048576 ? ' 1 ' : ' mm.id ').' AS md
				FROM phpgw_fud_thread t
				INNER JOIN phpgw_fud_forum f1 ON t.forum_id=f1.id
				INNER JOIN phpgw_fud_forum f2 ON f2.id='.$thrx->frm.'
				INNER JOIN phpgw_fud_msg m ON m.id=t.root_msg_id
				INNER JOIN phpgw_fud_users u ON u.id='.$thrx->req_by.'
				LEFT JOIN phpgw_fud_mod mm ON mm.forum_id='.$thrx->frm.' AND mm.user_id='._uid.'
				WHERE t.id='.$thrx->th);
		if (!$data) {
			invl_inp_err();
		}
		if (!$data->md) {
			std_error('access');
		}

		if (!empty($_POST['reason'])) {
			send_status_update($data->id, $data->login, $data->email, 'Moving of topic '.$data->subject.' into forum '.htmlspecialchars($data->f2_name).' was declined.', htmlspecialchars($_POST['reason']));
			thx_delete($thrx->id);
			$decl = null;
		} else {
			$thr_exch_data = '<form method="post" action="/egroupware/fudforum/3814588639/index.php?t=thr_exch" name="thr_exch">
'._hs.'<input type="hidden" name="decl" value="'.$decl.'">
<tr><td class="RowStyleC">Reason for declining topic <b>'.$data->subject.'</b> into forum <b>'.htmlspecialchars($data->f2_name).'</b><br /><textarea name="reason" cols=60 rows=10></textarea><br /><input type="submit" class="button" name="btn_submit" value="Submit"></td><tr>
</form>';
		}

		logaction($usr->id, 'THRXDECLINE', $thrx->th);
	}



	if (!isset($decl)) {
		$thr_exch_data = '';

		$r = uq('SELECT
				thx.*, m.subject, f1.name AS sf_name, f2.name AS df_name, u.alias
			 FROM phpgw_fud_thr_exchange thx
			 LEFT JOIN phpgw_fud_mod mm ON mm.forum_id=f2.id AND mm.user_id='._uid.'
			 INNER JOIN phpgw_fud_thread t ON thx.th=t.id
			 INNER JOIN phpgw_fud_msg m ON t.root_msg_id=m.id
			 INNER JOIN phpgw_fud_forum f1 ON t.forum_id=f1.id
			 INNER JOIN phpgw_fud_forum f2 ON thx.frm=f2.id
			 INNER JOIN phpgw_fud_users u ON thx.req_by=u.id
			 WHERE '.($usr->users_opt & 1048576 ? '1=1' : ' mm.id IS NOT NULL'));

		while ($obj = db_rowobj($r)) {
			$thr_exch_data .= '<tr><td>
<table border=0 cellspacing=0 cellpadding=3 width="100%">
	<tr class="RowStyleB">
		<td align=left nowrap valign=top>
			<font class="SmallText"><b>Topic move requested by:</b> <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$obj->req_by.'&amp;'._rsid.'">'.$obj->alias.'</a><br /></font>
		</td>
		<td align=center width="100%" valign=top>
			<font class="SmallText">
			<b>Reason for topic move:</b><br />
			<table border=1 cellspacing=1 cellpadding=0><tr><td align=left>&nbsp;'.$obj->reason_msg.' &nbsp;</td></tr></table>
			</font>
		</td>
	</tr>
	<tr class="RowStyleC">
		<td colspan=2>
			<table border=0 cellspacing=0 cellpadding=3 width="100%">
			<tr>
				<td align="left"><font size="-1"><b>Original Forum:</b> '.$obj->sf_name.'<br /><b>Destination Forum:</b> '.$obj->df_name.'<br /><b>Topic:</b> <a href="/egroupware/fudforum/3814588639/index.php?t=msg&amp;'._rsid.'&amp;th='.$obj->th.'">'.$obj->subject.'</a></font></td>
				<td align="right">[<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=thr_exch&amp;appr='.$obj->id.'&amp;'._rsid.'">accept topic</a>]&nbsp;&nbsp;[<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=thr_exch&amp;decl='.$obj->id.'&amp;'._rsid.'">decline topic</a>]</td>
			</tr>
			</table>
		</td>
	</tr>		
</table>
</td></tr>';
		}

		if (!$thr_exch_data) {
			$thr_exch_data = '<tr><td class="RowStyleA" align="center">No topics waiting approval.</td></tr>';
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
<table border="0" cellspacing="0" cellpadding="0" class="ContentTable">
<tr><th>Topic Exchange</th></tr>
<?php echo $thr_exch_data; ?>
</table>
<?php echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>