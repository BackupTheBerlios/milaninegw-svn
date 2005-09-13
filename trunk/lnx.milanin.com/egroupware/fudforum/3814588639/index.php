<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: root_index.php.t,v 1.5 2004/06/02 16:22:05 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

	require('./GLOBALS.php');
	
	/* before we go on, we need to do some very basic activation checks */
	if (!($FUD_OPT_1 & 1)) {
		fud_egw();
		fud_use('errmsg.inc');
		exit($DISABLED_REASON . __fud_ecore_adm_login_msg);
	}

	if (isset($_GET['t'])) {
		$t = $_GET['t'];
	} else if (isset($_POST['t'])) {
		$t = $_POST['t'];
	} else {
		$t = 'index';
	}
	if (preg_match('/[^A-Za-z0-9_]/', $t)) {
		$t = 'index';
	}

	if ($FUD_OPT_2 & 16384 && $t != 'getfile') {
		ob_start(array('ob_gzhandler', (int)$PHP_COMPRESSION_LEVEL));
	}

	fud_egw($t, 0);

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
}


	if ($t == 'rview') {
		if (isset($_GET['th']) || isset($_GET['goto'])) {
			$t = $_GET['t'] = d_thread_view;
		} else if (isset($_GET['frm_id'])) {
			$t = $_GET['t'] = t_thread_view;
		} else {
			$t = $_GET['t'] = 'index';
		}
	}

	fud_use('err.inc');

	define('__index_page_start__', true);
	if (isset($GLOBALS['fud_egw_hdr'])) {
		$GLOBALS['fud_egw_hdr'] = str_replace('/lib.js" type="text/javascript"></script>', '/lib.js" type="text/javascript"></script><link href="'.$GLOBALS['WWW_ROOT'].fud_theme.'/forum.css" type="text/css" rel="StyleSheet" /></HEAD>', $GLOBALS['fud_egw_hdr']);
	}
	require($WWW_ROOT_DISK . fud_theme . $t . '.php');
?>