<!-- $Id: hours_listhours.tpl,v 1.24.2.1 2004/11/06 12:15:58 ralfbecker Exp $ -->

{app_header}

<!-- BEGIN project_main -->

<table border="0" width="100%" cellpadding="2" cellspacing="0">
	<tr bgcolor="{th_bg}">
		<td colspan="7"><b>{lang_main}:&nbsp;<a href="{main_url}">{title_main}</a></b></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_number}:</td>
		<td>{number_main}</td>
		<td>{lang_url}:</td>
		<td colspan="4"><a href="http://{url_main}" taget="_blank">{url_main}</a></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_coordinator}:</td>
		<td>{coordinator_main}</td>
		<td>{lang_customer}:</td>
		<td colspan="4">{customer_main}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_hours}:</td>
		<td>{lang_planned}:</td>
		<td>{ptime_main}</td>
		<td>{lang_used_total} {lang_plus_jobs}:</td>
		<td>{utime_main}</td>
		<td>{lang_available} {lang_plus_jobs}:</td>
		<td>{atime_main}</td>
	</tr>
</table>

<!-- END project_main -->

<center>{error}</center>

<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr width="100%">
		<td colspan="8" align="left" width="100%">
			<table boder="0" width="100%">
				<tr>
				{left}
					<td align="center">{lang_showing}</td>
				{right}
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width="25%">
			<form method="POST" action="{action_url}" name="project_id">
			<select name="project_id" onChange="this.form.submit();"><option value="">{lang_select_project}</option>{project_list}</select>
			</form>
		</td>
		<td width="20%" align="center"><form method="POST" name="filter" action="{action_url}">{filter_list}</form></td>
		<td align="right"><form method="POST" name="query" action="{action_url}">{search_list}</form></td>
	</tr>
</table>

<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr bgcolor="{th_bg}">
		<td width="25%">{sort_hours_descr}</td>
		<td>{lang_billable}</td>
		<td width="10%" align="center">{sort_status}</td>
		<td width="10%" align="center">{sort_start_date}</td>
		<td width="10%" align="center">{sort_start_time}</td>
		<td width="10%" align="center">{sort_end_time}</td>
		<td width="10%" align="right">{sort_hours}</td>
		<td width="20%">{sort_employee}</td>
		<td width="5%">&nbsp;</td>
	</tr>

<!-- BEGIN hours_list -->

	<tr bgcolor="{tr_color}">
		<td>{hours_descr}</td>
		<td align="center">{billable}</td>
		<td align="center">{status}</td>
		<td align="center">{start_date}</td>
		<td align="center">{start_time}</td>
		<td align="center">{end_time}</td>
		<td align="right">{wh}</td>
		<td>{employee}</td>
		<td align="center"><a href="{view_url}"><img src="{view_img}" border="0" title="{lang_view_hours}"></a></td>
	</tr>

<!-- END hours_list -->

	<tr height="5">
		<td>&nbsp;</td>
	</tr>
</table>
<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr bgcolor="{th_bg}">
		<td colspan="8">{lang_hours}:</td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td>{lang_planned}:</td>
		<td>{phours}</td>
		<td>{lang_used_total}:</td>
		<td>{uhours_pro}</td>
		<td>{lang_used_total} {lang_plus_jobs}:</td>
		<td>{uhours_jobs}</td>
		<td>{lang_available} {lang_plus_jobs}:</td>
		<td>{ahours_jobs}</td>
	</tr>

<!-- BEGINN add   -->

	<tr>
		<td valign="bottom" height="50">
			{action}</td>
	</tr>

<!-- END add -->

</table>
</center>
