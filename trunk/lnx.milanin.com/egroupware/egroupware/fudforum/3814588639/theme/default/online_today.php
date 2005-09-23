<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: online_today.php.t,v 1.1.1.1 2003/10/17 21:11:30 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

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
}function draw_user_link($login, $type, $custom_color='')
{
	if ($custom_color) {
		return '<font style="color: '.$custom_color.'">'.$login.'</font>';
	}

	if (!($type & 1572864)) {
		return $login;
	} else if ($type & 1048576) {
		return '<font class="adminColor">'.$login.'</font>';
	} else if ($type & 524288) {
		return '<font class="modsColor">'.$login.'</font>';
	}
}

	ses_update_status($usr->sid, 'Viewing the list of people who were on the forum today.');



	$dt = explode(' ', date('m d Y', __request_timestamp__));
	$today = mktime(0, 0, 0, $dt[0], $dt[1], $dt[2]);

	$c = uq('SELECT
			u.alias AS login, u.users_opt, u.id, u.last_visit, u.custom_color,
			m.id AS mid, m.subject, m.post_stamp,
			t.forum_id,
			mm.id,
			(CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) AS gco
		FROM phpgw_fud_users u
		LEFT JOIN phpgw_fud_msg m ON u.u_last_post_id=m.id
		LEFT JOIN phpgw_fud_thread t ON m.thread_id=t.id
		LEFT JOIN phpgw_fud_mod mm ON mm.forum_id=t.forum_id AND mm.user_id='._uid.'
		INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id='.(_uid ? '2147483647' : '0').' AND g1.resource_id=t.forum_id
		LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=t.forum_id
		WHERE u.last_visit>'.$today.' AND '.(!($usr->users_opt & 1048576) ? "(u.users_opt & 32768)=0 AND" : '').' u.id!='._uid.'
		ORDER BY u.alias, u.last_visit');
	/*
		array(9) {
			   [0]=> string(4) "root" [1]=> string(1) "A" [2]=> string(4) "9944" [3]=> string(10) "1049362510"
		           [4]=> string(5) "green" [5]=> string(6) "456557" [6]=> string(33) "Re: Deactivating TCP checksumming"
		           [7]=> string(10) "1049299437" [8]=> string(1) "6"
		         }
	*/

	$user_entries='';
	while ($r = db_rowarr($c)) {
		$user_login = draw_user_link($r[0], $r[1], $r[4]);
		$user_login = '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$r[2].'&amp;'._rsid.'">'.$user_login.'</a>';

		if (!$r[7]) {
			$last_post = 'n/a';
		} else if ($r[10] & 1 || $r[9] || $usr->users_opt & 1048576) {
			$last_post = ''.strftime("%a, %d %B %Y %H:%M", $r[7]).'<br />
<a href="/egroupware/fudforum/3814588639/index.php?t='.d_thread_view.'&amp;goto='.$r[5].'&amp;'._rsid.'">'.$r[6].'</a>';
		} else {
			$last_post = 'You do not have appropriate permissions needed to see this topic.';
		}

		$user_entries .= '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'">
	<td class="GenText">'.$user_login.'</td>
	<td class="DateText">'.strftime("%H:%M:%S", $r[3]).'</td>
	<td class="SmallText">'.$last_post.'</td>
</tr>';
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
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th>User</th><th>Last Visited</th><th>Last Post</th></tr>
<?php echo $user_entries; ?>
</table>
<?php echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>