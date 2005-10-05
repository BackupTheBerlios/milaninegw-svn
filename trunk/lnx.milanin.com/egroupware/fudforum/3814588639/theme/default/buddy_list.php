<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: buddy_list.php.t,v 1.1.1.1 2003/10/17 21:11:26 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function buddy_add($user_id, $bud_id)
{
	q('INSERT INTO phpgw_fud_buddy (bud_id, user_id) VALUES ('.$bud_id.', '.$user_id.')');
	return buddy_rebuild_cache($user_id);
}

function buddy_delete($user_id, $bud_id)
{
	q('DELETE FROM phpgw_fud_buddy WHERE user_id='.$user_id.' AND bud_id='.$bud_id);
	return buddy_rebuild_cache($user_id);
}

function buddy_rebuild_cache($uid)
{
	$q = uq('SELECT bud_id FROM phpgw_fud_buddy WHERE user_id='.$uid);
	while ($ent = db_rowarr($q)) {
		$arr[$ent[0]] = 1;
	}

	if (isset($arr)) {
		q('UPDATE phpgw_fud_users SET buddy_list=\''.addslashes(serialize($arr)).'\' WHERE id='.$uid);
		return $arr;
	} else {
		q('UPDATE phpgw_fud_users SET buddy_list=NULL WHERE id='.$uid);
		return;
	}
}function check_return($returnto)
{
	if (!$returnto || !strncmp($returnto, 't=error', 7)) {
		header('Location: /egroupware/fudforum/3814588639/index.php?t=index&'._rsidl);
	} else {
		if (strpos($returnto, 'S=') === false && $GLOBALS['FUD_OPT_1'] & 128) {
			header('Location: /egroupware/fudforum/3814588639/index.php?'.$returnto.'&S='.s);
		} else {
			header('Location: /egroupware/fudforum/3814588639/index.php?'.$returnto);
		}
	}
	exit;
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
}

	if (!_uid) {
		std_error('login');
	}

	if (isset($_POST['add_login'])) {
		if (!($buddy_id = q_singleval("SELECT id FROM phpgw_fud_users WHERE alias='".addslashes(htmlspecialchars($_POST['add_login']))."'"))) {
			error_dialog('Unable to add user', 'The user you&#39;ve tried to add to your buddy list was not found on the forum.');
		}
		if ($buddy_id == _uid) {
			error_dialog('Info', 'Can&#39;t add your self to the buddy list');
		}

		if (!empty($usr->buddy_list)) {
			$usr->buddy_list = @unserialize($usr->buddy_list);
		}

		if (!isset($usr->buddy_list[$buddy_id])) {
			$usr->buddy_list = buddy_add(_uid, $buddy_id);
		} else {
			error_dialog('Info', 'You already have this user on your buddy list');
		}
	}

	/* incomming from message display page (add buddy link) */
	if (isset($_GET['add']) && ($_GET['add'] = (int)$_GET['add'])) {
		if (!empty($usr->buddy_list)) {
			$usr->buddy_list = @unserialize($usr->buddy_list);
		}

		if (($buddy_id = q_singleval('SELECT id FROM phpgw_fud_users WHERE id='.$_GET['add'])) && !isset($usr->buddy_list[$buddy_id])) {
			buddy_add(_uid, $buddy_id);
		}
		check_return($usr->returnto);
	}

	if (isset($_GET['del']) && ($_GET['del'] = (int)$_GET['del'])) {
		buddy_delete(_uid, $_GET['del']);
		/* needed for external links to this form */
		if (isset($_GET['redr'])) {
			check_return($usr->returnto);
		}
	}

	ses_update_status($usr->sid, 'Browsing own buddy list');

	if ($FUD_OPT_1 & 8388608 || (_uid && $FUD_OPT_1 & 4194304)) {
		$buddy_member_search = '<br>Or use the <a href="javascript://" class="GenLink" onClick="javascript: window_open(\'/egroupware/fudforum/3814588639/index.php?t=pmuserloc&amp;'._rsid.'&amp;js_redr=buddy_add.add_login&amp;overwrite=1\', \'user_list\', 250,250);">Find User</a> feature to find a person.';
	} else {
		$buddy_member_search = '';
	}

