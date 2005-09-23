<!-- start jinn main menu-->
<div>
<span style="position:relative;background-color:#aaaaaa;color:#ffffff;font-weight:bold;text-align:center;padding:0px 3px 0px 3px;">{jinn_main_menu}</span>
<table border="0" cellspacing="0" style="padding:5px;border:solid 1px #aaaaaa">
	<tr>
	<form method="POST" action="{main_form_action}">
	<input type="hidden" name="action" value="">
        <input type="hidden" name="form" value="main_menu">
	<input type="hidden" name="filter" value="none">
	<input type="hidden" name="qfield" value="">
	<input type="hidden" name="start" value="">
	<input type="hidden" name="order" value="">
	<input type="hidden" name="sort" value="">
	<input type="hidden" name="query" value="">

		<td align="center">{select_site}
			<select name="site_id" onChange="this.form.submit()">
			{site_options}
			</select>
			{admin_site_link}
	 	</td>
		<td align="center" style="padding-left:20px;">
			{select_object}
			<select name="site_object_id" onChange="this.form.submit()">
			{site_objects}
			</select>
			{admin_object_link}
		</td>
	</tr></form>
</table>
	<br>
</div>
<!-- end jinn main menu-->
