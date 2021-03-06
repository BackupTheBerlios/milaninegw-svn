<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: search.php.t,v 1.3 2003/12/18 18:20:49 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

/*{PRE_HTML_PHP}*/

	if (!($FUD_OPT_1 & 16777216)) {
		std_error('disabled');
	}
	if (!isset($_GET['start']) || !($start = (int)$_GET['start'])) {
		$start = 0;
	}

	$ppg = $usr->posts_ppg ? $usr->posts_ppg : $POSTS_PER_PAGE;
	$srch = isset($_GET['srch']) ? trim($_GET['srch']) : '';
	$forum_limiter = isset($_GET['forum_limiter']) ? $_GET['forum_limiter'] : '';
	$field = !isset($_GET['field']) ? 'all' : ($_GET['field'] == 'subject' ? 'subject' : 'all');
	$search_logic = (isset($_GET['search_logic']) && $_GET['search_logic'] == 'OR') ? 'OR' : 'AND';
	$sort_order = (isset($_GET['sort_order']) && $_GET['sort_order'] == 'ASC') ? 'ASC' : 'DESC';

function fetch_search_cache($qry, $start, $count, $logic, $srch_type, $order, $forum_limiter, &$total)
{
	if (strncmp($GLOBALS['usr']->lang, 'chinese', 7)) {
		$cs = array('!\W!', '!\s+!');
		$cd = array(' ', ' ');
		$qry = trim(preg_replace($cs, $cd, $qry));

		$w = array_unique(explode(' ', strtolower($qry)));
		$qr = ''; $i = 0;
		foreach ($w as $v) {
			$v = trim($v);
			if (strlen($v) <= 2) {
				continue;
			} else if ($i++ == 10) { /* limit query length to 10 words */
				break;
			}
			$qr .= " '".addslashes($v)."',";
		}

		if (!$qr) {
			return;
		} else {
			$qr = substr($qr, 0, -1);
		}
	} else { /* handling for multibyte languages */
		fud_use('isearch.inc');
		if (!($w = mb_word_split($qry))) {
			return;
		}
		$qr = implode(',', $w);
		$i = count($w);
	}

	if ($srch_type == 'all') {
		$tbl = 'index';
		$qt = '0';
	} else {
		$tbl = 'title_index';
		$qt = '1';
	}

	$qry_lck = md5($qr);

	/* remove expired cache */
	q('DELETE FROM {SQL_TABLE_PREFIX}search_cache WHERE expiry<'.(__request_timestamp__ - $GLOBALS['SEARCH_CACHE_EXPIRY']));

	if (!($total = q_singleval("SELECT count(*) FROM {SQL_TABLE_PREFIX}search_cache WHERE query_type=".$qt." AND srch_query='".$qry_lck."'"))) {
		if (__dbtype__ == 'mysql') {
			q("INSERT IGNORE INTO {SQL_TABLE_PREFIX}search_cache (srch_query, query_type, expiry, msg_id, n_match) SELECT '".$qry_lck."', ".$qt.", ".__request_timestamp__.", msg_id, count(*) as word_count FROM {SQL_TABLE_PREFIX}search s INNER JOIN {SQL_TABLE_PREFIX}".$tbl." i ON i.word_id=s.id WHERE word IN(".$qr.") GROUP BY msg_id ORDER BY word_count DESC LIMIT 500");
			if (!($total = (int) db_affected())) {
				return;
			}
		} else {
			q("BEGIN; DELETE FROM {SQL_TABLE_PREFIX}search_cache; INSERT INTO {SQL_TABLE_PREFIX}search_cache (srch_query, query_type, expiry, msg_id, n_match) SELECT '".$qry_lck."', ".$qt.", ".__request_timestamp__.", msg_id, count(*) as word_count FROM {SQL_TABLE_PREFIX}search s INNER JOIN {SQL_TABLE_PREFIX}".$tbl." i ON i.word_id=s.id WHERE word IN(".$qr.") GROUP BY msg_id ORDER BY word_count DESC LIMIT 500; COMMIT;");
		}
	}

	if ($forum_limiter) {
		if ($forum_limiter[0] != 'c') {
			$qry_lmt = ' AND f.id=' . (int)$forum_limiter . ' ';
		} else {
			$qry_lmt = ' AND c.id=' . (int)substr($forum_limiter, 1) . ' ';
		}
	} else {
		$qry_lmt = '';
	}

	$qry_lck = "'" . $qry_lck . "'";

	$total = q_singleval('SELECT count(*)
		FROM {SQL_TABLE_PREFIX}search_cache sc
		INNER JOIN {SQL_TABLE_PREFIX}msg m ON m.id=sc.msg_id
		INNER JOIN {SQL_TABLE_PREFIX}thread t ON m.thread_id=t.id
		INNER JOIN {SQL_TABLE_PREFIX}forum f ON t.forum_id=f.id
		INNER JOIN {SQL_TABLE_PREFIX}cat c ON f.cat_id=c.id
		INNER JOIN {SQL_TABLE_PREFIX}group_cache g1 ON g1.user_id='.(_uid ? '2147483647' : '0').' AND g1.resource_id=f.id
		LEFT JOIN {SQL_TABLE_PREFIX}mod mm ON mm.forum_id=f.id AND mm.user_id='._uid.'
		LEFT JOIN {SQL_TABLE_PREFIX}group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id
		WHERE
			sc.query_type='.$qt.' AND sc.srch_query='.$qry_lck.$qry_lmt.'
			'.($logic == 'AND' ? ' AND sc.n_match>='.$i : '').'
			'.($GLOBALS['usr']->users_opt & 1048576 ? '' : ' AND (mm.id IS NOT NULL OR ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 262146) >= 262146)'));
	if (!$total) {
		return;
	}

	return uq('SELECT u.alias, f.name AS forum_name, f.id AS forum_id,
			m.poster_id, m.id, m.thread_id, m.subject, m.poster_id, m.foff, m.length, m.post_stamp, m.file_id, m.icon
		FROM {SQL_TABLE_PREFIX}search_cache sc
		INNER JOIN {SQL_TABLE_PREFIX}msg m ON m.id=sc.msg_id
		INNER JOIN {SQL_TABLE_PREFIX}thread t ON m.thread_id=t.id
		INNER JOIN {SQL_TABLE_PREFIX}forum f ON t.forum_id=f.id
		INNER JOIN {SQL_TABLE_PREFIX}cat c ON f.cat_id=c.id
		INNER JOIN {SQL_TABLE_PREFIX}group_cache g1 ON g1.user_id='.(_uid ? '2147483647' : '0').' AND g1.resource_id=f.id
		LEFT JOIN {SQL_TABLE_PREFIX}users u ON m.poster_id=u.id
		LEFT JOIN {SQL_TABLE_PREFIX}mod mm ON mm.forum_id=f.id AND mm.user_id='._uid.'
		LEFT JOIN {SQL_TABLE_PREFIX}group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id
		WHERE
			sc.query_type='.$qt.' AND sc.srch_query='.$qry_lck.$qry_lmt.'
			'.($logic == 'AND' ? ' AND sc.n_match>='.$i : '').'
			'.($GLOBALS['usr']->users_opt & 1048576 ? '' : ' AND (mm.id IS NOT NULL OR ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 262146) >= 262146)').'
		ORDER BY sc.n_match DESC, m.post_stamp '.$order.' LIMIT '.qry_limit($count, $start));
}

