<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: mvthread.php.t,v 1.1.1.1 2003/10/17 21:11:26 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

	define('plain_form', 1);

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function logaction($user_id, $res, $res_id=0, $action=null)
{
	q('INSERT INTO phpgw_fud_action_log (logtime, logaction, user_id, a_res, a_res_id)
		VALUES('.__request_timestamp__.', '.strnull($action).', '.$user_id.', '.strnull($res).', '.(int)$res_id.')');
}

function clear_action_log()
{
	q('DELETE FROM phpgw_fud_action_log');
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

	$th = isset($_POST['th']) ? (int)$_POST['th'] : (isset($_GET['th']) ? (int)$_GET['th'] : 0);
	$thx = isset($_POST['thx']) ? (int)$_POST['thx'] : (isset($_GET['thx']) ? (int)$_GET['thx'] : 0);
	$to = isset($_GET['to']) ? (int)$_GET['to'] : 0;

	/* thread x-change */
	if ($th && $thx) {
		if (!($usr->users_opt & 1048576) && q_singleval('SELECT id FROM phpgw_fud_mod WHERE forum_id='.$thx.' AND user_id='._uid)) {
			std_error('access');
		}

		if (!empty($_POST['reason_msg'])) {
			fud_use('thrx_adm.inc', true);
			if (thx_add($_POST['reason_msg'], $th, $thx, _uid)) {
				logaction(_uid, 'THRXREQUEST', $th);
			}
			exit('<html><script>window.close();</script></html>');
		} else {
			$thr = db_sab('SELECT f.name AS frm_name, m.subject FROM phpgw_fud_forum f INNER JOIN phpgw_fud_thread t ON t.id='.$th.' INNER JOIN phpgw_fud_msg m ON t.root_msg_id=m.id WHERE f.id='.$thx);
			$table_data = '<tr><td><font size="-1"><b>'.htmlspecialchars($thr->frm_name).'</b></font></td></tr>
<tr><td><font size="-1">Why do you wish the topic to be moved?</font><br /><textarea name="reason_msg" rows=7 cols=30></textarea><td></tr>
<tr><td align="right"><input type="submit" class="button" name="submit" value="Submit Request"></td></tr>';
		}
	}

	/* moving a thread */
	if ($th && $to) {
		$thr = db_sab('SELECT
				t.id, t.forum_id, t.last_post_id, t.root_msg_id, t.last_post_date, t.last_post_id,
				f1.last_post_id AS f1_lpi, f2.last_post_id AS f2_lpi,
				'.($usr->users_opt & 1048576 ? ' 1 AS mod1, 1 AS mod2' : ' mm1.id AS mod1, mm2.id AS mod2').',
				(CASE WHEN gs2.id IS NOT NULL THEN gs2.group_cache_opt ELSE gs1.group_cache_opt END) AS sgco,
				(CASE WHEN gd2.id IS NOT NULL THEN gd2.group_cache_opt ELSE gd1.group_cache_opt END) AS dgco
			FROM phpgw_fud_thread t
			INNER JOIN phpgw_fud_forum f1 ON t.forum_id=f1.id
			INNER JOIN phpgw_fud_forum f2 ON f2.id='.$to.'
			LEFT JOIN phpgw_fud_mod mm1 ON mm1.forum_id=f1.id AND mm1.user_id='._uid.'
			LEFT JOIN phpgw_fud_mod mm2 ON mm2.forum_id=f2.id AND mm2.user_id='._uid.'
			INNER JOIN phpgw_fud_group_cache gs1 ON gs1.user_id=2147483647 AND gs1.resource_id=f1.id
			LEFT JOIN phpgw_fud_group_cache gs2 ON gs2.user_id='._uid.' AND gs2.resource_id=f1.id
			INNER JOIN phpgw_fud_group_cache gd1 ON gd1.user_id=2147483647 AND gd1.resource_id=f2.id
			LEFT JOIN phpgw_fud_group_cache gd2 ON gd2.user_id='._uid.' AND gd2.resource_id=f2.id
			WHERE t.id='.$th);

		if (!$thr) {
			invl_inp_err();
		}

		if ((!$thr->mod1 && !($thr->sgco & 8192)) || (!$thr->mod2 && !($thr->dgco & 8192))) {
			std_error('access');
		}

		/* fetch data about source thread & forum */
		$src_frm_lpi = (int) $thr->f1_lpi;
		/* fetch data about dest forum */
		$dst_frm_lpi = (int) $thr->f2_lpi;

		th_move($thr->id, $to, $thr->root_msg_id, $thr->forum_id, $thr->last_post_date, $thr->last_post_id);

		if ($src_frm_lpi == $thr->last_post_id) {
			$mid = (int) q_singleval('SELECT MAX(last_post_id) FROM phpgw_fud_thread t INNER JOIN phpgw_fud_msg m ON t.root_msg_id=m.id WHERE t.forum_id='.$thr->forum_id.' AND t.moved_to=0 AND m.apr=1');
			q('UPDATE phpgw_fud_forum SET last_post_id='.$mid.' WHERE id='.$thr->forum_id);
		}

		if ($dst_frm_lpi < $thr->last_post_id) {
			q('UPDATE phpgw_fud_forum SET last_post_id='.$thr->last_post_id.' WHERE id='.$to);
		}

		logaction(_uid, 'THRMOVE', $th);

		exit("<html><script>window.opener.location='/egroupware/fudforum/3814588639/index.php?t=".t_thread_view."&"._rsid."&frm_id=".$thr->forum_id."'; window.close();</script></html>");
	}



	if (!$thx) {
		$thr = db_sab('SELECT f.name AS frm_name, m.subject, t.forum_id, t.id FROM phpgw_fud_thread t INNER JOIN phpgw_fud_forum f ON f.id=t.forum_id INNER JOIN phpgw_fud_msg m ON t.root_msg_id=m.id WHERE t.id='.$th);

		$r = uq('SELECT f.name, f.id, c.name, m.user_id, (CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) AS gco
			FROM phpgw_fud_forum f
			INNER JOIN phpgw_fud_fc_view v ON v.f=f.id
			INNER JOIN phpgw_fud_cat c ON c.id=v.c
			LEFT JOIN phpgw_fud_mod m ON m.user_id='._uid.' AND m.forum_id=f.id
			INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id=2147483647 AND g1.resource_id=f.id
			LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id
			WHERE c.id!=0 AND f.id!='.$thr->forum_id.($usr->users_opt & 1048576 ? '' : ' AND (CASE WHEN m.user_id IS NOT NULL OR ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 1) > 0 THEN 1 ELSE 0 END)=1').'
			ORDER BY v.id');

		$table_data = $prev_cat = '';

		while ($ent = db_rowarr($r)) {
			if ($ent[2] !== $prev_cat) {
				$table_data .= '<tr><td class="mvTc">'.$ent[2].'</td></tr>';
				$prev_cat = $ent[2];
			}

			if ($ent[3] || $usr->users_opt & 1048576 || $ent[4] & 8192) {
				$table_data .= '<tr><td><a href="/egroupware/fudforum/3814588639/index.php?t=mvthread&amp;th='.$thr->id.'&amp;to='.$ent[1].'&amp;'._rsid.'">'.htmlspecialchars($ent[0]).'</a></td></tr>';
			} else {
				$table_data .= '<tr><td>'.htmlspecialchars($ent[0]).' [<a href="/egroupware/fudforum/3814588639/index.php?t=mvthread&amp;th='.$thr->id.'&amp;'._rsid.'&amp;thx='.$ent[1].'">request a move</a>]</td></tr>';
			}
		}
	}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-15">
<title><?php echo $GLOBALS['FORUM_TITLE'].$TITLE_EXTRA; ?></title>
<script language="JavaScript" src="<?php echo $GLOBALS['WWW_ROOT']; ?>/lib.js" type="text/javascript"></script>
<link rel="StyleSheet" href="/egroupware/fudforum/3814588639/theme/default/forum.css" type="text/css">
</head>
<body>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">
<form action="/egroupware/fudforum/3814588639/index.php?t=mvthread" name="mvthread" method="post">
<table border=0 width="100%" cellspacing=0 cellpadding=3 class="dashed">
<tr><td><font size="-1">Move topic <b><?php echo $thr->subject; ?></b> to:</font></td></tr>
<?php echo $table_data; ?>
</table>
<?php echo _hs; ?><input type="hidden" name="th" value="<?php echo $th; ?>"><input type="hidden" name="thx" value="<?php echo $thx; ?>"></form>
</td></tr></table></body></html>