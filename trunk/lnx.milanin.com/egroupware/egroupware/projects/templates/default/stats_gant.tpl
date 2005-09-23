<!-- $Id: stats_gant.tpl,v 1.8 2004/06/01 10:38:39 lkneschke Exp $ -->

{app_header}

<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<form method="POST" action="{action_url}">
	<input type="hidden" name="project_id" value="{project_id}">
	<tr>
		<td width="20%">&nbsp;</td>
		<td>{lang_start_date}:&nbsp;{sdate_select}</td>
		<td>{lang_end_date}:&nbsp;{edate_select}</td>
		<td align="right"><input type="submit" name="show" value="{lang_show_chart}"></td>
		<td width="20%">&nbsp;</td>
	</tr>
	<tr>
		<td width="20%">&nbsp;</td>
		<td><input type="checkbox" id="show_milestones" name="show_milestones" value="true" {show_milestones_checked}><label for="show_milestones">{lang_show_milestones}</label></td>
		<td><input type="checkbox" id="show_resources" name="show_resources" value="true" {show_resources_checked}><label for="show_resources">{lang_show_resources}</label></td>
		<td align="right">&nbsp;</td>
		<td width="20%">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="5" align="center"><img src="{pix_src}" border="0"></td>
	</tr>
	</form>
</table>
