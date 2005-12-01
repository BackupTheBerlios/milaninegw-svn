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
                
                $return['total']=$GLOBALS['phpgw']->accounts->get_count('accounts');
                
                $return['online']= $GLOBALS['phpgw']->accounts->get_online_count('accounts');
		$drop='<div id="OnlinersList">
				<table cellspacing="0" cellpadding="0" border="0" width="100%">
                                 ';
                $order_by='session_id';
		$online=$GLOBALS['phpgw']->accounts->get_online_list('accounts', $start_page, $order_type, $order_by, '', $offset_page);
		foreach ($online as $onliner){
                  if (($onliner['account_pwd']>0)){
                    $drop.="<tr><td><a href=\"/members/".$onliner['account_lid']."\">".
                    $onliner['account_firstname']." ".$onliner['account_lastname'].
                    "</a></td></tr>\n";
                  }
                }
                $drop.="</table>
				</div>";
		
                return "<table class=\"moduletable\"><!--tr><th colspan=\"2\">".lang("Members")."</th></tr--><tr>\n<td>".lang("Registered").
                        "</td><td>".$return['total']."</td></tr>\n".
                        "<tr><td>".
                        "<a href='#' title='Show List' onClick=\"toggleLayer('OnlinersList')\">".lang("Online")."</a></td>".
                        "<td>".$return['online']."</td></tr><tr><th colspan=\"2\">".
                        $drop."</th></tr>\n</table>";
	}

}
