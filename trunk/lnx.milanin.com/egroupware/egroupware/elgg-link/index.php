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
	// $parentdir = 'C:\Programmi\apache\Apache2\htdocs\egroupware';



	
	if (file_exists($parentdir.'/header.inc.php'))
	{
		include($parentdir.'/header.inc.php');
		//echo parse_navbar();

		$GLOBALS['phpgw']->common->phpgw_header();
		
		echo parse_navbar();

$offset_page=$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];

$start_page=0;
if ($_GET['start_from'] != null) {
$start_page=$_GET['start_from'];
}
$order_by='session_id';
if ($_GET['order_by'] != null) {
$order_by=$_GET['order_by'];
}

$order_type='asc';
if ($order_by == "session_id") {
$order_type='desc';
}

$select_str = "<form name='myform'>Order by: <select name='order_by' onChange='myform.submit();'><option value='session_id' ";
if ($order_by == "session_id") {
$select_str .= "selected='true'";
}
$select_str .= ">Online status</option><option value='account_firstname' ";
if ($order_by == "account_firstname") {
$select_str .= "selected='true'";
}
$select_str .= ">First Name</option><option value='account_lastname' ";
if ($order_by == "account_lastname") {
$select_str .= "selected='true'";
}
$select_str .= ">Last Name</option></select></form>";
		
$members['online']=$GLOBALS['phpgw']->accounts->get_online_list('accounts', $start_page, $order_type, $order_by, '', $offset_page);

$members_reg_count=$GLOBALS['phpgw']->accounts->get_count('accounts');

$members_online_count=$GLOBALS['phpgw']->accounts->get_online_count('accounts');

$guests_online_count=$GLOBALS['phpgw']->accounts->get_guest_count('accounts');

		echo "<table align=\"center\"><tr class=divSideboxEntry><th colspan=6>".lang("Members")." online: ".$members_online_count." <br>".lang("Anonymous")." : ".$guests_online_count."<br>".lang("Registered")." total: ".$members_reg_count."<br></th><th colspan=5 align=right>".$select_str."</th></tr>";
		foreach ($members['online'] as $member){
                    $user_location='http://'.$_SERVER['SERVER_NAME'].'/members/'.$member['account_lid'];
                    $linkedIn_user_location='https://www.linkedin.com/profile?viewProfile=&key='.$member[account_linkedin];
                    $emailuser_location='http://'.$_SERVER['SERVER_NAME'].'/egroupware/index.php?menuaction=email.uicompose.compose&fldball[folder]=INBOX&fldball[acctnum]=0&sort=1&order=1&start=0';
                    $pmuser_location='http://'.$_SERVER['SERVER_NAME'].'/egroupware/fudforum/3814588639/index.php?t=ppost&';
                    $user_status="";
                    echo "<tr class=divSideboxEntry>";
                    if ($member['account_pwd'] != null)
                    $user_status="<b>";
 
                    echo "<td>\n";
                    echo ($member['account_pwd'] != null) ? "<img src='/egroupware/elgg-link/templates/default/images/online.gif'>": "<img src='/egroupware/elgg-link/templates/default/images/offline.gif'>";
                    echo "</td>\n";

                    echo "<td>&nbsp;</td>\n";

                    echo "<td>"
                    echo "<i>" if $member['account_status'] == 'A';
                    echo $user_status.($member['account_firstname'])." ".($member['account_lastname']);
                    echo echo "</i>" if $member['account_status'] == 'A';
                    echo "</td>\n";
                    if $member['account_status'] != 'A'{
                      echo "<td>&nbsp;</td>\n";
                      
                      echo "<td><a href=".($user_location)." title='view profile: ".($member['account_lid'])."' target=_blank>";
                      echo "<img src='/egroupware/fudforum/3814588639/theme/default/images/msg_about.gif'>";
                      echo "</a></td>\n";
                      
                      echo "<td>&nbsp;</td>\n";
  
                      echo "<td><a href=".($linkedIn_user_location)." title='view LinkedIn profile: ".($member['account_lid'])."' target=_blank>";
                      echo "<img src='/egroupware/fudforum/3814588639/theme/default/images/linkedin_logo.gif'>";
                      echo "</a></td>\n";
                      
                      echo "<td>&nbsp;</td>\n";
                      
                      echo "<td><a href=".($emailuser_location)." title='send e-mail to: ".($member['account_lid'])."' target=_blank>";
                      echo "<img src='/egroupware/fudforum/3814588639/theme/default/images/msg_email.gif'>";
                      echo "</a></td>\n";
                      
                      echo "<td>&nbsp;</td>\n";
                      
                      echo "<td><a href=".($pmuser_location)." title='send private message to: ".($member['account_lid'])."' target=_blank>";
                      echo "<img src='/egroupware/fudforum/3814588639/theme/default/images/msg_pm.gif'>";
                      echo "</a></td>\n";
                    }else{
                      echo '<td colspan="8" align="right">'.lang("inactive")."</td>\n"
                    }
                    echo "</tr>";
                }
                echo "</table>";

$pages_count = round($members_reg_count/$offset_page);  
if (($pages_count*$offset_page) < $members_reg_count){    
$pages_count = $pages_count +1;          
}
echo "<p><table><tr class=divSideboxEntry><th colspan=11>Pages: ".$pages_count."</th></tr>\n";
echo "<tr class=divSideboxEntry colspan=".$pages_count.">";

for($x = 0;$x < $pages_count;$x++)
 {
 $next_page = ($offset_page * $x);
 echo "<td><a href=index.php?start_from=".$next_page."&order_by=".$order_by." title='go to page ".($x + 1)."'>".($x + 1)."</a></td>";
 }
echo "</tr>";       
echo "</table>";                       
               
		$GLOBALS['phpgw']->common->phpgw_footer();

	//	 	$location=$_SERVER['SERVER_NAME'].'/members/'.$GLOBALS['phpgw_info']['user']['account_lid'];
	//header("Location: http://$location");
		
	}
	else
	{
		//include('C:\Programmi\apache\Apache2\htdocs\egroupware'.'\header.inc.php');
		die("You need to make sure the elgg-link app is in the eGroupWare directory.");
	}

	
?>
