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
$current_page=0;
$order_by='session_id';
if ($_GET['order_by'] != null) {
$order_by=$_GET['order_by'];
}

$order_type='asc';
if ($order_by == "session_id") {
$order_type='desc';
}

$query_type='';
if ($_GET['query_type'] != null) {
$query_type=$_GET['query_type'];
}


$query='';
if ($_GET['query'] != null) {
$query=$_GET['query'];
}


$members['online']=$GLOBALS['phpgw']->accounts->get_online_list('accounts', $start_page, $order_type, $order_by, $query, $offset_page, $query_type);

$members_reg_count=$GLOBALS['phpgw']->accounts->get_count('accounts', $start_page, $order_type, $order_by, $query, $offset_page, $query_type);

$members_reg_count1=$GLOBALS['phpgw']->accounts->get_count('accounts');

$members_online_count=$GLOBALS['phpgw']->accounts->get_online_count('accounts', $start_page, $order_type, $order_by, $query, $offset_page, $query_type);

$guests_online_count=$GLOBALS['phpgw']->accounts->get_guest_count('accounts');

$pages_count = round($members_reg_count/$offset_page);  
if (($pages_count*$offset_page) < $members_reg_count){    
$pages_count = $pages_count +1;          
}

for($x = 0;$x < $pages_count;$x++){
 if (($offset_page * $x) == $start_page)
 $current_page = ($x + 1);
}
$prev_page=1;
if ($current_page > 1)
$prev_page = $current_page -1;

$next_page=$pages_count;
if (($current_page +1) < $pages_count)
$next_page = $current_page +1;


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
$select_str .= ">Last Name</option></select> <input type=submit value='Go' title=Go></form>";



//search box
$search_str1 = "<form name='mySearchform' ><table align=\"center\"><tr colspan=".(sizeOf($aar) - 3)."><td><input type='text' name='query' value=''></td><td><select name='query_type'><option value='all'>All</option><option value='firstname'>Name</option><option value='lastname'>Surname</option></select></td><td><input type='submit' name='Search' value='Search'></td>";
$search_str1 .= "</tr></table></form>";

			/* Setup query for 1st char of fullname, company, lastname using user lang */
			$chars = lang('alphabet');
			if($chars == 'alphabet*')
			{
				$chars = 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z';
			}
			$aar = explode(',', $chars);
			unset($chars);
			$aar[] = 'all';
			
$search_str = "<form name='mySearchform' ><table align=\"center\"><tr class=divSideboxEntry  colspan=".(sizeOf($aar) + 4).">";
$search_str .= "<td align='right'><a href=index.php?start_from=0&order_by=".$order_by."&query=".$query."&query_type=".$query_type." title='go to page 1'><img src='/egroupware/phpgwapi/templates/idots/images/first-grey.png' border='0' title='First' hspace='2' /></a></td><td align='right'><a href=index.php?start_from=".(($prev_page -1) * $offset_page)."&order_by=".$order_by."&query=".$query."&query_type=".$query_type." title='go to page ".($prev_page +1)."'><img src='/egroupware/phpgwapi/templates/idots/images/left-grey.png' border='0' title='Previous' hspace='2' /></a></td>";





			foreach($aar as $char)
			{
        $search_str .= "<td class='letter_box'>";
				if($char == 'all')
				{
					$search_str .= "<a href=index.php?order_by=".$order_by."&query=&query_type=>";
					$search_str .= $char;
					$search_str .= "</a>";
				}
				else
				{
					$search_str .= "<a href=index.php?order_by=".$order_by."&query=".$char."&query_type=lastname>";
					$search_str .= $char;
					$search_str .= "</a>";
				}
			$search_str .= "</td>";	
			}
			unset($aar);
			unset($char);
