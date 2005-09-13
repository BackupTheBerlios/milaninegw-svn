<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: pdf.php.t,v 1.1.1.1 2003/10/17 21:11:26 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

class fud_pdf
{
	var $pdf, $pw, $ph, $pg_num, $pg_title, $hmargin, $wmargin, $y, $fonts;

	function fud_pdf($author, $title, $subject, $page_type='letter', $hmargin=15, $wmargin=15)
	{
		$this->pdf = pdf_new();
		pdf_open_file($this->pdf, '');
		pdf_set_info($this->pdf, 'Author',	$author);
		pdf_set_info($this->pdf, 'Title',	$title);
		pdf_set_info($this->pdf, 'Creator',	$author);
		pdf_set_info($this->pdf, 'Subject',	$subject);
		pdf_set_value($this->pdf, 'compress', 	9);

		switch ($page_type) {
			case 'A0':
				$this->pw = 2380;
				$this->ph = 3368;
				break;
			case 'A1':
				$this->pw = 1684;
				$this->ph = 2380;
				break;
			case 'A2':
				$this->pw = 1190;
				$this->ph = 1684;
				break;
			case 'A3':
				$this->pw = 842;
				$this->ph = 1190;
				break;
			case 'A4':
				$this->pw = 595;
				$this->ph = 842;
				break;
			case 'A5':
				$this->pw = 421;
				$this->ph = 595;
				break;
			case 'A6':
				$this->pw = 297;
				$this->ph = 421;
				break;
			case 'B5':
				$this->pw = 501;
				$this->ph = 709;
				break;
			case 'letter':
			default:
				$this->pw = 612;
				$this->ph = 792;
				break;
			case 'legal':
				$this->pw = 612;
				$this->ph = 1008;
				break;
			case 'ledger':
				$this->pw = 1224;
				$this->ph = 792;
				break;
		}

		$this->hmargin = $hmargin;
		$this->wmargin = $wmargin;

		$fonts = array('Courier', 'Courier-Bold', 'Helvetica-Bold', 'Helvetica');
		foreach ($fonts as $f) {
			$this->fonts[$f] = pdf_findfont($this->pdf, $f, 'host', false);
		}
	}

	function begin_page($title)
	{
		pdf_begin_page($this->pdf, $this->pw, $this->ph);
		pdf_setlinewidth($this->pdf, 1);
		$ttl = $title;
		if ($this->pg_num) {
			$this->pg_num++;
			$ttl .= ' #'. $this->pg_num;
		} else {
			$this->pg_num = 1;
		}
		pdf_add_bookmark($this->pdf, $ttl);
		pdf_setfont($this->pdf, $this->fonts['Courier'], 12);
		pdf_set_text_pos($this->pdf, $this->wmargin, ($this->ph - $this->hmargin));
		$this->pg_title = $title;
	}

	function input_text($text)
	{
		pdf_setfont($this->pdf, $this->fonts['Courier'], 12);

		$max_cpl = pdf_stringwidth($this->pdf, 'w');
		$max_cpl = floor(($this->pw - 2 * $this->wmargin) / $max_cpl);

		foreach ($text as $line) {
			if (strlen($line) > $max_cpl) {
				$parts = explode("\n", wordwrap($line, $max_cpl, "\n", 1));
				$line = $parts[0];
				unset($parts[0]);
			}
			if (pdf_get_value($this->pdf, 'texty', 0) <= ($this->hmargin + 12)) {
				$this->end_page();
				$this->begin_page($this->pg_title);
			}
			pdf_continue_text($this->pdf, $line);
			if (isset($parts) && count($parts)) {
				foreach ($parts as $p) {
					if (pdf_get_value($this->pdf, 'texty', 0) <= ($this->hmargin + 12)) {
						$this->end_page();
						$this->begin_page($this->pg_title);
					}
					pdf_continue_text($this->pdf, $p);
				}
				unset($parts);
			}
		}
	}

	function end_page()
	{
		pdf_end_page($this->pdf);
	}

	function finish()
	{
		pdf_close($this->pdf);
		pdf_delete($this->pdf);
	}

	function draw_line()
	{
		$this->y = pdf_get_value($this->pdf, 'texty', 0) - 3;
		pdf_moveto($this->pdf, $this->wmargin, $this->y);
		pdf_lineto($this->pdf, ($this->pw - $this->wmargin), $this->y);
		pdf_stroke($this->pdf);
	}

