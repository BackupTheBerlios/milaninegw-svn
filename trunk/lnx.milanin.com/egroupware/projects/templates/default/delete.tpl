<!-- $Id: delete.tpl,v 1.17 2004/06/01 10:38:39 lkneschke Exp $ -->

{app_header}

<center>
<table border="0" with="65%">
<form method="POST" action="{action_url}">
	<tr colspan="2">
		<td align="center">{deleteheader}</td>
	</tr>
	<tr>
		<td align="center">{lang_subs}</td>
		<td align="center">{subs}</td>
	</tr>
	<tr>
		<td><input type="submit" name="yes" value="{lang_yes}"></td>
		<td><input type="submit" name="no" value="{lang_no}"></td>
	</tr>
</form>
</table>
</center>
