<?php
function letters_search_str($offset_page, $prev_page, $next_page, $pages_count)
{
	/* Setup query for 1st char of fullname, company, lastname using user lang */
	$chars = lang('alphabet');
	if($chars == 'alphabet*')
	{
		$chars = 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z';
	}
	$aar = explode(',', $chars);
	unset($chars);
	$aar[] = lang("elg_all");
	$search_str = '<input type="hidden" name="wordChar" id="wordChar" value="'.$_POST["wordChar"].'">';
	$search_str .= "<table align=\"center\"><tr class=divSideboxEntry  colspan=".(sizeOf($aar) + 4).">";

	foreach($aar as $char)
	{
      	$search_str .= "<td class='letter_box'>";
		if($char == lang("elg_all"))
		{
			$isThat = ($_POST["wordChar"] === "" || !isset($_POST["wordChar"]));
			$search_str .= "<a href=\"javascript:doABCQuery('');\">";
			$search_str .= sprintf("%s%s%s", $isThat ? "<span class='selectedLetter'>" : "", $char, $isThat ? "</span>" : "");
			$search_str .= "</a>";
		}
		else
		{
			$isThat = $_POST["wordChar"] === $char;
			$search_str .= "<a href=\"javascript:doABCQuery('".$char."');\">";
			$search_str .= sprintf("%s%s%s", $isThat ? "<span class='selectedLetter'>" : "", $char, $isThat ? "</span>" : "");
			$search_str .= "</a>";
		}
	$search_str .= "</td>";	
	}
	unset($aar);
	unset($char);
	$search_str .= "</tr></table>";
	return $search_str;
}

function input_text_search_str($query)
{
$search_str = "<table align=\"center\" border='0' width='520'><tr><td width='400'>
		<input type='text' name='query' value='".$query."' style='width:100%'></td>
		<td>&nbsp;<select name='query_type' style='width:99px;'><option value='all'>"
		.lang("elg_all")."</option>
		<option value='firstname'>"
		.lang("elg_first_name")."</option>
		<option value='lastname'>".lang("elg_last_name").
		"</option></select></td>";
$search_str .= "</tr></table>\n";
$search_str .= DisplayExtendedSearchField();
return $search_str;
}

function RenderSearchDropDownList($id, $arr, $useKey = true, $includeAll=true)
{
	$str = sprintf('<select name="%s" id="%s" style="width:200px;">', $id, $id);
	if($includeAll)
		$str .= '<option value="">'.lang("elg_all").'</option>';
	for($i=0;$i<count($arr);$i++)
	{
		$value = ($useKey ? $i : $arr[$i]);
		$str .= sprintf('<option value="%s"%s>%s</option>', $value, ($_POST[$id] === $value."" ? " selected" : ""), $arr[$i]);
	}
	$str .= "</select>";
	return $str;
}

function DisplayExtendedSearchField()
{
	$str = "";
	
	$str .='
	<table align="center" border="0" width="520">
		<tr>
			<td>'.lang("Industry").'</td>
			<td>'.RenderSearchDropDownList("industries", $GLOBALS['phpgw']->accounts->formCfg[lists][industries][source]).'</td>
			<td>'.lang("Country of residence").'</td>
			<td>'.RenderSearchDropDownList("residence_country", $GLOBALS['phpgw']->accounts->formCfg[lists][residence_country][source], false).'</td>
		</tr>
		<tr>
			<td>'.lang("Professional Status").'</td>
			<td>'.RenderSearchDropDownList("prof_profile", $GLOBALS['phpgw']->accounts->formCfg[lists][prof_profile][source]).'</td>
			<td>'.lang("Occupation area").'</td>
			<td>'.RenderSearchDropDownList("occ_areas", $GLOBALS['phpgw']->accounts->formCfg[lists][occ_areas][source]).'</td>
		</tr>
		
	</table>';
	return $str;
}

