<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: groupmgr.php.t,v 1.1.1.1 2003/10/17 21:11:30 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

function draw_tmpl_perm_table($perm, $perms, $names)
{
	$str = '';
	foreach ($perms as $k => $v) {
		$str .= ($perm & $v[0]) ? '<td title="'.$names[$k].'" class="permYES">Yes</td>' : '<td title="'.$names[$k].'" class="permNO">No</td>';
	}
	return $str;
}

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function alt_var($key)
{
	if (!isset($GLOBALS['_ALTERNATOR_'][$key])) {
		$args = func_get_args(); array_shift($args);
		$GLOBALS['_ALTERNATOR_'][$key] = array('p' => 1, 't' => count($args), 'v' => $args);
		return $args[0];
	}
	$k =& $GLOBALS['_ALTERNATOR_'][$key];
	if ($k['p'] == $k['t']) {
		$k['p'] = 0;
	}
	return $k['v'][$k['p']++];
}function grp_delete_member($id, $user_id)
{
	if (!$user_id || $user_id == '2147483647') {
		return;
	}

	q('DELETE FROM phpgw_fud_group_members WHERE group_id='.$id.' AND user_id='.$user_id);

	if (q_singleval("SELECT id FROM phpgw_fud_group_members WHERE user_id=".$user_id." LIMIT 1")) {
		/* we rebuild cache, since this user's permission for a particular resource are controled by
		 * more the one group. */
		grp_rebuild_cache(array($user_id));
	} else {
		q("DELETE FROM phpgw_fud_group_cache WHERE user_id=".$user_id);
	}
}

function grp_update_member($id, $user_id, $perm)
{
	q('UPDATE phpgw_fud_group_members SET group_members_opt='.$perm.' WHERE group_id='.$id.' AND user_id='.$user_id);
	grp_rebuild_cache(array($user_id));
}

function grp_rebuild_cache($user_id=null)
{
	$list = array();
	if ($user_id !== null) {
		$lmt = ' user_id IN('.implode(',', $user_id).') ';
	} else {
		$lmt = '';
	}

	/* generate an array of permissions, in the end we end up with 1ist of permissions */
	$r = uq("SELECT gm.user_id AS uid, gm.group_members_opt AS gco, gr.resource_id AS rid FROM phpgw_fud_group_members gm INNER JOIN phpgw_fud_group_resources gr ON gr.group_id=gm.group_id WHERE gm.group_members_opt>=65536 AND (gm.group_members_opt & 65536) > 0" . ($lmt ? ' AND '.$lmt : ''));
	while ($o = db_rowobj($r)) {
		foreach ($o as $k => $v) {
	        	$o->{$k} = (int) $v;
		}
		if (isset($list[$o->rid][$o->uid])) {
			if ($o->gco & 131072) {
				$list[$o->rid][$o->uid] |= $o->gco;
			} else {
				$list[$o->rid][$o->uid] &= $o->gco;
			}
		} else {
			$list[$o->rid][$o->uid] = $o->gco;
		}
	}

	$tmp_t = "phpgw_fud_gc_".__request_timestamp__;
	q("CREATE TEMPORARY TABLE ".$tmp_t." (a INT, b INT, c INT)");

	$tmp = array();
	foreach ($list as $k => $v) {
		foreach ($v as $u => $p) {
			$tmp[] = $k.", ".$p.", ".$u;
		}
	}

	if ($tmp) {
		if (__dbtype__ == 'mysql') {
			ins_m($tmp_t, "a,b,c", $tmp, 1);
		} else {
			ins_m($tmp_t, "a,b,c", $tmp, "integer, integer, integer");
		}
	}

	if (!db_locked()) {
		$ll = 1;
		db_lock("phpgw_fud_group_cache WRITE");
	}

	q("DELETE FROM phpgw_fud_group_cache" . ($lmt ? ' WHERE '.$lmt : ''));
	q("INSERT INTO phpgw_fud_group_cache (resource_id, group_cache_opt, user_id) SELECT a,b,c FROM ".$tmp_t);

	if (isset($ll)) {
		db_unlock();
	}

	q("DROP TABLE ".$tmp_t);
}

