<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: pgsql_db.inc,v 1.4 2003/12/18 16:46:16 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

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
	define('__dbtype__', 'pgsql');
	define('__FUD_SQL_CONCAT__', 'textcat');
}

function get_version()
{
	if (!defined('__FUD_SQL_VERSION__')) {
		define('__FUD_SQL_VERSION__', @current(pg_fetch_row(pg_query('SELECT VERSION()', fud_sql_lnk))));
	}
	return __FUD_SQL_VERSION__;
}

function db_lock($tables)
{
	if (!empty($GLOBALS['__DB_INC_INTERNALS__']['db_locked'])) {
		fud_sql_error_handler("Recursive Lock", "internal", "internal", get_version());
	} else {
		$tbls = explode(',', str_replace(' WRITE', '', $tables));
		foreach ($tbls as $k => $t) {
			$t = trim($t);
			if (($p = strpos($t, ' '))) {
				$tbls[$k] = substr($t, 0, $p);
			}
		}
		$tables = implode(',', $tbls);

		q('BEGIN WORK');
		q('LOCK TABLE '.$tables.' IN ACCESS EXCLUSIVE MODE');
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
	q('COMMIT WORK', fud_sql_lnk);
}

function db_locked()
{
	return isset($GLOBALS['__DB_INC_INTERNALS__']['db_locked']);
}

function db_affected()
{
	return pg_affected_rows($GLOBALS['__FLPG__']);
}

function uq($q)
{
	return q($q);
}

function q($q)
{
	$GLOBALS['__FLPG__'] = pg_query(fud_sql_lnk, $q) or die (fud_sql_error_handler($q, pg_last_error(fud_sql_lnk), '', get_version()));
	return $GLOBALS['__FLPG__'];
}

function db_count($r)
{
	$r = @pg_num_rows($r);
	if ($r < 0) {
		return 0;
	}
	return $r;
}

function db_seek($result, $pos)
{
	return pg_result_seek($result, $pos);
}

function &db_rowobj($result)
{
	return pg_fetch_object($result);
}

function &db_rowarr(&$result)
{
	return pg_fetch_row($result);
}

function &q_singleval($query)
{
	return @current(@pg_fetch_row(q($query)));
}

function get_field_list($tbl)
{
	return q("SELECT a.attname AS Field FROM pg_class c, pg_attribute a WHERE c.relname = '$tbl' AND a.attnum > 0 AND a.attrelid = c.oid ORDER BY a.attnum");
}

function qry_limit($limit, $off)
{
	return $limit.' OFFSET '.$off;
}

function get_fud_table_list()
{
	$r = q("SELECT relname FROM pg_class WHERE relkind='r' AND relname LIKE '".str_replace("_", "\\_", $GLOBALS['DBHOST_TBL_PREFIX'])."%'");
	while (list($ret[]) = db_rowarr($r));
	array_pop($ret);

	return $ret;
}

function optimize_tables($tbl_list=null)
{
	if (!$tbl_list) {
		$tbl_list = get_fud_table_list();
	}
	foreach ($tbl_list as $t) {
		q('VACUUM ANALYZE ' . $t);
	}
}

function &db_saq($q)
{
	return @pg_fetch_row(q($q));
}

function &db_sab($q)
{
	return @pg_fetch_object(q($q));
}

function db_qid($q)
{
	$r = q($q);
	preg_match('!('.$GLOBALS['DBHOST_TBL_PREFIX'].'[A-Za-z0-9_]+)!', $q, $m);
	return q_singleval('SELECT id FROM '.$m[1].' WHERE oid='.pg_last_oid($r));
}

function &db_arr_assoc($q)
{
	return @pg_fetch_array(q($q), 0, PGSQL_ASSOC);
}

function db_li($q, &$ef, $li=0)
{
	$r = @pg_query(fud_sql_lnk, $q);
	if ($r) {
		if ($li) {
			preg_match('!('.$GLOBALS['DBHOST_TBL_PREFIX'].'[A-Za-z0-9_]+)!', $q, $m);
			return q_singleval('SELECT id FROM '.$m[1].' WHERE oid='.pg_last_oid($r));
		}
		return $r;
	}

	$err = pg_last_error(fud_sql_lnk);

	/* duplicate key */
	if (strpos($err, 'duplicate key') !== false) {
		$ef = ltrim(strrchr($err, ' '));
		return null;
	} else {
		die(fud_sql_error_handler($q, pg_last_error(fud_sql_lnk), '', get_version()));
	}
}

function ins_m($tbl, $flds, $vals, $type)
{
	$pid = $GLOBALS['DBHOST_TBL_PREFIX'] . substr(md5(rand()), 0, 10);

	pg_query(fud_sql_lnk, 'PREPARE '.$pid.' ('.$type.') AS INSERT INTO '.$tbl.' ('.$flds.') VALUES($'.implode(',$', range(1, substr_count($flds, ',') + 1)).')');
	foreach ($vals as $v) {
		@pg_query(fud_sql_lnk, 'EXECUTE '.$pid.' ('.$v.')');
	}
}
?>