	function add_link($url, $caption)
	{
		$oh = pdf_get_value($this->pdf, 'texty', 0);
		pdf_show($this->pdf, $caption);
		$y = pdf_get_value($this->pdf, 'texty', 0);
		$w = pdf_get_value($this->pdf, 'textx', 0);
		$ow = pdf_get_value($this->pdf, 'textx', 0) - pdf_stringwidth($this->pdf, $caption);

		pdf_set_border_style($this->pdf, 'dashed', 0);
		pdf_add_weblink($this->pdf, $ow, $oh, $w, ($oh + 12), $url);
	}

	function add_attacments($attch)
	{
		pdf_setfont($this->pdf, $this->fonts['Courier-Bold'], 20);
		pdf_continue_text($this->pdf, 'File Attachments');

		$this->draw_line();

		pdf_setfont($this->pdf, $this->fonts['Helvetica'], 14);
		$y = $this->y - 3;
		$i = 0;
		foreach ($attch as $a) {
			pdf_set_text_pos($this->pdf, $this->wmargin, $y);
			pdf_continue_text($this->pdf, ++$i . ') ');
			$this->add_link($GLOBALS['WWW_ROOT'] . 'index.php?t=getfile&id='.$a['id'], $a['name']);
			pdf_show($this->pdf, ', downloaded '.$a['nd'].' times');
			$y -= 17;
		}
	}

	function add_poll($name, $opts, $ttl_votes)
	{
		$this->y = pdf_get_value($this->pdf, 'texty', 0) - 3;

		pdf_set_text_pos($this->pdf, $this->wmargin, $this->y - 3);
		pdf_setfont($this->pdf, $this->fonts['Courier-Bold'], 20);
		pdf_continue_text($this->pdf, $name);
		pdf_setfont($this->pdf, $this->fonts['Courier-Bold'], 16);
		pdf_show($this->pdf, '(total votes: '.$ttl_votes.')');

		$this->draw_line();

		$ttl_w = round($this->pw * 0.66);
		$ttl_h = 20;
		$p1 = floor($ttl_w / 100);
		$this->y -= 10;
		/* avoid /0 warnings and safe to do, since we'd be multiplying 0 since there are no votes */
		if (!$ttl_votes) {
			$ttl_votes = 1;
		}

		pdf_setfont($this->pdf, $this->fonts['Helvetica-Bold'], 14);

		foreach ($opts as $o) {
			$w1 = $p1 * (($o['votes'] / $ttl_votes) * 100);
			$h1 = $this->y - $ttl_h;

			pdf_setcolor($this->pdf, 'both', 'rgb', 0.92, 0.92, 0.92);
			pdf_rect($this->pdf, $this->wmargin, $h1, $w1, $ttl_h);
			pdf_fill_stroke($this->pdf);
			pdf_setcolor($this->pdf, 'both', 'rgb', 0, 0, 0);
			pdf_show_xy($this->pdf, $o['name'] . "\t\t" . $o['votes'] . '/('.round(($o['votes'] / $ttl_votes) * 100).'%)', $this->wmargin + 2, $h1 + 3);
			$this->y = $h1 - 10;
		}
	}

	function message_header($subject, $author, $date, $id, $th)
	{
		$y = pdf_get_value($this->pdf, 'texty', 0) - 3;
		if ($y < 100) {
			$this->end_page();
			$this->begin_page($this->pg_title);
			$y = $this->ph - $this->hmargin;
		}
		pdf_moveto($this->pdf, $this->wmargin, $y);
		pdf_lineto($this->pdf, ($this->pw - $this->wmargin), $y);
		pdf_moveto($this->pdf, $this->wmargin, $y - 3);
		pdf_lineto($this->pdf, ($this->pw - $this->wmargin), $y - 3);
		pdf_stroke($this->pdf);

		pdf_set_text_pos($this->pdf, $this->wmargin, ($y - 5));

		pdf_setfont($this->pdf, $this->fonts['Helvetica'], 14);
		pdf_continue_text($this->pdf, 'Subject: ' . $subject);
		pdf_continue_text($this->pdf, 'Posted by '.$author.' on '.gmdate('D, d M Y H:i:s \G\M\T', $date));
		pdf_continue_text($this->pdf, 'URL: ');
		$url = $GLOBALS['WWW_ROOT'].'?t=rview&th='.$th.'&goto='.$id;
		$this->add_link($url, $url);

		$this->draw_line();

		pdf_set_text_pos($this->pdf, $this->wmargin, ($this->y - 3));
	}