function group_perm_array()
{
	return array(
		'p_VISIBLE' => array(1, 'Visible'),
		'p_READ' => array(2, 'Read'),
		'p_POST' => array(4, 'Create new topics'),
		'p_REPLY' => array(8, 'Reply to messages'),
		'p_EDIT' => array(16, 'Edit messages'),
		'p_DEL' => array(32, 'Delete messages'),
		'p_STICKY' => array(64, 'Make topics sticky'),
		'p_POLL' => array(128, 'Create polls'),
		'p_FILE' => array(256, 'Attach files'),
		'p_VOTE' => array(512, 'Vote on polls'),
		'p_RATE' => array(1024, 'Rate topics'),
		'p_SPLIT' => array(2048, 'Split/Merge topics'),
		'p_LOCK' => array(4096, 'Lock/Unlock topics'),
		'p_MOVE' => array(8192, 'Move topics'),
		'p_SML' => array(16384, 'Use smilies/emoticons'),
		'p_IMG' => array(32768, 'Use [img] tags'),
		'p_SEARCH' => array(262144, 'Can Search')
	);
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
}function reverse_fmt(&$data)
{
	$data = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $data);
}

	if (!_uid) {
		std_error('access');
	}
	$group_id = isset($_POST['group_id']) ? (int)$_POST['group_id'] : (isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0);

	if ($group_id && !($usr->users_opt & 1048576) && !q_singleval('SELECT id FROM phpgw_fud_group_members WHERE group_id='.$group_id.' AND user_id='._uid.' AND group_members_opt>=131072 AND (group_members_opt & 131072) > 0')) {
		std_error('access');
	}

	$hdr = group_perm_array();
	/* fetch all the groups user has access to */
	if ($usr->users_opt & 1048576) {
		$r = uq('SELECT id, name, forum_id FROM phpgw_fud_groups WHERE id>2 ORDER BY name');
	} else {
		$r = uq('SELECT g.id, g.name, g.forum_id FROM phpgw_fud_group_members gm INNER JOIN phpgw_fud_groups g ON gm.group_id=g.id WHERE gm.user_id='._uid.' AND group_members_opt>=131072 AND (group_members_opt & 131072) > 0 ORDER BY g.name');
	}

	/* make a group selection form */
	$n = 0;
	$vl = $kl = '';
	while ($e = db_rowarr($r)) {
		$vl .= $e[0] . "\n";
	        $kl .= ($e[2] ? '* ' : '') . htmlspecialchars($e[1]) . "\n";
		$n++;
	}

	if (!$n) {
		std_error('access');
	} else if ($n == 1) {
		$group_id = rtrim($vl);
		$group_selection = '';
	} else {
		if (!$group_id) {
			$group_id = (int)$vl;
		}
		$group_selection = tmpl_draw_select_opt(rtrim($vl), rtrim($kl), $group_id, '', '');
		$group_selection = '<br /><br />
<form method="post" action="/egroupware/fudforum/3814588639/index.php?t=groupmgr">
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th colspan=2>Group Editor Selection</th></tr>
<tr class="RowStyleA">
	<td nowrap><b>Group:</b></td>
	<td width="100%"><select name="group_id">'.$group_selection.'</select></td>
</tr>
<tr class="RowStyleC"><td colspan=2 align="left"><input type="submit" class="button" name="btn_groupswitch" value="Edit Group"></td></tr>
</table>'._hs.'</form>';
	}



	if (isset($_POST['btn_cancel'])) {
		unset($_POST);
	}
	if (!($grp = db_sab('SELECT * FROM phpgw_fud_groups WHERE id='.$group_id))) {
		invl_inp_err();
	}

	/* fetch controlled resources */
	if (!$grp->forum_id) {
		$group_resources = '<b>This group controls permissions of the following forums:</b><br>';
		$c = uq('SELECT f.name FROM phpgw_fud_group_resources gr INNER JOIN phpgw_fud_forum f ON gr.resource_id=f.id WHERE gr.group_id='.$group_id);
		while ($r = db_rowarr($c)) {
			$group_resources .= '&nbsp;&nbsp;&nbsp;'.$r[0].'<br>';
		}
	} else {
		$fname = q_singleval('SELECT name FROM phpgw_fud_forum WHERE id='.$grp->forum_id);
		$group_resources = '<b>Primary group for forum:</b> '.htmlspecialchars($fname);
	}

	if ($usr->users_opt & 1048576) {
		$maxperms = 2147483647;
	} else {
		$maxperms = (int) $grp->groups_opt;
	}

	$indicator = '<div align="center">Currently Editing: <b>'.$grp->name.'</b><br>'.$group_resources.'</div>';

	$login_error = '';
	$gr_member = isset($_POST['gr_member']) ? $_POST['gr_member'] : '';
	$find_user = $FUD_OPT_1 & (8388608|4194304) ? '&nbsp;&nbsp;&nbsp;[<a href="javascript://" class="GenLink" onClick="javascript: window_open(\'/egroupware/fudforum/3814588639/index.php?t=pmuserloc&amp;'._rsid.'&amp;js_redr=groupmgr.gr_member&amp;overwrite=1\', \'user_list\', 250,250);">Find User</a>]' : '';
	$perm = 0;

	if (isset($_POST['btn_submit'])) {
		foreach ($hdr as $k => $v) {
			if (isset($_POST[$k]) && $_POST[$k] & $v[0]) {
				$perm |= $v[0];
			}
		}

		/* auto approve members */
		$perm |= 65536;

		if (empty($_POST['edit'])) {
			if (!($usr_id = q_singleval("SELECT id FROM phpgw_fud_users WHERE alias='".addslashes(htmlspecialchars($gr_member))."'"))) {
				$login_error = '<font class="ErrorText">There is no user with a login of "'.htmlspecialchars($gr_member).'"</font><br />';
			} else if (q_singleval('SELECT id FROM phpgw_fud_group_members WHERE group_id='.$group_id.' AND user_id='.$usr_id)) {
				$login_error = '<font class="ErrorText">User "'.htmlspecialchars($gr_member).'" already exists in this group.</font><br />';
			} else {
				q('INSERT INTO phpgw_fud_group_members (group_members_opt, user_id, group_id) VALUES ('.$perm.', '.$usr_id.', '.$group_id.')');
				grp_rebuild_cache(array($usr_id));
			}
		} else if (($usr_id = q_singleval('SELECT user_id FROM phpgw_fud_group_members WHERE group_id='.$group_id.' AND id='.(int)$_POST['edit'])) !== null) {
			if (q_singleval("SELECT user_id FROM phpgw_fud_group_members WHERE group_id=".$group_id." AND user_id=".$usr_id." AND group_members_opt>=131072 AND (group_members_opt & 131072) > 0")) {
				$perm |= 131072;
			}
			q('UPDATE phpgw_fud_group_members SET group_members_opt='.$perm.' WHERE id='.(int)$_POST['edit']);
			grp_rebuild_cache(array($usr_id));
		}
		if (!$login_error) {
			unset($_POST);
			$gr_member = '';
		}
	}

	if (isset($_GET['del']) && ($del = (int)$_GET['del']) && $group_id) {
		$is_gl = q_singleval("SELECT user_id FROM phpgw_fud_group_members WHERE group_id=".$group_id." AND user_id=".$del." AND group_members_opt>=131072 AND (group_members_opt & 131072) > 0");
		grp_delete_member($group_id, $del);

		/* if the user was a group moderator, rebuild moderation cache */
		if ($is_gl) {
			fud_use('groups_adm.inc', true);
			rebuild_group_ldr_cache($del);
		}
	}

	$edit = 0;
	if (isset($_GET['edit']) && ($edit = (int)$_GET['edit'])) {
		if (!($mbr = db_sab('SELECT gm.*, u.alias FROM phpgw_fud_group_members gm LEFT JOIN phpgw_fud_users u ON u.id=gm.user_id WHERE gm.group_id='.$group_id.' AND gm.id='.$edit))) {
			invl_inp_err();
		}
		if ($mbr->user_id == 0) {
			$gr_member = '<font class="anon">Anonymous</font>';
		} else if ($mbr->user_id == '2147483647') {
			$gr_member = '<font class="reg">All Registered Users</font>';
		} else {
			$gr_member = $mbr->alias;
		}
		$perm = $mbr->group_members_opt;
	} else if ($group_id > 2 && !isset($_POST['btn_submit']) && ($luser_id = q_singleval('SELECT MAX(id) FROM phpgw_fud_group_members WHERE group_id='.$group_id))) {
		/* help trick, we fetch the last user added to the group */
		if (!($mbr = db_sab('SELECT 1 AS user_id, group_members_opt FROM phpgw_fud_group_members WHERE id='.$luser_id))) {
			invl_inp_err();
		}
		$perm = $mbr->group_members_opt;
	}

	/* anon users cannot vote or rate */
	if (isset($mbr) && !$mbr->user_id) {
		$maxperms = $maxperms &~ (512|1024);
	}

	/* no members inside the group */
	if (!$perm && !isset($mbr)) {
		$perm = $maxperms;
	}

	/* translated permission names */
	$ts_list = array(
'p_VISIBLE'=>'Visible',
'p_READ'=>'Read',
'p_POST'=>'Post',
'p_REPLY'=>'Reply',
'p_EDIT'=>'Edit',
'p_DEL'=>'Delete',
'p_STICKY'=>'Sticky posts',
'p_POLL'=>'Create polls',
'p_FILE'=>'Attach files',
'p_VOTE'=>'Vote',
'p_RATE'=>'Rate topics',
'p_SPLIT'=>'Split topics',
'p_LOCK'=>'Lock topics',
'p_MOVE'=>'Move topics',
'p_SML'=>'Use smilies',
'p_IMG'=>'Use image tags',
'p_SEARCH'=>'Can Search');

	$perm_sel_hdr = $perm_select = $tmp = '';
	$i = 0;
	foreach ($hdr as $k => $v) {
		$selyes = '';
		if ($maxperms & $v[0]) {
			if ($perm & $v[0]) {
				$selyes = ' selected';
			}
			$perm_select .= '<td align="center">
<select name="'.$k.'" class="SmallText">
	<option value="0">No</option>
	<option value="'.$v[0].'"'.$selyes.'>Yes</option>
</select>
</td>';
		} else {
			/* only show the permissions the user can modify */
			continue;
		}
		$tmp .= '<th align="center">'.$ts_list[$k].'</th>';

		if (++$i == '6') {
			$perm_sel_hdr .= '<tr>'.$tmp.'</tr>
<tr class="RowStyleB">'.$perm_select.'</tr>';
			$perm_select = $tmp = '';
			$i = 0;
		}
	}

	if ($tmp) {
		while (++$i < '6' + 1) {
			$tmp .= '<th> </th>';
			$perm_select .= '<td> </td>';
		}
		$perm_sel_hdr .= '<tr>'.$tmp.'</tr>
<tr class="RowStyleB">'.$perm_select.'</tr>';
	}

	$n_perms = count($hdr);

	if (!$edit) {
		$member_input = '<tr class="RowStyleA"><td nowrap><b>Member</b></td><td width="100%" align="left">'.$login_error.'<input type="text" name="gr_member" value="'.$gr_member.'">'.$find_user.'</td></tr>';
		$submit_button = '<input type="submit" class="button" name="btn_submit" value="Add Member">';
	} else {
		$submit_button = '<input type="submit" class="button" name="btn_cancel" value="Cancel"> 
<input type="submit" class="button" name="btn_submit" value="Update Member">';
		$member_input = '<tr class="RowStyleA"><td nowrap><b>Member</b></td><td width="100%" align="left">'.$gr_member.'</td></tr>';
	}

	/* draw list of group members */
	$group_members_list = '';
	$r = uq('SELECT gm.id AS mmid, gm.*, g.*, u.alias FROM phpgw_fud_group_members gm INNER JOIN phpgw_fud_groups g ON gm.group_id=g.id LEFT JOIN phpgw_fud_users u ON gm.user_id=u.id WHERE gm.group_id='.$group_id.' ORDER BY gm.id');
	while ($obj = db_rowobj($r)) {
		$perm_table = draw_tmpl_perm_table($obj->group_members_opt, $hdr, $ts_list);

		if ($obj->user_id == '0') {
			$member_name = '<font class="anon">Anonymous</font>';
			$group_members_list .= '<tr class="'.alt_var('mem_list_alt','RowStyleA','RowStyleB').'">
<td nowrap>'.$member_name.'</td>
'.$perm_table.'
<td nowrap>[<a href="/egroupware/fudforum/3814588639/index.php?t=groupmgr&amp;'._rsid.'&amp;edit='.$obj->mmid.'&amp;group_id='.$obj->group_id.'">Edit</a>]</td></tr>';
		} else if ($obj->user_id == '2147483647')  {
			$member_name = '<font class="reg">All Registered Users</font>';
			$group_members_list .= '<tr class="'.alt_var('mem_list_alt','RowStyleA','RowStyleB').'">
<td nowrap>'.$member_name.'</td>
'.$perm_table.'
<td nowrap>[<a href="/egroupware/fudforum/3814588639/index.php?t=groupmgr&amp;'._rsid.'&amp;edit='.$obj->mmid.'&amp;group_id='.$obj->group_id.'">Edit</a>]</td></tr>';
		} else {
			$member_name = $obj->alias;
			$group_members_list .= '<tr class="'.alt_var('mem_list_alt','RowStyleA','RowStyleB').'">
<td nowrap>'.$member_name.'</td>
'.$perm_table.'
<td nowrap>[<a href="/egroupware/fudforum/3814588639/index.php?t=groupmgr&amp;'._rsid.'&amp;edit='.$obj->mmid.'&amp;group_id='.$obj->group_id.'">Edit</a>] [<a href="/egroupware/fudforum/3814588639/index.php?t=groupmgr&amp;'._rsid.'&amp;del='.$obj->user_id.'&amp;group_id='.$obj->group_id.'">Delete</a>]</td></tr>';
		}
	}
	$group_control_panel = ''.$group_selection.'
