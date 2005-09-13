<!-- BEGIN form_header -->
   <script language="javascript" type="text/javascript">
function img_popup(img,pop_width,pop_height,attr)
{
   options="width="+pop_width+",height="+pop_height+",location=no,menubar=no,directories=no,toolbar=no,scrollbars=yes,resizable=yes,status=no";
   parent.window.open("{popuplink}&path="+img+"&attr="+attr, "pop", options);
}
</script>


<form method="post" name="frm" action="{form_action}" enctype="multipart/form-data" {form_attributes}>
{where_string_form}
<table align="" cellspacing="2" cellpadding="2" style="background-color:#ffffff;border:solid 1px #cccccc;width:570px;">
<!-- END form_header -->



<!-- BEGIN js -->
<script language="JavaScript">
<!--

function onSubmitForm() {

{submit_script}

return true;
}

//-->
</script>
<!-- END js -->



<!-- BEGIN rows -->
<tr><td bgcolor="{row_color}" valign="top">{fieldname}</td>
<td bgcolor="{row_color}">{input}</td></tr>
<!-- END rows -->

<!-- BEGIN many_to_many -->
<tr><td bgcolor="{m2mrow_color}" valign="top">{m2mfieldname}</td>
<td bgcolor="{m2mrow_color}">
	<table cellspacing="0" cellpadding="3" border="1">
		<tr>
		   <td valign=top>{sel1_all_from}<br/>
	  			<select onDblClick="{on_dbl_click1}" multiple size="5" name="{sel1_name}">
				{sel1_options}	
				</select>
			</td>
			
			<td align="center" valign="top">{lang_add_remove}<br/><br/>
				<input onClick="{on_dbl_click1}" type="button" value=" &gt;&gt; " name="add">
				<br/>
				<input onClick="{on_dbl_click2}" type="button" value=" &lt;&lt; " name="remove">
			</td>
			
			<td valign="top">{lang_related}<br/>
				<select onDblClick="{on_dbl_click2}" multiple size="5" name="{sel2_name}">
				<!-- does this br belong here --><br/>
				{sel2_options}
				</select>

				<input type="hidden" name="{m2m_rel_string_name}" value="{m2m_rel_string_val}">
				<input type="hidden" name="{m2m_opt_string_name}">
			</td>
		</tr>
	</table>


		<!--{m2minput}-->
	</td>
</tr>
<!-- END many_to_many -->


<!-- BEGIN form_footer -->
	</tr>
	<tr><td colspan="2" bgcolor="{row_color}" align="center">{repeat_buttons}&nbsp;</td></tr>
	<tr><td colspan="2" >
	<table align="right" style="background-color:#ffffff">
	<tr>
	<td><input type="submit" name="continue" value="{add_edit_button_continue}"></td>
	<td><input type="submit" name="save" value="{add_edit_button}"></td>
<!--	<td><input type="reset" value="{reset_form}"></td>-->
	<td><input type="submit" name="delete" value="{delete}"></td>
	<td>{cancel}</td>
	</tr>
	</table>
	</td></tr>
</table>
</form>
<!-- END form_footer -->

