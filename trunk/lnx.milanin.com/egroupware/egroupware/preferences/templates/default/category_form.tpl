<!-- $Id: category_form.tpl,v 1.20 2004/05/26 07:25:28 ralfbecker Exp $ -->

<!-- BEGIN form -->
<br>
<center>
{message}<br>
<table border="0" width="80%" cellspacing="2" cellpadding="2"> 
<form name="edit_cat" action="{actionurl}" method="POST">
	<tr class="th">
		<td colspan="2">{lang_parent}</td>
		<td><select name="new_parent"><option value="">{lang_none}</option>{category_list}</select></td>
	</tr>
	<tr class="row_on">
		<td colspan="2">{lang_name}</font></td>
		<td><input name="cat_name" size="50" value="{cat_name}"></td>
	</tr>
	<tr class="row_off">
		<td colspan="2">{lang_descr}</td>
		<td colspan="2"><textarea name="cat_description" rows="4" cols="50" wrap="virtual">{cat_description}</textarea></td>
	</tr>
	<tr class="row_on">
		<td colspan="2">{lang_access}</td>
		<td colspan="2">{access}</td>
	</tr>
	<tr class="row_off">
		<td colspan="2">{lang_color}</td>
		<td colspan="2">{color}</td>
	</tr>
	<tr class="row_on">
		<td colspan="2">{lang_icon}</td>
		<td colspan="2">{select_icon} {icon}</td>
	</tr>
<!-- BEGIN data_row -->
	<tr class="{class}">
		<td colspan="2">{lang_data}</td>
		<td>{td_data}</td>
	</tr>
<!-- END data_row -->

<!-- BEGIN add -->

	<tr valign="bottom" height="50">
		<td><input type="submit" name="save" value="{lang_save}"></form></td>
		<td><form method="POST" action="{cancel_url}">
			<input type="submit" name="cancel" value="{lang_cancel}"></form></td>
	</tr>
</table>
</center>

<!-- END add -->

<!-- BEGIN edit -->

	<tr valign="bottom" height="50">
		<td>
			{hidden_vars}
			<input type="submit" name="save" value="{lang_save}"></form></td>
		<td>
			<form method="POST" action="{cancel_url}">
			<input type="submit" name="cancel" value="{lang_cancel}"></form></td>
		<td align="right">{delete}</td>
	</tr>
</table>
</center>

<!-- END edit -->

<!-- END form -->
