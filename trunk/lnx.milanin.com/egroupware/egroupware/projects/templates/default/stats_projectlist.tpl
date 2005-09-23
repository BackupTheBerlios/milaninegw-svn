<!-- $Id: stats_projectlist.tpl,v 1.20 2004/06/01 10:38:39 lkneschke Exp $ -->

{app_header}

<center>{msg}</center>

<!-- BEGIN project_main -->

<table border="0" width="100%" cellpadding="2" cellspacing="0">
	<tr bgcolor="{th_bg}">
		<td colspan="4"><b>{lang_main}:&nbsp;<a href="{main_url}">{title_main}</a></b></td>
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

<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr>
		<td width="100%" colspan="8">
			<table border="0" width="100%">
				<tr>
				{left}
					<td align="center" width="100%">{lang_showing}</td>
				{right}
				</tr>
			</table>
		</td>
	</tr>
	<form method="POST" action="{action_url}">
	<tr>
		<td width="30%" align="left">{action_list}</td>
		<td width="20%" align="center"><select name="status" onChange="this.form.submit();">{status_list}</select></td>
		<td width="25%" align="center">{filter_list}</td>
		<td width="25%" align="right">{search_list}</td>
	</tr>
</table>
<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr bgcolor="{th_bg}">
		<td width="10%">{sort_number}</td>
		<td width="25%">{sort_title}</td>
		<td width="25%">{sort_coordinator}</td>
		<td width="10%" align="center">{sort_sdate}</td>
		<td width="10%" align="center">{sort_edate}</td>
		<td align="center" width="10%">{lang_gantt_chart}</td>
		<td width="10%" align="center"><img src="{user_img}" border="0" title="{lang_view_employees}"></td>
		<td width="16">&nbsp;</td>
	</tr>

<!-- BEGIN projects_list -->

	<tr bgcolor="{tr_color}">
		<td>{number}</td>
		<td><a href="{projects_url}">{title}</a></td>
		<td>{coordinator}</td>
		<td align="center">{sdate}</td>
		<td align="center">{edate}</td>
		<td align="center"><input type="checkbox" name="values[gantt_id][{project_id}]" value="{project_id}"></td>
		<td align="center"><input type="checkbox" name="values[project_id][{project_id}]" value="{project_id}" {radio_user_checked}></td>
		<td><a href="{view_url}"><img src="{view_img}" border="0" title="{lang_view}"></a></td>
	</tr>

	{employee_list}

<!-- END projects_list -->
                                                                                                                                     
	<tr height="50" valign="bottom">
		<td colspan="5"><input type="submit" name="userstats" value="{lang_userstats}"></td>
		<td align="center"><input type="submit" name="viewgantt" value="{lang_view_gantt}"></td>
		<td align="center"><input type="submit" name="viewuser" value="{lang_view_users}"></td>
		<td width="16">&nbsp;</td>
	</tr>
</form>
</table>
</center>

<!-- BEGIN user_cols -->

	<tr>
		<td>&nbsp;</td>
		<td bgcolor="{th_bg}">{lang_name}</td>
		<td bgcolor="{th_bg}">{lang_role}</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>

<!-- BEGIN user_list -->

	<tr>
		<td>&nbsp;</td>
		<td bgcolor="{tr_color}">{emp_name}</td>
		<td bgcolor="{tr_color}">{emp_role}</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>

<!-- END user_list -->

	<tr height="15">
		<td>&nbsp;</td>
	</tr>

<!-- END user_cols -->
