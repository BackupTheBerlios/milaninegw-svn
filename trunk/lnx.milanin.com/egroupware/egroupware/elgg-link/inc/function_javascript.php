<script language="JavaScript" type="text/javascript">
<!--
function doQuery(query, start_from)	
				{
				  //alert(query+"-"+start_from);
				  //document.userSearchform.start_from.value=query;
				  if ((query != null)&&(query != ''))
				  document.userSearchform.query.value=query;
				  if (start_from != null)
				  document.userSearchform.start_from.value=start_from;
				  
				 document.userSearchform.submit();
				 return true;
				}
function doSubmit()	
				{
				  document.userSearchform.start_from.value=0;
				  return true;
				}				
function setValues(query_type, order_by, regstatus)	
				{
				  //alert("--"+document.userSearchform.regstatus.length);
				  for (var iSelect = 0; iSelect < document.userSearchform.query_type.length; iSelect++) {
				  if (document.userSearchform.query_type[iSelect].value == query_type){
				   document.userSearchform.query_type[iSelect].selected = true;
				   break;
				   }
				  }
				  
				  for (var iSelect = 0; iSelect < document.userSearchform.order_by.length; iSelect++) {
				  if (document.userSearchform.order_by[iSelect].value == order_by){
				   document.userSearchform.order_by[iSelect].selected = true;
				   break;
				   }
				  }
				  
				  for (var iSelect = 0; iSelect < document.userSearchform.regstatus.length; iSelect++) {
				  if (document.userSearchform.regstatus[iSelect].value == regstatus){
				   document.userSearchform.regstatus[iSelect].checked = true;
				   break;
				   }
				  }
				 }
-->
</script>		