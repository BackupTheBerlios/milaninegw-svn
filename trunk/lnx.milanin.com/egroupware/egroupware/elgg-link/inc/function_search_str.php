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
			
$search_str = "<table align=\"center\"><tr class=divSideboxEntry  colspan=".(sizeOf($aar) + 4).">";
$search_str .= "<td align='right'><a href=\"javascript:doQuery('', 0);\"><img src='/egroupware/phpgwapi/templates/idots/images/first-grey.png' title='".lang("elg_first")."' border='0' hspace='2' /></a></td><td align='right'><a href=\"javascript:doQuery('', ".(($prev_page -1) * $offset_page).");\"><img src='/egroupware/phpgwapi/templates/idots/images/left-grey.png' border='0' title='".lang("elg_previous")."' hspace='2' /></a></td>";

			foreach($aar as $char)
			{
        $search_str .= "<td class='letter_box'>";
				if($char == lang("elg_all"))
				{
					$search_str .= "<a href=\"javascript:doQuery('');\">";;
					$search_str .= $char;
					$search_str .= "</a>";
				}
				else
				{
					$search_str .= "<a href=\"javascript:doQuery('".$char."');\">";
					$search_str .= $char;
					$search_str .= "</a>";
				}
			$search_str .= "</td>";	
			}
			unset($aar);
			unset($char);
$search_str .= "<td align='right'><a href=\"javascript:doQuery('', ".(($next_page -1) * $offset_page).");\"><img src='/egroupware/phpgwapi/templates/idots/images/right-grey.png' border='0' title='".lang("elg_next")."' hspace='2' /></a></td><td align='right'><a href=\"javascript:doQuery('', ".(($pages_count -1) * $offset_page).");\"><img src='/egroupware/phpgwapi/templates/idots/images/last-grey.png' border='0' title='".lang("elg_last")."' hspace='2' /></a></td>";			
$search_str .= "</tr></table>";

return $search_str;
}
function input_text_search_str($query)
{
$search_str = "<table align=\"center\"><tr colspan=".(sizeOf($aar) - 3)."><td><input type='text' name='query' value='".$query."'></td><td><select name='query_type'><option value='all'>".lang("elg_all")."</option><option value='firstname'>".lang("elg_first_name")."</option><option value='lastname'>".lang("elg_last_name")."</option></select></td><td><input type='submit' name='Search' value='".lang("elg_search")."' onSubmit=\"javascript:doSubmit();\"></td>";
$search_str .= "</tr></table>";
return $search_str;
}

function table_header_search_str($members_online_count, $guests_online_count, $members_reg_count)
{
$search_str = "<tr class=divSideboxHeader><td align=\"left\" colspan=3>".lang("elg_members")." online: ".$members_online_count." <br>".lang("elg_anonymous")." : ".$guests_online_count."<br>".lang("elg_registered")." total: ".$members_reg_count."<br></td><td align=\"left\" colspan=6>".table_header_select_str()."</td></tr>";  
return $search_str;
}

function table_header_select_str()
{

$select_str .= "<table align=\"left\" ><tr ><td>".lang("elg_order_by").": </td><td colspan=4><select name='order_by' onChange='userSearchform.submit();'><option value='session_id' ";

$select_str .= ">".lang("elg_online_status")."</option><option value='account_firstname' ";

$select_str .= ">".lang("elg_first_name")."</option><option value='account_lastname' ";

$select_str .= ">".lang("elg_last_name")."</option></select> <input type=submit value='".lang("elg_go")."' title=".lang("elg_go")."></td></tr>";

$select_str .= "<tr><td>".lang("elg_registration_status")."</td><td><INPUT TYPE=\"radio\" NAME=\"regstatus\" VALUE=\"accounts_a\" ";


$select_str .= ">".lang("elg_active")."</td><td><INPUT TYPE=\"radio\" NAME=\"regstatus\" VALUE=\"accounts_p\"";


$select_str .= ">".lang("elg_inactive")."</td><td><INPUT TYPE=\"radio\" NAME=\"regstatus\" VALUE=\"accounts\" ";


$select_str .= ">".lang("elg_all")."</td></tr></table>";
  
  
return $select_str;
}

