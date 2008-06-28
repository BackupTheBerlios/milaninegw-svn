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

	class soprofile
	{
		var $db;
		var $owner;
                var $owner_groups;
                var $table_prefix;
                var $public_functions = array(
			'get_relative_percentage'          => True,
          		'get_members_views'          	   => True,
          		'get_guests_views'          	   => True,
		);
		function soprofile()
		{
			$this->db    = &$GLOBALS['phpgw']->db;
                        $this->table_prefix = "members_";
			$this->owner = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->owner_groups= array_keys($GLOBALS['phpgw']->accounts->search(array('type'=>'owngroups','query'=>$owner,'query_type'=>'id')));
			$config = CreateObject('phpgwapi.config');
			$config->read_repository();
			$GLOBALS['phpgw_info']['server']['profile'] = $config->config_data;
			unset($config);
		}
                function count_items()
                {
                	$this->db->query('SELECT count(*) as items FROM `'.
          				 $this->table_prefix.
          				 'profile_data` WHERE `owner` ='.$this->owner);
                        $this->db->next_record();
			return $this->db->f(0);
                }
                function get_max_items()
                {
                	$this->db->query("SELECT value FROM `global_config` where name='profile_data_count'",__LINE__,__FILE__);
                    $this->db->next_record();
					return $this->db->f(0);
                }
                function get_relative_percentage()
                {
                	return round($this->count_items()*100/$this->get_max_items(),0);
                }
                function get_members_views()
                {
                	$query='SELECT pf.viewer,
                                       u.name,
                                       u.username,
                                       i.filename,
                                       FROM_UNIXTIME( pf.timestamp, \'%D/%M/%y %h:%i\' ) AS viewdate
                                       FROM `'.$this->table_prefix.'profile_views` pf
                                       LEFT JOIN '.$this->table_prefix.'users u ON u.ident = pf.viewer
                                       LEFT JOIN '.$this->table_prefix.'icons i ON i.ident = u.icon
                                       WHERE pf.viewer !=0
                                       AND pf.owner ='.$this->owner.' '.
                                       'ORDER by pf.timestamp desc';
                        $this->db->query($query,__LINE__,__FILE__);
                        while ($this->db->next_record()){
                          $v[]=array(
                          	'name'=>$this->db->f('name'),
                          	'user'=>$this->db->f('username'),
                          	'icon'=>$this->db->f('filename'),
                          	'date'=>$this->db->f('viewdate'),
                          	);
                        }
			return $v;
                }
                function get_guests_views()
                {
                	$query='SELECT pf.referral,
                                       pf.counter,
                                       FROM_UNIXTIME( pf.timestamp, \'%D/%M/%y %h:%i\' ) AS viewdate
                                       FROM `'.$this->table_prefix.'profile_views` pf
                                       WHERE pf.viewer = 0
                                       AND pf.owner ='.$this->owner.' '.
                                       'ORDER by pf.timestamp desc';
                        $this->db->query($query,__LINE__,__FILE__);
                        while ($this->db->next_record()){
                          $v[]=array(
                          	'referral'=>$this->db->f('referral'),
                          	'date'=>$this->db->f('viewdate'),
                          	'counter'=>$this->db->f('counter')
                          	);
                        }
			return $v;
                }

}
