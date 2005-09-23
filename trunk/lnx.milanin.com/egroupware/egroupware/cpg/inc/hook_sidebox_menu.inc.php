<?php
	/**************************************************************************\
	* eGroupWare                                                               *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	* --------------------------------------------                             *
	\**************************************************************************/

{

 /*
	This hookfile is for generating an app-specific side menu used in the idots 
	template set.

	$menu_title speaks for itself
	$file is the array with link to app functions

	display_sidebox can be called as much as you like
 */
$TABLE_USERS = 'phpgw_cpg_users';
$TABLE_USERGROUPS = 'phpgw_cpg_usergroups';

$GLOBALS['phpgw']->db->query("SELECT user_id, user_group, user_group_list FROM {$TABLE_USERS} ". 
							 "WHERE user_name ='{$GLOBALS['phpgw_info']['user']['account_lid']}'");

$GLOBALS['phpgw']->db->next_record();

$uid = 10000 + (int)$GLOBALS['phpgw']->db->f("user_id");
$pri_group = (int)$GLOBALS['phpgw']->db->f("user_group");
$groups = array($GLOBALS['phpgw']->db->f("user_group_list"));

if ($GLOBALS['phpgw']->db->f("user_group_list") != ''){
	if (!in_array($pri_group, $groups)) array_push($groups, $pri_group);
}else{$groups[0] = $pri_group;}

$GLOBALS['phpgw']->db->query("SELECT MAX(can_rate_pictures) as can_rate_pictures, ".
						"MAX(can_send_ecards) as can_send_ecards, " .
                        "MAX(custom_user_upload) as custom_user_upload, MAX(num_file_upload) as num_file_upload, " .
                        "MAX(num_URI_upload) as num_URI_upload, " .
                        "MAX(can_post_comments) as can_post_comments, MAX(can_upload_pictures) as can_upload_pictures, " .
                        "MAX(can_create_albums) as can_create_albums, " .
                        "MAX(has_admin_access) as has_admin_access, " .
                        "MIN(pub_upl_need_approval) as pub_upl_need_approval, MIN( priv_upl_need_approval) as  priv_upl_need_approval ".
                        "FROM {$TABLE_USERGROUPS} WHERE group_id in (" .  implode(",", $groups). ")");

$GLOBALS['phpgw']->db->next_record();
$has_admin_access = (int)$GLOBALS['phpgw']->db->f("has_admin_access");
$can_upload_pictures = (int)$GLOBALS['phpgw']->db->f("can_upload_pictures");
$can_create_albums = (int)$GLOBALS['phpgw']->db->f("can_create_albums"); 

$cat = get_var('cat', array('GET','POST'));
$album = get_var('album', array('GET','POST'));

if ($cat != '') {$cat = '&cat='.$cat;}
if (is_numeric($album)) {$cat = '&cat=-'.$album;}

$menu_title = $GLOBALS['phpgw_info']['apps'][$appname]['title'] . ' '. lang('Menu');
	$file = Array(
		'Overview' => $GLOBALS['phpgw']->link('/cpg/index.php'),
		'My Gallery' => $GLOBALS['phpgw']->link('/cpg/index.php?cat='.$uid),
		'newest Uploads' => $GLOBALS['phpgw']->link('/cpg/thumbnails.php?album=lastup'.$cat),
	    'newest Comments' => $GLOBALS['phpgw']->link('/cpg/thumbnails.php?album=lastcom'.$cat),
		'most viewed' => $GLOBALS['phpgw']->link('/cpg/thumbnails.php?album=topn'.$cat),
		'most rated' => $GLOBALS['phpgw']->link('/cpg/thumbnails.php?album=toprated'.$cat),
		'My Favorites' => $GLOBALS['phpgw']->link('/cpg/thumbnails.php?album=favpics'),
		'Search' => $GLOBALS['phpgw']->link('/cpg/search.php'),
	);
	if ($can_upload_pictures == 1) {
		$file1 = Array('Upload' => $GLOBALS['phpgw']->link('/cpg/upload.php'),);
		$file = array_merge($file, $file1);
	}

	if ($can_create_albums == 1) {
		$file1 = Array('Album' => $GLOBALS['phpgw']->link('/cpg/albmgr.php'),
			'modify Album' => $GLOBALS['phpgw']->link('/cpg/modifyalb.php'),);
		$file = array_merge($file, $file1);
	}

	display_sidebox($appname,$menu_title,$file);
 
	if ($GLOBALS['phpgw_info']['user']['apps']['preferences'])
	{
		$menu_title = lang('Preferences');
		$file = Array(
			'My Profile' => $GLOBALS['phpgw']->link('/cpg/profile.php?op=edit_profile'),
		);
		display_sidebox($appname,$menu_title,$file);
	}
 
	if ($has_admin_access == 1)
	{
        $title = 'Administration';
        $file = Array(
                'Ckeck Upload'  => $GLOBALS['phpgw']->link('/cpg/editpics.php?mode=upload_approval'),
                'Preferences' => $GLOBALS['phpgw']->link('/cpg/config.php'),
				'Categories' => $GLOBALS['phpgw']->link('/cpg/catmgr.php'),
                'Users' => $GLOBALS['phpgw']->link('/cpg/usermgr.php'),
				'Groups' => $GLOBALS['phpgw']->link('/cpg/groupmgr.php'),
				'Ban User' => $GLOBALS['phpgw']->link('/cpg/banning.php'),
				'Show eCards' => $GLOBALS['phpgw']->link('/cpg/db_ecard.php'),
				'Edit Comments' => $GLOBALS['phpgw']->link('/cpg/reviewcom.php'),
				'Batch-Add' => $GLOBALS['phpgw']->link('/cpg/searchnew.php'),
				'Admin Tools' => $GLOBALS['phpgw']->link('/cpg/util.php'),
        );

		display_sidebox($appname,$title,$file);
	}
	unset($title);
	unset($file);
}
?>
