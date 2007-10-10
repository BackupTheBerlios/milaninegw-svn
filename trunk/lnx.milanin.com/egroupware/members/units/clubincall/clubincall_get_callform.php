<?
require_once("../../includes.php");
require_once('../users/conf.php');
require_once('../users/function_session_start.php');
run("profile:init");
global $profile_id;
global $individual;
if (isset($HTTP_COOKIE_VARS['last_number'])) {
 $last_number=$HTTP_COOKIE_VARS['last_number'];
}
if (isset($HTTP_COOKIE_VARS['last_prefix'])) {
 $last_prefix=$HTTP_COOKIE_VARS['last_prefix'];
}
 
$my_page_owner=$_GET['id'];

global $current_dst;
$current_dst_query="SELECT a.account_firstname as name, c.ident as dstid, IF(n.screened,n.description,n.number) as dstnumber FROM phpgw_accounts a
left join clubincall_dsts c
  on c.owner=a.account_id 
  and ( (weekday( curdate())+1) >= c.wstart and (weekday( curdate())+1) <= c.wend )
  and ( hour(curtime()) >=c.hstart and hour(curtime()) <= c.hend )
left join clubincall_numbers n 
  on n.ident=c.dst
WHERE
  a.account_id=".$my_page_owner;
$current_dst=db_query($current_dst_query);
$current_dst=$current_dst[0];
if (!$current_dst->dstid) $current_dst->dstnumber='voicemail';

header('Content-Type: text/xml');
header("Cache-Control: no-cache, must-revalidate");
//A date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
echo '<?xml version="1.0" ?>';
echo '<clubincall_form>';
echo '<name>'.$current_dst->name.'</name>';
echo '<dstid>'.$my_page_owner.'</dstid>';
echo '<debug><![CDATA['.print_r($current_dst,1)."\n".$current_dst_query.']]></debug>';
echo '<dstnumber>'.$current_dst->dstnumber.'</dstnumber>';
echo '<prefixes>';
$prefixes_query="select country_name, prefix from clubincall_prefixes where avail=1";
$prefixes=db_query($prefixes_query);
for ($i=0;$i<count($prefixes);$i++){
  echo "<prefix country_name=\"",$prefixes[$i]->country_name."\" value=\"".$prefixes[$i]->prefix."\" ";
  if (isset($last_prefix) && isset($last_number) && $prefixes[$i]->prefix==$last_prefix) echo 'selected="1" last_number="'.$last_number.'"';
  echo " />\n";
}
echo '</prefixes>';
if ($_SESSION['userid']==$my_page_owner){
  echo '<settings>'.$my_page_owner.'</settings>';
}
echo '</clubincall_form>';
// echo "\n";
// echo session_name();
// echo "\n";
// echo session_id();
// echo "\n";
// print_r($_SESSION);
// print_r($_GET);
// echo '</pre>';
?>
