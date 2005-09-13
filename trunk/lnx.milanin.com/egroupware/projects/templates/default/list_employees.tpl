<!-- $Id: list_employees.tpl,v 1.3 2004/06/01 10:38:39 lkneschke Exp $ -->

{app_header}

<center>
{message}
<table width="75%" border="0" cellpadding="2" cellspacing="2" align="center">
	<tr>
		<td colspan="3" width="100%">
			<table border="0" width="100%">
				<tr>
				{left}
					<td align="center">{lang_showing}</td>
				{right}
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width="100%" align="right" colspan="3"><form method="POST" name="query" action="{search_action}">{search_list}</form></td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td width="45%">{sort_name}</td>
		<td width="50%">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td colspan="2" align="center">{currency}&nbsp;{lang_accounting}</td>
				</tr>
				<tr>
					<td width="50%" align="right">{sort_per_hour}</td>
					<td width="50%" align="right">{sort_per_day}</td>
				</tr>
			</table>
		</td>
		<td width="5%">&nbsp;</td>
	</tr>

<!-- BEGIN emp_list -->

	<tr bgcolor="{tr_color}">
		<td><a href="{edit_url}">{emp_name}</a></td>
		<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr align="right">
					<td width="50%">{factor}</td>
					<td width="50%">{d_factor}</td>
				</tr>
			</table>
		</td>
		<td align="center"><a href="{delete_emp}"><img src="{delete_img}" title="{lang_delete_factor}" border="0"></a></td>
	</tr>

<!-- END emp_list -->

	<tr>
		<form method="POST" action="{action_url}">
			<td><select name="values[account_id]">{emp_select}</select></td>
			<td>
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr align="right">
						<td width="50%">[{currency}.cc]&nbsp;<input type="text" name="values[accounting]" value="{accounting}" size="10"></td>
						<td width="50%"><input type="text" name="values[d_accounting]" value="{d_accounting}" size="10"></td>
					</tr>
				</table>
			</td>
			<td align="center"><input type="submit" name="values[save]" value="{lang_save_factor}"></td>
		</form>
	<tr>
</table>
</center>
