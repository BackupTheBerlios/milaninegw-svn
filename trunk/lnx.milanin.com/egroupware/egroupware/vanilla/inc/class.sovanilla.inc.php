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
              function read_category_watchers(){
                $query="select c.cat_id,c.cat_name,coalesce(cw.CategoryID,0) as cat_watch
                        from phpgw_categories c 
                        left join LUM_UserCategoryWatch cw on cw.CategoryID=c.cat_id and cw.UserID=".$this->owner." ".
                        "where cat_appname = 'vanilla' and cat_owner in (".join(",", $this->owner_groups).")";
                $this->db->query($query,__LINE__,__FILE__);
                while ($this->db->next_record())
                {
                  $categories[] = array(
                                    'cat_id'=> $this->db->f('cat_id'),
                                    'cat_name'=> $this->db->f('cat_name'),
                                    'cat_watch'=> ($this->db->f('cat_watch') != 0 ? 0 : 1 )
                                    );
                }
                return $categories;
              }
              function read_disc_watchers(){
                $query="SELECT b.DiscussionID, d.Name,
                        (d.CountComments - dw.CountComments) NewComments
                        FROM `LUM_UserBookmark` b
                        JOIN LUM_Discussion d ON b.DiscussionID = d.DiscussionID
                        LEFT JOIN LUM_UserDiscussionWatch dw ON dw.DiscussionID = d.DiscussionID and dw.UserID=b.UserID
                        WHERE b.UserID=".$this->owner;
                $this->db->query($query,__LINE__,__FILE__);
                while ($this->db->next_record())
                {
                  $discs[] = array(
                                    'disc_id'=> $this->db->f('DiscussionID'),
                                    'disc_name'=> $this->db->f('Name'),
                                    'disc_newcomm'=> $this->db->f('NewComments'),
                                    );
                }
                if (sizeof($discs)<1){
                      $discs[] = array(
                                    'disc_id'=> 0,
                                    'disc_name'=> lang("No Discussions"),
                                    'disc_newcomm'=>0,
                                    );
                }
                return $discs;
              }
              function write_category_watchers($cat_watchers=Array())
              {
                $this->clear_category_watchers();
                foreach ( array_keys($cat_watchers) as $cat_id)
                {
                  if ($cat_watchers[$cat_id]==0){
                    $query="INSERT INTO LUM_UserCategoryWatch VALUES(".$this->owner.",".$cat_id.')';
                    $this->db->query($query,__LINE__,__FILE__);
                  }
                }
                return True;
              }
              function write_disc_watchers($disc_watchers=Array())
              {
                if ( is_array($disc_watchers)){
                  foreach ( array_keys($disc_watchers) as $disc_id)
                  {
                    if ($disc_watchers[$disc_id]==0){
                      $query="DELETE FROM LUM_UserBookmark WHERE UserID=".$this->owner." and DiscussionID=".$disc_id;
                      $this->db->query($query,__LINE__,__FILE__);
                    }
                  }
                }
                return True;
              }
              function clear_category_watchers()
              {
                $query='DELETE FROM LUM_UserCategoryWatch Where UserID ='.$this->owner;
                $this->db->query($query,__LINE__,__FILE__);
                $this->db->next_record();
                return True;
              }
              function get_settings(){
                $query='SELECT Settings FROM LUM_User Where UserID ='.$this->owner;
                $this->db->query($query,__LINE__,__FILE__);
                $this->db->next_record();
                $settings = $this->db->f('Settings');
                if ( isset( $settings ) )
                {
                  return $this->UnserializeAssociativeArray($this->db->f('Settings'));
                }else{
                  return Array('HtmlOn' => 1);
                }
              }
              function save_settings($settings)
              {
                if (!isset($settings['HtmlOn']) && $settings['HtmlOn'] != 1) $settings['HtmlOn'] = 1;
//                 echo '<!-- serialized: ['.$this->SerializeArray($settings).'] -->';
                $query='UPDATE LUM_User SET Settings=\''.$this->SerializeArray($settings).'\' Where UserID='.$this->owner;
                $this->db->query($query,__LINE__,__FILE__);
                return True;
              }
              function UnserializeAssociativeArray($InSerialArray) {
                $aReturn = array();
                if ($InSerialArray != "" && !is_array($InSerialArray)) {
                  $aReturn = unserialize($InSerialArray);
                }
                return $aReturn;	
              }
              function SerializeArray($InArray) {
                $sReturn = "";
                if (is_array($InArray)) {
                        if (count($InArray) > 0) {
                                $sReturn = serialize($InArray);
                                $sReturn = addslashes($sReturn);
                        }
                }
                return $sReturn;
              }
}