	function end_message()
	{
		$y = pdf_get_value($this->pdf, 'texty', 0) - 10;
		pdf_moveto($this->pdf, $this->wmargin, $y);
		pdf_lineto($this->pdf, ($this->pw - $this->wmargin), $y);
		pdf_moveto($this->pdf, $this->wmargin, $y - 3);
		pdf_lineto($this->pdf, ($this->pw - $this->wmargin), $y - 3);
		pdf_stroke($this->pdf);

		pdf_set_text_pos($this->pdf, $this->wmargin, ($y - 20));
	}
}

function post_to_smiley($text, $re)
{
	return ($re ? strtr($text, $re) : $text);
}

	require('./GLOBALS.php');
	fud_egw('', 1);
	fud_use('err.inc');

	/* this potentially can be a longer form to generate */
	@set_time_limit($PDF_MAX_CPU);

	/* before we go on, we need to do some very basic activation checks */
	if (!($FUD_OPT_1 & 1)) {
		fud_use('errmsg.inc');
		exit($DISABLED_REASON . __fud_ecore_adm_login_msg);
	}
	if (!$FORUM_TITLE && @file_exists($WWW_ROOT_DISK.'install.php')) {
		fud_use('errmsg.inc');
	        exit(__fud_e_install_script_present_error);
	}

function fud_sql_error_handler($query, $error_string, $error_number, $server_version)
{
	if (db_locked()) {
		if ((__dbtype__ == 'mysql' && $query != 'UNLOCK TABLES') || (__dbtype__ == 'pgsql' && $query != 'COMMIT WORK')) {
			db_unlock();
		}
	}

	if (function_exists("debug_backtrace")) {
		$tmp = debug_backtrace();
		$_SERVER['PATH_TRANSLATED'] = '';
		foreach ($tmp as $v) {
			$_SERVER['PATH_TRANSLATED'] .= "{$v['file']}:{$v['line']}<br />\n";
		}
	} else if (!isset($_SERVER['PATH_TRANSLATED'])) {
		$_SERVER['PATH_TRANSLATED'] = realpath(__FILE__);
	}

	$error_msg = "(".$_SERVER['PATH_TRANSLATED'].") ".$error_number.": ".$error_string."<br />\n";
	$error_msg .= "Query: ".htmlspecialchars($query)."<br />\n";
	$error_msg .= "Server Version: ".$server_version."<br />\n";
	if (isset($_SERVER['HTTP_REFERER'])) {
		$error_msg .= "[Referring Page] ".$_SERVER['HTTP_REFERER']."<br />\n";
	}

	if (!error_log('['.gmdate("D M j G:i:s T Y", __request_timestamp__).'] '.base64_encode($error_msg)."\n", 3, $GLOBALS['ERROR_PATH'].'sql_errors')) {
		echo "<b>UNABLE TO WRITE TO SQL LOG FILE</b><br>\n";
		echo $error_msg;
	} else {
		if (defined('forum_debug') || (isset($GLOBALS['usr']->users_opt) && $GLOBALS['usr']->users_opt & 1048576)) {
			echo $error_msg;
		} else {
			trigger_error('SQL Error has occurred, please contact the <a href="mailto:'.$GLOBALS['ADMIN_EMAIL'].'?subject=SQL%20Error">administrator</a> of the forum and have them review the forum&#39;s SQL query log', E_USER_ERROR);
			if (ini_get('display_errors') !== 1) {
				exit('SQL Error has occurred, please contact the <a href="mailto:'.$GLOBALS['ADMIN_EMAIL'].'?subject=SQL%20Error">administrator</a> of the forum and have them review the forum&#39;s SQL query log');
			}
		}
	}
	exit;
}

if (!defined('__dbtype__')) {
	define('__dbtype__', 'mysql');
	define('__FUD_SQL_CONCAT__', 'CONCAT');
}

function get_version()
{
	if (!defined('__FUD_SQL_VERSION__')) {
		define('__FUD_SQL_VERSION__', @current(mysql_fetch_row(mysql_query('SELECT VERSION()', fud_sql_lnk))));
	}
	return __FUD_SQL_VERSION__;
}


