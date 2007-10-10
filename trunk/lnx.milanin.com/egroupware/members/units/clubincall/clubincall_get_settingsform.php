<?
require_once("../../includes.php");
require_once('../users/conf.php');
require_once('../users/function_session_start.php');

global $profile_id;

$weekdays = array("Noday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday");
$my_page_owner=$_GET['id'];
if ($_SESSION['userid']==$_GET['id']){
  $numbers_query="SELECT n.*,IF(ISNULL(gd.used),0,gd.used) as used FROM clubincall_numbers n
                           left join (select count(*) as used,dst from clubincall_dsts d 
                                 where d.owner=".$_GET['id']." group by dst) gd 
                           on gd.dst=n.ident".
                  " where n.owner=".$_GET['id'];
  $numbers=db_query($numbers_query);
  $dsts_query="SELECT d.*,n.number, n.description FROM clubincall_dsts d 
               join clubincall_numbers n on n.ident=d.dst 
               where d.owner=".$_GET['id'];
  $dsts=db_query($dsts_query);
  header('Content-Type: text/xml');
  header("Cache-Control: no-cache, must-revalidate");
//A date in the past
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  echo '<?xml version="1.0" ?>';
  echo '<clubincall_settings>';
  echo '<debug><![CDATA['.$numbers_query."\n".$dsts_query.']]></debug>';
  echo '<dsts>';
  foreach ($dsts as $dst){
    echo '<dst id="'          .$dst->ident.
            '" nid="'         .$dst->dst.
            '" number="'      .$dst->number.
            '" ndescription="'.$dst->description.
            '">'.
            "\n";
    echo '<wstart value="'.$dst->wstart.'" name="'.$weekdays[$dst->wstart].'" />'."\n";
    echo '<wend   value="'.$dst->wend  .'" name="'.$weekdays[$dst->wend].  '" />'."\n";
    echo '<hstart value="'.$dst->hstart.'" />'."\n";
    echo '<hend value="'.$dst->hend.'" />'."\n";
    echo '</dst>';
  }
  echo '</dsts>';
  echo '<numbers>';
  foreach ($numbers as $number){
    echo '<number id="'.$number->ident.
               '" value="'.$number->number.
               '"  used="'.$number->used.
          '"/>';
  }
  echo '</numbers>';
  echo '</clubincall_settings>';
}

?>