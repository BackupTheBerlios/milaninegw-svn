<!-- $Id: list_mstones.tpl,v 1.4 2004/06/01 10:38:39 lkneschke Exp $ -->

{app_header}

<center>

<!-- BEGIN project_data -->

<table border="0" width="100%" cellpadding="2" cellspacing="0">
	<tr bgcolor="{th_bg}">
		<td colspan="4"><b>{lang_project}:&nbsp;<a href="{pro_url}">{title_pro}</a></b></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_number}:</td>
		<td>{number_pro}</td>
		<td>{lang_url}:</td>
		<td><a href="http://{url_pro}" taget="_blank">{url_pro}</a></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_coordinator}:</td>
		<td>{coordinator_pro}</td>
		<td>{lang_customer}:</td>
		<td>{customer_pro}</td>
	</tr>
	<tr height="5">
		<td></td>
	</tr>
</table>
{message}
<!-- END project_data -->

<table border="0" cellspacing="0" cellpadding="2" width="50%">

	<tr bgcolor="{th_bg}">
		<td width="49%">{lang_title}</td>
		<td width="49%">{lang_date_due}</td>
		<td>&nbsp;</td>
	</tr>

<!-- BEGIN mstone_list -->

	<tr bgcolor="{tr_color}">
		<td><a href="{edit_url}">{title}</a></td>
		<td>{datedue}</td>
		<td align="center"><a href="{delete_url}">{delete_img}</a></td>
	</tr>
	<tr bgcolor="{tr_color}">
		<td colspan="3">{description}</td>
	<tr>

<!-- END mstone_list -->

</table>
<table border="0" cellspacing="0" cellpadding="2">
	<form method="POST" action="{action_url}">
	<tr height="50" valign="bottom">
		<td><input type="text" name="values[title]" size="50" value="{title}"></td>
		<td>{end_date_select}</td>
		<td>
			<input type="hidden" name="values[old_edate]" value="{old_edate}">
			<input type="hidden" name="s_id" value="{s_id}">
			<input type="submit" name="save" value="{lang_save_mstone}">
		</td>
		<td><input type="checkbox" name="values[new]" value="True" {new_checked}>{lang_new}</td>
	</tr>
	<tr>
		<td colspan="4">
			<textarea name="values[description]" cols="50" rows="5">{description}</textarea>
		</td>
	</tr>
	<tr valign="bottom" height="50">
		<td align="right" colspan="4"><input type="submit" name="done" value="{lang_done}"></td>
	</tr>
</form>
</table>
</center>