<br />
'.$indicator.'
<br />
<form method="post" action="/egroupware/fudforum/3814588639/index.php?t=groupmgr" name="groupmgr">
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
'.$member_input.'
<tr class="RowStyleB">
	<td colspan=2>
		<table border=0 cellspacing=1 cellpadding=3 width="100%" class="COntentTable">
			'.$perm_sel_hdr.'
		</table>
	</td>
</tr>

<tr>
	<td align=right colspan=2 class="RowStyleC">
		'.$submit_button.'
	</td>
</tr>
</table><input type="hidden" name="group_id" value="'.$group_id.'"><input type="hidden" name="edit" value="'.$edit.'">'._hs.'</form>
<br /><br />
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th>Member</th><th colspan="'.$n_perms.'">Permissions <font size="-1">(move mouse over the permission to see permission type)</font></th><th align="center">Action</th></tr>
'.$group_members_list.'
</table>';

if ($FUD_OPT_2 & 2) {
	$page_gen_end = gettimeofday();
	$page_gen_time = sprintf('%.5f', ($page_gen_end['sec'] - $PAGE_TIME['sec'] + (($page_gen_end['usec'] - $PAGE_TIME['usec'])/1000000)));
	$page_stats = '<br /><div align="left" class="SmallText">Total time taken to generate the page: '.$page_gen_time.' seconds</div>';
} else {
	$page_stats = '';
}
?>
<?php echo $GLOBALS['fud_egw_hdr']; ?>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">
<?php echo $group_control_panel; ?>
<?php echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>