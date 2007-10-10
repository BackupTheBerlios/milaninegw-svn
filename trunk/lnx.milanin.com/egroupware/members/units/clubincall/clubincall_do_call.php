<?
require_once("../../includes.php");
if (isset($_GET['prefix']) && isset($_GET['number']) && isset($_GET['id'])){
  if (preg_match("/^\d+$/",$_GET['prefix'])>0 && preg_match("/^\d+$/",$_GET['number']) >0){
    setcookie("last_prefix", $_GET['prefix'],time()+60*60*24*365);
    setcookie("last_number", $_GET['number'],time()+60*60*24*365);
    $mydst=$_GET['prefix'].$_GET['number'];
    $current_dst_query="SELECT a.account_firstname as name, c.ident as dstid, n.number as dstnumber FROM phpgw_accounts a
    left join clubincall_dsts c
      on c.owner=a.account_id 
      and ( (weekday( curdate())+1) >= c.wstart and (weekday( curdate())+1) <= c.wend )
      and ( hour(curtime()) >=c.hstart and hour(curtime()) <= c.hend )
    left join clubincall_numbers n 
      on n.ident=c.dst
    WHERE
      a.account_id=".$_GET['id'];
    $current_dst=db_query($current_dst_query);
    $current_dst=$current_dst[0];
    
    $query="select max(length(prefix)) as longest_prefix from clubincall_prefixes";
    $longest_prefix=db_query($query);
    $longest_prefix=$longest_prefix[0]->longest_prefix;
    for ($i=1;$i<=$longest_prefix;$i++){
      $in.=($i==1 ? "(":"").substr($mydst,0,$i).($i==$longest_prefix ? ")" : ",");
    }
    $query="SELECT prefix,avail,type from clubincall_prefixes WHERE prefix IN $in ORDER BY LENGTH(prefix) DESC LIMIT 1";
    $prefix_match=db_query($query);
    $prefix_match=$prefix_match[0];
    if ($prefix_match->avail!=1){
      $result=1;
      $error="The number starting with ".$prefix_match->prefix." of type '".$prefix_match->type."' cannot be called. Sorry.";
    }
  }else{
    $result=-1;
    $error="Invalid number received: ".$_GET['prefix'].$_GET['number'];
  }
  header('Content-Type: text/xml');
  header("Cache-Control: no-cache, must-revalidate");
  //A date in the past
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  echo '<?xml version="1.0" ?>';
  echo '<clubincall_call>';
  echo '<debug><![CDATA['.print_r($prefix_match,1)."\n".print_r($current_dst,1)."\n".$current_dst_query.']]></debug>';
  echo '<result>'.(isset($result) ? $result : 0).'</result>';
  echo '<error>'.(isset($error) ? $error : "None").'</error>';
  echo '</clubincall_call>';
}
?>