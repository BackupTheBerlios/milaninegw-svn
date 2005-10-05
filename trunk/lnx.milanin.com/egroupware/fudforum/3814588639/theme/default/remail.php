<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: remail.php.t,v 1.1.1.1 2003/10/17 21:11:30 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
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
}$GLOBALS['__error__'] = 0;

function set_err($err, $msg)
{
	$GLOBALS['__err_msg__'][$err] = $msg;
	$GLOBALS['__error__'] = 1;
}

function is_post_error()
{
	return $GLOBALS['__error__'];
}

function get_err($err, $br=0)
{
	if(isset($err) && isset($GLOBALS['__err_msg__'][$err])) {
		return ($br ? '<font class="ErrorText">'.$GLOBALS['__err_msg__'][$err].'</font><br />' : '<br /><font class="ErrorText">'.$GLOBALS['__err_msg__'][$err].'</font>');
	}
}

function post_check_images()
{
	if ($GLOBALS['MAX_IMAGE_COUNT'] && $GLOBALS['MAX_IMAGE_COUNT'] < count_images($_POST['msg_body'])) {
		return -1;
	}

	return 0;
}

function check_post_form()
{
	/* make sure we got a valid subject */
	if (!strlen(trim($_POST['msg_subject']))) {
		set_err('msg_subject', 'Subject required');
	}

	/* make sure the number of images [img] inside the body do not exceed the allowed limit */
	if (post_check_images()) {
		set_err('msg_body', 'Maximum '.$GLOBALS['MAX_IMAGE_COUNT'].' images are allowed per post, please decrease the number of images');
	}

	return $GLOBALS['__error__'];
}

function check_ppost_form($msg_subject)
{
	if (!strlen(trim($msg_subject))) {
		set_err('msg_subject', 'Subject required');
	}

	if (post_check_images()) {
		set_err('msg_body', 'Maximum '.$GLOBALS['MAX_IMAGE_COUNT'].' images are allowed per post, please decrease the number of images');
	}
	$list = explode(';', $_POST['msg_to_list']);
	foreach($list as $v) {
		$v = trim($v);
		if (strlen($v)) {
			if (!($obj = db_sab('SELECT u.users_opt, u.id, ui.ignore_id FROM phpgw_fud_users u LEFT JOIN phpgw_fud_user_ignore ui ON ui.user_id=u.id AND ui.ignore_id='._uid.' WHERE u.alias='.strnull(addslashes(htmlspecialchars($v)))))) {
				set_err('msg_to_list', 'There is no user named "'.htmlspecialchars($v).'" this forum');
				break;
			}
			if (!empty($obj->ignore_id)) {
				set_err('msg_to_list', 'You cannot send a private message to "'.htmlspecialchars($v).'", because this person is ignoring you.');
				break;
			} else if (!($obj->users_opt & 32) && !($GLOBALS['usr']->users_opt & 1048576)) {
				set_err('msg_to_list', 'You cannot send a private message to "'.htmlspecialchars($v).'", because this person is not accepting private messages.');
				break;
			} else {
				$GLOBALS['recv_user_id'][] = $obj->id;
			}
		}
	}

	if (empty($_POST['msg_to_list'])) {
		set_err('msg_to_list', 'Cannot send a message, missing recipient');
	}

	return $GLOBALS['__error__'];
}

function check_femail_form()
{
	if (empty($_POST['femail']) || validate_email($_POST['femail'])) {
		set_err('femail', 'Please enter a valid email address of your friend.');
	}
	if (empty($_POST['subj'])) {
		set_err('subj', 'You cannot send an email without a subject.');
	}
	if (empty($_POST['body'])) {
		set_err('body', 'You cannot send an email without the message body.');
	}

	return $GLOBALS['__error__'];
}

