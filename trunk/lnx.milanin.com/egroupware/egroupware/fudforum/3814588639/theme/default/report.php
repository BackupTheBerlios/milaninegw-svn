<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: report.php.t,v 1.2 2003/12/18 18:20:49 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
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
}

	if ((!isset($_GET['msg_id']) || !($msg_id = (int)$_GET['msg_id'])) && (!isset($_POST['msg_id']) || !($msg_id = (int)$_POST['msg_id']))) {
		error_dialog('ERROR', 'No Such Message');
	}
	if (!_uid) {
		std_error('access');
	}

	/* permission check */
	is_allowed_user($usr);

	$msg = db_sab('SELECT t.forum_id, m.subject, m.post_stamp, u.alias, mm.id AS md, ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 2) > 0 AS gco, mr.id AS reported
			FROM phpgw_fud_msg m
			INNER JOIN phpgw_fud_thread t ON m.thread_id=t.id
			INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id='.(_uid ? '2147483647' : '0').' AND g1.resource_id=t.forum_id
			LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=t.forum_id
			LEFT JOIN phpgw_fud_mod mm ON mm.forum_id=t.forum_id AND mm.user_id='._uid.'
			LEFT JOIN phpgw_fud_users u ON m.poster_id=u.id
			LEFT JOIN phpgw_fud_msg_report mr ON mr.msg_id='.$msg_id.' AND mr.user_id='._uid.'
			WHERE m.id='.$msg_id.' AND m.apr=1');
	if (!$msg) {
		invl_inp_err();
	}

	if (!($usr->users_opt & 1048576) && !$msg->md && !$msg->gco) {
		std_error('access');
	}

	if ($msg->reported) {
		error_dialog('Already Reported', 'This message was already reported and the report is currently in moderation staff&#39;s review queue.');
	}

	if (!empty($_POST['reason']) && ($reason = trim($_POST['reason']))) {
		q("INSERT INTO phpgw_fud_msg_report (user_id, msg_id, reason, stamp) VALUES("._uid.", ".$msg_id.", '".addslashes(htmlspecialchars($reason))."', ".__request_timestamp__.")");
		check_return($usr->returnto);
	} else if (count($_POST)) {
		$reason_error = '<font class="ErrorText">You cannot send an empty report, please enter a reason for your report.</font><br />';
	} else {
		$reason_error = '';
	}



	$user_login = $msg->alias ? $msg->alias : $GLOBALS['ANON_NICK'];


?>
<?php echo $GLOBALS['fud_egw_hdr']; ?>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">
<form method="post" action="/egroupware/fudforum/3814588639/index.php?t=report">
<div align="center"><table border="0" cellspacing="1" cellpadding="2" class="MiniTable">
<tr><th>Report Post</th></tr>
<tr class="RowStyleB"><td><font class="GenText"><b>Reporting About:</b></font><br /><table border="0" cellspacing="0" cellpadding="0"><tr><td class="repI"><b>Subject:</b> <?php echo $msg->subject; ?> <br /><b>By:</b> <?php echo $user_login; ?> <br /><b>Posted on:</b> <font class="DateText"><?php echo strftime("%a, %d %B %Y %H:%M", $msg->post_stamp); ?></font></td></tr></table></td></tr>
<tr class="RowStyleB"><td><font class="GenText">Please give a reason why you are reporting this message:</font><br /><?php echo $reason_error; ?><textarea name="reason" cols=80 rows=25></textarea></td></tr>
<tr class="RowStyleB"><td align=right><input type="submit" class="button" name="btn_report" value="Submit Report"></td></tr>
</table></div>
<input type="hidden" name="msg_id" value="<?php echo $msg_id; ?>"><?php echo _hs; ?></form>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>