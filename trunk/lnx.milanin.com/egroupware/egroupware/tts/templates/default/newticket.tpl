<!-- BEGIN options_select -->
    <option value="{optionvalue}" {optionselected}>{optionname}</option>
<!-- END options_select -->

<!-- BEGIN form -->
<br>
{messages}

<form method="POST" action="{form_action}">

<table border="0" width="80%" cellspacing="0" align="center">
	<tr class="th">
		<td colspan="4">&nbsp;</td>
	</tr>

	<tr class="row_off">
		<td align="left">{lang_assignedto}:</td>
		<td align="left">{value_assignedto}</td>
		<td align="left">{lang_billable_hours}:</td>
		<td align="left"><input name="ticket_billable_hours" value="{value_billable_hours}"></td>
	</tr>

	<tr class="row_on">
		<td align="left">{lang_priority}:</td>
		<td align="left">{value_priority}</td>
		<td align="left">{lang_billable_hours_rate}:</td>
		<td align="left"><input name="ticket_billable_rate" value="{value_billable_hours_rate}"></td>
	</tr>

	<tr class="row_off">
		<td align="left">{lang_group}:</td>
		<td align="left"><select name="ticket_group">{options_group}</select></b></td>
		<td align="left">{lang_category}:</td>
		<td align="left">{value_category}</td>
	</tr>

	<tr class="row_on">
		<td align="left">{lang_initialstate}:</td>
		<td align="left"><select name="ticket_state">{options_state}</select></b></td>
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
	</tr>

	<tr class="row_off">
		<td>{lang_subject}:</td>
		<td colspan="3"><input name="ticket_subject" value="{value_subject}" size="65"></td>
	</tr>

	<tr class="row_on">
		<td>{lang_details}:</td>
		<td colspan="3"><textarea rows="10" name="ticket_details" cols="65" wrap="hard">{value_details}</textarea></td>
	</tr>

	<tr>
 		<td colspan="2" align="left" height="40">
 			<input type="submit" name="submit" value="{lang_submit}"> &nbsp;
 			<input type="submit" name="cancel" value="{lang_cancel}">
 		</td>
	</tr>
</table>

</form>
<!-- END form -->
