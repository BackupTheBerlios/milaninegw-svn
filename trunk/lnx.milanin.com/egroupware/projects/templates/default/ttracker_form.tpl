<!-- $Id: ttracker_form.tpl,v 1.3.2.1 2004/11/06 12:15:58 ralfbecker Exp $ -->

{app_header}

<center>
{message}
<table width="100%" border="0" cellspacing="2" cellpadding="2">
<form method="POST" action="{action_url}">
	<tr>
		<td>{lang_activity}:</td>
		<td colspan="3">
<!-- BEGIN activity -->

			<select name="values[activity_id]">{activity_list}</select>

<!-- END activity -->

<!-- BEGIN act_own -->

			<input type="text" name="values[hours_descr]" size="50" value="{hours_descr}"> &nbsp; {billable_checked}

<!-- END act_own -->

		</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td valign="top">{lang_remark}:</td>
		<td colspan="3"><textarea name="values[remark]" rows="5" cols="50" wrap="VIRTUAL">{remark}</textarea></td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td colspan="4"><b>{lang_work_date}</b></td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_start_date}:</td>
		<td>{start_date_select}</td>
		<td>{lang_end_date}:</td>
		<td>{end_date_select}</td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td colspan="4"><b>{lang_work_time}</b></td>
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
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_hours}:</td>
		<td colspan="3">
			<input type="text" name="values[hours]" value="{hours}" size=3 maxlength=2>.
			<input type="text" name="values[minutes]" value="{minutes}" size=3 maxlength=2>&nbsp;[hh.mm]
		</td>
	</tr>
	<tr valign="bottom" height="50">
		<td><input type="submit" name="save" value="{lang_save}"></td>
		<td><input type="submit" name="cancel" value="{lang_cancel}"></td>
	</tr>
</form>
</table>
</center>
