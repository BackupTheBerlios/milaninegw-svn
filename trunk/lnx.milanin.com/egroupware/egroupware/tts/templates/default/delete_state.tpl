<!-- $Id: delete_state.tpl,v 1.3 2003/11/18 22:47:28 dragob Exp $ -->
<!-- BEGIN delete_state.tpl -->
<br>

{messages}

<!-- BEGIN tts_list -->
<P><B>{lang_are_you_sure}</B></P>
<P><B>{lang_tickets_in_state}</B></P>
<table width="98%" cellspacing="1" cellpadding="1" border="0" align="center">
	<tr class="th">
		<td width="22">&nbsp;</td>
		<td align="center">{tts_head_ticket}</td>
		<td align="center">{tts_head_subject}</td>
		<td align="center">{tts_head_state}</td>
		<td align="center">{tts_head_dateopened}</td>
		<td align="center">{tts_head_group}</td>
		<td align="center">{tts_head_category}</td>
		<td align="center">{tts_head_assignedto}</td>
		<td align="center">{tts_head_openedby}</td>
		<td align="center">{tts_head_status}</td>
	</tr>
	{rows}
</table>
<!-- END tts_list -->
<!-- BEGIN form -->
<b>{lang_viewjobdetails}</b>
<hr><p>

<center><font color=red>{messages}</font></center>

<form method="POST" action="{delete_state_link}">
<table border="0" width="80%" cellspacing="0" align="center">
	<tr bgcolor="{row_off}">
		<td><input name="ticket_state" type="radio" value="-100" CHECKED>{lang_delete_the_tickets}</td>
	</tr>
<!-- BEGIN update_state_items -->
	<tr bgcolor="{row_off}">
		<td><input name="ticket_state" type="radio" value="{update_state_value}">{update_state_text}</td>
	</tr>
<!-- END update_state_items -->

	<tr bgcolor="{row_off}">
		<td><input name="ticket_state" type="radio" value="-200">{lang_irregular_move_into_state}:&nbsp; &nbsp;<select name="ticket_newstate">{options_state}</select></b></td>
	</tr>

	<tr height="40">
		<td>
			<input type="submit" name="delete" value="{lang_delete}"> &nbsp;
			<input type="submit" name="cancel" value="{lang_cancel}">
		</td>
	</tr>

   </table><br>
</form>
<!-- END form -->

<!-- END delete_state.tpl -->

<!-- BEGIN tts_row -->
	<tr bgcolor="{tts_row_color}">
		<td width="22">{row_status}</td>
		<td align="center">{row_ticket_id}</td>
		<td align="center">{tts_t_subject}</td>
		<td align="center">{tts_t_state}</td>
		<td align="center">{tts_t_timestampopened}</td>
		<td style="font-size=12" align=center>{row_group}</td>
		<td style="font-size=12" align=center>{row_category}</td>
		<td align="center">{tts_t_assignedto}</td>
		<td align="center">{tts_t_user}</td>
		{tts_col_status}
	</tr>
<!-- END tts_row -->

<!-- BEGIN tts_col_ifviewall -->
  <td align=center>{tts_t_timestampclosed}</td>
<!-- END tts_col_ifviewall -->

<!-- BEGIN tts_head_ifviewall -->
    <td align=center>{tts_head_dateclosed}</td>
<!-- END tts_head_ifviewall -->