function db_lock($tables)
{
	if (!empty($GLOBALS['__DB_INC_INTERNALS__']['db_locked'])) {
		fud_sql_error_handler("Recursive Lock", "internal", "internal", get_version());
	} else {
		q('LOCK TABLES '.$tables);
		$GLOBALS['__DB_INC_INTERNALS__']['db_locked'] = 1;
	}
}

function db_unlock()
{
	if (empty($GLOBALS['__DB_INC_INTERNALS__']['db_locked'])) {
		unset($GLOBALS['__DB_INC_INTERNALS__']['db_locked']);
		fud_sql_error_handler("DB_UNLOCK: no previous lock established", "internal", "internal", get_version());
	}
	
	if (--$GLOBALS['__DB_INC_INTERNALS__']['db_locked'] < 0) {
		unset($GLOBALS['__DB_INC_INTERNALS__']['db_locked']);
		fud_sql_error_handler("DB_UNLOCK: unlock overcalled", "internal", "internal", get_version());
	}
	unset($GLOBALS['__DB_INC_INTERNALS__']['db_locked']);
	q('UNLOCK TABLES', fud_sql_lnk);
}

function db_locked()
{
	return isset($GLOBALS['__DB_INC_INTERNALS__']['db_locked']);
}

function db_affected()
{
	return mysql_affected_rows(fud_sql_lnk);	
}

function q($query)
{
	$r = mysql_query($query, fud_sql_lnk) or die (fud_sql_error_handler($query, mysql_error(fud_sql_lnk), mysql_errno(fud_sql_lnk), get_version()));
	return $r;
}

function uq($query)
{
	$r = mysql_unbuffered_query($query,fud_sql_lnk) or die (fud_sql_error_handler($query, mysql_error(fud_sql_lnk), mysql_errno(fud_sql_lnk), get_version()));
	return $r;
}

function db_count($result)
{
	return (int) @mysql_num_rows($result);
}
function db_seek($result, $pos)
{
	return mysql_data_seek($result, $pos);
}
function &db_rowobj($result)
{
	return mysql_fetch_object($result);
}
function &db_rowarr($result)
{
	return mysql_fetch_row($result);
}

function &q_singleval($query)
{
	if (($res = @mysql_fetch_row(q($query))) === false) {
		return null;
	} else {
		return $res[0];
	}
}

function get_field_list($tbl)
{
	return uq('show fields from ' . $tbl);
}

function qry_limit($limit, $off)
{
	return $off.','.$limit;
}

function get_fud_table_list()
{
	$r = uq("SHOW TABLES LIKE '".str_replace("_", "\\_", $GLOBALS['DBHOST_TBL_PREFIX'])."%'");
	while (list($ret[]) = db_rowarr($r));
	array_pop($ret);

	return $ret;	
}

function optimize_tables($tbl_list=null)
{
	if (!$tbl_list) {
		$tbl_list = get_fud_table_list();
	}

	q('OPTIMIZE TABLE '. implode(', ', $tbl_list));
}

function &db_saq($q)
{
	return @mysql_fetch_row(q($q));
}
function &db_sab($q)
{
	return @mysql_fetch_object(q($q));
}
function db_qid($q)
{
	q($q);
	return mysql_insert_id(fud_sql_lnk);
}
function &db_arr_assoc($q)
{
	return mysql_fetch_array(q($q), MYSQL_ASSOC);
}

function db_li($q, &$ef, $li=0)
{
	$r = mysql_query($q, fud_sql_lnk);
	if ($r) {
		return ($li ? mysql_insert_id(fud_sql_lnk) : $r);
	}

	/* duplicate key */
	if (mysql_errno() == 1062) {
		$ef = ltrim(strrchr(mysql_error(), ' '));
		return null;
	} else {
		die(fud_sql_error_handler($query, mysql_error(fud_sql_lnk), mysql_errno(fud_sql_lnk), get_version()));
	}
}

