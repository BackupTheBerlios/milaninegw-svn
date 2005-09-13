<!-- $Id: transitions.tpl,v 1.2 2003/10/09 21:59:39 ralfbecker Exp $ -->
<!-- BEGIN transitions.tpl -->

<br>

<!-- BEGIN transition_list -->
<table width="98%" cellspacing="1" cellpadding="1" border="0" align="center">
	<tr class="th">
		<td align="center">{tts_head_transition_id}</td>
		<td>{tts_head_transition}</td>
		<td>{tts_head_source_state}</td>
		<td>{tts_head_target_state}</td>
		<td>{tts_head_description}</td>
		<td align="center">{lang_edit}</td>
		<td align="center">{lang_delete}</td>
	</tr>
	{rows}
	<tr class="{row_class}">
		<td colspan="5">&nbsp;</td>
		<td align="center"><A HREF="{tts_transitionadd_link}">[{lang_add}]</A></td>
		<td align="center">&nbsp;</td>
	</tr>
</table>
<br>
<!-- END transition_list -->

<!-- END transitions.tpl -->

<!-- BEGIN transition_row -->
	<tr class="{row_class}">
		<td align="center">{transition_id}</td>
		<td>{transition_name}</td>
		<td>{transition_source_state}</td>
		<td>{transition_target_state}</td>
		<td>{transition_description}</td>
		<td align="center"><A HREF="{tts_transitionedit_link}">[{lang_edit}]</A></td>
		<td align="center"><A HREF="{tts_transitiondelete_link}">[{lang_delete}]</A></td>
	</tr>
<!-- END transition_row -->