function table_header_search_str($members_online_count, $guests_online_count, $members_reg_count)
{
$search_str = "<tr class=divSideboxHeader><td align=\"left\" colspan=\"2\">".
		lang("elg_members")." online: ".$members_online_count." <br/>".
		lang("elg_anonymous")." : ".$guests_online_count."<br/>".
		lang("elg_registered")." total: ".$members_reg_count.
		"<br/></td><td align=\"left\" colspan=\"3\">".
		table_header_select_str()."</td><td valign='bottom' style='padding-bottom:4px;'><input type='submit' name='Search' value='".lang("elg_search")."' onSubmit=\"javascript:doSubmit();\"></td></tr>\n";
return $search_str;
}
/*$search_str .= "</tr> ";*/
function table_header_select_str()
{

$select_str .= "<table align=\"left\" ><tr ><td>".lang("elg_order_by").": </td><td colspan=\"4\"><select name='order_by'><option value='session_id' ";

$select_str .= ">".lang("elg_online_status")."</option><option value='account_firstname' ";

$select_str .= ">".lang("elg_first_name")."</option><option value='account_lastname' ";

$select_str .= ">".lang("elg_last_name")."</option></select></td></tr>";

$select_str .= "<tr><td>".lang("elg_registration_status")."</td><td><INPUT TYPE=\"radio\" NAME=\"regstatus\" VALUE=\"accounts_a\" ";


$select_str .= ">".lang("elg_active")."</td><td><INPUT TYPE=\"radio\" NAME=\"regstatus\" VALUE=\"accounts_p\"";


$select_str .= ">".lang("elg_inactive")."</td><td><INPUT TYPE=\"radio\" NAME=\"regstatus\" VALUE=\"accounts\" ";


$select_str .= ">".lang("elg_all")."</td></tr>";
$select_str .= "</table>\n";
  
  
return $select_str;
}

function table_header_result_count_str($sizeOf, $query, $query_result_count)
{ 
if ($query !="") { 
if ($sizeOf < 1){
$res_str = "<tr align=center><td colspan=\"5\" ><b>".lang("elg_returned_no_result").".</b></td></tr>"; 

} else {
    //show result count message if any search 
$res_str = "<tr align=\"center\" class=divSideboxHeader ><td  colspan=\"5\"><b>".lang("elg_returned_result")." ".$query_result_count." ".lang("elg_result").".</b></td> </tr>\n";
}
}  
  return $res_str;
}

function get_member_info($db, $account_lid)
{
	$arr = array();
	$sql = sprintf("select * from members_profile_data where (access='PUBLIC' or access='LOGGED_IN') and owner in (select ident from members_users where username = '%s')", $account_lid);
	$db->query($sql, __LINE__ ,__FILE__);
	if($db->num_rows())
		while($db->next_record())
		{
			$arr[ $db->f('name') ] = $db->f('value');
		}
	
	return $arr;
}

function GetValueFromElggList($id, $values)
{
	if(!$GLOBALS['phpgw']->accounts->formCfg[lists][$id][source][$values])
		return "-";
	
	return $GLOBALS['phpgw']->accounts->formCfg[lists][$id][source][$values];
}

