<!-- $Id: list_pro_hours.tpl,v 1.3 2004/06/01 10:38:39 lkneschke Exp $ -->

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
		<td>{lang_used_total}{lang_plus_jobs}:</td>
		<td>{utime_main}</td>
		<td>{lang_available}{lang_plus_jobs}:</td>
		<td>{atime_main}</td>
	</tr>
</table>

<!-- END project_main -->

<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr width="100%">
		<td colspan="6" width="100%">
			<table border="0" width="100%">
				<tr width="100%">
				{left}
					<td align="center">{lang_showing}</td>
				{right}
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width="25%" align="left"><form method="POST" action="{action_url}">{action_list}</form></td>
		<td  width="20%" align="center">
			<form method="POST" name="status" action="{action_url}">
				<select name="status" onChange="this.form.submit();">{status_list}</select>
			</form>
		</td>
		<td width="15%" align="center"><form method="POST" name="filter" action="{action_url}">{filter_list}</form></td>
		<td width="40%" align="right"><nobr><form method="POST" name="query" action="{action_url}">{search_list}</nobr></form></td>
	</tr>
</table>
<table border="0" width="100%" cellpadding="2" cellspacing="2" align="center">
	<tr bgcolor="{th_bg}">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td colspan="5"><b>{lang_hours}</b></td>
	</tr>
	<tr bgcolor="{th_bg}" valign="top">
		<td width="10%">{sort_number}</td>
		<td width="25%">{sort_title}</td>
		<td width="5%" align="right">{sort_planned}</td>
        <td width="15%">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td colspan="2" align="center">{lang_used_billable}</td>
				</tr>
				<tr align="right">
					<td width="50%">{lang_project}</td>
					<td width="50%"><nobr>{lang_plus_jobs}</nobr></td>
				</tr>
			</table>
		</td>
        <td width="15%">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td colspan="2" align="center">{lang_used_not_billable}</td>
				</tr>
				<tr align="right">
					<td width="50%">{lang_project}</td>
					<td width="50%"><nobr>{lang_plus_jobs}</nobr></td>
				</tr>
			</table>
		</td>
		<td width="15%">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td colspan="2" align="center">{lang_used_total}</td>
				</tr>
				<tr align="right">
					<td width="50%">{lang_project}</td>
					<td width="50%"><nobr>{lang_plus_jobs}</nobr></td>
				</tr>
			</table>
		</td>
		<td width="15%">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td colspan="2" align="center">{lang_available}</td>
				</tr>
				<tr align="right">
					<td width="50%">{lang_project}</td>
					<td width="50%"><nobr>{lang_plus_jobs}</nobr></td>
				</tr>
			</table>
		</td>
	</tr>

<!-- BEGIN projects_list -->

	<tr bgcolor="{tr_color}">
		<td>{number}</td>
		<td><a href="{projects_url}">{title}</a></td>
		<td align="right">{phours}</td>
        <td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr align="right">
					<td width="50%">{uhours_pro_bill}</td>
					<td width="50%">{uhours_jobs_bill}</td>
				</tr>
			</table>
		</td>
        <td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr align="right">
					<td width="50%">{uhours_pro_nobill}</td>
					<td width="50%">{uhours_jobs_nobill}</td>
				</tr>
			</table>
		</td>
		<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr align="right">
					<td width="50%">{uhours_pro}</td>
					<td width="50%">{uhours_jobs}</td>
				</tr>
			</table>
		</td>
		<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr align="right">
					<td width="50%">{ahours_pro}</td>
					<td width="50%">{ahours_jobs}</td>
				</tr>
			</table>
		</td>
	</tr>

<!-- END projects_list -->

</table>
