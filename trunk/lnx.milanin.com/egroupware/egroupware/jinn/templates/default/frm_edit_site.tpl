<!-- BEGIN header -->
<form method="post" name="frm" action="{form_action}" enctype="multipart/form-data" {form_attributes}>
{where_key_form}
{where_value_form}
<table  cellspacing="2" cellpadding="2"  style="width:570px;background-color:#ffffff;border:solid 1px #cccccc;">
<!-- END header -->

<!-- BEGIN rows -->
<tr><td bgcolor={row_color} valign="top">{fieldname}</td><td bgcolor={row_color}>{input}</td></tr>
<!-- END rows -->

<!-- BEGIN footer -->
</tr>
</table>

<script language="javascript" type="text/javascript">
function testdbfield()
{
   dbvals=document.frm.FLDsite_db_name.value+':'+document.frm.FLDsite_db_host.value+':'+document.frm.FLDsite_db_user.value+':'+document.frm.FLDsite_db_password.value+':'+document.frm.FLDsite_db_type.value  +':'+   document.frm.FLDdev_site_db_name.value+':'+document.frm.FLDdev_site_db_host.value+':'+document.frm.FLDdev_site_db_user.value+':'+document.frm.FLDdev_site_db_password.value+':'+document.frm.FLDdev_site_db_type.value;
   sessionlink='{test_access_link}';
   link=sessionlink+'&dbvals='+dbvals;
   window.open(link,'', 'width=400,height=300,location=no,menubar=no,directories=no,toolbar=no,scrollbars=yes,resizable=yes,status=no');
}
</script>

<table  style="width:570px;background-color:#ffffff;border:solid 1px #cccccc;margin:3px 0px 3px 0px">
	<tr>
        <td align="center">
		<input type="submit" name="continue" value="{save_and_continue_button}">
		<input type="submit" name="add" value="{save_button}">
		<input type="button" onClick="{onclick_cancel}" value="{lang_cancel}" />
		<input type="button" onClick="if(window.confirm('{confirm_del}')){onclick_delete}" value="{lang_delete}" />
		<input type="hidden" name="testdbvals">
		<input type="button" onClick="testdbfield()" value="{lang_test_access}">
		<input type=button onClick="{onclick_export}" value="{lang_export}">
	</tr>
</table>
</form>
<!-- END footer -->
