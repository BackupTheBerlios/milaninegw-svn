<!-- BEGIN list -->
<STYLE type="text/css">
 <!--
   .topsort { color: #000000; }   
 -->
</STYLE>

<script>

function check_all()
{
	for (i=0; i<document.messages.elements.length; i++) {
		if (document.messages.elements[i].type == "checkbox") {
			if (document.messages.elements[i].checked) {
				document.messages.elements[i].checked = false;
			} else {
				document.messages.elements[i].checked = true;
			}
		} 
	}
}
</script>

{app_header}

<form action="{form_action}" method="POST" name="messages">
 <table border="0" width="95%" align="center" bgcolor="#DCDCDC">
  <tr>
   <td>
 
    <table border="0" width="100%" cellpadding="2" cellspacing="1">
     <tr bgcolor="#FFFFCC">
      <td width="1%" align="center"><input type="checkbox" onClick="check_all()"></td>
      <td width="1%">&nbsp;</td>
      <td width="8%">{sort_date}</td>
      <td width="27%">{sort_from}</td>
      <td width="60%">{sort_subject}</td>
     </tr>
 
     {rows}
    </table>
 
   </td>
  </tr>
 </table>

 <table border="0" width="95%" align="center">
  <tr>
   <td align="right">
    {button_delete}&nbsp;
   </td>
  </tr>
 </table>
</form>
<!-- END list -->

<!-- BEGIN row -->
    <tr bgcolor="#FFFFFF">
     <td width="1%" align="center">{row_checkbox}</td>
     <td align="center"><b>{row_status}</b></td>
     <td>{row_date}</td>
     <td>{row_from}</td>
     <td>{row_subject}</td>
    </tr>
<!-- END row -->

<!-- BEGIN row_empty -->
    <tr bgcolor="#FFFFFF">
     <td colspan="5" align="center">{lang_empty}</td>
    </tr>
<!-- END row_empty -->
