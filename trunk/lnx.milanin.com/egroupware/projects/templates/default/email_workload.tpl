<!-- $Id: email_workload.tpl,v 1.1 2004/06/14 00:19:03 lkneschke Exp $ -->

<!-- BEGIN body_html -->

<table border="0" width="100%" cellpadding="2" cellspacing="0">
	<tr bgcolor="{th_bg}">
		<td><b>{lang_workload_warning_for}&nbsp;{lang_project_name}({lang_project_description})</b></td>
	</tr>
	<tr>
		<td>
			&nbsp;
		</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{employee} {lang_is_schedules_for}!</td>
	</tr>

</table>
<br>

<!-- END body_html -->

<!-- BEGIN body_text -->
	{lang_workload_warning_for} {lang_project_name}({lang_project_description})

	{employee} {lang_is_schedules_for} {lang_hours}.
<!-- END body_text -->

