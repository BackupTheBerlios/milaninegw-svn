<!-- $Id: form_emp_roles.tpl,v 1.2 2004/06/01 10:38:39 lkneschke Exp $ -->

{app_header}

<center>
{message}
<table width="100%" border="0" cellpadding="2" cellspacing="2" align="center">
	<tr bgcolor="{th_bg}">
		<td width="35%">{sort_name}</td>
		<td width="25%">{sort_role}</td>
		<td width="35%">{lang_events}</td>
		<td width="5%">&nbsp;</td>
	</tr>

<!-- BEGIN role_list -->

	<tr bgcolor="{tr_color}">
		<td valign="top">{edit_link}{emp_name}{end_link}</td>
		<td valign="top">{role_name}</td>
		<td>{events}</td>
		<td align="center" valign="top">{delete_role}{delete_img}</td>
	</tr>

<!-- END role_list -->

	<form method="POST" action="{action_url}">
	<input type="hidden" name="order" value="{order}">
	<input type="hidden" name="sort" value="{sort}">
	<tr>
		<td valign="top"><select name="values[account_id]">{emp_select}</select></td>
		<td valign="top"><select name="values[role_id]"><option value="">{lang_select_role}</option>{role_select}</select></td>
		<td><select name="values[events][]" multiple>{event_select}</select></td>
		<td align="center" valign="top"><input type="submit" name="save" value="{lang_assign}"></td>
	<tr>
	<tr height="50" valign="bottom">
		<td><input type="submit" name="done" value="{lang_done}"></td>
	<tr>
	</form>
</table>
</center>
