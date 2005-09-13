<!-- $Id: alarm.tpl,v 1.6 2004/03/14 22:30:50 ak703 Exp $ -->
<!-- BEGIN alarm_management -->
<form id="calendar_alarmform" action="{action_url}" method="post" name="alarmform">
{hidden_vars}
	<table id="calendar_alarmform_table" border="0" width="90%" align="center">
		{rows}
		<tr>
			<td colspan="4">
				<br>&nbsp;{input_days}&nbsp;{input_hours}&nbsp;{input_minutes}&nbsp;{input_owner}&nbsp;{input_add}<br>&nbsp;
			</td>
			<td align="right">
				{input_cancel}
			</td>
		</tr>
	</table>
</form>
<!-- END alarm_management -->

<!-- BEGIN alarm_headers -->
	<tr bgcolor="{tr_color}">
		<th align="left" width="25%">{lang_time}</th>
		<th align="left" width="30%">{lang_text}</th>
		<th align="left" width="25%">{lang_owner}</th>
		<th width="10%">{lang_enabled}</th>
		<th width="10%">{lang_select}</th>
	</tr>
<!-- END alarm_headers -->

<!-- BEGIN list -->
	<tr bgcolor="{tr_color}">
		<td>
			<b>{field}:</b>
		</td>
		<td>
			{data}
		</td>
		<td>
			{owner}
		</td>
		<td align="center">
			{enabled}
		</td>
		<td align="center">
			{select}
		</td>
	</tr>
<!-- END list -->

<!-- BEGIN hr -->
	<tr bgcolor="{th_bg}">
		<td colspan="5" align="center">
			<b>{hr_text}</b>
		</td>
	</tr>
<!-- END hr -->

<!-- BEGIN buttons -->
	<tr>
		<td colspan="6" align="right">
			{enable_button}&nbsp;{disable_button}&nbsp;{delete_button}
		</td>
	</tr>
<!-- END buttons -->
