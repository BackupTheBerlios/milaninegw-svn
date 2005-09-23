<!-- BEGIN header -->
<form method="post" name="frm" action="{form_action}" enctype="multipart/form-data" {form_attributes}>
{where_key_form}
{where_value_form}
<table align="" cellspacing="2" cellpadding="2" style="background-color:#ffffff;border:solid 1px #cccccc;">
<!-- END header -->

<!-- BEGIN rows -->
<tr><td bgcolor={row_color} valign="top">{fieldname}</td><td bgcolor={row_color}>{input}</td></tr>
<!-- END rows -->

<!-- BEGIN plugins_header -->
<tr><td bgcolor={prow_color} valign="top">{lang_field_configuration}</td><td bgcolor={prow_color}>
<div id="divPlugins">
<table style="background-color:#ffffff;border:solid 1px #cccccc;">
		<tr>
			<td>{lang_fields}</td><td>			
			{lang_field_plugin}</td><td>{lang_plugin_conf}</td><td>{lang_afc}</td>
		</tr>
<!-- END plugins_header -->

<!-- BEGIN plugins_row -->
<tr><td>{field_name}</td>
	<td>
{hidden_value}
		<select name="PLG{field_name}">
		{plg_options}
		</select>
	</td>
	<td>
	<input type="hidden" name="CFG_PLG{field_name}" value="{plg_conf}">
	<input type="button" onClick="{popup_onclick}" value="{lang_plugin_conf}">
</td>
<td>
		<input type="button" onClick="{popup_onclick_afc}" value="{lang_afc}">
	</td>
</tr>
<!-- END plugins_row -->

<!-- BEGIN plugins_footer -->
</table>
</div>
</td></tr>
<!-- END plugins_footer -->

<!-- BEGIN relations_header -->
<tr><td bgcolor={rrow_color} valign="top">{lang_relations}</td><td bgcolor={rrow_color}>{hidden_value}
<!-- END relations_header -->

<!-- BEGIN relation_defined1 -->
{type1_num}. {r1txt}<input type=checkbox name="DELrelation{total_num}" value="{relation}">{lang_delete}<br/><br/>
<!-- END relation_defined1 -->

<!-- BEGIN relation_defined2 -->
{type2_num}. {r2txt}<input type=checkbox name="DELrelation{total_num}" value="{relation}">{lang_delete}<br/><br/>
<!-- END relation_defined2 -->

<!-- BEGIN relation_defined3 -->
{type3_num}. {r3txt}<input type=checkbox name="DELrelation{total_num}" value="{relation}">{lang_delete}<br/><br/>

<!-- END relation_defined3 -->

<!-- BEGIN relations1 -->
<b>{lang_new_rel1}</b><br/>
<table>
	<tr>
		<td colspan="2">{lang_field}:<br/>
		<select name="1_relation_org_field">
		{rel1_options1}
		</select></td></tr>

		<tr><td>{lang_has_1rel}:<br/>
		<select name="1_relation_table_field">
		{rel1_options2}
		</select></td></tr>

		<tr><td colspan="2">{lang_displaying}:<br/>
		<select name="1_display_field">
		{rel1_options3}
		</select></td>
	</tr>
</table>
<br/>
<!-- END relations1 -->

<!-- BEGIN relations2 -->
<b>{lang_new_rel2}</b><br/>
<table>
	<tr><td colspan="2">
		{lang_the_id_of}:<br/>
		<select name="2_relation_via_primary_key">
		{rel2_options1}	
		</select></td></tr>

		<tr><td>{lang_has_rel2_with}:<br/>
		<select name="2_relation_foreign_key">
		{rel2_options2}
		</select></td></tr>

		<tr><td colspan="2">{lang_represented_by}<br/>
		<select name="2_relation-via-foreign-key">
		{rel2_options3}
		</select></td></tr>

		<tr><td>{lang_showing}:<br>
		<select name="2_display_field">
		{rel2_options4}
		</select></td></tr>
</table>
<!-- END relations2 -->

<!-- BEGIN relations3 -->
<b>{lang_new_rel3}</b><br/>
<table>
	<tr>
		<td colspan="2">{lang_field}:<br/>
		<select name="3_relation_org_field">
		{rel3_options1}
		</select></td></tr>

		<tr><td>{lang_has_3rel}:<br/>
		<select name="3_relation_table_field">
		{rel3_options2}
		</select></td></tr>

		<tr><td colspan="2">{lang_object_conf}:<br/>
		<select name="3_relation_object_conf">
		{rel3_options3}
		</select></td>
	</tr>
</table>
<br/>
<!-- END relations3 -->

<!-- BEGIN relations_footer -->
</td></tr>
<!-- END relations_footer -->

<!-- BEGIN footer -->
</tr>

<td align="center" colspan="2">
	<input type="submit" name="continue" value="{save_and_continue_button}" />
	<input type="submit" name="add" value="{save_button}" />
	<input type="button" onClick="location='{cancel_link}'" value="{cancel_text}" />
	<input type="button" onClick="if(window.confirm('{confirm_del}'))location='{link_delete}'" value="{lang_delete}" />
</table>
</form>
<!-- END footer -->
