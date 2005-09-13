<!-- $Id: admin_form.tpl,v 1.12 2004/01/25 18:19:52 reinerj Exp $ -->

<center>
<form method="POST" action="{action_url}">
{message}
<table width="85%" border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td bgcolor="{bg_color}" colspan="2"><b>{lang_b_config}</b></td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_b_create}</td>
		<td>{b_create}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_b_intval}:</td>
		<td><select name="values[b_intval]"><option value="">{lang_select_b_intval}</option>{intval_list}</select></td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_config_path}:</td>
		<td><input type="text" name="values[script_path]" value="{script_path}"></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_versions}:</td>
		<td><input type="text" name="values[versions]" value="{versions}" size="3" maxlenght="3"></td>
	</tr>
	<tr>
		<td bgcolor="{row_off}" colspan="2"><b>{lang_b_data}</b></td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_b_sql}:</td>
		<td>{b_sql}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_b_ldap}:</td>
		<td>{b_ldap}</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_b_email}:</td>
		<td>{b_email}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_b_type}:</td>
		<td><select name="values[b_type]"><option value="">{lang_select_b_type}</option>{type_list}</select></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td bgcolor="{bg_color}" colspan="2"><b>{lang_l_config}</b></td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_l_save}</td>
		<td>{l_save}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_path}:</td>
		<td><input type="text" name="values[l_path]" value="{l_path}"></td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_l_websave}</td>
		<td>{l_websave}</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	
</table>

<table width="85%" border="0" cellspacing="2" cellpadding="2">
	<tr valign="bottom" height="50">
		<td>
			<input type="submit" name="values[save]" value="{lang_save}"></form></td>
		<td align="right">
			<form method="POST" action="{cancel_url}">
			<input type="submit" name="cancel" value="{lang_cancel}"></form></td>
	</tr>
</table>
</center>