$search_str .= "<td align='right'><a href=index.php?start_from=".(($next_page -1) * $offset_page)."&order_by=".$order_by."&query=".$query."&query_type=".$query_type." title='go to page ".($next_page +1)."'><img src='/egroupware/phpgwapi/templates/idots/images/right-grey.png' border='0' title='Next' hspace='2' /></a></td><td align='right'><a href=index.php?start_from=".(($pages_count -1) * $offset_page)."&order_by=".$order_by."&query=".$query."&query_type=".$query_type." title='go to page ".($pages_count)."'><img src='/egroupware/phpgwapi/templates/idots/images/last-grey.png' border='0' title='Last' hspace='2' /></a></td>";			
$search_str .= "</tr></table></form>";
		

    echo $search_str1;
    echo $search_str;
		echo "<table align=\"center\"><tr class=divSideboxEntry><th colspan=6>".lang("Members")." online: ".$members_online_count." <br>".lang("Anonymous")." : ".$guests_online_count."<br>".lang("Registered")." total: ".$members_reg_count1."<br></th><th colspan=5 align=right>".$select_str."</th></tr>";

if (sizeOf($members['online']) < 1){
echo "<tr colspan=5 align=center>Your request returned no result.</tr>"; 

} else {
    if ($members_reg_count1 > $members_reg_count){
    echo "<tr colspan=5 align=center>Your request returned ".$members_reg_count." results.</tr>";
     }
		foreach ($members['online'] as $member){
                    $user_location='http://'.$_SERVER['SERVER_NAME'].'/members/'.$member['account_lid'];
                    $linkedIn_user_location='https://www.linkedin.com/profile?viewProfile=&key='.$member[account_linkedin];
$emailuser_location='http://'.$_SERVER['SERVER_NAME'].'email/compose.php?to='.$member[account_email];
                                       $pmuser_location='http://'.$_SERVER['SERVER_NAME'].'/egroupware/fudforum/3814588639/index.php?t=ppost&';
                    $user_status="";
                    echo "<tr class=divSideboxEntry>";
                    if ($member['account_pwd'] != null)
                    $user_status="<b>";
 
                    echo "<td>\n";
                    echo ($member['account_pwd'] != null) ? "<img src='/egroupware/elgg-link/templates/default/images/online.gif'>": "<img src='/egroupware/elgg-link/templates/default/images/offline.gif'>";
                    echo "</td>\n";

                    echo "<td>&nbsp;</td>\n";

                    echo "<td>";
                    if ($member['account_status'] != 'A'){ echo "<i>"; };
                    echo $user_status.($member['account_firstname'])." ".($member['account_lastname']);
                    if ($member['account_status'] != 'A') {echo "</i>";};
                    echo "</td>\n";
                    if ($member['account_status'] == 'A'){
                      echo "<td>&nbsp;</td>\n";
                      
                      echo "<td><a href=".($user_location)." title='view profile: ".($member['account_lid'])."' target=_blank>";
                      echo "<img src='/egroupware/fudforum/3814588639/theme/default/images/msg_about.gif'>";
                      echo "</a></td>\n";
                      
                      echo "<td>&nbsp;</td>\n";
  
                      echo "<td><a href=".($linkedIn_user_location)." title='view LinkedIn profile: ".($member['account_lid'])."' target=_blank>";
                      echo "<img src='/egroupware/elgg-link/templates/default/images/linkedin_logo.gif'>";
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
                      echo '<td colspan="8" align="right">'.lang("inactive")."</td>\n";
                    }
                    echo "</tr>";
                }
                
}                
                echo "</table>";


echo "<table><tr class=divSideboxEntry><th colspan=11>Total pages: ".$pages_count."</th></tr>";
echo "<tr class=divSideboxEntry colspan=".$pages_count.">";

for($x = 0;$x < $pages_count;$x++)
 {
 $next_page = ($offset_page * $x);
 if ($next_page == $start_page){
 echo "<td>".($x + 1)."</td>";
 }
 else 
 echo "<td><a href=index.php?start_from=".$next_page."&order_by=".$order_by."&query=".$query."&query_type=".$query_type." title='go to page ".($x + 1)."'>".($x + 1)."</a></td>";

 }
echo "</tr>"; 
echo "<tr class=divSideboxEntry colspan=".$pages_count.">";  
echo "Current page:".$current_page; 
echo "</tr></table>";                       
               
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