function table_header_result_count_str($sizeOf, $query, $query_result_count)
{ 
if ($query !="") { 
if ($sizeOf < 1){
$res_str = "<tr colspan=5 align=center>".lang("elg_returned_no_result").".</tr>"; 

} else {
    //show result count message if any search 
$res_str = "<tr align=\"center\" class=divSideboxHeader colspan=9>".lang("elg_returned_result")." ".$query_result_count." ".lang("elg_result").". </tr>";
}
}  
  return $res_str;
}

function table_result_str($members)
{
  foreach ($members as $member)
{
                    $user_location='http://'.$_SERVER['SERVER_NAME'].'/members/'.$member['account_lid'];
                    $linkedIn_user_location='https://www.linkedin.com/profile?viewProfile=&key='.$member[account_linkedin];
                                       $pmuser_location='http://'.$_SERVER['SERVER_NAME'].'/egroupware/index.php?menuaction=messenger.uimessenger.compose&message_to='.$member['account_lid'].'&';
                    $user_status="";
                    $res_str .= "<tr class=divSideboxEntry>";
                    if ($member['account_pwd'] != null)
                    $user_status="<b>";
 
                    $res_str .= "<td>\n";
                    $res_str .= ($member['account_pwd'] != null) ? "<img src='/egroupware/elgg-link/templates/default/images/online.gif'>": "<img src='/egroupware/elgg-link/templates/default/images/offline.gif'>";
                    $res_str .= "</td>\n";

                    $res_str .= "<td>&nbsp;</td>\n";

                    $res_str .= "<td>";
                    if ($member['account_status'] != 'A'){ echo "<i>"; };
                    $res_str .= $user_status.($member['account_firstname'])." ".($member['account_lastname']);
                    if ($member['account_status'] != 'A') {echo "</i>";};
                    $res_str .= "</td>\n";
                    if ($member['account_status'] == 'A'){
                      $res_str .= "<td>&nbsp;</td>\n";
                      
                      $res_str .= "<td><a href=".($user_location)." title='".lang("elg_view_profile").": ".($member['account_lid'])."' target=_blank>";
                      $res_str .= "<img src='/egroupware/fudforum/3814588639/theme/default/images/msg_about.gif'>";
                      $res_str .= "</a></td>\n";
                      
                      $res_str .= "<td>&nbsp;</td>\n";
  
                      $res_str .= "<td><a href=".($linkedIn_user_location)." title='".lang("elg_view_linkedIn_profile").": ".($member['account_lid'])."' target=_blank>";
                      $res_str .= "<img src='/egroupware/elgg-link/templates/default/images/linkedin_logo.gif'>";
                      $res_str .= "</a></td>\n";
                      
                      $res_str .= "<td>&nbsp;</td>\n";
                                            
                      $res_str .= "<td><a href=".($pmuser_location)." title='".lang("elg_send_private_message_to").": ".($member['account_lid'])."' target=_blank>";
                      $res_str .= "<img src='/egroupware/fudforum/3814588639/theme/default/images/msg_pm.gif'>";
                      $res_str .= "</a></td>\n";
                    }else{
                      $res_str .= '<td colspan="8" align="right">'.lang("elg_inactive")."</td>\n";
                    }
                    $res_str .= "</tr>";
                }  
  
  return $res_str;
}

function pages_str($pages_count, $offset_page, $start_page, $current_page)
{
$res_str .= "<table align=\"center\"><th class=divSideboxHeader colspan=11>".lang("elg_total_pages").": ".$pages_count."<br>".lang("elg_current_page").":".$current_page." </th>";
$res_str .= "<tr class=divSideboxEntry colspan=".$pages_count.">";

for($x = 0;$x < $pages_count;$x++)
 {
 $next_page = ($offset_page * $x);
 if ($next_page == $start_page){
 $res_str .= "<td>".($x + 1)."</td>";
 }
 else 
 $res_str .= "<td><a href=\"javascript:doQuery('', ".$next_page.");\" title='".lang("elg_go_to_page")." ".($x + 1)."'>".($x + 1)."</a></td>";

 }
$res_str .= "</tr></table>"; 

    return $res_str;
}

?>