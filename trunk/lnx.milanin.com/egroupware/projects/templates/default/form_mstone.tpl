<!-- $Id: form_mstone.tpl,v 1.9 2004/06/01 10:38:39 lkneschke Exp $ -->

{app_header}

<center>
<form method="POST" action="{edit_url}">
<table border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td colspan"2">{lang_title}:</font></td>
		<td><input type="text" name="values[title]" size="50" value="{title}"></td>
	</tr>
	<tr>
		<td colspan"2">{lang_end_date}:</td>
		<td>{end_date_select}</td>
	</tr>
	<tr valign="bottom" height="50">
		<td>
			<input type="hidden" name="values[old_edate]" value="{old_edate}">
			<input type="submit" name="save" value="{lang_save}">
		</td>
		<td align="right">{delete}</td>
		<td align="right"><input type="submit" name="cancel" value="{lang_cancel}"></td>
	</tr>
</table>
</form>
</center>
