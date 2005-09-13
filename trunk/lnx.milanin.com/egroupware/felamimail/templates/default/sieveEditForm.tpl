<!-- BEGIN main -->
<script language="JavaScript" type="text/javascript">

function SubmitForm(a)
{
    if (a == 'delete'){
	if (!confirm("Are you sure you want to delete this rule?")){
                return true;
        }
    }
    document.thisRule.submit();
}

</script>
<form ACTION="{action_url}" METHOD="post" NAME="thisRule">

<table WIDTH="100%" CELLPADDING="2" CELLSPACING="1" style="border: 1px solid silver;">
	<tr CLASS="th">
		<td>
						{lang_edit_rule}      
		</td>
	</tr>
	<tr CLASS="sieveRowActive">
		<td>
			<input TYPE="checkbox" NAME="continue" id="continue" VALUE="continue" {continue_checked}><label for="continue">Check message against next rule also</label><br>
			<input TYPE="checkbox" NAME="keep" id="keep" VALUE="keep" {keep_checked}><label for="keep">Keep a copy of the message in your Inbox</label><br>
			<input TYPE="checkbox" NAME="regexp" id="regexp" VALUE="regexp" {regexp_checked}><label for="regexp">Use regular expressions</label>
		</td>
	</tr>
	<tr>
		<td>
			&nbsp;
		</td>
	<tr>
	<tr>
		<td>
			<table WIDTH="100%" CELLPADDING="2" CELLSPACING="1" style="border: 1px solid silver;">
				<tr CLASS="th">
					<td colspan="2">
						CONDITIONS:
					</td>
				</tr>
				<tr CLASS="sieveRowActive">
					<td NOWRAP="nowrap" rowspan="5">
						Match
						<select class="input_text" NAME="anyof">
							<option VALUE="0" {anyof_selected0}> all of
							<option VALUE="1" {anyof_selected4}> any of
						</select>
					</td>
					<td NOWRAP="nowrap">
						If message 'From:' contains: <input class="input_text" TYPE="text" NAME="from" SIZE="50" value="{value_from}">
					</td>
				</tr>
				<tr CLASS="sieveRowActive">
					<td>
						If message 'To:' contains: <input class="input_text" TYPE="text" NAME="to" SIZE="50" value="{value_to}">
					</td>
				</tr>
				<tr CLASS="sieveRowActive">
					<td>
						If message 'Subject:' contains: <input class="input_text" TYPE="text" NAME="subject" SIZE="50" value="{value_subject}">
					</td>
				</tr>
				<tr CLASS="sieveRowActive">
					<td>
						If message size is
						<select class="input_text" NAME="gthan">
							<option VALUE="0" {gthan_selected0}> less than
							<option VALUE="1" {gthan_selected2}> greater than
						</select>
						<input class="input_text" TYPE="text" NAME="size" SIZE="5" value="{value_size}"> KiloBytes
					</td>
				</tr>
				<tr CLASS="sieveRowActive">
					<td>
						If mail header: 
						<input class="input_text" TYPE="text" NAME="field" SIZE="20" value="{value_field}"> 
						contains: 
						<input class="input_text" TYPE="text" NAME="field_val" SIZE="30" value="{value_field_val}">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td>
			<table WIDTH="100%" CELLPADDING="2" CELLSPACING="1" style="border: 1px solid silver;">
				<tr CLASS="th">
					<td colspan="2">
						ACTIONS:
					</td>
				</tr>
				<tr CLASS="sieveRowActive">
					<td>
						<input TYPE="radio" NAME="action" VALUE="folder" id="action_folder" {checked_action_folder}> File Into:
					</td>
					<td>
						<select class="input_text" NAME="folder" onchange="document.getElementById('action_folder').checked = true;">
							{folder_rows}
						</select>
					</td>
				</tr>
				<tr CLASS="sieveRowActive">
					<td>
						<input TYPE="radio" NAME="action" VALUE="address" id="action_address" {checked_action_address}> Forward to address:
					</td>
					<td>
						<input class="input_text" TYPE="text" NAME="address" onchange="document.getElementById('action_address').checked = true;" SIZE="40" value="{value_address}">
					</td>
				</tr>
				<tr CLASS="sieveRowActive">
					<td>
						<input TYPE="radio" NAME="action" VALUE="reject" id="action_reject" {checked_action_reject}> Send a reject message:
					</td>
					<td>
						<textarea class="input_text" NAME="reject" onchange="document.getElementById('action_reject').checked = true;" ROWS="3" COLS="40" WRAP="hard" TABINDEX="14">{value_reject}</textarea>
					</td>
				</tr>
				<tr CLASS="sieveRowActive">
					<td>
						<input TYPE="radio" NAME="action" VALUE="discard" {checked_action_discard}> Discard the message.
					</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td>
			<table WIDTH="100%" CELLPADDING="2" BORDER="0" CELLSPACING="0">
				<tr>
					<td>
						<a href="{url_back}">{lang_back}</a>
					</td>
					<td CLASS="options" style="text-align : right;">
						<a CLASS="option" HREF="javascript:SubmitForm('save');" onmouseover="window.status='Save Changes';" onmouseout="window.status='';">Save Changes</a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<input type="hidden" name="ruleID" value="{value_ruleID}">
</form>
<!-- END main -->

<!-- BEGIN folder -->
							<option VALUE="{folderName}">{folderDisplayName}</option>
<!-- END folder -->
	