function count_images($text)
{
	$text = strtolower($text);
	$a = substr_count($text, '[img]');
	$b = substr_count($text, '[/img]');

	return (($a > $b) ? $b : $a);
}include $GLOBALS['FORUM_SETTINGS_PATH'] . 'ip_filter_cache';
	include $GLOBALS['FORUM_SETTINGS_PATH'] . 'login_filter_cache';
	include $GLOBALS['FORUM_SETTINGS_PATH'] . 'email_filter_cache';

function is_ip_blocked($ip)
{
	if (!count($GLOBALS['__FUD_IP_FILTER__'])) {
		return;
	}
	$block =& $GLOBALS['__FUD_IP_FILTER__'];
	list($a,$b,$c,$d) = explode('.', $ip);

	if (!isset($block[$a])) {
		return;
	}
	if (isset($block[$a][$b][$c][$d])) {
		return 1;
	}

	if (isset($block[$a][256])) {
		$t = $block[$a][256];
	} else if (isset($block[$a][$b])) {
		$t = $block[$a][$b];
	} else {
		return;
	}

	if (isset($t[$c])) {
		$t = $t[$c];
	} else if (isset($t[256])) {
		$t = $t[256];
	} else {
		return;
	}

	return (isset($t[$d]) || isset($t[256])) ? 1 : null;
}

function is_login_blocked($l)
{
	foreach ($GLOBALS['__FUD_LGN_FILTER__'] as $v) {
		if (preg_match($v, $l)) {
			return 1;
		}
	}
	return;
}

function is_email_blocked($addr)
{
	if (!count($GLOBALS['__FUD_EMAIL_FILTER__'])) {
		return;
	}
	$addr = strtolower($addr);
	foreach ($GLOBALS['__FUD_EMAIL_FILTER__'] as $k => $v) {
		if (($v && (strpos($addr, $k) !== false)) || (!$v && preg_match($k, $addr))) {
			return 1;
		}
	}
	return;
}

