<!-- BEGIN header -->
   <script language="javascript" type="text/javascript">
function img_popup(img,pop_width,pop_height,attr)
{
   options="width="+pop_width+",height="+pop_height+",location=no,menubar=no,directories=no,toolbar=no,scrollbars=yes,resizable=yes,status=no";
   parent.window.open("{popuplink}&path="+img+"&attr="+attr, "pop", options);
}
</script>

<!--<form method="post" name="frm" action="{form_action}" enctype="multipart/form-data" {form_attributes}>
{where_string_form}-->
<table align="" cellspacing="2" cellpadding="2" style="background-color:#ffffff;border:solid 1px #cccccc;width:570px">
<!-- END header -->


<!-- BEGIN rows -->
<tr><td bgcolor="{row_color}" valign="top">{fieldname}</td>
<td bgcolor="{row_color}">{input}</td></tr>
<!-- END rows -->

<!-- BEGIN back_button -->
	<input type="button" onClick="{back_onclick}" value="{lang_back}">
<!-- END back_button -->

<!-- BEGIN footer -->
	</tr>
	<tr>
	<td colspan="2" bgcolor="{row_color}">
	<input type="button" onClick="{edit_onclick}" value="{lang_edit}">
	{extra_back_button}
	</td></tr>
	<tr><td colspan="2" >
	<table align="right" style="background-color:#ffffff">
	<tr>
	<td>	
	</td>
	</tr>
	</table>
	
	</td></tr>

</table>
<!--</form>-->
<!-- END footer -->

