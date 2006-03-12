<?php
	/**************************************************************************\
	* eGroupWare - Webpage news admin                                          *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	* --------------------------------------------                             *
	* This program was sponsered by Golden Glair productions                   *
	* http://www.goldenglair.com                                               *
	\**************************************************************************/

	/* $Id: hook_home.inc.php,v 1.7.2.1 2004/09/04 09:57:45 dawnlinux Exp $ */
	$owner = $GLOBALS['phpgw_info']['user']['account_id'];
	$owner_groups= array_keys($GLOBALS['phpgw']->accounts->search(array('type'=>'owngroups','query'=>'','query_type'=>'id')));
	$config = CreateObject('phpgwapi.config');
	$config->read_repository();
	$GLOBALS['phpgw_info']['server']['vanilla'] = $config->config_data;
	unset($config);
        
                $lastlogin = $GLOBALS['phpgw']->session->appsession('account_previous_login','phpgwapi');
		$GLOBALS['phpgw']->translation->add_app('vanilla');
		$title = lang('Discussions with fresh comments ');
		
		$portalbox = CreateObject('phpgwapi.listbox',array(
			'title'     => $title,
			'primary'   => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
			'secondary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
			'tertiary'  => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
			'width'     => '100%',
			'outerborderwidth' => '0',
			'header_background_image' => $GLOBALS['phpgw']->common->image('phpgwapi/templates/default','bg_filler')
		));

		
		
		$app_id = $GLOBALS['phpgw']->applications->name2id('vanilla');
		$GLOBALS['portal_order'][] = $app_id;
		$order='t.DateLastActive';
		$query="select t.DiscussionID,
                    t.FirstCommentID,
                    t.AuthUserID,
                    t.Active,
                    t.Closed,
                    t.Sticky,
                    t.Name,
                    t.DateCreated,
                    t.LastUserID,
                    t.DateLastActive,
                    t.CountComments,
                    t.CategoryID,
                    egwu.account_lid as AuthUsername,
                    concat(egwu.account_firstname, ' ',egwu.account_lastname) as AuthFullName,
                    egwlu.account_lid as LastUsername,
                    concat(egwlu.account_firstname, ' ',egwlu.account_lastname) as LastFullName,
                    c.cat_name as Category,
                    b.DiscussionID is not null as Bookmarked,
                    utw.LastViewed, 
                    coalesce(utw.CountComments, 0) as LastViewCountComments 
                  from LUM_Discussion t 
                  left join phpgw_accounts egwu on t.AuthUserID = egwu.account_id 
                  left join phpgw_accounts egwlu on t.LastUserID = egwlu.account_id 
                  left join phpgw_categories c on t.CategoryID = c.cat_id 
                  left join LUM_CategoryRoleBlock crb on t.CategoryID = crb.CategoryID and crb.RoleID = 3 
                  left outer join LUM_UserBookmark b on t.DiscussionID = b.DiscussionID and b.UserID = 14 
                  left join LUM_UserDiscussionWatch utw on t.DiscussionID = utw.DiscussionID and utw.UserID = 14 
                  left join LUM_CategoryBlock cb on t.CategoryID = cb.CategoryID and cb.UserID = 14 ".
                  //where (t.DateCreated > FROM_UNIXTIME( ".$lastlogin." ) OR t.DateLastActive > FROM_UNIXTIME( ".$lastlogin." )) 
                  "where t.CountComments > coalesce(utw.CountComments, 0)
                  and coalesce(crb.Blocked, 0) = 0 
                  and c.cat_owner in (".join(",", $owner_groups).") 
                  and t.Active = '1' 
                  and coalesce(cb.Blocked,0) <> '1' 
                  and (t.WhisperUserID = '0' or t.WhisperUserID = 0 or t.WhisperUserID is null )   
                  group by t.DiscussionID, t.DiscussionID 
                  order by ".$order." desc
                  limit 0,".
                  (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) ? $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] : 15);
                  $GLOBALS['phpgw']->db->query($query,__LINE__,__FILE__);
                  
                  while ($GLOBALS['phpgw']->db->next_record())
                  {
		    $portalbox->data[] = array(
                                            'text' => $GLOBALS['phpgw']->db->f('Category')."  ". 
                                             $GLOBALS['phpgw']->db->f('Name')." ".
                                             lang("from")." ".$GLOBALS['phpgw']->db->f('LastFullName')." ".
                                             $GLOBALS['phpgw']->db->f('LastViewCountComments')."/".$GLOBALS['phpgw']->db->f('CountComments'),
                                            'link' => 'http://'.$_SERVER['SERVER_NAME'].
                                                      '/vanilla/comments.php?DiscussionID='.$GLOBALS['phpgw']->db->f('DiscussionID')
                    );
                  }

		$GLOBALS['portal_order'][] = $app_id;
		$var = Array(
				'up'    => Array('url'  => '/set_box.php', 'app'        => $app_id),
				'down'  => Array('url'  => '/set_box.php', 'app'        => $app_id),
				'close' => Array('url'  => '/set_box.php', 'app'        => $app_id),
				'question'      => Array('url'  => '/set_box.php', 'app'        => $app_id),
				'edit'  => Array('url'  => '/set_box.php', 'app'        => $app_id)
		);

		while(list($key,$value) = each($var))
		{
			$portalbox->set_controls($key,$value);
		}

		$tmp = "\r\n"
			. '<!-- start Vanilla -->' . "\r\n"
			. $portalbox->draw()
			. '<!-- end Vanilla -->'. "\r\n";
		print $tmp;
	
?>
