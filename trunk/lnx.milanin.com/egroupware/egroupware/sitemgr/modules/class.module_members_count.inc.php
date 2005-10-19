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

/*INSERT INTO `phpgw_sitemgr_modules` ( `module_id` , `module_name` , `description` )
VALUES (
'', 'module_members_count', 'Shows counter of the members registered and online '
);*/

	/* $Id: class.module_hello.inc.php,v 1.3 2004/02/10 14:56:33 ralfbecker Exp $ */

class module_members_count extends Module 
{
	function module_members_count()
	{
		$this->arguments = array();
		$this->properties = array();
                $this->title = lang('Members');
		$this->description = lang('This is a members counter module');
	}

	function get_content(&$arguments,$properties) 
	{
                
                $this->db=$GLOBALS['phpgw']->db;
                $this->db->query("SELECT COUNT(*) members FROM `phpgw_accounts` WHERE `account_type` = 'u' and `account_status` = 'A'");
                while($row = $this->db->row(True)){
                  $return['total']=$row['members'];
                }
                
                $return['online']= $GLOBALS['phpgw']->session->list_sessions(0,'','session_logintime');
		
                return "<table class=\"moduletable\"><!--tr><th colspan=\"2\">".lang("Members")."</th></tr--><tr>\n<td>".lang("Registered").
                        "</td><td>".$return['total']."</td></tr>\n".
                        "<tr><td>".lang("Online")."</td><td>".sizeof($return['online'])."</td></tr><tr><th colspan=\"2\"></th></tr>\n</table>";
	}

}
