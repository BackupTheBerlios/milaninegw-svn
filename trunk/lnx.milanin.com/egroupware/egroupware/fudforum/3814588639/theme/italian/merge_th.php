<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: merge_th.php.t,v 1.2 2003/12/18 18:20:49 iliaa Exp $
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


	$frm = isset($_GET['frm']) ? (int)$_GET['frm'] : (isset($_POST['frm']) ? (int)$_POST['frm'] : 0);
	if (!$frm) {
		invl_inp_err();
	}

	/* permission check */
	if (!($usr->users_opt & 1048576)) {
		$perms = db_saq('SELECT mm.id, '.(_uid ? ' (CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) AS gco ' : ' g1.group_cache_opt AS gco ').'
				FROM phpgw_fud_forum f
				LEFT JOIN phpgw_fud_mod mm ON mm.user_id='._uid.' AND mm.forum_id=f.id
				'.(_uid ? 'INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id=2147483647 AND g1.resource_id=f.id LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id' : 'INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id=0 AND g1.resource_id=f.id').'
				WHERE f.id='.$frm);
		if (!$perms || !$perms[0] && !($perms[1] & 2048)) {
			std_error('access');
		}
	}

	$forum = isset($_POST['forum']) ? (int)$_POST['forum'] : 0;
	$error = '';
	$post = (isset($_POST['next']) || isset($_POST['prev'])) ? 0 : 1;

	if (isset($_POST['sel_th'])) {
		foreach ($_POST['sel_th'] as $k => $v) {
			if (!(int)$v) {
				unset($_POST['sel_th'][$k]);
			}
			$_POST['sel_th'][$k] = (int) $v;
		}
		if (count($_POST['sel_th']) != q_singleval("SELECT count(*) FROM phpgw_fud_thread WHERE forum_id={$frm} AND id IN(".implode(',', $_POST['sel_th']).")")) {
			std_error('access');
		}
	}

	if ($frm && $post && !empty($_POST['new_title']) && !empty($_POST['sel_th']) && count($_POST['sel_th'])) {
		/* we need to make sure that the user has access to destination forum */
		if (!($usr->users_opt & 1048576) && !q_singleval('SELECT f.id FROM phpgw_fud_forum f LEFT JOIN phpgw_fud_mod mm ON mm.user_id='._uid.' AND mm.forum_id=f.id '.(_uid ? 'INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id=2147483647 AND g1.resource_id=f.id LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id' : 'INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id=0 AND g1.resource_id=f.id').' WHERE f.id='.$forum.' AND (mm.id IS NOT NULL OR '.(_uid ? ' ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END)' : ' (g1.group_cache_opt').' & 4) > 0)')) {
			std_error('access');
		}

		/* sanity check */
		if (!count($_POST['sel_th'])) {
			header('Location: /egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&th='.$th.'&'._rsidl);
			exit;
		} else if (count($_POST['sel_th']) > 1) {
			apply_custom_replace($_POST['new_title']);

			db_lock('phpgw_fud_thread_view WRITE, phpgw_fud_thread WRITE, phpgw_fud_forum WRITE, phpgw_fud_msg WRITE, phpgw_fud_poll WRITE');

			$tl = implode(',', $_POST['sel_th']);

			list($start, $repl) = db_saq("SELECT MIN(root_msg_id), SUM(replies) FROM phpgw_fud_thread WHERE id IN({$tl})");
			$repl += count($_POST['sel_th']) - 1;
			list($lpi, $lpd) = db_saq("SELECT last_post_id, last_post_date FROM phpgw_fud_thread WHERE id IN({$tl}) ORDER BY last_post_date DESC LIMIT 1");

			$new_th = th_add($start, $forum, $lpd, 0, 0, $repl, $lpi);
			q("UPDATE phpgw_fud_msg SET reply_to=0, subject='".addslashes(htmlspecialchars($_POST['new_title']))."' WHERE id=".$start);
			q("UPDATE phpgw_fud_msg SET reply_to={$start} WHERE thread_id IN({$tl}) AND (reply_to=0 OR reply_to=id) AND id!={$start}");
			if ($forum != $frm) {
				$p = array();
				$c = q('SELECT poll_id FROM phpgw_fud_msg WHERE thread_id IN('.$tl.') AND apr=1 AND poll_id>0');
				while ($r = db_rowarr($c)) {
					$p[] = $r[0];
				}
				unset($c);
				if (count($p)) {
					q('UPDATE phpgw_fud_poll SET forum_id='.$forum.' WHERE id IN('.implode(',', $p).')');
				}
			}
			q("UPDATE phpgw_fud_msg SET thread_id={$new_th} WHERE thread_id IN({$tl})");
			q("DELETE FROM phpgw_fud_thread WHERE id IN({$tl})");

			rebuild_forum_view($forum);
			if ($forum != $frm) {
				rebuild_forum_view($frm);
				foreach (array($frm, $forum) as $v) {
					$r = db_saq("SELECT MAX(last_post_id), SUM(replies), COUNT(*) FROM phpgw_fud_thread INNER JOIN phpgw_fud_msg ON root_msg_id=phpgw_fud_msg.id AND phpgw_fud_msg.apr=1 WHERE forum_id={$v}");
					if (empty($r[2])) {
						$r = array(0,0,0);
					}
					q("UPDATE phpgw_fud_forum SET thread_count={$r[2]}, post_count={$r[1]}, last_post_id={$r[0]} WHERE id={$v}");
				}
			} else {
				q("UPDATE phpgw_fud_forum SET thread_count=thread_count-".(count($_POST['sel_th']) - 1)." WHERE id={$frm}");
			}
			db_unlock();

			logaction(_uid, 'THRMERGE', $new_th, count($_POST['sel_th']));
			unset($_POST['sel_th']);
		}
	}

	/* fetch a list of accesible forums */
	$c = uq('SELECT f.id, f.name
			FROM phpgw_fud_forum f
			INNER JOIN phpgw_fud_fc_view v ON v.f=f.id
			INNER JOIN phpgw_fud_cat c ON c.id=f.cat_id
			LEFT JOIN phpgw_fud_mod mm ON mm.forum_id=f.id AND mm.user_id='._uid.'
			INNER JOIN phpgw_fud_group_cache g1 ON g1.resource_id=f.id AND g1.user_id='.(_uid ? '2147483647' : '0').'
			'.(_uid ? ' LEFT JOIN phpgw_fud_group_cache g2 ON g2.resource_id=f.id AND g2.user_id='._uid : '').'
			'.($usr->users_opt & 1048576 ? '' : ' WHERE mm.id IS NOT NULL OR ((CASE WHEN g2.id IS NULL THEN g1.group_cache_opt ELSE g2.group_cache_opt END) & 2) > 0').'
			ORDER BY v.id');
	$vl = $kl = '';
	while ($r = db_rowarr($c)) {
		$vl .= $r[0] . "\n";
		$kl .= $r[1] . "\n";
	}

	$forum_sel = tmpl_draw_select_opt(rtrim($vl), rtrim($kl), $frm, '', '');

	$page = !empty($_POST['page']) ? (int) $_POST['page'] : 1;
	if ($page > 1 && isset($_POST['prev'])) {
		--$page;
	} else if (isset($_POST['next'])) {
		++$page;
	}

	$thread_sel = '';
	if (isset($_POST['sel_th'])) {
		$c = uq("SELECT t.id, m.subject FROM phpgw_fud_thread t INNER JOIN phpgw_fud_msg m ON t.root_msg_id=m.id WHERE t.id IN(".implode(',', $_POST['sel_th']).")");
		while ($r = db_rowarr($c)) {
			$thread_sel .= '<option value="'.$r[0].'" selected>'.$r[1].'</option>';
		}
		unset($_POST['sel_th']);
	}
	$c = uq("SELECT t.id, m.subject FROM phpgw_fud_thread_view tv INNER JOIN phpgw_fud_thread t ON t.id=tv.thread_id INNER JOIN phpgw_fud_msg m ON m.id=t.root_msg_id WHERE tv.forum_id={$frm} AND page={$page} ORDER BY pos");
	while ($r = db_rowarr($c)) {
		$thread_sel .= '<option value="'.$r[0].'">'.$r[1].'</option>';
	}

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
<form name="merge_th" action="/egroupware/fudforum/3814588639/index.php?t=merge_th" method="post"><?php echo _hs; ?>
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th width="100%" colspan=2>Merge Topic Control Panel</th></tr>
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
		<table border="0" cellspacing="1" cellpadding="2">
			<tr><td colspan=3 align="center">Select Topics To Merge <font class="SmallText">(you must select more then 1 topic)</font></td></tr>
			<tr><td colspan=3 align="center"><select name="sel_th[]" size=10 multiple><?php echo $thread_sel; ?></select></td></tr>
			<tr> 
				<td align="left" width="100"><input type="submit" name="prev" value="Previous Topics"></td>
				<td align="center" width"200"><input type="submit" name="merge" value="Merge Selected Topics"></td>
				<td align="right" width="100"><input type="submit" name="next" value="Next Topics"></td>
			</tr>
		</table>
	</td>
</tr>
</table>
<input type="hidden" name="page" value="<?php echo $page; ?>">
<input type="hidden" name="frm" value="<?php echo $frm; ?>">
</form>
<?php echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>
