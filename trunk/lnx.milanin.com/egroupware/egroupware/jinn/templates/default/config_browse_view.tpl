<form action="{form_action}" method=post>
<table align=center cellspacing="1" width="80%">
	<tr>
		<td bgcolor="{th_bg}" align="center"><b>{lang_config_table}</b></td>
	</tr>
</table>
<table align=center cellspacing="1" width="80%">
	<tr>
		<td bgcolor="{th_bg}" align="left">{lang_column_name}</td>
		<td bgcolor="{th_bg}" align="left">{lang_show_column}</td>
		<td bgcolor="{th_bg}" colspan="2" align="left">{lang_default_order}</td>
	</tr>
	{rows}
</table>
<table align=center cellspacing="1" width="80%">
	<tr>
		<td align="center">
			{button_save}
		</td>
		<td align="center">
			{button_cancel}
		</td>
	</tr>
</table>

</form>
	
