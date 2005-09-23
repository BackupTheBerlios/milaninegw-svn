<!-- $Id: list_budget.tpl,v 1.8 2004/06/01 10:38:39 lkneschke Exp $ -->

{app_header}

<!-- BEGIN project_main -->

<table border="0" width="100%" cellpadding="2" cellspacing="0">
	<tr bgcolor="{th_bg}">
		<td colspan="7"><b>{lang_main}:&nbsp;<a href="{main_url}">{title_main}</a></b></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_number}:</td>
		<td colspan="2">{number_main}</td>
		<td>{lang_url}:</td>
		<td colspan="3"><a href="http://{url_main}" taget="_blank">{url_main}</a></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_coordinator}:</td>
		<td colspan="2">{coordinator_main}</td>
		<td>{lang_customer}:</td>
		<td colspan="3">{customer_main}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_budget}:&nbsp;{currency}</td>
		<td>{lang_planned}:</td>
		<td>{pbudget_main}</td>
		<td>{lang_used_total} {lang_plus_jobs}:</td>
		<td>{ubudget_main}</td>
		<td>{lang_available} {lang_plus_jobs}:</td>
		<td>{abudget_main}</td>
	</tr>
</table>

<!-- END project_main -->

<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr>
		<td colspan="7">
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
		<td width="25%">
			<form method="POST" action="{action_url}">
			{action_list}
			</form>
		</td>
		<td width="20%" align="center">
			<form method="POST" name="status" action="{action_url}">
				<select name="status" onChange="this.form.submit();">{status_list}</select>
			</form>
		</td>
		<td width="20%" align="center"><form method="POST" name="filter" action="{action_url}">{filter_list}</form></td>
		<td width="35%" align="right"><form method="POST" name="query" action="{action_url}">{search_list}</form></td>
	</tr>
</table>
<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr bgcolor="{th_bg}">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td colspan="4"><b>{currency}&nbsp;{lang_budget}</b></td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td width="10%" valign="top">{sort_number}</td>
		<td width="20%" valign="top">{sort_title}</td>
		<td width="5%" align="right" valign="top">{sort_planned}</td>
		<td width="20%">
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="3" align="center">{lang_used_total}</td>
				</tr>
				<tr align="right">
					<td width="35%">{lang_project}</td>
					<td width="65%">{lang_plus_jobs}</td>
				</tr>
			</table>
		</td>
		<td width="20%">
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="3" align="center">{lang_used_billable}</td>
				</tr>
				<tr align="right">
					<td width="35%">{lang_project}</td>
					<td width="65%">{lang_plus_jobs}</td>
				</tr>
			</table>
		</td>
		<td width="20%">
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2" align="center">{lang_available}</td>
				</tr>
				<tr align="right">
					<td width="35%">{lang_project}</td>
					<td width="65%"><nobr>{lang_plus_jobs}</nobr></td>
				</tr>
			</table>
		</td>
	</tr>

<!-- BEGIN projects_list -->

	<tr bgcolor="{tr_color}">
		<td>{number}</td>
		<td><a href="{sub_url}">{title}</a></td>
		<td align="right">{p_budget}</td>
        <td>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr align="right">
					<td width="35%">{u_budget}</td>
        			<td width="65%">{u_budget_jobs}</td>
				</tr>
			</table>
		</td>
		<td>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr align="right">
					<td width="35%">{b_budget}</td>
					<td width="65%">{b_budget_jobs}</td>
				</tr>
			</table>
		</td>
		<td>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr align="right">
        			<td width="35%">{a_budget}</td>
					<td width="65%">{a_budget_jobs}</td>
				</tr>
			</table>
		</td>
	</tr>

<!-- END projects_list -->

	<tr height="15">
		<td>&nbsp;</td>
	<tr>
	<tr bgcolor="{th_bg}">
		<td colspan="2"><b>{lang_sum_budget}:&nbsp;{currency}</b></td>
		<td align="right"><b>{sum_budget}</b></td>
		<td>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr align="right">
        			<td width="35%"><b>{sum_budget_used}</b></td>
					<td width="65%"><b>{sum_budget_jobs}</b></td>
				</tr>
			</table>
		</td>
		<td>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr align="right">
        			<td width="35%"><b>{sum_b_budget}</b></td>
					<td width="65%"><b>{sum_b_budget_jobs}</b></td>
				</tr>
			</table>
		</td>
		<td>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr align="right">
        			<td width="35%"><b>{sum_a_budget}</b></td>
					<td width="65%"><b>{sum_a_budget_jobs}</b></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</center>
