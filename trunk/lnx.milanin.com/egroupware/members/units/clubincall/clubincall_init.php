<?
global $page_owner;
global $current_dst;

$current_dst=db_query("SELECT c.*,n.* FROM clubincall_dsts c 
join clubincall_numbers n 
  on n.ident=c.dst 
WHERE
  ( (weekday( curdate())+1) >= c.wstart and (weekday( curdate())+1) <= c.wend )
  AND
  ( hour(curtime()) >=c.hstart and hour(curtime()) <= c.hend )
  AND c.owner=".$page_owner);
if (count($current_dst)<2) $current_dst=array('voicemail');

?>
