<?php
$run_result .= <<< END
 
 <script language="JavaScript" type="text/javascript">
<!--
var i = 0;
var template_content = new Array();
template_content[0]="";
-->
</script>		
END;

//$qry = "SELECT * FROM template_elements where name like 'msg%:title".$parameter[1].":language".$parameter[0]."'";
$qry = "SELECT * FROM template_elements where name like 'msg%:title%:".$parameter[0]."'";


$result = mysql_query($qry) or die ("<center> ERROR: ".mysql_error()."</center>");
while ($riga = mysql_fetch_object($result)) {
   $run_result .= <<< END
   <script language="JavaScript" type="text/javascript">
				<!--
				template_content[i]="$riga->content";
				i++;
				-->
			</script>
END;
}
        
mysql_free_result($result);
?>


