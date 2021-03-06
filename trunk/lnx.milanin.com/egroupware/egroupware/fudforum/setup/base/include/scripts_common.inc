<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: scripts_common.inc,v 1.2 2003/10/29 05:34:53 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

function match_user_to_post($from_email, $from_name, $create_users, &$user_id)
{
	/* Try to identify user by email */
	$user_id = q_singleval("SELECT id FROM ".sql_p."users WHERE email='".addslashes($from_email)."'");

	/* If user was not found via email, try to look the user up by login */
	if (empty($user_id) && !empty($from_name)) {
		$user_id = q_singleval("SELECT id FROM ".sql_p."users WHERE login='".addslashes($from_name)."'");
	}

	if (empty($user_id)) {
		$user_id = 0;
	}

	return $user_id;
}

function get_fud_reply_id($complex, $forum_id, $subject, $data)
{
	if (!empty($data)) {
		if (is_string($data)) {
			$data = array($data);
		}

		foreach ($data as $reply_id) {
			if (($r = db_saq("SELECT id, thread_id FROM ".sql_p."msg WHERE mlist_msg_id='".addslashes($reply_id)."'"))) {
				break;
			}
		}
	}
	if (empty($r) && $complex) {
		// This is slow, but only way to match 'rouge' replies in the event no reference fields are avaliable
		if (preg_match('!(Re|Wa)\s*:(.*)$!i', $subject, $matches)) {
			$r = db_saq('SELECT m.id, m.thread_id FROM '.sql_p.'msg m INNER JOIN '.sql_p.'thread t ON m.thread_id=t.id WHERE t.forum_id='.$forum_id.' AND m.subject='.strnull(addslashes(trim($matches[2]))));
			if (!$r) {
				$r = db_saq("SELECT m.id, m.thread_id FROM ".sql_p."msg m INNER JOIN ".sql_p."thread t ON m.thread_id=t.id WHERE t.forum_id=".$forum_id." AND m.subject LIKE '".str_replace('_', '\\_', addslashes(trim($matches[2])))."%' LIMIT 1");
			}
		}
	}

	return !empty($r) ? array((int)$r[0], (int)$r[1]) : array(0, 0);
}

function parse_ip($str)
{
	if (preg_match('!([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})!', $str, $m)) {
		return $m[1];
	} else {
		return;
	}
}

function decode_string($str, $encoding)
{
	switch ($encoding) {
		case 'quoted-printable':
			// Remove soft line breaks & decode
		        return quoted_printable_decode(preg_replace("!=\r?\n!", '', $str));
			break;
		case 'base64':
			return base64_decode($str);
			break;
		default:
			return $str;
			break;
	}
}

function decode_header_value($val)
{
	// check if string needs to be decoded
	if (strpos($val, '?') === false) {
		return trim($val);
	}

	// Decode String
	if (preg_match_all('!(.*?)(=\?([^?]+)\?(Q|B)\?([^?]*)\?=)[[:space:]]*(.*)!i', $val, $m)) {
		$newval = '';

		$c = count($m[4]);
		for ($i = 0; $i < $c; $i++) {
			$ec_type = strtolower($m[4][$i]);

			if ($ec_type == 'q') {
				$newval .= decode_string(str_replace('_', ' ', $m[5][$i]), 'quoted-printable');
			} else if ($ec_type == 'b') {
				$newval .= decode_string($m[5][$i], 'base64');
			}

			if (!empty($m[5][$i])) {
				$newval .= ' '.$m[6][$i];
			}
			if (!empty($m[1][$i])) {
				$newval = $m[1][$i].$newval;
			}
		}
		$val = trim($newval);
	}
	return trim($val);
}
?>