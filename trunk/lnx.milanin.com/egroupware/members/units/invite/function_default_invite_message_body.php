<?php
$run_result .= <<< END
 
 <script language="JavaScript" type="text/javascript">
<!--
var i = 0;
var str = new String("");
var template_content = new Array();
template_content[0]="";
-->
</script>		
END;

//$qry = "SELECT * FROM template_elements where name like 'msg%:title".$parameter[1].":language".$parameter[0]."'";
$qry = "SELECT * FROM template_elements where name like 'msg%:title%:".$parameter[0]."'";


$result = db_query($qry) or die ("<center> ERROR: ".mysql_error()."</center>");

$sql1 = "SELECT count(*) as count FROM users"; 
$db = db_query($sql1);

for ($i=0;$i<sizeof($result);$i++)
   {
   $run_result .= '
   <script language="JavaScript" type="text/javascript">
				<!--
				
				str="'.$result[$i]->content.'";
				str = new String(str.replace("<<$members_count>>","'.$db[0]->count.'"));

				template_content[i]=str;
				i++;
				-->
			</script>
';
}
        
?>