$tabs = '';
if (_uid) {
	$tablist = array(
'Settings'=>'register',
'Subscriptions'=>'subscribed',
'Referrals'=>'referals',
'Buddy List'=>'buddy_list',
'Ignore List'=>'ignore_list'
);
	if (isset($_POST['mod_id'])) {
		$mod_id_chk = $_POST['mod_id'];
	} else if (isset($_GET['mod_id'])) {
		$mod_id_chk = $_GET['mod_id'];
	} else {
		$mod_id_chk = null;
	}

	if (!$mod_id_chk) {
		if ($FUD_OPT_1 & 1024) {
			$tablist['Private Messaging'] = 'pmsg';
		}
		$pg = ($_GET['t'] == 'pmsg_view' || $_GET['t'] == 'ppost') ? 'pmsg' : $_GET['t'];

		foreach($tablist as $tab_name => $tab) {
			$tab_url = '/egroupware/fudforum/3814588639/index.php?t='.$tab.'&amp;'._rsid;
			if ($tab == 'referals') {
				if (!($FUD_OPT_2 & 8192)) {
					continue;
				}
				$tab_url .= '&amp;id='._uid;
			}
			$tabs .= $pg == $tab ? '<td class="tabA"><div class="tabT"><a href="'.$tab_url.'">'.$tab_name.'</a></div></td>' : '<td class="tabI"><div class="tabT"><a href="'.$tab_url.'">'.$tab_name.'</a></div></td>';
		}

		$tabs = '<table border=0 cellspacing=1 cellpadding=0 class="tab">
<tr class="tab">'.$tabs.'</tr>
</table>';
	}
}

	$c = uq('SELECT b.bud_id, u.id, u.alias, u.join_date, u.bday, (u.users_opt & 32768), u.posted_msg_count, u.home_page, u.last_visit AS time_sec
		FROM phpgw_fud_buddy b INNER JOIN phpgw_fud_users u ON b.bud_id=u.id WHERE b.user_id='._uid);

	$buddies = '';
	/* Result index
	 * 0 - bud_id	1 - user_id	2 - login	3 - join_date	4 - bday	5 - users_opt	6 - msg_count
	 * 7 - home_page	8 - last_visit
	 */

	if (($r = @db_rowarr($c))) {
		do {
			$homepage_link = $r[7] ? '<a class="GenLink" href="'.$r[7].'" target="_blank"><img src="/egroupware/fudforum/3814588639/theme/default/images/homepage.gif" alt="" /></a>' : '';
			if ((!($r[5] & 32768) && $FUD_OPT_2 & 32) || $usr->users_opt & 1048576) {
				$online_status = (($r[8] + $LOGEDIN_TIMEOUT * 60) > __request_timestamp__) ? '<img src="/egroupware/fudforum/3814588639/theme/default/images/online.gif" title="'.$r[2].' is currently online" alt="'.$r[2].' is currently online" />' : '<img src="/egroupware/fudforum/3814588639/theme/default/images/offline.gif" title="'.$r[2].' is currently offline" alt="'.$r[2].' is currently offline" />';
			} else {
				$online_status = '';
			}

			if ($r[5] && substr($r[4], 4) == date('md')) {
				$age = date('Y')  - substr($r[4], 0, 4);
				$bday_indicator = '<img src="blank.gif" alt="" width=10 height=1 /><img src="/egroupware/fudforum/3814588639/theme/default/images/bday.gif" alt="" />Today '.$r[2].' turns '.$age;
			} else {
				$bday_indicator = '';
			}

			$contact_link = $FUD_OPT_1 & 1024 ? '<a href="/egroupware/fudforum/3814588639/index.php?t=ppost&amp;'._rsid.'&amp;toi='.urlencode($r[0]).'" class="GenLink">'.$r[2].'</a>' : '<a href="/egroupware/fudforum/3814588639/index.php?t=email&amp;toi='.$r[1].'&amp;'._rsid.'" class="GenLink">'.$r[2].'</a>';

			$buddies .= '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'">
	<td align="center">'.$online_status.'</td>
	<td width="100%" class="GenText">'.$contact_link.'&nbsp;<font class="SmallText">(<a href="/egroupware/fudforum/3814588639/index.php?t=buddy_list&amp;'._rsid.'&amp;del='.$r[0].'" class="GenLink">remove</a>)</font>&nbsp;'.$bday_indicator.'</td>
	<td align="center">'.$r[6].'</td>
	<td align="center" nowrap>'.strftime("%a, %d %B %Y %H:%M", $r[3]).'</td>
	<td class="GenText" nowrap><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$r[1].'&amp;'._rsid.'"><img src="/egroupware/fudforum/3814588639/theme/default/images/msg_about.gif" alt="" /></a>&nbsp;<a href="/egroupware/fudforum/3814588639/index.php?t=showposts&amp;'._rsid.'&amp;id='.$r[1].'" class="GenLink"><img src="/egroupware/fudforum/3814588639/theme/default/images/show_posts.gif" alt="" /></a> '.$homepage_link.'</td>
</tr>';
		} while (($r = db_rowarr($c)));
		$buddies = '<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th>Status</th><th>My Buddies</th><th nowrap align="center">Post Count</th><th nowrap align="center">Registered On</th><th nowrap align="center">Action</th></tr>
'.$buddies.'
</table>';
	}

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
<?php echo $tabs; ?>
<?php echo $buddies; ?>
<br /><br />
<form name="buddy_add" action="/egroupware/fudforum/3814588639/index.php?t=buddy_list" method="post"><?php echo _hs; ?><div align="center">
<table align="center" border="0" cellspacing="1" cellpadding="2" class="MiniTable">
<tr><th nowrap>Add Buddy</th></tr>
<tr class="RowStyleA">
<td nowrap class="GenText"><font class="Smalltext">Enter the login of the user you wish to add.<?php echo $buddy_member_search; ?></font><p>
<input type="text" name="add_login" value="" maxlength=100 size=25> <input type="submit" class="button" name="submit" value="Add"></td></tr>
</table></div></form>
<?php echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>