<!-- BEGIN form -->
<br>

{messages}

<form method="POST" action="{form_action}">

<table border="0" width="95%" cellspacing="0" align="center">
	<tr class="th">
		<td colspan="2">&nbsp;</td>
	</tr>

<!-- BEGIN autoid -->
	<tr class="row_on">
		<td colspan="2"><input name="state[autoid]" type="checkbox" CHECKED >{lang_auto_id}</td>
	</tr>
<!-- END autoid -->
	<tr class="row_off">
		<td>{lang_state_id}:</td>
		<td><input name="state[id]" value="{value_id}"></td>
	</tr>

	<tr class="row_on">
		<td>{lang_state_name}:</td>
		<td><input name="state[name]" value="{value_name}"></td>
	</tr>

	<tr class="row_off">
		<td>{lang_state_description}:</td>
		<td><textarea rows="10" name="state[description]" cols="65" wrap="hard">{value_description}</textarea></td>
	</tr>

	<tr class="row_on">
		<td colspan="2"><input type="checkbox" name="state[initial]" {value_initial}>{lang_new_ticket_into_state}</td>
	</tr>

	<tr height="40">
		<td colspan="2">
			<input type="submit" name="save" value="{lang_save}"> &nbsp;
			<input type="submit" name="cancel" value="{lang_cancel}">
		</td>
	</tr>
</table>

<!-- END form -->
