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
				
function doABCQuery(letter, start_from)	
{
	document.userSearchform.wordChar.value=letter;
	if (start_from != null)
		document.userSearchform.start_from.value=start_from;
	document.userSearchform.submit();
	return true;
}

var lastShownId = null;

function HideInfo(id)
{
	var obj = document.getElementById("divInfo"+id);
	if(obj != null)
	{
		obj.style.display = 'none';
		lastShownId = null;
	}
}

function ShowInfo(id)
{
	var obj = document.getElementById("divInfo"+id);
	if(obj != null)
	{
		if(lastShownId != null)
			HideInfo(lastShownId);

		obj.style.display = 'block';
		lastShownId = id;
	}
}
function WarningMessage(mess)
{
	alert(mess);
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
<style>
	.divContainer
	{
		position:absolute;
	}
	.divContainer .divInfo
	{
		background-color:white;
		position:relative;
		border:1px solid gray;
		display:none;
		width:250px;
		padding:10px 3px 10px 3px;
		top:-10px;
		left:-230px;
		z-Index:1000;
	}
	.tableHeader td
	{
		background-color:#006699;
		text-align:center;
		font-weight:bold;
		color:white;
	}
	.tableLayout
	{
		border-color:#CCCCCC;
		border-style:none;
		border-width:1px;
		background-color:white;
		color:#000066;
	}
	
	.tableLayout .Row
	{
		background-color:#d2f1ff;
	}
	
	.tableLayout .altRow
	{
		background-color:#c7e4f1;
	}
</style>	