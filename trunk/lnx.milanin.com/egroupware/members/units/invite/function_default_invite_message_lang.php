<?php
$qry = "SELECT * from ".tbl_prefix."template_elements where name like 'language%'";
$result = db_query($qry) or die ("<center> ERROR: ".mysql_error()."</center>");
         for ($i=0;$i<sizeof($result);$i++)
       {
        $run_result['lang'][] = array($result[$i]->content,$result[$i]->name);
        }  // end of while loop 
?>