function get_view_layout($member, $userInfo)
{
	$result = sprintf('<div class="divContainer" onmouseout="HideInfo(%d)"><div class="divInfo" ID="divInfo%d" onmouseover="ShowInfo(%d)">',$member['account_id'], $member['account_id'], $member['account_id']);
	$serverURL = ( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') ? "https" : "http" ) . '://'.$_SERVER['SERVER_NAME'];
	
	//link to profile.
	$user_location = $serverURL.'/members/'.$member['account_lid'];
	$user_location = sprintf('<a href="%s" title="%s" target=_blank><img src="/egroupware/fudforum/3814588639/theme/default/images/msg_about.gif"></a>', $user_location, lang("elg_view_profile").": ".$member['account_lid']);

	$account_linkedin = str_is_int($member[account_linkedin]) ? setInteger($member[account_linkedin]) : 1;
	if($account_linkedin < 1000)
	{
		$account_linkedin = '<a href="javascript:WarningMessage(\''.lang("WarningEmtpyLinkedInProfile").'\');void(0);" title="'.lang("WarningEmtpyLinkedInProfile").'"><img src="/egroupware/elgg-link/templates/default/images/linkedin_logo_invalid.gif"></a>';
	}
	else
	{
		$linkedIn_user_location = 'https://www.linkedin.com/profile?viewProfile=&key='.$member[account_linkedin];
		$account_linkedin = sprintf('<a href="%s" target="top" title="%s"><img src="/egroupware/elgg-link/templates/default/images/linkedin_logo.gif"></a>', 
									$linkedIn_user_location, lang("elg_view_linkedIn_profile").": ".$member['account_lid']);
	}
	
	$pmuser_location = $serverURL.'/egroupware/index.php?menuaction=messenger.uimessenger.compose&message_to='.$member['account_lid'].'&';
	$pmuserStr = sprintf('<a href="%s" title="%s" target=_blank><img src="/egroupware/fudforum/3814588639/theme/default/images/msg_pm.gif"></a>',
						$pmuser_location, lang("elg_send_private_message_to").": ".$member['account_lid']);

	$result .= sprintf('<table border="0" width="100%%" cellspacing="3" cellpadding="0" onmouseout="HideInfo(%d)">
	<tr>
		<td>%s</td>
		<td>%s</td>
	</tr>
	<tr>
		<td>%s</td>
		<td>%s</td>
	</tr>
	<tr>
		<td>%s</td>
		<td>%s</td>
	</tr>
	</table>', $member['account_id'], 
		lang("Profile URL"), $user_location, 
		lang("linkedin URL"), $account_linkedin, 
		lang("Instant MSG"), $pmuserStr);

	$result .='</div></div>';
	return $result;
}

function table_result_str($members, $offset_page, $prev_page, $next_page, $pages_count, $current_page)
{
	$img = array("&nbsp;", "&nbsp;", "&nbsp;", "&nbsp;");
	if($current_page != 1)
	{
		$img[0] = "<a href=\"javascript:doQuery('', 0);\"><img src='/egroupware/phpgwapi/templates/idots/images/first-grey.png' title='".lang("elg_first")."' border='0' hspace='2' /></a>";
		$img[1] = "<a href=\"javascript:doQuery('', ".(($prev_page -1) * $offset_page).");\"><img src='/egroupware/phpgwapi/templates/idots/images/left-grey.png' border='0' title='".lang("elg_previous")."' hspace='2' /></a>";
	}
	if($pages_count > 1 && $current_page != $pages_count)
	{
		$img[2] = "<a href=\"javascript:doQuery('', ".(($next_page -1) * $offset_page).");\"><img src='/egroupware/phpgwapi/templates/idots/images/right-grey.png' border='0' title='".lang("elg_next")."' hspace='2' /></a>";
		$img[3] = "<a href=\"javascript:doQuery('', ".(($pages_count -1) * $offset_page).");\"><img src='/egroupware/phpgwapi/templates/idots/images/last-grey.png' border='0' title='".lang("elg_last")."' hspace='2' /></a>";
	}

	$res_str = '<table align="center" border="0" cellspacing="1" class="tableLayout">';
	$res_str .= sprintf('<tr><td colspan="8">
		<table width="100%%" border="0" cellspacing="1" cellpadding="0">
			<tr>
				<td width="15">%s</td>
				<td width="15">%s</td>
				<td width="90%%">%s</td>
				<td width="15">%s</td>
				<td width="15">%s</td>
			</tr>
		</table>
	</td></tr>', $img[0], $img[1], "&nbsp;", $img[2], $img[3]);
	
	$res_str .= sprintf('<tr class="tableHeader">
							<td>%s</td>
							<td>%s</td>
							<td>%s</td>
							<td>%s</td>
							<td>%s</td>
							<td>%s</td>
							<td>%s</td>
							<td>&nbsp;</td>
						</tr>', lang("Status"), lang("Name/Surname"), lang("Member since (date)"), lang("Professional Status"), lang("Industry"), lang("Occupation area"), lang("City of Residence"));

	$db = $GLOBALS['phpgw']->db;
	$counter = 0;
	foreach ($members as $member)
	{
		$userInfo = get_member_info($db, $member[account_lid]);
		$sufix = ($userInfo[gender]."" == "" || $userInfo[gender]."" == "0") ? "m" : "w";
		$imgStatus = '<img border="0" width="16" height="16" alt="'.($member[account_status] == "A" ? lang("Active") : lang("Disabled") ).'" src="/members/_templates/default/'.($member[account_status] == "A" ? "user-".$sufix."-active.png" : "user-".$sufix."-disabled.png").'"/>';
		$isOnline = ($member['account_pwd'] != null);
		if ($member['account_status'] == 'A')
		{
			$res_str .= sprintf('<tr class="%s"><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s<a href="javascript:ShowInfo(%d);void(0);">%s</a></td></tr>',
					(( $counter++ % 2 == 0 ) ? "altRow" : "Row"), 
					$imgStatus, ($isOnline ? "<b>" : "").$member['account_firstname']." ".$member['account_lastname'].($isOnline ? "</b>" : ""),
					$member['account_membership_date'], GetValueFromElggList("prof_profile", $userInfo["prof_profile"]),
					GetValueFromElggList("industries", $userInfo["industries"]), 
					GetValueFromElggList("occ_areas", $userInfo["occ_areas"]), ($userInfo[residence_city] ? $userInfo[residence_city] : "-"),
					get_view_layout($member, $userInfo), $member['account_id'], lang("Details"));
		}
		else
		{
			$res_str .= sprintf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td><font color="red">%s</font></td></tr>',
					$imgStatus, ($isOnline ? "<b>" : "").$member['account_firstname']." ".$member['account_lastname'].($isOnline ? "</b>" : ""),
					$member['account_membership_date'], GetValueFromElggList("prof_profile", $userInfo["prof_profile"]),
					GetValueFromElggList("industries", $userInfo["industries"]), 
					GetValueFromElggList("occ_areas", $userInfo["occ_areas"]), ($userInfo[residence_city] ? $userInfo[residence_city] : "-"),
					lang("elg_inactive"));
		}
	}
  $res_str .= '</table>';
  return $res_str;
}

function total_information($totalUser, $totalOnlyActive, $totalSelected)
{
	$res_str = sprintf('<table align="center" border="0" cellspacing="1" class="tableLayout"><tr class="tableHeader"><td><b>%s</b></td><td><b>%s</b></td><td><b>%s</b></td></tr>', 
					lang("Total users"), lang("Total ACTIVE users"), lang("Total SELECTED users"));
	$res_str .= sprintf('<tr><td align="center"><b>%s</b></td><td align="center"><b>%s</b></td><td align="center"><b>%s</b></td></tr>', $totalUser, $totalOnlyActive, $totalSelected);
	$res_str .= "</table>";
	return $res_str;
}

function pages_str($pages_count, $offset_page, $start_page, $current_page)
{
$res_str .= "<table align=\"center\"><tr><th class=\"divSideboxHeader\">".lang("elg_total_pages").": ".$pages_count."<br/>".lang("elg_current_page").":".$current_page." </th></tr>\n";
$res_str .= "<tr class=divSideboxEntry><td><table><tr>\n";
$c = 0;
for($x = 0;$x < $pages_count;$x++)
 {
 	if($c++ == 25)
		{	$res_str .= "\t<td>&nbsp;</td></tr><tr>\n"; $c=1;}
	
	$next_page = ($offset_page * $x);
	if ($next_page == $start_page)
	{
		$res_str .= "\t<td>".($x + 1)."</td>\n";
	}
	else 
	 $res_str .= "\t<td><a href=\"javascript:doQuery('', ".$next_page.");\" title='".lang("elg_go_to_page")." ".($x + 1)."'>".($x + 1)."</a></td>\n";
 }
$res_str .= "</tr></table></tr></table>\n"; 

    return $res_str;
}

function str_is_int($str) {
	$var=intval($str);
	return ($str==$var."");
}
function setInteger($value)
{
	settype($value, "integer");
	return $value;
}
function IsValidDate($date) #return '' if not mail
{
	if(trim($date) == "") return true;
	$date = split("/", $date);
	while( count($date) < 3 )
		array_push($date, "0");
	if( !(is_numeric($date[0]) && is_numeric($date[1]) && is_numeric($date[2])) )
		return false;

	$date = array_map("setInteger", $date);
	return checkdate($date[1], $date[0], $date[2]);
}
?>
