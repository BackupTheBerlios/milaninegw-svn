<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: split_th.php.t,v 1.2 2003/12/18 18:20:49 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function th_lock($id, $lck)
{
	q("UPDATE phpgw_fud_thread SET thread_opt=(thread_opt|1)".(!$lck ? '& ~ 1' : '')." WHERE id=".$id);
}

function th_inc_view_count($id)
{
	q('UPDATE phpgw_fud_thread SET views=views+1 WHERE id='.$id);
}

function th_inc_post_count($id, $r, $lpi=0, $lpd=0)
{
	if ($lpi && $lpd) {
		q('UPDATE phpgw_fud_thread SET replies=replies+'.$r.', last_post_id='.$lpi.', last_post_date='.$lpd.' WHERE id='.$id);
	} else {
		q('UPDATE phpgw_fud_thread SET replies=replies+'.$r.' WHERE id='.$id);
	}
}

function th_frm_last_post_id($id, $th)
{
	return (int) q_singleval('SELECT phpgw_fud_thread.last_post_id FROM phpgw_fud_thread INNER JOIN phpgw_fud_msg ON phpgw_fud_thread.root_msg_id=phpgw_fud_msg.id WHERE phpgw_fud_thread.forum_id='.$id.' AND phpgw_fud_thread.id!='.$th.' AND phpgw_fud_thread.moved_to=0 AND phpgw_fud_msg.apr=1 ORDER BY phpgw_fud_thread.last_post_date DESC LIMIT 1');
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
}function tmpl_draw_select_opt($values, $names, $selected, $normal_tmpl, $selected_tmpl)
{
	$vls = explode("\n", $values);
	$nms = explode("\n", $names);

	if (($a = count($vls)) != count($nms)) {
		exit("FATAL ERROR: inconsistent number of values inside a select<br>\n");
	}

	$options = '';
	for ($i = 0; $i < $a; $i++) {
		$options .= $vls[$i] != $selected ? '<option value="'.$vls[$i].'" '.$normal_tmpl.'>'.$nms[$i].'</option>' : '<option value="'.$vls[$i].'" selected '.$selected_tmpl.'>'.$nms[$i].'</option>';
	}

	return $options;
}function logaction($user_id, $res, $res_id=0, $action=null)
{
	q('INSERT INTO phpgw_fud_action_log (logtime, logaction, user_id, a_res, a_res_id)
		VALUES('.__request_timestamp__.', '.strnull($action).', '.$user_id.', '.strnull($res).', '.(int)$res_id.')');
}

function clear_action_log()
{
	q('DELETE FROM phpgw_fud_action_log');
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
}


	$th = isset($_GET['th']) ? (int)$_GET['th'] : (isset($_POST['th']) ? (int)$_POST['th'] : 0);
	if (!$th) {
		invl_inp_err();
	}

	/* permission check */
	if (!($usr->users_opt & 1048576)) {
		$perms = db_saq('SELECT mm.id, '.(_uid ? ' (CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) AS gco ' : ' g1.group_cache_opt AS gco ').'
				FROM phpgw_fud_thread t
				LEFT JOIN phpgw_fud_mod mm ON mm.user_id='._uid.' AND mm.forum_id=t.forum_id
				'.(_uid ? 'INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id=2147483647 AND g1.resource_id=t.forum_id LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=t.forum_id' : 'INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id=0 AND g1.resource_id=t.forum_id').'
				WHERE t.id='.$th);
		if (!$perms || !$perms[0] && !($perms[1] & 2048)) {
			std_error('access');
		}
	}

	$forum = isset($_POST['forum']) ? (int)$_POST['forum'] : 0;

	if ($forum && !empty($_POST['new_title']) && isset($_POST['sel_th']) && ($mc = count($_POST['sel_th']))) {
		/* we need to make sure that the user has access to destination forum */
		if (!($usr->users_opt & 1048576) && !q_singleval('SELECT f.id FROM phpgw_fud_forum f LEFT JOIN phpgw_fud_mod mm ON mm.user_id='._uid.' AND mm.forum_id=f.id '.(_uid ? 'INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id=2147483647 AND g1.resource_id=f.id LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id' : 'INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id=0 AND g1.resource_id=f.id').' WHERE f.id='.$forum.' AND (mm.id IS NOT NULL OR '.(_uid ? ' ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END)' : ' (g1.group_cache_opt').' & 4) > 0)')) {
			std_error('access');
		}

		foreach ($_POST['sel_th'] as $k => $v) {
			if (!(int)$v) {
				unset($_POST['sel_th'][$k]);
			}
			$_POST['sel_th'][$k] = (int) $v;
		}
		/* sanity check */
		if (!count($_POST['sel_th'])) {
			header('Location: /egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&th='.$th.'&'._rsidl);
			exit;
		}

		if (isset($_POST['btn_selected'])) {
			sort($_POST['sel_th']);
			$mids = implode(',', $_POST['sel_th']);
			$start = $_POST['sel_th'][0];
			$end = $_POST['sel_th'][($mc - 1)];
		} else {
			$c = uq('SELECT id FROM phpgw_fud_msg WHERE thread_id='.$th.' AND id NOT IN('.implode(',', $_POST['sel_th']).') AND apr=1 ORDER BY post_stamp ASC');
			while ($r = db_rowarr($c)) {
				$a[] = $r[0];
			}
			/* sanity check */
			if (!isset($a)) {
				header('Location: /egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&th='.$th_id.'&'._rsidl);
				exit;
			}
			$mids = implode(',', $a);
			$mc = count($a);
			$start = $a[0];
			$end = $a[($mc - 1)];
		}

		/* fetch all relevant information */
		$data = db_sab('SELECT
				t.id, t.forum_id, t.replies, t.root_msg_id, t.last_post_id, t.last_post_date,
				m1.post_stamp AS new_th_lps, m1.id AS new_th_lpi,
				m2.post_stamp AS old_fm_lpd,
				f1.last_post_id AS src_lpi,
				f2.last_post_id AS dst_lpi
				FROM phpgw_fud_thread t
				INNER JOIN phpgw_fud_forum f1 ON t.forum_id=f1.id
				INNER JOIN phpgw_fud_forum f2 ON f2.id='.$forum.'
				INNER JOIN phpgw_fud_msg m1 ON m1.id='.$end.'
				INNER JOIN phpgw_fud_msg m2 ON m2.id=f2.last_post_id

		WHERE t.id='.$th);

		/* sanity check */
		if (!$data->replies) {
			header('Location: /egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&th='.$th_id.'&'._rsidl);
			exit;
		}

		apply_custom_replace($_POST['new_title']);

		if ($mc != ($data->replies + 1)) { /* check that we need to move the entire thread */
			db_lock('phpgw_fud_thread_view WRITE, phpgw_fud_thread WRITE, phpgw_fud_forum WRITE, phpgw_fud_msg WRITE, phpgw_fud_poll WRITE');

			$new_th = th_add($start, $forum, $data->new_th_lps, 0, 0, ($mc - 1), $data->new_th_lpi);

			/* Deal with the new thread */
			q('UPDATE phpgw_fud_msg SET thread_id='.$new_th.' WHERE id IN ('.$mids.')');
			q('UPDATE phpgw_fud_msg SET reply_to='.$start.' WHERE thread_id='.$new_th.' AND reply_to NOT IN ('.$mids.')');
			q("UPDATE phpgw_fud_msg SET reply_to=0, subject='".addslashes(htmlspecialchars($_POST['new_title']))."' WHERE id=".$start);

			/* Deal with the old thread */
			list($lpi, $lpd) = db_saq("SELECT id, post_stamp FROM phpgw_fud_msg WHERE thread_id=".$data->id." AND apr=1 ORDER BY post_stamp DESC LIMIT 1");$old_root_msg_id = q_singleval("SELECT id FROM phpgw_fud_msg WHERE thread_id=".$data->id." AND apr=1 ORDER BY post_stamp ASC LIMIT 1");
			q("UPDATE phpgw_fud_msg SET reply_to=".$old_root_msg_id." WHERE thread_id=".$data->id." AND reply_to IN(".$mids.")");
			q('UPDATE phpgw_fud_msg SET reply_to=0 WHERE id='.$old_root_msg_id);
			q('UPDATE phpgw_fud_thread SET root_msg_id='.$old_root_msg_id.', replies=replies-'.$mc.', last_post_date='.$lpd.', last_post_id='.$lpi.' WHERE id='.$data->id);

			if ($forum != $data->forum_id) {
				$c = q('SELECT poll_id FROM phpgw_fud_msg WHERE thread_id='.$new_th.' AND apr=1 AND poll_id>0');
				while ($r = db_rowarr($c)) {
					$p[] = $r[0];
				}
				unset($c);
				if (isset($p)) {
					q('UPDATE phpgw_fud_poll SET forum_id='.$data->forum_id.' WHERE id IN('.implode(',', $p).')');
				}

				/* deal with the source forum */
				if ($data->src_lpi != $data->last_post_id || $data->last_post_date <= $lpd) {
					q('UPDATE phpgw_fud_forum SET post_count=post_count-'.$mc.' WHERE id='.$data->forum_id);
				} else {
					q('UPDATE phpgw_fud_forum SET post_count=post_count-'.$mc.', last_post_id='.th_frm_last_post_id($data->forum_id, $data->id).' WHERE id='.$data->forum_id);
				}

				/* deal with destination forum */
				if ($data->old_fm_lpd > $data->new_th_lps) {
					q('UPDATE phpgw_fud_forum SET post_count=post_count+'.$mc.', thread_count=thread_count+1 WHERE id='.$forum);
				} else {
					q('UPDATE phpgw_fud_forum SET post_count=post_count+'.$mc.', thread_count=thread_count+1, last_post_id='.$data->new_th_lpi.' WHERE id='.$forum);
				}

				rebuild_forum_view($forum);
			} else {
				if ($data->src_lpi == $data->last_post_id && $data->last_post_date >= $lpd) {
					q('UPDATE phpgw_fud_forum SET thread_count=thread_count+1 WHERE id='.$data->forum_id);
				} else {
					q('UPDATE phpgw_fud_forum SET thread_count=thread_count+1, last_post_id='.$data->new_th_lpi.' WHERE id='.$data->forum_id);
				}
			}
			rebuild_forum_view($data->forum_id);
			db_unlock();
			logaction(_uid, 'THRSPLIT', $new_th);
			$th_id = $new_th;
		} else { /* moving entire thread */
			q("UPDATE phpgw_fud_msg SET subject='".addslashes(htmlspecialchars($_POST['new_title']))."' WHERE id=".$data->root_msg_id);
			if ($forum != $data->forum_id) {
				th_move($data->id, $forum, $data->root_msg_id, $thr->forum_id, $data->last_post_date, $data->last_post_id);

				if ($data->src_lpi == $data->last_post_id) {
					q('UPDATE phpgw_fud_forum SET last_post_id='.th_frm_last_post_id($data->forum_id, $data->id).' WHERE id='.$data->forum_id);
				}
				if ($data->old_fm_lpd < $data->last_post_date) {
					q('UPDATE phpgw_fud_forum SET last_post_id='.$data->last_post_id.' WHERE id='.$forum);
				}

				logaction(_uid, 'THRMOVE', $th);
			}
			$th_id = $data->id;
		}
		header('Location: /egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&th='.$th_id.'&'._rsidl);
		exit;
	}
	/* fetch a list of accesible forums */
	$c = uq('SELECT f.id, f.name
			FROM phpgw_fud_forum f
			INNER JOIN phpgw_fud_fc_view v ON v.f=f.id
			INNER JOIN phpgw_fud_cat c ON c.id=f.cat_id
			LEFT JOIN phpgw_fud_mod mm ON mm.forum_id=f.id AND mm.user_id='._uid.'
			INNER JOIN phpgw_fud_group_cache g1 ON g1.resource_id=f.id AND g1.user_id='.(_uid ? '2147483647' : '0').'
			'.(_uid ? ' LEFT JOIN phpgw_fud_group_cache g2 ON g2.resource_id=f.id AND g2.user_id='._uid : '').'
			'.($usr->users_opt & 1048576 ? '' : ' WHERE mm.id IS NOT NULL OR ((CASE WHEN g2.id IS NULL THEN g1.group_cache_opt ELSE g2.group_cache_opt END) & 4) > 0').'
			ORDER BY v.id');
	$vl = $kl = '';
	while ($r = db_rowarr($c)) {
		$vl .= $r[0] . "\n";
		$kl .= $r[1] . "\n";
	}

	if (!$forum) {
		$forum = q_singleval('SELECT forum_id FROM phpgw_fud_thread WHERE id='.$th);
	}

	$forum_sel = tmpl_draw_select_opt(rtrim($vl), rtrim($kl), $forum, '', '');

	$c = uq("SELECT m.id, m.foff, m.length, m.file_id, m.subject, m.post_stamp, u.alias FROM phpgw_fud_msg m LEFT JOIN phpgw_fud_users u ON m.poster_id=u.id WHERE m.thread_id=".$th." AND m.apr=1 ORDER BY m.post_stamp ASC");

	$anon_alias = htmlspecialchars($ANON_NICK);

	$msg_entry = '';
	while ($r = db_rowobj($c)) {
		if (!$r->alias) {
			$r->alias = $anon_alias;
		}
		$msg_body = read_msg_body($r->foff, $r->length, $r->file_id);
		$msg_entry .= '<tr>
<td class="RowStyleC" valign="top" align="center"><input type="checkbox" name="sel_th[]" value="'.$r->id.'"></td>
<td class="RowStyleA">
<table cellspacing=1 cellpadding=2 border=0 class="ContentTable">
<tr class="RowStyleB">
	<td><font size="-1">
	<b>Inviato da:</b> '.$r->alias.'<br />
	<b>Scritto:</b> '.strftime("%a, %d %B %Y %H:%M", $r->post_stamp).'<br />
	<b>Oggetto:</b> '.$r->subject.'
	</font></td>
</tr>
<tr class="RowStyleA">
	<td>'.$msg_body.'</td>
</tr>
</table>
</td>
</tr>';
	}
	un_register_fps();

if ($FUD_OPT_2 & 2) {
	$page_gen_end = gettimeofday();
	$page_gen_time = sprintf('%.5f', ($page_gen_end['sec'] - $PAGE_TIME['sec'] + (($page_gen_end['usec'] - $PAGE_TIME['usec'])/1000000)));
	$page_stats = '<br /><div align="left" class="SmallText">Tempo totale richiesto per generare la pagina: '.$page_gen_time.' secondi</div>';
} else {
	$page_stats = '';
}
?>
<?php echo $GLOBALS['fud_egw_hdr']; ?>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">
<form name="split_th" action="/egroupware/fudforum/3814588639/index.php?t=split_th" method="post"><?php echo _hs; ?><input type="hidden" name="th" value="<?php echo $th; ?>">
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th width="100%" colspan=2>Thread Split Control Panel</th></tr>
<tr class="RowStyleA">
	<td align="left"><b>Titolo del nuovo topic:</b></td>
	<td ><input type="text" name="new_title" value="" size=50></td>
</tr>
<tr class="RowStyleA">
	<td align="left"><b>Forum:</b></td>
	<td align="left"><select name="forum"><?php echo $forum_sel; ?></select></td>
</tr>
<tr class="RowStyleC">
	<td colspan=2 align="center">
		<input type="submit" class="button" name="btn_selected" value="Spezza i messaggi selezionati">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" class="button" name="btn_unselected" value="Spezza i messaggi non selezionati">
	</td>
</tr>
</table>
<br />
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th nowrap>Seleziona</th><th width="100%">Messaggi</th></tr>
<?php echo $msg_entry; ?>
</table>
<br />
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr class="RowStyleC">
	<td colspan=2 align="center">
		<input type="submit" class="button" name="btn_selected" value="Spezza i messaggi selezionati">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" class="button" name="btn_unselected" value="Spezza i messaggi non selezionati">
	</td>
</tr>
</table>
</form>
<?php echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>