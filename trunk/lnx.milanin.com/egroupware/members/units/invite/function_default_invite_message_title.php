<?php
$qry = "SELECT * FROM template_elements where name like 'title%:".$parameter[0]."'";
//echo $qry;
$result = mysql_query($qry) or die ("<center> ERROR: ".mysql_error()."</center>");
         while($row = mysql_fetch_assoc($result))
       {
        $run_result['lang'][] = array($row["content"],$row["name"]);
        }  // end of while loop 
mysql_free_result($result);
?>