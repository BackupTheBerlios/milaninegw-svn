<?
global $page_owner;
if (
  	( (!isset($_SESSION['userid'])
            ||
            $_SESSION['userid']<=0)
       	  && 
      	  isset($_SERVER['HTTP_REFERER']))
  	|| 
  	(isset($_SESSION['userid']) && $_SESSION['userid']!=$page_owner)
   )
{
  if (isset($_SESSION['userid']) && $_SESSION['userid']>0 && $page_owner>0)
  {
          $viewer=$_SESSION['userid'];
          $referral='';
  }else{
          $viewer=0;
          $referral=addslashes($_SERVER['HTTP_REFERER']);
  }
  $query='INSERT INTO '.tbl_prefix.
          'profile_views (owner,viewer,referral,timestamp,counter) VALUES ('.
          $page_owner.','.$viewer.',\''.substr($referral,0,255).'\','.time().',1'.
  	  ') ON DUPLICATE KEY UPDATE counter=counter+1';
  db_query($query);
  $query='DELETE FROM '.tbl_prefix.
          'profile_views WHERE `timestamp` < '.(time()-(86400*14));
  db_query($query);
}
?>
