<!-- BEGIN form -->
<br>

{messages}

<form method="POST" action="{form_action}">

<table border="0" width="95%" cellspacing="0" align="center">
	<tr class="th">
		<td colspan="2">&nbsp;</td>
	</tr>

	<tr class="row_on">
		<td width="20%">{lang_transition_name}:</td>
		<td><input name="transition[name]" value="{value_name}"></td>
	</tr>

	<tr class="row_off">
		<td>{lang_transition_description}:</td>
		<td><textarea rows="10" name="transition[description]" cols="65" wrap="hard">{value_description}</textarea></td>
	</tr>


	<tr class="row_on">
		<td>{lang_source_state}:</td>
		<td><select name="transition[source_state]">{options_source_state}</select></b></td>
	</tr>

	<tr class="row_off">
		<td>{lang_target_state}:</td>
		<td><select name="transition[target_state]">{options_target_state}</select></b></td>
	</tr>

	<tr height="40">
		<td>
			<input type="submit" name="save" value="{lang_save}"> &nbsp;
			<input type="submit" name="cancel" value="{lang_cancel}">
		</td>
	</tr>
</table>
<br>


<!-- END form -->
