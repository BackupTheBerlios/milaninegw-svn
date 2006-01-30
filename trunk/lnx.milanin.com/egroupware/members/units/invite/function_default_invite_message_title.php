<?php
$qry = "SELECT * FROM template_elements where name like 'title%:".$parameter[0]."'";
//echo $qry;
$result = db_query($qry) or die ("<center> ERROR: ".mysql_error()."</center>");
         for ($i=0;$i<sizeof($result);$i++)
       {
        $run_result['lang'][] = array($result[$i]->content,$result[$i]->name);
        }  // end of while loop 
?>
