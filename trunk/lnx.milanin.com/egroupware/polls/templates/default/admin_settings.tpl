<!-- BEGIN settings -->
<form action="{form_action}" method="post">
<table border="0">
<tbody>
	<tr>
		<td>{lang_allowmultiple}</td>    
		<td><input type="checkbox" name="settings[allow_multiple_votes]" {check_allow_multiple_votes} value="true"></td>
	</tr>
	<tr>
		<td>{lang_selectpoll}</td>    
		<td>
			<select name="settings[currentpoll]">
				{poll_questions}
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" name="submit" value="{lang_submit}">&nbsp;<input type="submit" name="cancel" value="{lang_cancel}"></td>
	</tr>
</tbody>
</table>
</form>
<!-- END settings -->

