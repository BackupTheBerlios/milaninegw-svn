<?php
	/**************************************************************************\
	* eGroupWare - Messenger                                                   *
	* http://www.egroupware.org                                                *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* --------------------------------------------                             *
	* Funding for this program was provided by http://www.checkwithmom.com     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: class.somessenger.inc.php,v 1.6.2.2 2004/08/18 11:56:44 reinerj Exp $ */

	class sovanilla
	{
		var $db;
		var $owner;
                var $owner_groups;
		function sovanilla()
		{
			$this->db    = &$GLOBALS['phpgw']->db;
			$this->owner = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->owner_groups= array_keys($GLOBALS['phpgw']->accounts->search(array('type'=>'owngroups','query'=>$owner,'query_type'=>'id')));
			$config = CreateObject('phpgwapi.config');
			$config->read_repository();
			$GLOBALS['phpgw_info']['server']['vanilla'] = $config->config_data;
			unset($config);
		}
                function total_messages(){
                }
		function top_discussions($order='t.DateLastActive')
		{
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
                  left join LUM_CategoryBlock cb on t.CategoryID = cb.CategoryID and cb.UserID = 14   
                  where coalesce(crb.Blocked, 0) = 0 
                  and c.cat_owner in (".join(",", $this->owner_groups).") 
                  and t.Active = '1' 
                  and coalesce(cb.Blocked,0) <> '1' 
                  and (t.WhisperUserID = '0' or t.WhisperUserID = 0 or t.WhisperUserID is null )   
                  group by t.DiscussionID, t.DiscussionID 
                  order by ".$order." desc
                  limit 0,10";
                  
                $this->db->query($query,__LINE__,__FILE__);
                
                while ($this->db->next_record())
                {
                  $discussions[] = array(
                                    'DiscussionID'=>$this->db->f('DiscussionID'),
                                            'Name'=>$this->db->f('Name'),
                                            'DateLastActive'=>$this->db->f('DateLastActive'),
                                            'CountComments' =>$this->db->f('CountComments'),
                                                  'Category'=>$this->db->f('Category'),
                                              'LastFullName'=>$this->db->f('LastFullName'),
                                     'LastViewCountComments'=>$this->db->f('LastViewCountComments')
                                     );
		}
                return $discussions;
              }
}