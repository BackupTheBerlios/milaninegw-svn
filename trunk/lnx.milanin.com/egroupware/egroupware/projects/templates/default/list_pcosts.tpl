<!-- $Id: list_pcosts.tpl,v 1.5 2004/06/01 10:38:39 lkneschke Exp $ -->

{app_header}

<center>
<table border="0" width="40%" cellpadding="2" cellspacing="2">
	<tr bgcolor="{th_bg}">
		<td width="50%" align="center">{lang_month}</td>
		<td width="50%" align="right">{lang_pcosts}</td>
	</tr>

<!-- BEGIN projects_list -->

	<tr bgcolor="{tr_color}">
		<td align="center">{month}</td>
		<td align="right">{pcosts}</td>
	</tr>

<!-- END projects_list -->

	<tr>
		<td colspan="2" height="50">
			<form method="POST" action="{done_action}">
			<input type="submit" name="done" value="{lang_done}">
			</form>
		</td>
	</tr>
</table>
</center>