/*{POST_HTML_PHP}*/

	$search_options = tmpl_draw_radio_opt('field', "all\nsubject", "{TEMPLATE: search_entire_msg}\n{TEMPLATE: search_subect_only}", $field, '{TEMPLATE: radio_button}', '{TEMPLATE: radio_button_selected}', '{TEMPLATE: radio_button_separator}');
	$logic_options = tmpl_draw_select_opt("AND\nOR", "{TEMPLATE: search_and}\n{TEMPLATE: search_or}", $search_logic, '{TEMPLATE: search_normal_option}', '{TEMPLATE: search_selected_option}');
	$sort_options = tmpl_draw_select_opt("DESC\nASC", "{TEMPLATE: search_desc_order}\n{TEMPLATE: search_asc_order}", $sort_order, '{TEMPLATE: search_normal_option}', '{TEMPLATE: search_selected_option}');

	$TITLE_EXTRA = ': {TEMPLATE: search_title}';

	ses_update_status($usr->sid, '{TEMPLATE: search_update}');

	$page_pager = '';
	if ($srch) {
		if (!($c =& fetch_search_cache($srch, $start, $ppg, $search_logic, $field, $sort_order, $forum_limiter, $total))) {
			$search_data = '{TEMPLATE: no_search_results}';
		} else {
			$i = 0;
			$search_data = '';
			while ($r = db_rowobj($c)) {
				$body = trim_body(read_msg_body($r->foff, $r->length, $r->file_id));
				$poster_info = !empty($r->poster_id) ? '{TEMPLATE: registered_poster}' : '{TEMPLATE: unregistered_poster}';
				++$i;
				$search_data .= '{TEMPLATE: search_entry}';
			}
			un_register_fps();
			$search_data = '{TEMPLATE: search_results}';
			$page_pager = tmpl_create_pager($start, $ppg, $total, '{ROOT}?t=search&amp;srch='.urlencode($srch).'&amp;field='.$field.'&amp;'._rsid.'&amp;search_logic='.$search_logic.'&amp;sort_order='.$sort_order.'&amp;forum_limiter='.$forum_limiter);
		}
	} else {
		$search_data = '';
	}


/*{POST_PAGE_PHP_CODE}*/
?>
{TEMPLATE: SEARCH_PAGE}