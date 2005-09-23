<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - Web Content Management                              *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: class.ACL_SO.inc.php,v 1.4 2004/02/10 14:56:33 ralfbecker Exp $ */

class ACL_SO
{
	var $db;
	var $acl;
	var $acct;

	function ACL_SO()
	{
		$this->db = $GLOBALS['phpgw']->db;
		$this->acl = CreateObject('phpgwapi.acl');
		$this->acct = CreateObject('phpgwapi.accounts');
	}

	function get_permission($location)
	{
		$memberships = $this->acct->membership($this->acl->logged_in_user);
		$sql = 'SELECT acl_rights FROM phpgw_acl WHERE acl_location=\''.$location.
			'\' and acl_account in ('.$GLOBALS['phpgw_info']['user']['account_id'];
		if (is_array($memberships))
		{
			foreach($memberships as $group)
			{
				$sql .= ','.$group['account_id'];
			}
		}
		$sql .= ')';
		$this->db->query($sql,__LINE__,__FILE__);
		$permission = 0;
		while ($this->db->next_record())
		{
			$permission = $permission | $this->db->f('acl_rights');
		}
		return $permission;
	}

	function get_rights($account_id, $location)
	{
		$sql = 'select acl_rights from phpgw_acl where acl_appname=\'sitemgr\' and acl_location=\''.$location.'\' and acl_account=\''.$account_id.'\'';
		$this->db->query($sql,__LINE__,__FILE__);
		if ($this->db->next_record())
		{
			return $this->db->f('acl_rights');
		}
		else
		{
			return 0;
		}
	}

	function copy_rights($fromlocation,$tolocation)
	{
		$sql = 'select acl_account,acl_rights from phpgw_acl where acl_appname=\'sitemgr\' and acl_location=\''.$fromlocation.'\'';
		$this->db->query($sql,__LINE__,__FILE__);
		while ($this->db->next_record())
		{
			$this->acl->add_repository('sitemgr',$tolocation,$this->db->f('acl_account'),$this->db->f('acl_rights'));
		}
	}

	function remove_location($location)
	{
		$sql = 'delete from phpgw_acl where acl_appname=\'sitemgr\' and acl_location=\''.
			$location.'\'';
		$this->db->query($sql,__LINE__,__FILE__);
	}
}
?>
