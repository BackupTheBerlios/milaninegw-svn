<!-- $Id: export_email.tpl,v 1.2 2004/06/01 10:38:39 lkneschke Exp $ -->

<!-- BEGIN project_main_mail -->

{app_header}

<form action="{url_action}" method="post">

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
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">Sent to:<input style="width:300px;" type="text" name="email_to"></td>
		<td style="text-align:right;"><input type="submit" value="{lang_send}" name="send_email"></td>
	</tr>
</table>

</form>

<!-- END project_main_mail -->

