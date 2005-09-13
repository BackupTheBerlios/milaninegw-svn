<!-- $Id: stats_projectstat.tpl,v 1.19 2004/06/01 10:38:39 lkneschke Exp $ -->

{app_header}

<center>
{pref_message}
<form method="POST" name="projects_form" action="{actionurl}">
<table width="75%" border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td>{lang_project}:</td>
		<td>{project}</td>
	</tr>
	<tr>
		<td>{lang_status}:</td>
		<td>{status}</td>
	</tr>
	<tr>
		<td>{lang_budget}:</td>
		<td>{budget}</td>
	</tr>
	<tr>
		<td>{lang_start_date}:</td>
		<td>{start_date_select}</td>
	</tr>
	<tr>
		<td>{lang_end_date}:</td>
		<td>{end_date_select}</td>
	</tr>
	<tr>
		<td>{lang_coordinator}:</td>
		<td>{coordinator}</td>
	</tr>
	<tr>
		<td>{lang_customer}:</td>
		<td>{customer}</td>
	</tr>
	<tr>
		<td>{lang_billedonly}:</td>
		<td>{billed}</td>
	</tr>
</table>
<table width="75%" border="0" cellspacing="2" cellpadding="2">
	<tr valign="bottom">
		<td height="50">
			<input type="submit" name="submit" value="{lang_calculate}">
		</td>
	</tr>
</table>
</form>
<table width="85%" border="0" cellspacing="2" cellpadding="2">
	<tr bgcolor="{th_bg}">
		<td width="10%" bgcolor="{th_bg}">{lang_employee}</td>
		<td width="10%" bgcolor="{th_bg}">{lang_activity}</td>
		<td width="10%" align="right" bgcolor="{th_bg}">{lang_hours}</td>
	</tr>

<!-- BEGIN stat_list -->

	<tr bgcolor="{tr_color}">
		<td>{e_account}</td>
		<td>{e_activity}</td>
		<td align="right">{e_hours}</td>
	</tr>

<!-- END stat_list -->

</table>
</center>