function is_allowed_user(&$usr)
{
	if ($GLOBALS['FUD_OPT_2'] & 1024 && $usr->users_opt & 2097152) {
		error_dialog('Unverified Account', 'The administrator had chosen to review all accounts manually prior to activation. Until your account has been validated by the administrator you will not be able to utilize the full capabilities of your account.');
	}

	if ($usr->users_opt & 65536 || is_email_blocked($usr->email) || is_login_blocked($usr->login) || is_ip_blocked(get_ip())) {
		error_dialog('ERROR: you aren&#39;t allowed to post', 'Your account has been blocked from posting');
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

	if (isset($_POST['done'])) {
		check_return($usr->returnto);
	}

	is_allowed_user($usr);

	if ((isset($_GET['th']) && ($th = (int)$_GET['th'])) || (isset($_POST['th']) && ($th = (int)$_POST['th']))) {
		$data = db_sab('SELECT m.subject, t.id, mm.id AS md, (CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) AS gco
				FROM phpgw_fud_thread t
				INNER JOIN phpgw_fud_msg m ON t.root_msg_id=m.id
				LEFT JOIN phpgw_fud_mod mm ON mm.forum_id=t.forum_id AND mm.user_id='._uid.'
				INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id='.(_uid ? '2147483647' : '0').' AND g1.resource_id=t.forum_id
				LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=t.forum_id
				WHERE t.id='.$th);

	}
	if (empty($data)) {
		invl_inp_err();
	}
	if (!($usr->users_opt & 1048576) && !$data->md && !($data->gco & 2)) {
		std_error('access');
	}



	if (isset($_POST['posted']) && _uid && !check_femail_form()) {
		$to = empty($POST['fname']) ? $_POST['femail'] : $_POST['fname'].' <'.$_POST['femail'].'>';
		$from = $usr->alias. '<'.$usr->email.'>';
		send_email($from, $to, $_POST['subj'], $_POST['body']);

		error_dialog('Email was sent', 'The email to your friend at '.htmlspecialchars($_POST['femail']).' about the '.$data->subject.' topic was successfully sent.');
	} else if (!isset($_POST['posted'])) {
		$def_thread_view = $FUD_OPT_2 & 4 ? 'msg' : 'tree';
	}

	$remail_error = is_post_error() ? '<h4 align="center"><font class="ErrorText">You have an error</font></h4>' : '';

	$body = isset($_POST['body']) ? htmlspecialchars($_POST['body']) : 'Hello,\n\nThere is an interesting topic about "'.$data->subject.'" on '.$GLOBALS['FORUM_TITLE'].' forum that you may to want read. You can see the topic at:\n /egroupware/fudforum/3814588639/index.php?t='.$def_thread_view.'&amp;th='.$data->id.'&amp;rid='._uid.'\n\n Your friend,\n\n'.$usr->alias.'\n';

	if (_uid) {
		$femail_error = get_err('femail');
		$subject_error = get_err('subj');
		$body_error = get_err('body');

		$fname = isset($_POST['fname']) ? $_POST['fname'] : '';
		$femail = isset($_POST['femail']) ? $_POST['femail'] : '';
		$subject = isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : $data->subject;

		$form_data = '<tr class="'.alt_var('page_alt','RowStyleA','RowStyleB').'"><td class="GenText" nowrap>Your Name:</td><td width="100%">'.$usr->alias.'</td></tr>
<tr class="'.alt_var('page_alt','RowStyleA','RowStyleB').'"><td class="GenText" nowrap>Your Email:</td><td width="100%">'.$usr->email.'</td></tr>
<tr class="'.alt_var('page_alt','RowStyleA','RowStyleB').'"><td class="GenText" nowrap>Friend&#39;s Name:</td><td width="100%"><input type="text" name="fname" value="'.htmlspecialchars($fname).'"></td></tr>
<tr class="'.alt_var('page_alt','RowStyleA','RowStyleB').'"><td class="GenText" valign="top" nowrap><font class="SmallText">Friend&#39;s Email:<br /><i>required</i></font></td><td valign="top"><input type="text" name="femail" value="'.htmlspecialchars($femail).'">'.$femail_error.'</td></tr>
<tr class="'.alt_var('page_alt','RowStyleA','RowStyleB').'"><td class="GenText" valign="top" nowrap><font class="SmallText">Subject:<br /><i>required</i></font></td><td nowrap valign="top"><input type="text" name="subj" value="'.$subject.'">'.$subject_error.'</td></tr>
<tr class="'.alt_var('page_alt','RowStyleA','RowStyleB').'"><td class="GenText" valign="top" nowrap>Message:<font class="SmallText"><br /><i>required</i></font></td><td valign="top" nowrap><textarea name="body" rows="19" cols="78" wrap="PHYSICAL">'.$body.'</textarea>'.$body_error.'</td></tr>
<tr class="'.alt_var('page_alt','RowStyleA','RowStyleB').'"><td class="GenText" colspan=2 align="right"><input type="submit" class="button" name="submit" value="Send Email Now"></td></tr>';
	} else {
		$form_data = '<tr class="'.alt_var('page_alt','RowStyleA','RowStyleB').'"><td class="GenText" align="center"><font class="SmallText">Copy this message into a mail client of your choice to send it to your friend(s).</font></td></tr>
<tr class="'.alt_var('page_alt','RowStyleA','RowStyleB').'"><td class="GenText"><textarea name="body" rows="19" cols="78">'.$body.'</textarea></td></tr>
<tr class="'.alt_var('page_alt','RowStyleA','RowStyleB').'"><td class="GenText" align="right"><input type="submit" class="button" name="done" value="Done"></td></tr>';
	}
	$form_data = str_replace('\n', "\n", $form_data);


?>
<?php echo $GLOBALS['fud_egw_hdr']; ?>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">
<div align=center>
<?php echo $remail_error; ?>
<form action="/egroupware/fudforum/3814588639/index.php?t=remail" name="remail" method="post"><input type="hidden" name="posted" value="1">
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th colspan=2>Email This Topic To A Friend</th></tr>
<?php echo $form_data; ?>
</table>
<?php echo _hs; ?><input type="hidden" name="th" value="<?php echo $th; ?>"></form>
</div>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>