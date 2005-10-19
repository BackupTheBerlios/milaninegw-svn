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

	/* $Id: index.php,v 1.10.2.1 2004/08/27 18:24:41 ralfbecker Exp $ */

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp' => 'elgg-link',
		'noheader'   => True,
		'nonavbar'   => True,
		'noapi'      => False
	);
	$parentdir = dirname(dirname($_SERVER['SCRIPT_FILENAME']));
	if (file_exists($parentdir.'/header.inc.php'))
	{
		include($parentdir.'/header.inc.php');
		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();
		$members['online']=Array();
		
                
		foreach ($GLOBALS['phpgw']->session->list_sessions(0,'','session_logintime') as $member){
                  if (!in_array($member['session_lid'],$members['online'])) $members['online'][]=$member['session_lid'];
                  //echo print_r($members['online'],1);
                }
                
		echo "<table><tr class=divSideboxEntry><th colspan=3>".lang("Members")." ".lang("Registered")."</th></tr>\n";
		$members['registered']=$GLOBALS['phpgw']->accounts->get_list('accounts');
		foreach ($members['registered'] as $member){
                  if ($member['account_status'] == "A") {
                    echo "<tr class=divSideboxEntry><td><pre>".print_r($member,1)."</pre></td><td>Link to profile</td><td>";
                    echo (in_array($member['account_lid'],$members['online'])) ? lang("Online") : lang("Offline");
                    echo "</td></tr>";
                  }
                }
                echo "</table>";
		$GLOBALS['phpgw']->common->phpgw_footer();
	}
	else
	{
		die("You need to make sure the elgg-link app is in the eGroupWare directory.");
	}
	$location=$_SERVER['SERVER_NAME'].'/members/'.$GLOBALS['phpgw_info']['user']['account_lid'];
	//header("Location: http://$location");
	
?>
