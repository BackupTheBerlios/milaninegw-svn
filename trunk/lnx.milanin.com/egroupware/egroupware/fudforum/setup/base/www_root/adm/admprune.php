<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: admprune.php,v 1.2 2003/12/18 16:42:31 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

	@set_time_limit(6000);

	require('./GLOBALS.php'); fud_egw();
	fud_use('adm.inc', true);
	fud_use('widgets.inc', true);
	fud_use('imsg_edt.inc');
	fud_use('th.inc');
	fud_use('ipoll.inc');
	fud_use('attach.inc');
	fud_use('th_adm.inc');

	if (isset($_POST['btn_prune']) && !empty($_POST['thread_age'])) {
		/* figure out our limit if any */
		if ($_POST['forumsel'] == '0') {
			$lmt = '';
			$msg = '<font color="red">from all forums</font>';
		} else if (!strncmp($_POST['forumsel'], 'cat_', 4)) {
			$c = uq('SELECT id FROM '.$DBHOST_TBL_PREFIX.'forum WHERE cat_id='.(int)substr($_POST['forumsel'], 4));
			while ($r = db_rowarr($c)) {
				$l[] = $r[0];
			}
			if ($lmt = implode(',', $l)) {
				$lmt = ' AND forum_id IN('.$lmt.') ';
			}
			$msg = '<font color="red">from all forums in category "'.q_singleval('SELECT name FROM '.$DBHOST_TBL_PREFIX.'cat WHERE id='.(int)substr($_POST['forumsel'], 4)).'"</font>';
		} else {
			$lmt = ' AND forum_id='.(int)$_POST['forumsel'].' ';
			$msg = '<font color="red">from forum "'.q_singleval('SELECT name FROM '.$DBHOST_TBL_PREFIX.'forum WHERE id='.(int)$_POST['forumsel']).'"</font>';
		}
		$back = __request_timestamp__ - $_POST['units'] * $_POST['thread_age'];

		if (!isset($_POST['btn_conf'])) {
			/* count the number of messages & topics that will be affected */
			$topic_cnt = q_singleval('SELECT count(*) FROM '.$DBHOST_TBL_PREFIX.'thread WHERE last_post_date<'.$back.$lmt);
			$msg_cnt = q_singleval('SELECT SUM(replies) FROM '.$DBHOST_TBL_PREFIX.'thread WHERE last_post_date<'.$back.$lmt) + $topic_cnt;
?>
<html>
<body bgcolor="white">
<div align=center>You are about to delete <font color="red"><?php echo $topic_cnt; ?></font> topics containing <font color="red"><?php echo $msg_cnt; ?></font> messages,
which were posted before <font color="red"><?php echo strftime('%Y-%m-%d %T', $back); ?></font> <?php echo $msg; ?><br><br>
			Are you sure you want to do this?<br>
			<form method="post">
			<input type="hidden" name="btn_prune" value="1">
			<?php echo _hs; ?>
			<input type="hidden" name="thread_age" value="<?php echo $_POST['thread_age']; ?>">
			<input type="hidden" name="units" value="<?php echo $_POST['units']; ?>">
			<input type="hidden" name="forumsel" value="<?php echo $_POST['forumsel']; ?>">
			<input type="submit" name="btn_conf" value="Yes">
			<input type="submit" name="btn_cancel" value="No">
			</form>
</div>
</body>
</html>
<?php
			exit;
		} else {
			db_lock($DBHOST_TBL_PREFIX.'thr_exchange WRITE, '.$DBHOST_TBL_PREFIX.'thread_view WRITE, '.$DBHOST_TBL_PREFIX.'level WRITE, '.$DBHOST_TBL_PREFIX.'forum WRITE, '.$DBHOST_TBL_PREFIX.'forum_read WRITE, '.$DBHOST_TBL_PREFIX.'thread WRITE, '.$DBHOST_TBL_PREFIX.'msg WRITE, '.$DBHOST_TBL_PREFIX.'attach WRITE, '.$DBHOST_TBL_PREFIX.'poll WRITE, '.$DBHOST_TBL_PREFIX.'poll_opt WRITE, '.$DBHOST_TBL_PREFIX.'poll_opt_track WRITE, '.$DBHOST_TBL_PREFIX.'users WRITE, '.$DBHOST_TBL_PREFIX.'thread_notify WRITE, '.$DBHOST_TBL_PREFIX.'msg_report WRITE, '.$DBHOST_TBL_PREFIX.'thread_rate_track WRITE');

			$c = q('SELECT root_msg_id, forum_id FROM '.$DBHOST_TBL_PREFIX.'thread WHERE last_post_date<'.$back.$lmt);
			while ($r = db_rowarr($c)) {
				fud_msg_edit::delete(false, $r[0], 1);
				$frm_list[$r[1]] = $r[1];
			}
			unset($r);
			foreach ($frm_list as $v) {
				rebuild_forum_view($v);
			}
			db_unlock();
			echo '<h2 color="red">It is highly recommended that you run a consitency checker after prunning.</h2>';
		}
	}

	require($WWW_ROOT_DISK . 'adm/admpanel.php');
?>
<h2>Topic Prunning</h2>
<form method="post" action="admprune.php">
<table class="datatable">
<tr class="field">
	<td nowrap>Topics with last post made:</td>
	<td ><input type="text" name="thread_age"></td>
	<td nowrap><?php draw_select("units", "Day(s)\nWeek(s)\nMonth(s)\nYear(s)", "86400\n604800\n2635200\n31622400", '86400'); ?>&nbsp;&nbsp;ago</td>
</tr>

<tr class="field">
	<td >Limit to forum:</td>
	<td colspan=2 nowrap>
	<?php
		$oldc = '';
		$c = uq('SELECT f.id, f.name, c.name, c.id FROM '.$DBHOST_TBL_PREFIX.'forum f INNER JOIN '.$DBHOST_TBL_PREFIX.'cat c ON f.cat_id=c.id ORDER BY c.view_order, f.view_order');
		echo '<select name="forumsel"><option value="0">- All Forums -</option>';
		while ($r = db_rowarr($c)) {
			if ($oldc != $r[3]) {
				echo '<option value="cat_'.$r[3].'">'.$r[2].'</option>';
				$oldc = $r[3];
			}
			echo '<option value="'.$r[0].'">&nbsp;&nbsp;-&nbsp;'.$r[1].'</option>';
		}
		echo '</select>';
	?>
</tr>

<tr class="field">
	<td align=right colspan=3><input type="submit" name="btn_prune" value="Prune"></td>
</tr>
</table>
<?php echo _hs; ?>
</form>
<?php require($WWW_ROOT_DISK . 'adm/admclose.php'); ?>
