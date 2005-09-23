<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: ppost.php.t,v 1.3 2003/12/18 18:20:49 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
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
}function get_spell_suggest($word)
{
	return pspell_suggest(__FUD_PSPELL_LINK__, $word);
}

function spell_check_w($word)
{
	return pspell_check(__FUD_PSPELL_LINK__, $word);
}

function init_spell($type, $dict)
{
	$pspell_config = pspell_config_create($dict);
	pspell_config_mode($pspell_config, $type);
	pspell_config_personal($pspell_config, $GLOBALS['FORUM_SETTINGS_PATH']."forum.pws");
	pspell_config_ignore($pspell_config, 2);
	define('__FUD_PSPELL_LINK__', pspell_new_config($pspell_config));

	return true;
}

function tokenize_string($data)
{
	if (!($len = strlen($data))) {
		return array();
	}
	$wa = array();

	$i=$p=0;
	while ($i < $len) {
		switch ($data{$i}) {
			case ' ':
			case '/':
			case '\\':
			case '.':
			case ',':
			case '!':
			case '>':
			case '?':
			case "\n":
			case "\r":
			case "\t":
			case ")":
			case "(":
			case "}":
			case "{":
			case "[":
			case "]":
			case "*":
			case ";":
			case '=':
			case ':':
				if (isset($str)) {
					$wa[] = array('token'=>$str, 'check'=>1);
					unset($str);
				}

				$wa[] = array('token'=>$data[$i], 'check'=>0);

				break;
			case '<':
				if (($p = strpos($data, '>', $i)) !== false) {
					if (isset($str)) {
						$wa[] = array('token'=>$str, 'check'=>1);
						unset($str);
					}

					$wrd = substr($data,$i,($p-$i)+1);
					$p3=$l=null;

					if ($wrd == '<pre>') {
						$l = 'pre';
					} else if ($wrd == '<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1">') {
						$l = 1;
						$p3 = $p;

						while ($l > 0) {
							$p3 = strpos($data, 'table', $p3);

							if ($data[$p3-1] == '<') {
								$l++;
							} else if ($data[$p3-1] == '/' && $data[$p3-2] == '<') {
								$l--;
							}

							$p3 = strpos($data, '>', $p3);
						}
					}

					if ($p3) {
						$p = $p3;
						$wrd = substr($data, $i, ($p-$i)+1);
					} else if ($l && ($p2 = strpos($data, '</'.$l.'>', $p))) {
						$p = $p2+1+strlen($l)+1;
						$wrd = substr($data,$i,($p-$i)+1);
					}


					$wa[] = array('token'=>$wrd, 'check'=>0);

					$i=$p;
				} else {
					$str .= $data[$i];
				}
				break;
			case ':':
				if ($data[$i+1] == '/' && $data[$i+2] == '/') {
					$tmp_string = substr($data,$i+3);
					$regs = array();
					if (preg_match('!([A-Za-z0-9\-_\.\%\?\&=/]+)!is', $tmp_string, $regs)) {
						$wa[] = array('token'=>$str.'//'.$regs[1], 'check'=>0);
						unset($str);

						$i += 2+strlen($regs[1]);
						break;
					}
				} else if ($str == 'Re') {
					$wa[] = array('token'=>$str.':', 'check'=>0);
					unset($str);
					break;
				}

				if (isset($str)) {
					$wa[] = array('token'=>$str, 'check'=>1);
					unset($str);
				}
				$wa[] = array('token'=>$data[$i], 'check'=>0);

				break;
			case '&':
				if (isset($str)) {
					$wa[] = array('token'=>$str, 'check'=>1);
					unset($str);
				}

				$regs = array();
				if (preg_match('!(\&[A-Za-z]{2,5}\;)!', substr($data,$i,6), $regs)) {
					$wa[] = array('token'=>$regs[1], 'check'=>0);
					$i += strlen($regs[1])-1;
				} else {
					$wa[] = array('token'=>$data[$i], 'check'=>0);
				}
				break;
			default:
				if (isset($str)) {
					$str .= $data[$i];
				} else {
					$str = $data[$i];
				}
		}
		$i++;
	}

	if (isset($str)) {
		$wa[] = array('token'=>$str, 'check'=>1);
	}

	return $wa;
}

function draw_spell_sug_select($v,$k,$type)
{
	$sel_name = 'spell_chk_'.$type.'_'.$k;
	$data = '<select name="'.$sel_name.'">';
	$data .= '<option value="'.htmlspecialchars($v['token']).'">'.htmlspecialchars($v['token']).'</option>';
	$sug = get_spell_suggest($v['token']);
	$i=0;
	foreach($sug as $va) {
		$data .= '<option value="'.$va.'">'.++$i.') '.$va.'</option>';
	}

	if (!count($sug)) {
		$data .= '<option value="">no alternatives</option>';
	}

	$data .= '</select>';

	return $data;
}

function spell_replace($wa,$type)
{
	$data = '';

	foreach($wa as $k => $v) {
		if( $v['check']==1 && isset($_POST['spell_chk_'.$type.'_'.$k]) && strlen($_POST['spell_chk_'.$type.'_'.$k])) {
			$data .= $_POST['spell_chk_'.$type.'_'.$k];
		} else {
			$data .= $v['token'];
		}
	}

	return $data;
}

function spell_check_ar($wa,$type)
{
	foreach($wa as $k => $v) {
		if ($v['check'] > 0 && !spell_check_w($v['token'])) {
			$wa[$k]['token'] = draw_spell_sug_select($v, $k, $type);
		}
	}

	return $wa;
}

function reasemble_string($wa)
{
	$data = '';
	foreach($wa as $v) {
		$data .= $v['token'];
	}

	return $data;
}

function check_data_spell($data, $type, $dict)
{
	if (!$data) {
		return $data;
	}
	if (!defined('__FUD_PSPELL_LINK__') && !init_spell(PSPELL_FAST, $dict)) {
		return $data;
	}

	$wa = tokenize_string($data);
	$wa = spell_check_ar($wa, $type);
	$data = reasemble_string($wa);

	return $data;
}function fud_wrap_tok($data)
{
	$wa = array();
	$len = strlen($data);

	$i=$j=$p=0;
	$str = '';
	while ($i < $len) {
		switch ($data{$i}) {
			case ' ':
			case "\n":
			case "\t":
				if ($j) {
					$wa[] = array('word'=>$str, 'len'=>($j+1));
					$j=0;
					$str ='';
				}

				$wa[] = array('word'=>$data[$i]);

				break;
			case '<':
				if (($p = strpos($data, '>', $i)) !== false) {
					if ($j) {
						$wa[] = array('word'=>$str, 'len'=>($j+1));
						$j=0;
						$str ='';
					}
					$s = substr($data, $i, ($p - $i) + 1);
					if ($s == '<pre>') {
						$s = substr($data, $i, ($p = (strpos($data, '</pre>', $p) + 6)) - $i);
						--$p;
					} else if ($s == '<span name="php">') {
						$s = substr($data, $i, ($p = (strpos($data, '</span>', $p) + 7)) - $i);
						--$p;
					}

					$wa[] = array('word' => $s);

					$i = $p;
					$j = 0;
				} else {
					$str .= $data[$i];
					$j++;
				}
				break;

			case '&':
				if (($e = strpos($data, ';', $i))) {
					$st = substr($data, $i, ($e - $i + 1));
					if (($st{1} == '#' && is_numeric(substr($st, 3, -1))) || !strcmp($st, '&nbsp;') || !strcmp($st, '&gt;') || !strcmp($st, '&lt;') || !strcmp($st, '&quot;')) {
						if ($j) {
							$wa[] = array('word'=>$str, 'len'=>($j+1));
							$j=0;
							$str ='';
						}

						$wa[] = array('word' => $st, 'sp' => 1);
						$i=$e;
						$j=0;
						break;
					}
				} /* fall through */
			default:
				$str .= $data[$i];
				$j++;
		}
		$i++;
	}

	if ($j) {
		$wa[] = array('word'=>$str, 'len'=>($j+1));
	}

	return $wa;
}

