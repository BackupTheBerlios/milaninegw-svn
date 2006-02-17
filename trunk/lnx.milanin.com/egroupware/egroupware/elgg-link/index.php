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


		include($parentdir.'/header.inc.php');
		//echo parse_navbar();

		$GLOBALS['phpgw']->common->phpgw_header();
		
		echo parse_navbar();

		
include('function_javascript.php');

include('function_search_str.php');


	// You must be logged on to view this!
		if (logged_on) {
$start_from=0;
if ($_REQUEST['start_from'] != null) {
$start_from=$_REQUEST['start_from'];
}

$current_page=0;
$order_by='session_id';
if ($_REQUEST['order_by'] != null) {
$order_by=$_REQUEST['order_by'];
}

$order_type='asc';
if ($order_by == "session_id") {
$order_type='desc';
}

$query_type='';
if ($_REQUEST['query_type'] != null) {
$query_type=$_REQUEST['query_type'];
}
$query='';
if ($_REQUEST['query'] != null) {
$query=$_REQUEST['query'];
}
$regstatus='accounts_a';
if ($_REQUEST['regstatus'] != null) {
$regstatus=$_REQUEST['regstatus'];
}
//do calc
//$offset_page=$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
$offset_page=4;


$members['online']=$GLOBALS['phpgw']->accounts->get_online_list($regstatus, $start_from, $order_type, $order_by, $query, $offset_page, $query_type);

$query_result_count=$GLOBALS['phpgw']->accounts->get_count($regstatus, $start_from, $order_type, $order_by, $query, $offset_page, $query_type);

$members_reg_count=$GLOBALS['phpgw']->accounts->get_count('accounts');

$members_online_count=$GLOBALS['phpgw']->accounts->get_online_count('accounts', $start_from, $order_type, $order_by, $query, $offset_page, $query_type);

$guests_online_count=$GLOBALS['phpgw']->accounts->get_guest_count('accounts');

$pages_count = round($query_result_count/$offset_page);  
if (($pages_count*$offset_page) < $query_result_count){    
$pages_count = $pages_count +1;          
}

for($x = 0;$x < $pages_count;$x++){
 if (($offset_page * $x) == $start_from)
 $current_page = ($x + 1);
}
$prev_page=1;
if ($current_page > 1)
$prev_page = $current_page -1;

$next_page=$pages_count;
if (($current_page +1) < $pages_count)
$next_page = $current_page +1;
		
$body .= <<< END
<form name='userSearchform'  action='' method='post' onsubmit="return doSubmit()">

END;
    $body .= input_text_search_str($query);
    $body .= letters_search_str($offset_page, $prev_page, $next_page, $pages_count);
		$body .= <<< END
 <input type="hidden" name="start_from" value="$start_from">

<table align="center">
END;

$body .= table_header_result_count_str(sizeOf($members['online']), $query_result_count);
$body .= table_header_search_str($members_online_count, $guests_online_count, $members_reg_count);
if (sizeOf($members['online']) > 0)
$body .= table_result_str($members['online']);


$body .= <<< END
</table>
</form>		
END;
$body .= pages_str($pages_count, $offset_page, $start_from, $current_page);

$body .= <<< END
 <script language="JavaScript" type="text/javascript">
<!--
setValues("$query_type", "$order_by", "$regstatus");
-->
</script>		
END;

		echo $javascript;
		echo $body;

				} else {
					header("Location: " . url);
				}

		$GLOBALS['phpgw']->common->phpgw_footer();

	//	 	$location=$_SERVER['SERVER_NAME'].'/members/'.$GLOBALS['phpgw_info']['user']['account_lid'];
	//header("Location: http://$location");
		

	
?>