function ins_m($tbl, $flds, $vals, $type=0)
{
	if (!$type) {
		q("INSERT IGNORE INTO ".$tbl." (".$flds.") VALUES (".implode('),(', $vals).")");
	} else {
		q("INSERT INTO ".$tbl." (".$flds.") VALUES (".implode('),(', $vals).")");
	}
}function ses_update_status($ses_id, $str=null, $forum_id=0, $ret='')
{
	q('UPDATE phpgw_fud_ses SET forum_id='.$forum_id.', time_sec='.__request_timestamp__.', action='.($str ? "'".addslashes($str)."'" : 'NULL').', returnto='.(!is_int($ret) ? strnull(addslashes($_SERVER['QUERY_STRING'])) : 'returnto').' WHERE id='.$ses_id);
}

function ses_putvar($ses_id, $data)
{
	$cond = is_int($ses_id) ? 'id='.(int)$ses_id : "ses_id='".$ses_id."'";

	if (empty($data)) {
		q('UPDATE phpgw_fud_ses SET data=NULL WHERE '.$cond);
	} else {
		q("UPDATE phpgw_fud_ses SET data='".addslashes(serialize($data))."' WHERE ".$cond);
	}
}function init_user()
{
	$o1 =& $GLOBALS['FUD_OPT_1'];
	$o2 =& $GLOBALS['FUD_OPT_2'];

	$phpgw =& $GLOBALS['phpgw_info']['user'];

	/* delete old sessions */
	if (!(rand() % 10)) {
		q("DELETE FROM phpgw_fud_ses WHERE time_sec+".$GLOBALS['phpgw_info']['server']['sessions_timeout']." < ".__request_timestamp__);
	}

	$u = db_sab("SELECT 
			s.id AS sid, s.data, s.returnto, 
			t.id AS theme_id, t.lang, t.name AS theme_name, t.locale, t.theme, t.pspell_lang, t.theme_opt, 
			u.alias, u.posts_ppg, u.time_zone, u.sig, u.last_visit, u.last_read, u.cat_collapse_status, u.users_opt, u.ignore_list, u.ignore_list, u.buddy_list, u.id, u.group_leader_list, u.email, u.login 
			FROM phpgw_fud_ses s
			INNER JOIN phpgw_fud_users u ON u.id=(CASE WHEN s.user_id>2000000000 THEN 1 ELSE s.user_id END) 
			INNER JOIN phpgw_fud_themes t ON t.id=u.theme WHERE s.ses_id='".s."'");
	if (!$u) {
		/* registered user */
		if ($phpgw['account_lid'] != $GLOBALS['ANON_NICK']) {
			/* this means we do not have an entry for this user in the sessions table */
			$uid = q_singleval("SELECT id FROM phpgw_fud_users WHERE egw_id=".(int)$phpgw['account_id']);
			$id = db_qid("INSERT INTO phpgw_fud_ses (user_id, ses_id, time_sec) VALUES(".$uid.", '".s."', ".__request_timestamp__.")");
			$u = db_sab('SELECT s.id AS sid, s.data, s.returnto, t.id AS theme_id, t.lang, t.name AS theme_name, t.locale, t.theme, t.pspell_lang, t.theme_opt, u.alias, u.posts_ppg, u.time_zone, u.sig, u.last_visit, u.last_read, u.cat_collapse_status, u.users_opt, u.ignore_list, u.ignore_list, u.buddy_list, u.id, u.group_leader_list, u.email, u.login FROM phpgw_fud_ses s INNER JOIN phpgw_fud_users u ON u.id=s.user_id INNER JOIN phpgw_fud_themes t ON t.id=u.theme WHERE s.id='.$id);
		} else { /* anonymous user */
			do {
				$uid = 2000000000 + mt_rand(1, 147483647);
			} while (!($id = db_li("INSERT INTO phpgw_fud_ses (time_sec, ses_id, user_id) VALUES (".__request_timestamp__.", '".s."', ".$uid.")", $ef, 1)));
			$u = db_sab('SELECT s.id AS sid, s.data, s.returnto, t.id AS theme_id, t.lang, t.name AS theme_name, t.locale, t.theme, t.pspell_lang, t.theme_opt, u.alias, u.posts_ppg, u.time_zone, u.sig, u.last_visit, u.last_read, u.cat_collapse_status, u.users_opt, u.ignore_list, u.ignore_list, u.buddy_list, u.id, u.group_leader_list, u.email, u.login FROM phpgw_fud_ses s INNER JOIN phpgw_fud_users u ON u.id=1 INNER JOIN phpgw_fud_themes t ON t.id=u.theme WHERE s.id='.$id);
		}
	}
	/* grant admin access */
	if (!empty($phpgw['apps']['admin'])) {
		$u->users_opt |= 1048576;
	}

	/* this is ugly, very ugly, but there is no way around it, we need to see if the 
	 * user's language had changed and we can only do it this way.
	 */
	$langl = array('bg'=>'bulgarian', 'zh'=>'chinese_big5', 'cs'=>'czech', 'nl'=>'dutch', 'fr'=>'french', 'de'=>'german', 'it'=>'italian', 'lv'=>'latvian', 'no'=>'norwegian', 'pl'=>'polish', 'pt'=>'portuguese', 'ro'=>'romanian', 'ru'=>'russian', 'sk'=>'slovak', 'es'=>'spanish', 'sv'=>'swedish', 'tr'=>'turkish', 'en'=>'english');
	$lang =& $phpgw['preferences']['common']['lang'];
	if (isset($langl[$lang]) && $langl[$lang] != $u->lang) {
		if (!($o = db_sab("SELECT * FROM phpgw_fud_themes WHERE lang='{$langl[$lang]}'"))) {
			fud_use('compiler.inc', true);
			fud_use('theme.inc', true);
			$thm = new fud_theme;
			$thm->name = $thm->lang = $langl[$lang];
			$thm->theme = 'default';
			$thm->pspell_lang = file_get_contents($GLOBALS['DATA_DIR'].'thm/default/i18n/'.$langl[$lang].'/pspell_lang');
			$thm->locale = file_get_contents($GLOBALS['DATA_DIR'].'thm/default/i18n/'.$langl[$lang].'/locale');
			$thm->theme_opt = 1;
			$thm->add();
			compile_all('default', $langl[$lang], $langl[$lang]);
			$o = db_sab("SELECT * FROM phpgw_fud_themes WHERE lang='{$langl[$lang]}'");
		}
		$u->lang = $o->lang;
		$u->theme_name = $o->name;
		$u->locale = $o->locale;
		$u->theme_id = $o->id;
		$u->theme = $o->theme;
		$u->pspell_lang = $o->pspell_lang;
		$u->theme_opt = $o->theme_opt;

		q("UPDATE phpgw_fud_users SET theme=".$u->theme_id." WHERE id=".$u->id);
	}

	if ($u->data) {
		$u->data = @unserialize($u->data);
	}
	$u->users_opt = (int) $u->users_opt;

	/* set timezone */
	@putenv('TZ=' . $u->time_zone);
	/* set locale */
	setlocale(LC_ALL, $u->locale);

	/* view format for threads & messages */
	define('d_thread_view', $u->users_opt & 256 ? 'msg' : 'tree');
	define('t_thread_view', $u->users_opt & 128 ? 'thread' : 'threadt');

	/* theme path */
	@define('fud_theme', 'theme/' . ($u->theme_name ? $u->theme_name : 'default') . '/');

	/* define _uid, which, will tell us if this is a 'real' user or not */
	define('__fud_real_user__', ($u->id != 1 ? $u->id : 0));
	define('_uid', __fud_real_user__);

	if (__fud_real_user__) {
		q('UPDATE phpgw_fud_users SET last_visit='.__request_timestamp__.' WHERE id='.$u->id);
	}

	return $u;
}

function user_alias_by_id($id)
{
	return q_singleval('SELECT alias FROM phpgw_fud_users WHERE id='.$id);
}

function user_register_forum_view($frm_id)
{
	q('UPDATE phpgw_fud_forum_read SET last_view='.__request_timestamp__.' WHERE forum_id='.$frm_id.' AND user_id='._uid);
	if (!db_affected()) {
		db_li('INSERT INTO phpgw_fud_forum_read (forum_id, user_id, last_view) VALUES ('.$frm_id.', '._uid.', '.__request_timestamp__.')', $ef);
	}
}

function user_register_thread_view($thread_id, $tm=__request_timestamp__, $msg_id=0)
{
	if (!db_li('INSERT INTO phpgw_fud_read (last_view, msg_id, thread_id, user_id) VALUES('.$tm.', '.$msg_id.', '.$thread_id.', '._uid.')', $ef)) {
		q('UPDATE phpgw_fud_read SET last_view='.$tm.', msg_id='.$msg_id.' WHERE thread_id='.$thread_id.' AND user_id='._uid);
	}
}

function user_set_post_count($uid)
{
	$pd = db_saq("SELECT MAX(id),count(*) FROM phpgw_fud_msg WHERE poster_id=".$uid." AND apr=1");
	$level_id = (int) q_singleval('SELECT id FROM phpgw_fud_level WHERE post_count <= '.$pd[1].' ORDER BY post_count DESC LIMIT 1');
	q('UPDATE phpgw_fud_users SET u_last_post_id='.(int)$pd[0].', posted_msg_count='.(int)$pd[1].', level_id='.$level_id.' WHERE id='.$uid);
}

function user_mark_all_read($id)
{
	q('UPDATE phpgw_fud_users SET last_read='.__request_timestamp__.' WHERE id='.$id);
	q('DELETE FROM phpgw_fud_read WHERE user_id='.$id);
	q('DELETE FROM phpgw_fud_forum_read WHERE user_id='.$id);
}

function user_mark_forum_read($id, $fid, $last_view)
{
	if (__dbtype__ == 'mysql') {
		q('REPLACE INTO phpgw_fud_read (user_id, thread_id, msg_id, last_view) SELECT '.$id.', id, last_post_id, '.__request_timestamp__.' FROM phpgw_fud_thread WHERE forum_id='.$fid);
	} else {
		if (!db_li('INSERT INTO phpgw_fud_read (user_id, thread_id, msg_id, last_view) SELECT '.$id.', id, last_post_id, '.__request_timestamp__.' FROM phpgw_fud_thread WHERE forum_id='.$fid)) {
			q("UPDATE phpgw_fud_read SET user_id=".$id.", thread_id=id, msg_id=last_post_id, last_view=".__request_timestamp__." WHERE user_id=".$id." SELECT id, last_post_id FROM phpgw_fud_thread WHERE forum_id=".$fid);
		}
	}
}

if (!defined('forum_debug')) {
	$GLOBALS['usr'] =& init_user();
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
}function reverse_fmt(&$data)
{
	$data = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $data);
}


	if (!($FUD_OPT_2 &134217728) || !extension_loaded('pdf')) {
		std_error('disabled');
	}

	if ($FUD_OPT_2 & 16384) {
		ob_start(array('ob_gzhandler', $PHP_COMPRESSION_LEVEL));
	}

	$forum	= isset($_GET['frm']) ? (int)$_GET['frm'] : 0;
	$thread	= isset($_GET['th']) ? (int)$_GET['th'] : 0;
	$msg	= isset($_GET['msg']) ? (int)$_GET['msg'] : 0;
	$page	= isset($_GET['page']) ? (int)$_GET['page'] : 0;

	if ($forum) {
		if (!($FUD_OPT_2 & 268435456) && !$page) {
			$page = 1;
		}

		if ($page) {
			$join = 'FROM phpgw_fud_thread_view tv
				INNER JOIN phpgw_fud_thread t ON t.id=tv.thread_id
				INNER JOIN phpgw_fud_forum f ON f.id='.$forum.'
				INNER JOIN phpgw_fud_msg m ON m.thread_id=t.id
				LEFT JOIN phpgw_fud_users u ON m.poster_id=u.id
				LEFT JOIN phpgw_fud_poll p ON m.poll_id=p.id
			';
			$lmt = ' AND tv.forum_id='.$forum.' AND tv.page='.$page;
		} else {
			$join = 'FROM phpgw_fud_forum f
				INNER JOIN phpgw_fud_thread t ON t.forum_id=f.id
				INNER JOIN phpgw_fud_msg m ON m.thread_id=t.id
				LEFT JOIN phpgw_fud_users u ON m.poster_id=u.id
				LEFT JOIN phpgw_fud_poll p ON m.poll_id=p.id
			';
			$lmt = ' AND f.id='.$forum;
		}
	} else if ($thread) {
		$join = 'FROM phpgw_fud_msg m
				INNER JOIN phpgw_fud_thread t ON t.id=m.thread_id
				INNER JOIN phpgw_fud_forum f ON f.id=t.forum_id
				LEFT JOIN phpgw_fud_users u ON m.poster_id=u.id
				LEFT JOIN phpgw_fud_poll p ON m.poll_id=p.id
			';
		$lmt = ' AND m.thread_id='.$thread;
	} else if ($msg) {
		$lmt = ' AND m.id='.$msg;
		$join = 'FROM phpgw_fud_msg m
				INNER JOIN phpgw_fud_thread t ON t.id=m.thread_id
				INNER JOIN phpgw_fud_forum f ON f.id=t.forum_id
				LEFT JOIN phpgw_fud_users u ON m.poster_id=u.id
				LEFT JOIN phpgw_fud_poll p ON m.poll_id=p.id
			';
	} else {
		invl_inp_err();
	}

	$c = uq('SELECT code, '.__FUD_SQL_CONCAT__.'(\'images/smiley_icons/\', img), descr FROM phpgw_fud_smiley');
	while ($r = db_rowarr($c)) {
		$im = '<img src="'.$r[1].'" border=0 alt="'.$r[2].'">';
		$re[$im] = (($p = strpos($r[0], '~')) !== false) ? substr($r[0], 0, $p) : $r[0];
	}
	if (!isset($re)) {
		$re = null;
	}

	if (_uid) {
		if (!($usr->users_opt & 1048576)) {
			$join .= '	INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id=2147483647 AND g1.resource_id=f.id
					LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id
					LEFT JOIN phpgw_fud_mod mm ON mm.forum_id=f.id AND mm.user_id='._uid.' ';
			$lmt .= " AND (mm.id IS NOT NULL OR ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 2) > 0)";
		}
	} else {
		$join .= ' INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id=0 AND g1.resource_id=f.id ';
		$lmt .= " AND (g1.group_cache_opt & 2) > 0";
	}

	if ($forum) {
		$subject = q_singleval('SELECT name FROM phpgw_fud_forum WHERE id='.$forum);
	}

	$c = uq('SELECT
				m.id, m.thread_id, m.subject, m.post_stamp,
				m.attach_cnt, m.attach_cache, m.poll_cache,
				m.foff, m.length, m.file_id,
				(CASE WHEN u.alias IS NULL THEN \''.$ANON_NICK.'\' ELSE u.alias END) as alias,
				p.name AS poll_name, p.total_votes
			'.$join.'
			WHERE
				m.apr=1 '.$lmt.' ORDER BY m.post_stamp, m.thread_id');

	if (!($o = db_rowobj($c))) {
		invl_inp_err();
	}

	if ($thread || $msg) {
		$subject = $o->subject;
	}

	$fpdf = new fud_pdf('FUDforum ' . $FORUM_VERSION, $FORUM_TITLE, $subject, $PDF_PAGE, $PDF_WMARGIN, $PDF_HMARGIN);
	$fpdf->begin_page($subject);
	do {
		/* write message header */
		reverse_fmt($o->alias);
		reverse_fmt($o->subject);
		$fpdf->message_header($o->subject, $o->alias, $o->post_stamp, $o->id, $o->thread_id);

		/* write message body */
		$msg_body = strip_tags(post_to_smiley(read_msg_body($o->foff, $o->length, $o->file_id), $re));
		reverse_fmt($msg_body);
		$fpdf->input_text(explode("\n", $msg_body));

		/* handle attachments */
		if ($o->attach_cnt && $o->attach_cache) {
			$a = unserialize($o->attach_cache);
			if (is_array($a) && @count($a)) {
				foreach ($a as $i) {
					$attch[] = array('id' => $i[0], 'name' => $i[1], 'nd' => $i[3]);
				}
				$fpdf->add_attacments($attch);
			}
		}

		/* handle polls */
		if ($o->poll_name && $o->poll_cache) {
			$pc = @unserialize($o->poll_cache);
			if (is_array($pc) && count($pc)) {
				reverse_fmt($o->poll_name);
				foreach ($pc as $opt) {
					$opt[0] = strip_tags(post_to_smiley($opt[0], $re));
					reverse_fmt($opt[0]);
					$votes[] = array('name' => $opt[0], 'votes' => $opt[1]);
				}
				$fpdf->add_poll($o->poll_name, $votes, $o->total_votes);
			}
		}

		$fpdf->end_message();
	} while (($o = db_rowobj($c)));
	un_register_fps();

	$fpdf->end_page();
	pdf_close($fpdf->pdf);
	$pdf = pdf_get_buffer($fpdf->pdf);

	header('Content-type: application/pdf');
	header('Content-length: '.strlen($pdf));
	header('Content-disposition: inline; filename=FUDforum'.date('Ymd').'.pdf');
	echo $pdf;

	pdf_delete($fpdf->pdf);
?>