function fud_wordwrap(&$data)
{
	if (!$GLOBALS['WORD_WRAP'] || $GLOBALS['WORD_WRAP'] >= strlen($data)) {
		return;
	}

	$wa = fud_wrap_tok($data);
	$m = (int) $GLOBALS['WORD_WRAP'];
	$l = 0;
	$data = null;
	foreach($wa as $v) {
		if (isset($v['len']) && $v['len'] > $m) {
			if ($v['len'] + $l > $m) {
				$l = 0;
				$data .= ' ';
			}
			$data .= wordwrap($v['word'], $m, ' ', 1);
			$l += $v['len'];
		} else {
			if (isset($v['sp'])) {
				if ($l > $m) {
					$data .= ' ';
					$l = 0;
				}
				++$l;
			} else if (!isset($v['len'])) {
				$l = 0;
			} else {
				$l += $v['len'];
			}
			$data .= $v['word'];
		}
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
}function tmpl_post_options($arg, $perms=0)
{
	$post_opt_html		= '<b>HTML</b> code is <b>OFF</b>';
	$post_opt_fud		= '<b>FUDcode</b> is <b>OFF</b>';
	$post_opt_images 	= '<b>Images</b> are <b>OFF</b>';
	$post_opt_smilies	= '<b>Smilies</b> are <b>OFF</b>';
	$edit_time_limit	= '';

	if (is_int($arg)) {
		if ($arg & 16) {
			$post_opt_fud = '<a href="/egroupware/fudforum/3814588639/index.php?section=readingposting&amp;t=help_index&amp;'._rsid.'#style" target="_blank"><b>FUDcode</b> is <b>ON</b></a>';
		} else if (!($arg & 8)) {
			$post_opt_html = '<b>HTML</b> is <b>ON</b>';
		}
		if ($perms & 16384) {
			$post_opt_smilies = '<a href="/egroupware/fudforum/3814588639/index.php?section=readingposting&amp;t=help_index&amp;'._rsid.'#sml" target="_blank"><b>Smilies</b> are <b>ON</b></a>';
		}
		if ($perms & 32768) {
			$post_opt_images = '<b>Images</b> are <b>ON</b>';
		}
		$edit_time_limit = $GLOBALS['EDIT_TIME_LIMIT'] ? '<br><b>Editing Time Limit</b>: <b>'.$GLOBALS['EDIT_TIME_LIMIT'].'</b> minutes' : '<br><b>Editing Time Limit</b>: <b>Unlimited</b>';
	} else if ($arg == 'private') {
		$o =& $GLOBALS['FUD_OPT_1'];

		if ($o & 4096) {
			$post_opt_fud = '<a href="/egroupware/fudforum/3814588639/index.php?section=readingposting&amp;t=help_index&amp;'._rsid.'#style" target="_blank"><b>FUDcode</b> is <b>ON</b></a>';
		} else if (!($o & 2048)) {
			$post_opt_html = '<b>HTML</b> is <b>ON</b>';
		}
		if ($o & 16384) {
			$post_opt_images = '<b>Images</b> are <b>ON</b>';
		}
		if ($o & 8192) {
			$post_opt_smilies = '<a href="/egroupware/fudforum/3814588639/index.php?section=readingposting&amp;t=help_index&amp;'._rsid.'#sml" target="_blank"><b>Smilies</b> are <b>ON</b></a>';
		}
	} else if ($arg == 'sig') {
		$o =& $GLOBALS['FUD_OPT_1'];

		if ($o & 131072) {
			$post_opt_fud = '<a href="/egroupware/fudforum/3814588639/index.php?section=readingposting&amp;t=help_index&amp;'._rsid.'#style" target="_blank"><b>FUDcode</b> is <b>ON</b></a>';
		} else if (!($o & 65536)) {
			$post_opt_html = '<b>HTML</b> is <b>ON</b>';
		}
		if ($o & 524288) {
			$post_opt_images = '<b>Images</b> are <b>ON</b>';
		}
		if ($o & 262144) {
			$post_opt_smilies = '<a href="/egroupware/fudforum/3814588639/index.php?section=readingposting&amp;t=help_index&amp;'._rsid.'#sml" target="_blank"><b>Smilies</b> are <b>ON</b></a>';
		}
	}

	return '<font class="SmallText"><b>Forum Options</b><br />
'.$post_opt_html.'<br />
'.$post_opt_fud.'<br />
'.$post_opt_images.'<br />
'.$post_opt_smilies.$edit_time_limit.'</font><br />';
}function reverse_fmt(&$data)
{
	$data = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $data);
}$GLOBALS['seps'] = array(' '=>' ', "\n"=>"\n", "\r"=>"\r", "'"=>"'", '"'=>'"', '['=>'[', ']'=>']', '('=>'(', ';'=>';', ')'=>')', "\t"=>"\t", '='=>'=', '>'=>'>', '<'=>'<');

function fud_substr_replace($str, $newstr, $pos, $len)
{
        return substr($str, 0, $pos).$newstr.substr($str, $pos+$len);
}

function char_fix(&$str)
{
	$str = str_replace(
		array('&amp;#0', '&amp;#1', '&amp;#2', '&amp;#3', '&amp;#4', '&amp;#5', '&amp;#6', '&amp;#7','&amp;#8','&amp;#9'),
		array('&#0', '&#1', '&#2', '&#3', '&#4', '&#5', '&#6', '&#7', '&#8', '&#9'),
		$str);
}

