<!-- $Id: editMilestones.tpl,v 1.1.2.1 2004/09/21 05:01:58 lkneschke Exp $ -->
<!-- BEGIN main -->
<form method="POST" action="{actionURL}">
<table border="0" cellspacing="0" cellpadding="2" width="100%">
	<tr class="th" valign="bottom">
		<td align="left" ><input type="text" name="values[title]" size="50" value="{title}"></td>
		<td align="right">{end_date_select}</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<textarea name="values[description]" rows="5" style="width:100%;">{description}</textarea>
		</td>
	</tr>
	<tr valign="bottom">
		<td align="left" colspan="1">
			<input type="submit" name="done" value="{lang_done}" onclick="window.close();">
			<INPUT type="hidden" name="old_edate" value="{old_edate}">
		</td>
		<td align="right" colspan="1"><input type="submit" name="save" value="{lang_save}"></td>
	</tr>
</table>
</form>
<!-- END main -->