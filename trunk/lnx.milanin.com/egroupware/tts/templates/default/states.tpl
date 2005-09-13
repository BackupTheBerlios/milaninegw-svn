<!-- $Id: states.tpl,v 1.2 2003/10/09 21:59:39 ralfbecker Exp $ -->
<!-- BEGIN states.tpl -->

<br>
<!-- BEGIN state_list -->
<table width="98%" cellspacing="1" cellpadding="3" border="0" align="center">
	<tr class="th">
		<td align="center">{tts_head_stateid}</td>
		<td>{tts_head_state}</td>
		<td>{tts_head_description}</td>
		<td align="center">{lang_edit}</td>
		<td align="center">{lang_delete}</td>
	</tr>
	{rows}
	<tr class="{row_class}">
		<td colspan=3>&nbsp;</td>
		<td align="center"><A HREF="{tts_stateadd_link}">[{lang_add}]</A></td>
		<td >&nbsp;</td>
	</tr>
</table>
<br>
<!-- END state_list -->

<!-- END states.tpl -->

<!-- BEGIN state_row -->
	<tr class="{row_class}">
		<td align="center">{state_id}</td>
		<td>{state_name}</td>
		<td>{state_description}</td>
		<td align="center"><A HREF="{tts_stateedit_link}">[{lang_edit}]</A></td>
		<td align="center"><A HREF="{tts_statedelete_link}">[{lang_delete}]</A></td>
	</tr>
<!-- END state_row -->
