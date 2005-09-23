<!-- $Id: hours_formhours.tpl,v 1.32.2.1 2004/11/06 12:15:58 ralfbecker Exp $ -->

{app_header}

<center>{message}</center>

<!-- BEGIN main -->

<table width="100%" border="0" cellspacing="2" cellpadding="2">
	<tr bgcolor="{th_bg}">
		<td width="100%" colspan="7"><b>{lang_main}</b>:&nbsp;<a href="{main_url}">{pro_main}</a></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_number}:</td>
		<td colspan="2">{number_main}</td>
		<td>{lang_url}:</td>
		<td colspan="3"><a href="http://{url_main}" target="_blank">{url_main}</a></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_coordinator}:</td>
		<td colspan="2">{coordinator_main}</td>
		<td>{lang_customer}:</td>
		<td colspan="3">{customer_main}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_hours}:</td>
		<td>{lang_planned}:</td>
		<td>{ptime_main}</td>
		<td>{lang_used_total}{lang_plus_jobs}:</td>
		<td>{utime_main}</td>
		<td>{lang_available}{lang_plus_jobs}:</td>
		<td>{atime_main}</td>
	</tr>
	<tr height="5">
		<td>&nbsp;</td>
	</tr>
</table>

<!-- END main -->

<table width="100%" border="0" cellspacing="2" cellpadding="2">
<form method="POST" action="{action_url}">
{hidden_vars}
	<tr bgcolor="{row_off}">
		<td>{lang_project}:</td>
		<td colspan="2">{project_name}</td>
		<td>{lang_employee}:</td>
		<td colspan="2">{employee}</td>
	</tr>
	<tr bgcolor="{row_on}" valign="top">
		<td>{lang_activity}:</td>
		<td colspan="2">
<!-- BEGIN activity -->

			<select name="values[activity_id]">{activity_list}</select>

<!-- END activity -->

<!-- BEGIN activity_own -->

			<input type="text" name="values[hours_descr]" size="30" value="{hours_descr}"> &nbsp; {billable_checked}

<!-- END activity_own -->

		</td>
		<td>{lang_costtype}:</td>
		<td colspan="2">
<!-- BEGIN cost -->
			<select name="values[cost_id]">{cost_list}</select>
<!-- END cost -->
		</td>
	</tr>
	<tr bgcolor="{row_on}" valign="top">
		<td>{lang_remark}:</td>
		<td colspan="5" align="center">
			<textarea style="width:99%;" name="values[remark]" rows="5" cols="60" wrap="VIRTUAL">{remark}</textarea>
		</td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td colspan="6"><b>{lang_work_date}</b></td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_start_date}:</td>
		<td colspan="2">{start_date_select}</td>
		<td>{lang_end_date}:</td>
		<td colspan="2">{end_date_select}</td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td colspan="6"><b>{lang_work_time}</b></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_start_time}:</td>
		<td>
			<input type="text" name="values[shour]" value="{shour}" size="3" maxlength="2">:
			<input type="text" name="values[smin]" value="{smin}" size="3" maxlength="2">&nbsp;[hh:mm]
			&nbsp;{sradio}
		</td>
		<td>{lang_end_time}:</td>
		<td>
			<input type="text" name="values[ehour]" value="{ehour}" size=3 maxlength=2>:
			<input type="text" name="values[emin]" value="{emin}" size=3 maxlength=2>&nbsp;[hh:mm]
			&nbsp;{eradio}
		</td>
		<td>{lang_hours}:</td>
		<td>
			<input type="text" name="values[hours]" value="{hours}" size=3 maxlength=2>:
			<input type="text" name="values[minutes]" value="{minutes}" size=3 maxlength=2>&nbsp;[hh:mm]
		</td>
	</tr>
	<tr height="5">
		<td>&nbsp;</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_status}:</td>
		<td colspan="5"><select name="values[status]">{status_list}</select></td>
	</tr>

	<tr bgcolor="{row_off}">
		<td>{lang_distance}:</td>
		<td colspan="2"><input type="text" name="values[km_distance]" value="{km_distance}"></td>
		<td>{lang_time_of_journey}:</td>
		<td colspan="2"><input type="text" name="values[t_journey]" value="{t_journey}">&nbsp;[hh.mm]</td>
	</tr>

	<tr valign="bottom" height="50">
		<td><input type="submit" name="save" value="{lang_save}"></td>
		<td><input type="submit" name="cancel" value="{lang_cancel}"></td>
		<td colspan="4" align="right">{delete}</td>
	</tr>
</form>
</table>
</center>
