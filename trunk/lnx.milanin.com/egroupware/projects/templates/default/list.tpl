<!-- $Id: list.tpl,v 1.38.2.1 2004/11/06 12:15:58 ralfbecker Exp $ -->

{app_header}

<!-- BEGIN project_main -->

<table border="0" width="100%" cellpadding="2" cellspacing="0">
	<tr bgcolor="{th_bg}">
		<td colspan="3"><b>{lang_main}:&nbsp;<a href="{main_url}">{title_main}</a></b></td>
		<td style="text-align:right;">{lang_export_as}:&nbsp;<a href="javascript:displayPDF('{url_export_pdf}')">PDF</a>&nbsp;<a href="{url_export_email}">EMail</a></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_number}:</td>
		<td>{number_main}</td>
		<td>{lang_url}:</td>
		<td><a href="http://{url_main}" taget="_blank">{url_main}</a></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_coordinator}:</td>
		<td>{coordinator_main}</td>
		<td>{lang_customer}:</td>
		<td>{customer_main}</td>
	</tr>
</table>

<!-- END project_main -->

<center>{message}</center>
<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr width="100%">
		<td colspan="11" width="100%">
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
		<td width="20%" align="center">
			<form method="POST" name="status" action="{action_url}">
				<select name="status" onChange="this.form.submit();">{status_list}</select>
			</form>
		</td>
		<td width="20%" align="center"><form method="POST" name="filter" action="{action_url}">{filter_list}</form></td>
		<td width="35%" align="right"><nobr><form method="POST" name="query" action="{action_url}">{search_list}</nobr></form></td>
	</tr>
</table>
<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr bgcolor="{th_bg}">
		<td>{sort_title}</td>

		<!-- BEGIN pro_sort_cols -->

		<td align="{col_align}">{sort_column}</td>

		<!-- END pro_sort_cols -->

		<td width="16" align="center" colspan="3">&nbsp;</td>
	</tr>

<!-- BEGIN projects_list -->

	<tr bgcolor="{tr_color}">
		<td valign="top"><a href="{projects_url}">{title}</a></td>

		{pro_column}

		<td align="center" valign="top"><a href="{add_job_url}">{add_job_img}</a></td>
		<td align="center" valign="top"><a href="{view_url}"><img src="{view_img}" title="{lang_view}" border="0"></a></td>
		<td align="center" valign="top"><a href="{edit_url}">{edit_img}</a></td>
	</tr>

<!-- END projects_list -->

	<tr valign="bottom">
		<td height="50">{add}</td>
	</tr>
</table>

<!-- BEGIN pro_cols -->

		<td align="{col_align}">{column}</td>

<!-- END pro_cols -->
