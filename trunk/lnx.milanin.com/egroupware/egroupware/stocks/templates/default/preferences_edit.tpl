<!-- $Id: preferences_edit.tpl,v 1.10 2003/08/28 14:30:42 ralfbecker Exp $ -->

<p><b>&nbsp;&nbsp;&nbsp;{lang_action}</b><br>
<hr noshade width="98%" align="center" size="1">

<center>
<form method="POST" name="preferences_edit" action="{actionurl}">
{common_hidden_vars}
<table border="0" cellspacing="2" cellpadding="2" width="40%">
	<tr bgcolor="{th_bg}">
		<td colspan="2" align="center">{h_lang_edit}</td>
	</tr>
	<tr bgcolor="{tr_color1}">
		<td>{lang_symbol}:</td>
		<td align="center"><input type="text" name="symbol" value="{symbol}"></td>
	</tr>
	<tr bgcolor="{tr_color2}">
		<td>{lang_company}:</td> 
		<td align="center"><input type="text" name="name" value="{name}"></td>
	</tr>

<!-- BEGIN edit -->

	<tr valign="bottom">
		<td colspan="2" align="center">
			<input type="submit" name="edit" value="{lang_edit}">
		</td>
	</tr>
</table>
</form>
</center>
         
<!-- END edit -->
