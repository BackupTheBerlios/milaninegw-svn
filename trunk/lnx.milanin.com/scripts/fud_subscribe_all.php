<?
//Settings
$db=Array(
'db_host' => '62.149.150.38',
'db_port' => '3306',
'db_name' => 'Sql73134_1',
'db_user' => 'Sql73134',
'db_pass' => '4e455633'
);
$public_forums=Array(2,4,13,10,9,8,11,15,7,24,25,23,21,5,22);
$dry_run=1;
$header="<html>\n<head><title>Subscribe all for forums</title></head>\n<body>";
$link=mysql_connect($db['db_host'].":".$db['db_port'],$db['db_user'],$db['db_pass']) 
  or die("Cant connect: ".mysql_error()." !");
mysql_select_db($db['db_name']) 
  or die("Cant select db: ".mysql_error()." !");
$forums_query="SELECT id,name from phpgw_fud_forum WHERE id in (".implode(",",$public_forums).")";
$result=mysql_query($forums_query);
if (!$result) die("Cant query: [".$forums_query."] because: ".mysql_error()." !");
$header.= '<p><pre>'.$forums_query."</pre></p>\n";
$header.= "<h1>List of forums to subscribe</h1>\n<dl>\n";
while ($row=mysql_fetch_array($result,MYSQL_BOTH)){
  $header.="<dt>[".$row['id']."] : [".$row['name']."]\n";
}
$header.="</dl>\n";
$subscribed_query="SELECT a.account_lid, count( s.id ) as subscribtions 
FROM phpgw_fud_forum_notify s
LEFT JOIN phpgw_fud_users u ON u.id = s.user_id
LEFT JOIN phpgw_accounts a ON a.account_id = u.egw_id
GROUP BY s.user_id";
$result=mysql_query($subscribed_query);
if (!$result) die("Cant query: [".$subscribed_query."] because: ".mysql_error()." !");
$header.= '<p><pre>'.$subscribed_query."</pre></p>\n";
$header.= "<h1>List of subscribed members and counts</h1>\n<dl>\n";
while ($row=mysql_fetch_array($result,MYSQL_BOTH)){
  $header.="<dt>[".$row['account_lid']."] : [".$row['subscribtions']."]\n";
}
$header.="</dl>\n";
$non_subscribed_query="SELECT u.id, a.account_lid
FROM phpgw_fud_users u
JOIN phpgw_accounts a ON a.account_id = u.egw_id
LEFT JOIN phpgw_fud_forum_notify n ON n.user_id = u.id
WHERE (
n.user_id IS NULL 
)
AND (
a.account_primary_group !=35
)
AND (
a.account_lid != 'admin'
)
AND (
a.account_lid != 'anonymous'
)";

$unsubscribed=Array();

$result=mysql_query($non_subscribed_query);
if (!$result) die("Cant query: [".$non_subscribed_query."] because: ".mysql_error()." !");
$header.= '<p><pre>'.$non_subscribed_query."</pre></p>\n";
$header.= "<h1>List of non subscribed members (".mysql_num_rows($result).")</h1>\n<p>\n";
while ($row=mysql_fetch_array($result,MYSQL_BOTH)){
  $header.="[".$row['account_lid']."], ";
  $unsubscribed[]=$row['id'];
}
$header.="</p>\n";
if (!isset($_REQUEST['perform'])||$_REQUEST['perform']!=strtolower("yes")) {
$header.="<form>\n<table><tr><td>Perform subscribtions? (type 'yes' to perform): <input type=\"text\" name=\"perform\"/>\n
            <input type=\"submit\" value=\"Go\"/></form>";
echo $header;
}elseif (isset($_REQUEST['perform']) && $_REQUEST['perform']=strtolower("yes")){
  echo $header;
  echo "Subscribing members....<br/>";
  foreach ($unsubscribed as $member_id){
    foreach ($public_forums as $forum_id){
      $add_subs_quesry="INSERT INTO phpgw_fud_forum_notify (user_id,forum_id) VALUES (".$member_id.",".$forum_id.")";
      $result=mysql_query($add_subs_quesry);
      if (!$result || mysql_affected_rows($link)<1) 
          die("Cant query: [".$add_subs_quesry."] because: ".mysql_error()."or because rows is:[".mysql_affected_rows($link)."] !");
      echo "Inserted ".$forum_id." for ".$member_id.", ";
    }
    echo "Finished ".$member_id."<br/>\n";
  }
  echo "<p><h1>Done</h1></p>\n";
}
mysql_free_result($result);
mysql_close($link);
echo "</body></html>";




?>