<!-- $Id: export_email_body.tpl,v 1.3 2004/06/13 13:33:10 lkneschke Exp $ -->

<!-- BEGIN body_html -->

<table border="0" width="100%" cellpadding="2" cellspacing="0">
	<tr bgcolor="{th_bg}">
		<td colspan="4"><b>{lang_main}:&nbsp;{title_main}</b></td>
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
<br>
{project_list}

<!-- END body_html -->

<!-- BEGIN body_text -->
	{lang_enable_html}
<!-- END body_text -->

