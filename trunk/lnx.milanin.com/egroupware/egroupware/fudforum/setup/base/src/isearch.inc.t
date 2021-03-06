<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: isearch.inc.t,v 1.2 2003/12/18 18:20:49 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/


function delete_msg_index($msg_id)
{
	q('DELETE FROM {SQL_TABLE_PREFIX}index WHERE msg_id='.$msg_id);
	q('DELETE FROM {SQL_TABLE_PREFIX}title_index WHERE msg_id='.$msg_id);
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
		ins_m('{SQL_TABLE_PREFIX}search', 'word', $w2);
	} else {
		ins_m('{SQL_TABLE_PREFIX}search', 'word', $w2, 'text');
	}

	/* This allows us to return right away, meaning we don't need to wait
	 * for any locks to be released etc... */
	if (__dbtype__ == 'mysql') {
		$del = 'DELAYED';
	} else {
		$del = '';
	}

	if (isset($w1)) {
		db_li('INSERT '.$del.' INTO {SQL_TABLE_PREFIX}title_index (word_id, msg_id) SELECT id, '.$msg_id.' FROM {SQL_TABLE_PREFIX}search WHERE word IN('.implode(',', $w1).')', $ef);
	}
	db_li('INSERT '.$del.' INTO {SQL_TABLE_PREFIX}index (word_id, msg_id) SELECT id, '.$msg_id.' FROM {SQL_TABLE_PREFIX}search WHERE word IN('.implode(',', $w2).')', $ef);
}
?>