function tags_to_html($str, $allow_img=1, $no_char=0)
{
	if (!$no_char) {
		$str = htmlspecialchars($str);
	}

	$str = nl2br($str);

	$ostr = '';
	$pos = $old_pos = 0;

	while (($pos = strpos($str, '[', $pos)) !== false) {
		if (isset($GLOBALS['seps'][$str[$pos + 1]])) {
			++$pos;
			continue;
		}

		if (($epos = strpos($str, ']', $pos)) === false) {
			break;
		}
		if (!($epos-$pos-1)) {
			$pos = $epos + 1;
			continue;
		}
		$tag = substr($str, $pos+1, $epos-$pos-1);
		if (($pparms = strpos($tag, '=')) !== false) {
			$parms = substr($tag, $pparms+1);
			if (!$pparms) { /*[= exception */
				$pos = $epos+1;
				continue;
			}
			$tag = substr($tag, 0, $pparms);
		} else {
			$parms = '';
		}

		$tag = strtolower($tag);

		switch ($tag) {
			case 'quote title':
				$tag = 'quote';
				break;
			case 'list type':
				$tag = 'list';
				break;
		}

		if ($tag[0] == '/') {
			if (isset($end_tag[$pos])) {
				if( ($pos-$old_pos) ) $ostr .= substr($str, $old_pos, $pos-$old_pos);
				$ostr .= $end_tag[$pos];
				$pos = $old_pos = $epos+1;
			} else {
				$pos = $epos+1;
			}

			continue;
		}

		$cpos = $epos;
		$ctag = '[/'.$tag.']';
		$ctag_l = strlen($ctag);
		$otag = '['.$tag;
		$otag_l = strlen($otag);
		$rf = 1;
		while (($cpos = strpos($str, '[', $cpos)) !== false) {
			if (isset($end_tag[$cpos]) || isset($GLOBALS['seps'][$str[$cpos + 1]])) {
				++$cpos;
				continue;
			}

			if (($cepos = strpos($str, ']', $cpos)) === false) {
				break 2;
			}

			if (strcasecmp(substr($str, $cpos, $ctag_l), $ctag) == 0) {
				--$rf;
			} else if (strcasecmp(substr($str, $cpos, $otag_l), $otag) == 0) {
				++$rf;
			} else {
				++$cpos;
				continue;
			}

			if (!$rf) {
				break;
			}
			$cpos = $cepos;
		}

		if (!$cpos || ($rf && $str[$cpos] == '<')) { /* left over [ handler */
			++$pos;
			continue;
		}

		if ($cpos !== false) {
			if (($pos-$old_pos)) {
				$ostr .= substr($str, $old_pos, $pos-$old_pos);
			}
			switch ($tag) {
				case 'notag':
					$ostr .= '<span name="notag">'.substr($str, $epos+1, $cpos-1-$epos).'</span>';
					$epos = $cepos;
					break;
				case 'url':
					if (!$parms) {
						$url = substr($str, $epos+1, ($cpos-$epos)-1);
					} else {
						$url = $parms;
					}

					if (!strncasecmp($url, 'www.', 4)) {
						$url = 'http&#58;&#47;&#47;'. $url;
					} else if (strpos(strtolower($url), 'javascript:') !== false) {
						$ostr .= substr($str, $pos, $cepos - $pos + 1);
						$epos = $cepos;
						$str[$cpos] = '<';
						break;
					} else {
						$url = str_replace('://', '&#58;&#47;&#47;', $url);
					}

					$end_tag[$cpos] = '</a>';
					$ostr .= '<a href="'.$url.'" target="_blank">';
					break;
				case 'i':
				case 'u':
				case 'b':
				case 's':
				case 'sub':
				case 'sup':
					$end_tag[$cpos] = '</'.$tag.'>';
					$ostr .= '<'.$tag.'>';
					break;
				case 'email':
					if (!$parms) {
						$parms = str_replace('@', '&#64;', substr($str, $epos+1, ($cpos-$epos)-1));
						$ostr .= '<a href="mailto:'.$parms.'" target="_blank">'.$parms.'</a>';
						$epos = $cepos;
						$str[$cpos] = '<';
					} else {
						$end_tag[$cpos] = '</a>';
						$ostr .= '<a href="mailto:'.str_replace('@', '&#64;', $parms).'" target="_blank">';
					}
					break;
				case 'color':
				case 'size':
				case 'font':
					if ($tag == 'font') {
						$tag = 'face';
					}
					$end_tag[$cpos] = '</font>';
					$ostr .= '<font '.$tag.'="'.$parms.'">';
					break;
				case 'code':
					$param = substr($str, $epos+1, ($cpos-$epos)-1);
					reverse_nl2br($param);

					$ostr .= '<div class="pre"><pre>'.$param.'</pre></div>';
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'pre':
					$param = substr($str, $epos+1, ($cpos-$epos)-1);
					reverse_nl2br($param);

					$ostr .= '<pre>'.$param.'</pre>';
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'php':
					$param = substr($str, $epos+1, ($cpos-$epos)-1);
					reverse_nl2br($param);
					reverse_fmt($param);
					$param = trim($param);

					if (strncmp($param, '<?php', 5)) {
						if (strncmp($param, '<?', 2)) {
							$param = "<?php\n" . $param;
						} else {
							$param = "<?php\n" . substr($param, 3);
						}
					}
					if (substr($param, -2) != '?>') {
						$param .= "\n?>";
					}

					$ostr .= '<span name="php">'.trim(@highlight_string($param, true)).'</span>';
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'img':
					if (!$allow_img) {
						$ostr .= substr($str, $pos, ($cepos-$pos)+1);
					} else {
						if (!$parms) {
							$parms = substr($str, $epos+1, ($cpos-$epos)-1);
							if (strpos(strtolower($parms), 'javascript:') === false) {
								$ostr .= '<img src="'.$parms.'" border=0 alt="'.$parms.'">';
							} else {
								$ostr .= substr($str, $pos, ($cepos-$pos)+1);
							}
						} else {
							if (strpos(strtolower($parms), 'javascript:') === false) {
								$ostr .= '<img src="'.$parms.'" border=0 alt="'.substr($str, $epos+1, ($cpos-$epos)-1).'">';
							} else {
								$ostr .= substr($str, $pos, ($cepos-$pos)+1);
							}
						}
					}
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'quote':
					if (!$parms) {
						$parms = 'Quote:';
					}
					$ostr .= '<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>'.$parms.'</b></td></tr><tr><td class="quote"><br>';
					$end_tag[$cpos] = '<br></td></tr></table>';
					break;
				case 'align':
					$end_tag[$cpos] = '</div>';
					$ostr .= '<div align="'.$parms.'">';
					break;
				case 'list':
					$tmp = substr($str, $epos, ($cpos-$epos));
					$tmp_l = strlen($tmp);
					$tmp2 = str_replace(array('[*]', '<br />'), array('<li>', ''), $tmp);
					$tmp2_l = strlen($tmp2);
					$str = str_replace($tmp, $tmp2, $str);

					$diff = $tmp2_l - $tmp_l;
					$cpos += $diff;

					if (isset($end_tag)) {
						foreach($end_tag as $key => $val) {
							if ($key < $epos) {
								continue;
							}

							$end_tag[$key+$diff] = $val;
						}
					}

					switch (strtolower($parms)) {
						case '1':
						case 'a':
							$end_tag[$cpos] = '</ol>';
							$ostr .= '<ol type="'.$parms.'">';
							break;
						case 'square':
						case 'circle':
						case 'disc':
							$end_tag[$cpos] = '</ul>';
							$ostr .= '<ul type="'.$parms.'">';
							break;
						default:
							$end_tag[$cpos] = '</ul>';
							$ostr .= '<ul>';
					}
					break;
				case 'spoiler':
					$rnd = get_random_value(64);
					$end_tag[$cpos] = '</div></div>';
					$ostr .= '<div class="dashed" style="padding: 3px;" align="center" width="100%"><a href="javascript://" OnClick="javascript: layerVis(\''.$rnd.'\', 1);">Reveal Spoiler</a><div align="left" id="'.$rnd.'" style="visibility: hidden;">';
					break;
			}

			$str[$pos] = '<';
			$pos = $old_pos = $epos+1;
		} else {
			$pos = $epos+1;
		}
	}
	$ostr .= substr($str, $old_pos, strlen($str)-$old_pos);

	/* url paser */
	$pos = 0;
	$ppos = 0;
	while (($pos = @strpos($ostr, '://', $pos)) !== false) {
		if ($pos < $ppos) {
			break;
		}
		// check if it's inside any tag;
		$i = $pos;
		while (--$i && $i > $ppos) {
			if ($ostr[$i] == '>' || $ostr[$i] == '<') {
				break;
			}
		}
		if ($ostr[$i]=='<') {
			$pos+=3;
			continue;
		}

		// check if it's inside the a tag
		if (($ts = strpos($ostr, '<a ', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</a>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 3;
			continue;
		}

		// check if it's inside the pre tag
		if (($ts = strpos($ostr, '<pre>', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</pre>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 3;
			continue;
		}

		// check if it's inside the span tag
		if (($ts = strpos($ostr, '<span>', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</span>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 3;
			continue;
		}

		$us = $pos;
		$l = strlen($ostr);
		while (1) {
			--$us;
			if ($ppos > $us || $us >= $l || isset($GLOBALS['seps'][$ostr[$us]])) {
				break;
			}
		}

		unset($GLOBALS['seps']['=']);
		$ue = $pos;
		while (1) {
			++$ue;
			if ($ue >= $l || isset($GLOBALS['seps'][$ostr[$ue]])) {
				break;
			}

			if ($ostr[$ue] == '&') {
				if ($ostr[$ue+4] == ';') {
					$ue += 4;
					continue;
				}
				if ($ostr[$ue+3] == ';' || $ostr[$ue+5] == ';') {
					break;
				}
			}

			if ($ue >= $l || isset($GLOBALS['seps'][$ostr[$ue]])) {
				break;
			}
		}
		$GLOBALS['seps']['='] = '=';

		$url = substr($ostr, $us+1, $ue-$us-1);
		if (!strncasecmp($url, 'javascript', strlen('javascript'))) {
			$pos = $ue;
			continue;
		}
		$html_url = '<a href="'.$url.'" target="_blank">'.$url.'</a>';
		$html_url_l = strlen($html_url);
		$ostr = fud_substr_replace($ostr, $html_url, $us+1, $ue-$us-1);
		$ppos = $pos;
		$pos = $us+$html_url_l;
	}

	/* email parser */
	$pos = 0;
	$ppos = 0;
	while (($pos = @strpos($ostr, '@', $pos)) !== false) {
		if ($pos < $ppos) {
			break;
		}

		// check if it's inside any tag;
		$i = $pos;
		while (--$i && $i>$ppos) {
			if ( $ostr[$i] == '>' || $ostr[$i] == '<') {
				break;
			}
		}
		if ($ostr[$i]=='<') {
			++$pos;
			continue;
		}


		// check if it's inside the a tag
		if (($ts = strpos($ostr, '<a ', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</a>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 1;
			continue;
		}

		// check if it's inside the pre tag
		if (($ts = strpos($ostr, '<div class="pre"><pre>', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</pre></div>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 1;
			continue;
		}

		for ($es = ($pos - 1); $es > ($ppos - 1); $es--) {
			if (
				( ord($ostr[$es]) >= ord('A') && ord($ostr[$es]) <= ord('z') ) ||
				( ord($ostr[$es]) >= ord(0) && ord($ostr[$es]) <= ord(9) ) ||
				( $ostr[$es] == '.' || $ostr[$es] == '-' || $ostr[$es] == '\'')
			) { continue; }
			++$es;
			break;
		}
		if ($es == $pos) {
			$ppos = $pos += 1;
			continue;
		}
		if ($es < 0) {
			$es = 0;
		}

		for ($ee = ($pos + 1); @isset($ostr[$ee]); $ee++) {
			if (
				( ord($ostr[$ee]) >= ord('A') && ord($ostr[$ee]) <= ord('z') ) ||
				( ord($ostr[$ee]) >= ord(0) && ord($ostr[$ee]) <= ord(9) ) ||
				( $ostr[$ee] == '.' || $ostr[$ee] == '-' )
			) { continue; }
			break;
		}
		if ($ee == ($pos+1)) {
			$ppos = $pos += 1;
			continue;
		}

		$email = str_replace('@', '&#64;', substr($ostr, $es, $ee-$es));
		$email_url = '<a href="mailto:'.$email.'" target="_blank">'.$email.'</a>';
		$email_url_l = strlen($email_url);
		$ostr = fud_substr_replace($ostr, $email_url, $es, $ee-$es);
		$ppos =	$es+$email_url_l;
		$pos = $ppos;
	}

	return $ostr;
}

if (!function_exists('html_entity_decode')) {
	function html_entity_decode($s)
	{
		return strtr($s, array_flip(get_html_translation_table(HTML_ENTITIES)));
	}
}

function html_to_tags($fudml)
{
	while (preg_match('!<span name="php">(.*?)</span>!is', $fudml, $res)) {
		$tmp = trim(html_entity_decode(strip_tags(str_replace('<br />', "\n", $res[1]))));
		$m = md5($tmp);
		$php[$m] = $tmp;
		$fudml = str_replace($res[0], "[php]\n".$m."\n[/php]", $fudml);
	}

	if (strpos($fudml, '<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>')  !== false) {
		$fudml = str_replace(array('<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>','</b></td></tr><tr><td class="quote"><br>','<br></td></tr></table>'), array('[quote title=', ']', '[/quote]'), $fudml);
	}

	if (preg_match('!<div class="dashed" style="padding: 3px;" align="center" width="100%"><a href="javascript://" OnClick="javascript: layerVis\(\'.*?\', 1\);">Reveal Spoiler</a><div align="left" id=".*?" style="visibility: hidden;">!is', $fudml)) {
		$fudml = preg_replace('!\<div class\="dashed" style\="padding: 3px;" align\="center" width\="100%"\>\<a href\="javascript://" OnClick\="javascript: layerVis\(\'.*?\', 1\);">Reveal Spoiler\</a\>\<div align\="left" id\=".*?" style\="visibility: hidden;"\>!is', '[spoiler]', $fudml);
		$fudml = str_replace('</div></div>', '[/spoiler]', $fudml);
	}

	while (preg_match('!<font (color|face|size)=".+?">.*?</font>!is', $fudml)) {
		$fudml = preg_replace('!<font (color|face|size)="(.+?)">(.*?)</font>!is', '[\1=\2]\3[/\1]', $fudml);
	}
	while (preg_match('!<(o|u)l type=".+?">.*?</\\1l>!is', $fudml)) {
		$fudml = preg_replace('!<(o|u)l type="(.+?)">(.*?)</\\1l>!is', '[list type=\2]\3[/list]', $fudml);
	}

	$fudml = str_replace(
	array(
		'<b>', '</b>', '<i>', '</i>', '<u>', '</u>', '<s>', '</s>', '<sub>', '</sub>', '<sup>', '</sup>',
		'<div class="pre"><pre>', '</pre></div>', '<div align="center">', '<div align="left">', '<div align="right">', '</div>',
		'<ul>', '</ul>', '<span name="notag">', '</span>', '<li>', '&#64;', '&#58;&#47;&#47;', '<br />', '<pre>', '</pre>'
	),
	array(
		'[b]', '[/b]', '[i]', '[/i]', '[/u]', '[/u]', '[s]', '[/s]', '[sub]', '[/sub]', '[sup]', '[/sup]', 
		'[code]', '[/code]', '[align=center]', '[align=left]', '[align=right]', '[/align]', '[list]', '[/list]',
		'[notag]', '[/notag]', '[*]', '@', '://', '', '[pre]', '[/pre]'
	), 
	$fudml);

	while (preg_match('!<img src="(.*?)" border=0 alt="\\1">!is', $fudml)) {
		$fudml = preg_replace('!<img src="(.*?)" border=0 alt="\\1">!is', '[img]\1[/img]', $fudml);
	}
	while (preg_match('!<a href="mailto:(.+?)" target="_blank">\\1</a>!is', $fudml)) {
		$fudml = preg_replace('!<a href="mailto:(.+?)" target="_blank">\\1</a>!is', '[email]\1[/email]', $fudml);
	}
	while (preg_match('!<a href="(.+?)" target="_blank">\\1</a>!is', $fudml)) {
		$fudml = preg_replace('!<a href="(.+?)" target="_blank">\\1</a>!is', '[url]\1[/url]', $fudml);
	}

	if (strpos($fudml, '<img src="') !== false) {
		$fudml = preg_replace('!<img src="(.*?)" border=0 alt="(.*?)">!is', '[img=\1]\2[/img]', $fudml);
	}
	if (strpos($fudml, '<a href="mailto:') !== false) {
		$fudml = preg_replace('!<a href="mailto:(.+?)" target="_blank">(.+?)</a>!is', '[email=\1]\2[/email]', $fudml);
	}
	if (strpos($fudml, '<a href="') !== false) { 
		$fudml = preg_replace('!<a href="(.+?)" target="_blank">(.+?)</a>!is', '[url=\1]\2[/url]', $fudml);
	}

	if (isset($php)) {
		$fudml = str_replace(array_keys($php), array_values($php), $fudml);
	}

	/* unhtmlspecialchars */
	reverse_fmt($fudml);

	return $fudml;
}


function filter_ext($file_name)
{
	include $GLOBALS['FORUM_SETTINGS_PATH'] . 'file_filter_regexp';
	if (!count($GLOBALS['__FUD_EXT_FILER__'])) {
		return;
	}
	if (($p = strrpos($file_name, '.')) === false) {
		return 1;
	}
	return !in_array(strtolower(substr($file_name, ($p + 1))), $GLOBALS['__FUD_EXT_FILER__']);
}

function safe_tmp_copy($source, $del_source=0, $prefx='')
{
	if (!$prefx) {
		 $prefx = getmypid();
	}

	$umask = umask(($GLOBALS['FUD_OPT_2'] & 8388608 ? 0177 : 0111));
	if (!move_uploaded_file($source, ($name = tempnam($GLOBALS['TMP'], $prefx.'_')))) {
		return;
	}
	umask($umask);
	if ($del_source) {
		@unlink($source);
	}
	umask($umask);

	return basename($name);
}

function reverse_nl2br(&$data)
{
	$data = str_replace('<br />', '', $data);
}function apply_custom_replace($text)
{
	if (!defined('__fud_replace_init')) {
		make_replace_array();
	}
	if (!isset($GLOBALS['__FUD_REPL__'])) {
		return $text;
	}

	return preg_replace($GLOBALS['__FUD_REPL__']['pattern'], $GLOBALS['__FUD_REPL__']['replace'], $text);
}

function make_replace_array()
{
	$c = uq('SELECT with_str, replace_str FROM phpgw_fud_replace WHERE replace_str IS NOT NULL AND with_str IS NOT NULL AND LENGTH(replace_str)>0');
	while ($r = db_rowarr($c)) {
		$GLOBALS['__FUD_REPL__']['pattern'][] = $r[1];
		$GLOBALS['__FUD_REPL__']['replace'][] = $r[0];
	}

	define('__fud_replace_init', 1);
}

function make_reverse_replace_array()
{
	$c = uq('SELECT replace_opt, with_str, replace_str, from_post, to_msg FROM phpgw_fud_replace');

	while ($r = db_rowarr($c)) {
		if (!$r[0]) {
			$GLOBALS['__FUD_REPLR__']['pattern'][] = $r[3];
			$GLOBALS['__FUD_REPLR__']['replace'][] = $r[4];
		} else if ($r[0] && strlen($r[1]) && strlen($r[2])) {
			$GLOBALS['__FUD_REPLR__']['pattern'][] = '/'.str_replace('/', '\\/', preg_quote(stripslashes($r[1]))).'/';
			preg_match('/\/(.+)\/(.*)/', $r[2], $regs);
			$GLOBALS['__FUD_REPLR__']['replace'][] = str_replace('\\/', '/', $regs[1]);
		}
	}

	define('__fud_replacer_init', 1);
}

function apply_reverse_replace($text)
{
	if (!defined('__fud_replacer_init')) {
		make_reverse_replace_array();
	}
	if (!isset($GLOBALS['__FUD_REPLR__'])) {
		return $text;
	}
	return preg_replace($GLOBALS['__FUD_REPLR__']['pattern'], $GLOBALS['__FUD_REPLR__']['replace'], $text);
}$folders = array(1=>'Inbox', 2=>'Saved', 4=>'Draft', 3=>'Sent', 5=>'Trash');

function tmpl_cur_ppage($folder_id, $folders, $msg_subject='')
{
	if (!$folder_id || (!$msg_subject && $_GET['t'] == 'ppost')) {
		$user_action = 'Writing a Private Message';
	} else {
		$user_action = $msg_subject ? '<a href="/egroupware/fudforum/3814588639/index.php?t=pmsg&amp;folder_id='.$folder_id.'&amp;'._rsid.'" class="GenLink">'.$folders[$folder_id].'</a> &raquo; '.$msg_subject : 'Browsing <b>'.$folders[$folder_id].'</b> folder';
	}

	return '<font class="SmallText"><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=pmsg&amp;'._rsid.'">Private Messaging</a>&nbsp;&raquo;&nbsp;'.$user_action.'</font><br /><img src="blank.gif" alt="" height=4 width=1 /><br />';
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
}function draw_post_smiley_cntrl()
{
	$c = uq('SELECT code, descr, img FROM phpgw_fud_smiley ORDER BY vieworder LIMIT '.$GLOBALS['MAX_SMILIES_SHOWN']);
	$data = '';
	while ($r = db_rowarr($c)) {
		$r[0] = ($a = strpos($r[0], '~')) ? substr($r[0], 0, $a) : $r[0];
		$data .= '<a href="javascript: insertTag(document.post_form.msg_body, \'\', \' '.$r[0].' \');"><img title="'.$r[1].'" alt="'.$r[1].'" src="images/smiley_icons/'.$r[2].'" /></a>&nbsp;';
	}

	return ($data ? '<tr class="RowStyleA"><td nowrap valign=top class="GenText">Smiley Shortcuts:
	<br /><font size="-1">[<a href="javascript://" onClick="javascript: window_open(\'/egroupware/fudforum/3814588639/index.php?t=smladd\', \'sml_list\', 220, 200);">list all smilies</a>]</font>
</td>
<td valign=top><table border=0 cellspacing=5 cellpadding=0><tr valign="bottom"><td>'.$data.'</td></tr></table></td></tr>' : '');
}

function draw_post_icons($msg_icon)
{
	$tmp = $data = '';
	$allowed_ext = array('.jpg' => 1, '.png' => 1, '.jpeg' => 1, '.gif' => 1);
	$p = -1;
	$rl = (int) $GLOBALS['POST_ICONS_PER_ROW'];

	$none_checked = !$msg_icon ? ' checked' : '';

	if ($d = opendir($GLOBALS['WWW_ROOT_DISK'] . 'images/message_icons')) {
		while ($f = readdir($d)) {
			if ($f == '.' || $f == '..') continue;
			if (strlen($f) < 4 || !isset($allowed_ext[strtolower(strrchr($f, '.'))])) {
				continue;
			}
			if (++$p > $rl) {
				$data .= '<tr>'.$tmp.'</tr>';
				$tmp = ''; $p = 0;
			}
			$checked = $f == $msg_icon ? ' checked' : '';
			$tmp .= '<td nowrap valign="middle"><input type="radio" name="msg_icon" value="'.$f.'"'.$checked.'><img src="images/message_icons/'.$f.'" alt="" /></td>';
		}
		closedir($d);
		if ($tmp) {
			$data .= '<tr>'.$tmp.'</tr>';
		}
	}

	return ($data ? '<tr class="RowStyleA"><td valign=top class="GenText">Post Icon:</td><td>
<table border=0 cellspacing=0 cellpadding=2>
<tr><td class="GenText" colspan='.$GLOBALS['POST_ICONS_PER_ROW'].'><input type="radio" name="msg_icon" value=""'.$none_checked.'>No Icon</td></tr>
'.$data.'
</table>
</td></tr>' : '');
}

function draw_post_attachments($al, $max_as, $max_a, $attach_control_error, $private='', $msg_id)
{
	$attached_files = '';
	$i = 0;

	if (!empty($al) && count($al)) {
		$enc = base64_encode(@serialize($al));
		$c = uq('SELECT a.id,a.fsize,a.original_name,m.mime_hdr
		FROM phpgw_fud_attach a
		LEFT JOIN phpgw_fud_mime m ON a.mime_type=m.id
		WHERE a.id IN('.implode(',', $al).') AND message_id IN(0, '.$msg_id.') AND attach_opt='.($private ? 1 : 0));
		while ($r = db_rowarr($c)) {
			$sz = ( $r[1] < 100000 ) ? number_format($r[1]/1024,2).'KB' : number_format($r[1]/1048576,2).'MB';
			$insert_uploaded_image = strncasecmp('image/', $r[3], 6) ? '' : '&nbsp;|&nbsp;<a href="javascript: insertTag(document.post_form.msg_body, \'[img]/egroupware/fudforum/3814588639/index.php?t=getfile&id='.$r[0].$private.'\', \'[/img]\');">Insert image into message body</a>';
			$attached_files .= '<tr>
	<td class="RowStyleB">'.$r[2].'</td>
	<td class="RowStyleB">'.$sz.'</td>
	<td class="RowStyleB"><a class="GenLink" href="javascript: document.post_form.file_del_opt.value=\''.$r[0].'\'; document.post_form.submit();">Delete</a>'.$insert_uploaded_image.'</td>
</tr>';
			$i++;
		}
	}

	if ($i) {
		$attachment_list = '<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr>
	<th>Name</th>
	<th>Size</th>
	<th>Action</th>
</tr>
'.$attached_files.'
</table>
<input type="hidden" name="file_del_opt" value="">
<input type="hidden" name="file_array" value="'.$enc.'">';
		$attached_status = '<font class="SmallText"> currently attached '.$i.' file(s)';
	} else {
		$attached_status = $attachment_list = '';
	}

	$upload_file = (($i + 1) <= $max_a) ? '<input type="file" name="attach_control"> <input type="submit" class="button" name="attach_control_add" value="Upload File">' : '';

	if (!$private && $GLOBALS['MOD'] && $GLOBALS['frm']->forum_opt & 32) {
		$allowed_extensions = '(unrestricted)';
	} else {
		include $GLOBALS['FORUM_SETTINGS_PATH'] . 'file_filter_regexp';
		if (!count($GLOBALS['__FUD_EXT_FILER__'])) {
			$allowed_extensions = '(unrestricted)';
		} else {
			$allowed_extensions = implode(' ', $GLOBALS['__FUD_EXT_FILER__']);
		}
	}
	return '<tr class="RowStyleB"><td nowrap valign=top class="GenText">File Attachments:</td><td>
'.$attachment_list.'
'.$attach_control_error.'
<font class="SmallText"><b>Allowed File Extensions:</b> '.$allowed_extensions.'<br /><b>Maximum File Size:</b> '.$max_as.'Kb<br /><b>Maximum Files per Message:</b> '.$max_a.'
'.$attached_status.'
</font><p>
'.$upload_file.'
</td></tr>';
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

function export_msg_data($m, &$msg_subject, &$msg_body, &$msg_icon, &$msg_smiley_disabled, &$msg_show_sig, &$msg_track, &$msg_to_list, $repl=0)
{
	$msg_subject = $m->subject;
	$msg_body = read_pmsg_body($m->foff, $m->length);
	$msg_icon = $m->icon;
	$msg_smiley_disabled = $m->pmsg_opt & 2 ? '2' : '';
	$msg_show_sig = $m->pmsg_opt & 1 ? '1' : '';
	$msg_track = $m->pmsg_opt & 4 ? '4' : '';
	$msg_to_list = $m->to_list;

	reverse_fmt($msg_subject);
	/* we do not revert replacment for forward/quote */
	if ($repl) {
		$msg_subject = apply_reverse_replace($msg_subject);
		$msg_body = apply_reverse_replace($msg_body);
	}
	if (!$msg_smiley_disabled) {
		$msg_body = post_to_smiley($msg_body);
	}
	if ($GLOBALS['FUD_OPT_1'] & 4096) {
		$msg_body = html_to_tags($msg_body);
	} else if ($GLOBALS['FUD_OPT_1'] & 2048) {
		reverse_fmt($msg_body);
		reverse_nl2br($msg_body);
	}
}

	if (!_uid) {
		std_error('perms');
	}
	if (!($FUD_OPT_1 & 1024)) {
		error_dialog('ERROR: Private Messaging Disabled', 'You cannot use the private messaging system, it has been disabled by the administrator.');
	}
	if (!($usr->users_opt & 32)) {
		error_dialog('Private Messaging Disabled', 'You cannot send private messages until you enable the &#39;Allow Private Messages&#39; option in your profile.');
	}
	if (($fldr_size = q_singleval('SELECT SUM(length) FROM phpgw_fud_pmsg WHERE duser_id='._uid)) > $MAX_PMSG_FLDR_SIZE) {
		error_dialog('No space left!', 'Your private message folders exceed the allowed storage size of <b>'.$GLOBALS['MAX_PMSG_FLDR_SIZE'].' bytes</b>, as they currently contain <b>'.$fldr_size.' bytes</b> worth of messages.<br><br><font class="ErrorText">Remove some old messages to clear up space</font>.');
	}
	is_allowed_user($usr);

	$attach_control_error = null;
	$pm_find_user = $FUD_OPT_1 & (8388608|4194304) ? '[<a href="javascript://" class="GenLink" onClick="javascript: window_open(\'/egroupware/fudforum/3814588639/index.php?t=pmuserloc&amp;'._rsid.'&amp;js_redr=post_form.msg_to_list\',\'user_list\',250,250);">Find User</a>]' : '';

	$attach_count = 0; $file_array = '';

	/* this is a hack, it essentially disables file attachment code when file_uploads are off */
	if (ini_get("file_uploads") != 1 || $PRIVATE_ATTACHMENTS < 1) {
		$ppost_enctype = '';
		$PRIVATE_ATTACHMENTS = 0;
	} else {
		$ppost_enctype = 'enctype="multipart/form-data"';
	}

	if (!isset($_POST['prev_loaded'])) {
		/* setup some default values */
		$msg_subject = $msg_body = $msg_icon = $old_subject = $msg_ref_msg_id = '';
		$msg_track = '';
		$msg_show_sig = $usr->users_opt & 2048 ? '1' : '';
		$msg_smiley_disabled = $FUD_OPT_1 & 8192 ? '' : '2';
		$reply = $forward = $msg_id = 0;

		/* deal with users passed via GET */
		if (isset($_GET['toi']) && ($toi = (int)$_GET['toi'])) {
			$msg_to_list = q_singleval('SELECT alias FROM phpgw_fud_users WHERE id='.$toi.' AND id>1');
		} else {
			$msg_to_list = '';
		}

		if (isset($_GET['msg_id']) && ($msg_id = (int)$_GET['msg_id'])) { /* editing a message */
			if (($msg_r = db_sab('SELECT id, subject, length, foff, to_list, icon, attach_cnt, pmsg_opt, ref_msg_id FROM phpgw_fud_pmsg WHERE id='.$msg_id.' AND duser_id='._uid))) {
				export_msg_data($msg_r, $msg_subject, $msg_body, $msg_icon, $msg_smiley_disabled, $msg_show_sig, $msg_track, $msg_to_list, 1);
			}
		} else if (isset($_GET['quote']) || isset($_GET['forward'])) { /* quote or forward message */
			if (($msg_r = db_sab('SELECT id, post_stamp, ouser_id, subject, length, foff, to_list, icon, attach_cnt, pmsg_opt, ref_msg_id '.(isset($_GET['quote']) ? ', to_list' : '').' FROM phpgw_fud_pmsg WHERE id='.(int)(isset($_GET['quote']) ? $_GET['quote'] : $_GET['forward']).' AND duser_id='._uid))) {
				$reply = $quote = isset($_GET['quote']) ? (int)$_GET['quote'] : 0;
				$forward = isset($_GET['forward']) ? (int)$_GET['forward'] : 0;

				export_msg_data($msg_r, $msg_subject, $msg_body, $msg_icon, $msg_smiley_disabled, $msg_show_sig, $msg_track, $msg_to_list);
				$msg_id = $msg_to_list = '';

				if ($quote) {
					$msg_to_list = q_singleval('SELECT alias FROM phpgw_fud_users WHERE id='.$msg_r->ouser_id);
				}

				if ($quote) {
					if ($FUD_OPT_1 & 4096) {
						$msg_body = '[quote title='.$msg_to_list.' wrote on '.strftime("%a, %d %B %Y %H:%M", $msg_r->post_stamp).']'.$msg_body.'[/quote]';
					} else if ($FUD_OPT_1 & 2048) {
						$msg_body = str_replace('<br>', "\n", 'Quote: '.$msg_to_list.' wrote on '.strftime("%a, %d %B %Y %H:%M", $msg_r->post_stamp).'<br />----------------------------------------------------<br />'.$msg_body.'<br />----------------------------------------------------<br />');
					} else {
						$msg_body = '<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>'.$msg_to_list.' wrote on '.strftime("%a, %d %B %Y %H:%M", $msg_r->post_stamp).'</b></td></tr><tr><td class="quote"><br />'.$msg_body.'<br /></td></tr></table>';
					}

					if (strncmp($msg_subject, 'Re: ', 4)) {
						$old_subject = $msg_subject = 'Re: ' . $msg_subject;
					}
					$msg_ref_msg_id = 'R'.$reply;
					unset($msg_r);
				} else if ($forward && strncmp($msg_subject, 'Fwd: ', 5)) {
					$old_subject = $msg_subject = 'Fwd: ' . $msg_subject;
					$msg_ref_msg_id = 'F'.$forward;
				}
			}
		} else if (isset($_GET['reply']) && ($reply = (int)$_GET['reply'])) {
			if (($msg_r = db_saq('SELECT p.subject, u.alias FROM phpgw_fud_pmsg p INNER JOIN phpgw_fud_users u ON p.ouser_id=u.id WHERE p.id='.$reply.' AND p.duser_id='._uid))) {
				$msg_subject = $msg_r[0];
				$msg_to_list = $msg_r[1];

				if (strncmp($msg_subject, 'Re: ', 4)) {
					$old_subject = $msg_subject = 'Re: ' . $msg_subject;
				}
				reverse_fmt($msg_subject);
				unset($msg_r);
				$msg_ref_msg_id = 'R'.$reply;
			}
		}

		/* restore file attachments */
		if (!empty($msg_r->attach_cnt) && $PRIVATE_ATTACHMENTS > 0) {
			$c = uq('SELECT id FROM phpgw_fud_attach WHERE message_id='.$msg_r->id.' AND attach_opt=1');
	 		while ($r = db_rowarr($c)) {
	 			$attach_list[$r[0]] = $r[0];
	 		}
		}
	} else {
		if (isset($_POST['btn_action'])) {
			if ($_POST['btn_action'] == 'draft') {
				$_POST['btn_draft'] = 1;
			} else if ($_POST['btn_action'] == 'send') {
				$_POST['btn_submit'] = 1;
			}
		}

		$msg_to_list = htmlspecialchars($_POST['msg_to_list']);
		$msg_subject = $_POST['msg_subject'];
		$old_subject = $_POST['old_subject'];
		$msg_body = $_POST['msg_body'];
		$msg_icon = (isset($_POST['msg_icon']) && basename($_POST['msg_icon']) == $_POST['msg_icon'] && @file_exists($WWW_ROOT_DISK.'images/message_icons/'.$_POST['msg_icon'])) ? $_POST['msg_icon'] : '';
		$msg_track = isset($_POST['msg_track']) ? '4' : '';
		$msg_smiley_disabled = isset($_POST['msg_smiley_disabled']) ? '2' : '';
		$msg_show_sig = isset($_POST['msg_show_sig']) ? '1' : '';

		$reply = isset($_POST['quote']) ? (int)$_POST['quote'] : 0;
		$forward = isset($_POST['forward']) ? (int)$_POST['forward'] : 0;
		$msg_id = isset($_POST['msg_id']) ? (int)$_POST['msg_id'] : 0;
		$msg_ref_msg_id = isset($_POST['msg_ref_msg_id']) ? (int)$_POST['msg_ref_msg_id'] : '';

		/* restore file attachments */
		if (!empty($_POST['file_array']) && $PRIVATE_ATTACHMENTS > 0) {
			$attach_list = @unserialize(base64_decode($_POST['file_array']));
		}
	}

	if (isset($attach_list)) {
		$attach_count = count($attach_list);
		$enc = base64_encode(@serialize($attach_list));
		foreach ($attach_list as $v) {
			if (!$v) {
				$attach_count--;
			}
		}
		/* remove file attachment */
		if (isset($_POST['file_del_opt']) && isset($attach_list[$_POST['file_del_opt']])) {
			if ($attach_list[$_POST['file_del_opt']]) {
				$attach_list[$_POST['file_del_opt']] = 0;
				/* Remove any reference to the image from the body to prevent broken images */
				if (strpos($msg_body, '[img]/egroupware/fudforum/3814588639/index.php?t=getfile&id='.$_POST['file_del_opt'].'[/img]') !== false) {
					$msg_body = str_replace('[img]/egroupware/fudforum/3814588639/index.php?t=getfile&id='.$_POST['file_del_opt'].'[/img]', '', $msg_body);
				}
				$attach_count--;
			}
		}
	} else {
		$attach_count = 0;
		$file_array = '';
	}

	/* deal with newly uploaded files */
	if ($PRIVATE_ATTACHMENTS > 0 && isset($_FILES['attach_control']) && $_FILES['attach_control']['size'] > 0) {
		if ($_FILES['attach_control']['size'] > $PRIVATE_ATTACH_SIZE) {
			$MAX_F_SIZE = $PRIVATE_ATTACH_SIZE;
			$attach_control_error = '<font class="ErrorText">File Attachment is too big (over allowed limit of '.$MAX_F_SIZE.' bytes)</font><br>';
		} else {
			if (filter_ext($_FILES['attach_control']['name'])) {
				$attach_control_error = '<font class="ErrorText">The file you are trying to upload doesn&#39;t match the allowed file types.</font><br>';
			} else {
				if (($attach_count+1) <= $PRIVATE_ATTACHMENTS) {
					$val = attach_add($_FILES['attach_control'], _uid, 1);
					$attach_list[$val] = $val;
					$attach_count++;
				} else {
					$attach_control_error = '<font class="ErrorText">You are trying to upload more files then it is allowed.</font><br>';
				}
			}
		}
	}

	if ((isset($_POST['btn_submit']) && !check_ppost_form($_POST['msg_subject'])) || isset($_POST['btn_draft'])) {
		$msg_p = new fud_pmsg;
		$msg_p->pmsg_opt = (int) $msg_smiley_disabled | (int) $msg_show_sig | (int) $msg_track;
		$msg_p->attach_cnt = $attach_count;
		$msg_p->icon = $msg_icon;
		$msg_p->body = $msg_body;
		$msg_p->subject = $msg_subject;
		$msg_p->fldr = isset($_POST['btn_submit']) ? 3 : 4;
		$msg_p->to_list = $_POST['msg_to_list'];

		$msg_p->body = apply_custom_replace($msg_p->body);
		if ($FUD_OPT_1 & 4096) {
			$msg_p->body = tags_to_html($msg_p->body, $FUD_OPT_1 & 16384);
		} else if ($FUD_OPT_1 & 2048) {
			$msg_p->body = nl2br(htmlspecialchars($msg_p->body));
		}

		if ($FUD_OPT_1 & 6144) {
			char_fix($msg_p->body);
		}

		if (!($msg_p->pmsg_opt & 2)) {
			$msg_p->body = smiley_to_post($msg_p->body);
		}
		fud_wordwrap($msg_p->body);

		$msg_p->ouser_id = _uid;

		$msg_p->subject = apply_custom_replace($msg_p->subject);
		$msg_p->subject = htmlspecialchars($msg_p->subject);

		char_fix($msg_p->subject);

		if (empty($_POST['msg_id'])) {
			$msg_p->pmsg_opt = $msg_p->pmsg_opt &~ 96;
			if ($_POST['reply']) {
				$msg_p->ref_msg_id = 'R'.$_POST['reply'];
				$msg_p->pmsg_opt |= 64;
			} else if ($_POST['forward']) {
				$msg_p->ref_msg_id = 'F'.$_POST['forward'];
			} else {
				$msg_p->ref_msg_id = null;
				$msg_p->pmsg_opt |= 32;
			}

			$msg_p->add();
		} else {
			$msg_p->id = (int) $_POST['msg_id'];
			$msg_p->sync();
		}

		if (isset($attach_list)) {
			attach_finalize($attach_list, $msg_p->id, 1);

			/* we need to add attachments to all copies of the message */
			if (!isset($_POST['btn_draft'])) {
				$c = uq('SELECT id, original_name, mime_type, fsize FROM phpgw_fud_attach WHERE message_id='.$msg_p->id.' AND attach_opt=1');
				while ($r = db_rowarr($c)) {
					$atl[$r[0]] = "'".addslashes($r[1])."', ".$r[2].", ".$r[3];
				}
				if (isset($atl)) {
					foreach ($GLOBALS['send_to_array'] as $mid) {
						foreach ($atl as $k => $v) {
							$aid = db_qid('INSERT INTO phpgw_fud_attach (owner, attach_opt, message_id, original_name, mime_type, fsize, location) VALUES(' . $mid[0] . ', 1, ' . $mid[1] . ', ' . $v .', \'\')');
							$aidl[] = $aid;
							copy($FILE_STORE . $k . '.atch', $FILE_STORE . $aid . '.atch');
							@chmod($FILE_STORE . $aid . '.atch', ($FUD_OPT_2 & 8388608 ? 0600 : 0666));
						}
					}
					$cc = __FUD_SQL_CONCAT__.'('.__FUD_SQL_CONCAT__."('".$FILE_STORE."', id), '.atch')";
					q('UPDATE phpgw_fud_attach SET location='.$cc.' WHERE id IN('.implode(',', $aidl).')');
				}
			}
		}

		header('Location: /egroupware/fudforum/3814588639/index.php?t=pmsg&'._rsidl.'&fldr=1');
		exit;
	}

	$no_spell_subject = ($reply && $old_subject == $msg_subject) ? 1 : 0;

	if (isset($_POST['btn_spell'])) {
		$text = apply_custom_replace($_POST['msg_body']);
		$text_s = apply_custom_replace($_POST['msg_subject']);

		if ($FUD_OPT_1 & 4096) {
			$text = tags_to_html($text, $FUD_OPT_1 & 16384);
		} else if ($FUD_OPT_1 & 2048) {
			$text = htmlspecialchars($text);
		}

		if ($FUD_OPT_1 & 6144) {
			char_fix($text);
		}

		if ($FUD_OPT_1 & 8192 && !$msg_smiley_disabled) {
			$text = smiley_to_post($text);
		}

	 	if ($text) {
			$text = spell_replace(tokenize_string($text), 'body');

			if ($FUD_OPT_1 & 8192 && !$msg_smiley_disabled) {
				$msg_body = post_to_smiley($text);
			}

			if ($FUD_OPT_1 & 4096) {
				$msg_body = html_to_tags($msg_body);
			} else if ($FUD_OPT_1 & 2048) {
				reverse_fmt($msg_body);
			}
			$msg_body = apply_reverse_replace($msg_body);
		}

		if ($text_s && !$no_spell_subject) {
			$text_s = htmlspecialchars($text_s);
			char_fix($text_s);
			$text_s = spell_replace(tokenize_string($text_s), 'subject');
			reverse_fmt($text_s);
			$msg_subject = apply_reverse_replace($text_s);
		}
	}

	ses_update_status($usr->sid, 'Using private messaging');

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

	$cur_ppage = tmpl_cur_ppage('', $folders);

	$spell_check_button = ($FUD_OPT_1 & 2097152 && extension_loaded('pspell') && $usr->pspell_lang) ? '<input type="submit" class="button" value="Spell-check Message" name="spell">&nbsp;' : '';

	if (isset($_POST['preview']) || isset($_POST['spell'])) {
		$text = apply_custom_replace($_POST['msg_body']);
		$text_s = apply_custom_replace($_POST['msg_subject']);

		if ($FUD_OPT_1 & 4096) {
			$text = tags_to_html($text, $FUD_OPT_1 & 16384);
		} else if ($FUD_OPT_1 & 2048) {
			$text = nl2br(htmlspecialchars($text));
		}

		if ($FUD_OPT_1 & 6144) {
			char_fix($text);
		}

		if ($FUD_OPT_1 & 8192 && !$msg_smiley_disabled) {
			$text = smiley_to_post($text);
		}
		$text_s = htmlspecialchars($text_s);
		char_fix($text_s);

		$spell = $spell_check_button && isset($_POST['spell']);

		if ($spell && strlen($text)) {
			$text = check_data_spell($text, 'body', $usr->pspell_lang);
		}
		fud_wordwrap($text);

		$subj = ($spell && !$no_spell_subject && $text_s) ? check_data_spell($text_s, 'subject', $usr->pspell_lang) : $text_s;

		$signature = ($FUD_OPT_1 & 32768 && $usr->sig && $msg_show_sig) ? '<p><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br />'.$usr->sig.'' : '';
		$apply_spell_changes = $spell ? '<input type="submit" class="button" name="btn_spell" value="Apply Spelling Changes">&nbsp;' : '';
		$preview_message = '<div align="center"><table border="0" cellspacing="1" cellpadding="2" class="PreviewTable">
<tr><th colspan=2>Post Preview</th></tr>
<tr><td class="RowStyleA"><font class="MsgSubText">'.$subj.'</font></td></tr>
<tr><td class="RowStyleA"><font class="MsgBodyText">'.$text.$signature.'</font></td></tr>
<tr><td align="left" class="RowStyleB">'.$apply_spell_changes.'<input type="submit" class="button" name="btn_submit" value="Send" tabindex="5" onClick="javascript: document.post_form.btn_action.value=\'send\';">&nbsp;<input type="submit" tabindex="4" class="button" value="Preview Message" name="preview">&nbsp;'.$spell_check_button.'<input type="submit" class="button" name="btn_draft" value="Save Draft" onClick="javascript: document.post_form.btn_action.value=\'draft\';"></td></tr>
</table></div><br />';
	} else {
		$preview_message = '';
	}

	$post_error = is_post_error() ? '<h4 align="center"><font class="ErrorText">You have an error</font></h4>' : '';

	$to_err = get_err('msg_to_list');
	$msg_subect_err = get_err('msg_subject');
	$message_err = get_err('msg_body',1);

	$post_smilies = $FUD_OPT_1 & 8192 ? draw_post_smiley_cntrl() : '';
	$post_icons = draw_post_icons($msg_icon);
	$fud_code_icons = $FUD_OPT_1 & 4096? '<tr class="RowStyleA"><td nowrap class="GenText">Formatting Tools:</td><td>
<table border=0 cellspacing=0 cellpadding=0>
<tr><td>
<table border=0 cellspacing=1 cellpadding=2 class="FormattingToolsBG">
<tr>
<td class="FormattingToolsCLR"><a href="javascript: insertTag(document.post_form.msg_body, \'[B]\', \'[/B]\');"><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/b_bold.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: insertTag(document.post_form.msg_body, \'[I]\', \'[/I]\');"><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/b_italic.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: insertTag(document.post_form.msg_body, \'[U]\', \'[/U]\');"><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/b_underline.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: insertTag(document.post_form.msg_body, \'[ALIGN=left]\', \'[/ALIGN]\');"><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/b_aleft.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: insertTag(document.post_form.msg_body, \'[ALIGN=center]\', \'[/ALIGN]\');"><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/b_acenter.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: insertTag(document.post_form.msg_body, \'[ALIGN=right]\', \'[/ALIGN]\');"><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/b_aright.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: url_insert();"><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/b_url.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: email_insert();"><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/b_email.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: image_insert();"><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/b_image.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: window_open(\'/egroupware/fudforum/3814588639/index.php?t=mklist&amp;'._rsid.'&amp;tp=OL:1\', \'listmaker\', 350, 350);"><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/b_numlist.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: window_open(\'/egroupware/fudforum/3814588639/index.php?t=mklist&amp;'._rsid.'&amp;tp=UL:square\', \'listmaker\', 350, 350);"><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/b_bulletlist.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: insertTag(document.post_form.msg_body, \'[QUOTE]\', \'[/QUOTE]\');"><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/b_quote.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: insertTag(document.post_form.msg_body, \'[CODE]\', \'[/CODE]\');"><img alt="" src="/egroupware/fudforum/3814588639/theme/default/images/b_code.gif" /></a></td>
</tr>
</table>
</td>
<td>&nbsp;&nbsp;
<select name="fnt_size" onChange="javascript:insertTag(document.post_form.msg_body, \'[SIZE=\'+document.post_form.fnt_size.options[this.selectedIndex].value+\']\', \'[/SIZE]\'); document.post_form.fnt_size.options[0].selected=true">
<option value="" selected>Size</option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
</select>
<select name="fnt_color" onChange="javascript:insertTag(document.post_form.msg_body, \'[COLOR=\'+document.post_form.fnt_color.options[this.selectedIndex].value+\']\', \'[/COLOR]\'); document.post_form.fnt_color.options[0].selected=true">
<option value="">Color</option>
<option value="skyblue" style="color:skyblue">Sky Blue</option>
<option value="royalblue" style="color:royalblue">Royal Blue</option>
<option value="blue" style="color:blue">Blue</option>
<option value="darkblue" style="color:darkblue">Dark Blue</option>
<option value="orange" style="color:orange">Orange</option>
<option value="orangered" style="color:orangered">Orange Red</option>
<option value="crimson" style="color:crimson">Crimson</option>
<option value="red" style="color:red">Red</option>
<option value="firebrick" style="color:firebrick">Firebrick</option>
<option value="darkred" style="color:darkred">Dark Red</option>
<option value="green" style="color:green">Green</option>
<option value="limegreen" style="color:limegreen">Lime Green</option>
<option value="seagreen" style="color:seagreen">Sea Green</option>
<option value="deeppink" style="color:deeppink">Deep Pink</option>
<option value="tomato" style="color:tomato">Tomato</option>
<option value="coral" style="color:coral">Coral</option>
<option value="purple" style="color:purple">Purple</option>
<option value="indigo" style="color:indigo">Indigo</option>
<option value="burlywood" style="color:burlywood">Burly Wood</option>
<option value="sandybrown" style="color:sandybrown">Sandy Brown</option>
<option value="sienna" style="color:sienna">Sienna</option>
<option value="chocolate" style="color:chocolate">Chocolate</option>
<option value="teal" style="color:teal">Teal</option>
<option value="silver" style="color:silver">Silver</option>
</select>
<select name="fnt_face" onChange="javascript:insertTag(document.post_form.msg_body, \'[FONT=\'+document.post_form.fnt_face.options[this.selectedIndex].value+\']\', \'[/FONT]\'); document.post_form.fnt_face.options[0].selected=true">
<option value="">Font</option>
<option value="Arial" style="font-family:Arial">Arial</option>
<option value="Times" style="font-family:Times">Times</option>
<option value="Courier" style="font-family:Courier">Courier</option>
<option value="Century" style="font-family:Century">Century</option>
</select>
</td></tr></table></td></tr>' : '';

	$post_options = tmpl_post_options('private');

	if ($FUD_OPT_2 & 32768) {
		$private = '1';
	} else {
		$private = '&amp;private=1';
	}

	if ($PRIVATE_ATTACHMENTS > 0) {
		$file_attachments = draw_post_attachments((isset($attach_list) ? $attach_list : ''), round($PRIVATE_ATTACH_SIZE / 1024), $PRIVATE_ATTACHMENTS, $attach_control_error, $private, $msg_id);
	} else {
		$file_attachments = '';
	}

	$msg_track_check = $msg_track ? ' checked' : '';
	$msg_show_sig_check = $msg_show_sig ? ' checked' : '';

	if ($FUD_OPT_1 & 8192) {
		$msg_smiley_disabled_check = $msg_smiley_disabled ? ' checked' : '';
		$disable_smileys = '<tr><td><input type="checkbox" name="msg_smiley_disabled" value="Y"'.$msg_smiley_disabled_check.'></td><td class="GenText"><b>Disable smilies in this message</b></td></tr>';
	} else {
		$disable_smileys = '';
	}

	if ($reply && ($mm = db_sab('SELECT p.*, u.sig, u.alias, u.users_opt, u.posted_msg_count, u.join_date, u.last_visit FROM phpgw_fud_pmsg p INNER JOIN phpgw_fud_users u ON p.ouser_id=u.id WHERE p.duser_id='._uid.' AND p.id='.$reply))) {
		fud_use('drawpmsg.inc');
		$dpmsg_prev_message = $dpmsg_next_message = '';
		$reference_msg = tmpl_drawpmsg($mm, $usr, true);
		$reference_msg = '<br /><br />
<div align="center">message you are forwarding or replying to</div>
<table border=0 width="100%" cellspacing=0 cellpadding=3 class="dashed">
<tr><td>
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
'.$reference_msg.'
</table>
</td></tr>
</table>';
	} else {
		$reference_msg = '';
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
<form action="/egroupware/fudforum/3814588639/index.php?t=ppost" method="post" name="post_form" <?php echo $ppost_enctype; ?> onSubmit="javascript: document.post_form.btn_submit.disabled = true; document.post_form.btn_draft.disabled = true;">
<?php echo $post_error; ?>
<?php echo $preview_message; ?>
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th colspan=2>Post Form<a name="ptop"> </a></th></tr>
<tr class="RowStyleB"><td nowrap class="GenText">Logged in user:</td><td class="GenText" width="100%"><?php echo $usr->login; ?></td></tr>
<tr class="RowStyleB"><td class="GenText">To:</td><td class="GenText"><input type="text" name="msg_to_list" value="<?php echo $msg_to_list; ?>" tabindex="1"> <?php echo $pm_find_user; ?> [<a href="javascript://" onClick="javascript: window_open('/egroupware/fudforum/3814588639/index.php?t=qbud&amp;<?php echo _rsid; ?>&amp;1=1', 'buddy_list',275,300);" class="GenLink">Select from Buddy List</a>]<?php echo $to_err; ?></td></tr>
<tr class="RowStyleB"><td class="GenText">Title:</td><td class="GenText"><input type="text" maxLength=100 name="msg_subject" value="<?php echo $msg_subject; ?>" size=50 tabindex="2"> <?php echo $msg_subect_err; ?></td></tr>
<?php echo $post_icons; ?>
<?php echo $post_smilies; ?>
<?php echo $fud_code_icons; ?>
<tr class="RowStyleA"><td nowrap valign=top class="GenText">Body:<br /><br /><?php echo $post_options; ?></td><td><?php echo $message_err; ?><textarea id="txtb" rows="20" cols="65" wrap="virtual" tabindex="3" name="msg_body" onKeyUp="storeCaret(this);" onClick="storeCaret(this);" onSelect="storeCaret(this);"><?php echo $msg_body; ?></textarea></td></tr>
<?php echo $file_attachments; ?>
<tr class="RowStyleB" valign="top">
<td class="GenText">Options:</td>
<td>
<table border=0 cellspacing=0 cellpadding=1>
<tr><td><input type="checkbox" name="msg_track" value="Y"<?php echo $msg_track_check; ?>></td><td class="GenText"><b>Track This Message</b></td></tr>
<tr><td>&nbsp;</td><td><font class="SmallText">Notify me (via private message) when this message gets read.</font></td></tr>
<tr><td><input type="checkbox" name="msg_show_sig" value="Y"<?php echo $msg_show_sig_check; ?>></td><td class="GenText"><b>Include Signature</b></td></tr>
<tr><td>&nbsp;</td><td><font class="SmallText">Include my profile signature.</font></td></tr>
<?php echo $disable_smileys; ?>
</table>
</td>
</tr>
<tr class="RowStyleA"><td class="GenText" align="right" colspan=2><input type="submit" tabindex="4" class="button" value="Preview Message" name="preview">&nbsp;<?php echo $spell_check_button; ?><input type="submit" class="button" name="btn_draft" value="Save Draft" onClick="javascript: document.post_form.btn_action.value='draft';">&nbsp;<input type="submit" class="button" name="btn_submit" value="Send" tabindex="5" onClick="javascript: document.post_form.btn_action.value='send';"></td></tr>
</table>
<?php echo _hs; ?>
<input type="hidden" name="btn_action" value="">
<input type="hidden" name="msg_id" value="<?php echo $msg_id; ?>">
<input type="hidden" name="reply" value="<?php echo $reply; ?>">
<input type="hidden" name="forward" value="<?php echo $forward; ?>">
<input type="hidden" name="old_subject" value="<?php echo $old_subject; ?>">
<input type="hidden" name="msg_ref_msg_id" value="<?php echo $msg_ref_msg_id; ?>">
<input type="hidden" name="prev_loaded" value="1">
</form>
<?php echo $reference_msg; ?>
<?php echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>