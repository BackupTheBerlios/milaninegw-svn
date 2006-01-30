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


$result = db_query($qry) or die ("<center> ERROR: ".mysql_error()."</center>");
for ($i=0;$i<sizeof($result);$i++)
   {
   $run_result .= '
   <script language="JavaScript" type="text/javascript">
				<!--
				template_content[i]="'.$result[$i]->content.'";
				i++;
				-->
			</script>
';
}
        
?>


