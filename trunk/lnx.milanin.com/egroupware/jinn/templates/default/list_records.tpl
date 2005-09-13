<!-- BEGIN header -->

	<table cellpadding="0" cellspacing="0" style="border:solid 1px #cccccc">
<tr>
	<td><form action="{menu_action}" method="post">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td><input type="submit" name="direction" value="<<"></td>
					<td><input type="submit" name="direction" value="<"></td>
					<td><input type="submit" name="direction" value=">"></td>
					<td><input type="submit" name="direction" value=">>">
						<input type="hidden" name="limit_start" value="{limit_start}">
						<input type="hidden" name="limit_stop" value="{limit_stop}">
						<input type="hidden" name="search" value="{search_string}">
						<input type="hidden" name="orderby" value="{orderby}">
					</td>
				</tr>
			</table>
		</form>
	</td>
	<td align="center" style="padding-left:20px;">
		<form action="{menu_action}" method="post">{search_for}&nbsp;<input type="text" size="8" name="search" value="{search_string}">
		<input type="submit" value="{search}">
		<input type="hidden" name="limit_start" value="0">
		<input type="hidden" name="limit_stop" value="30">
		</form>	
	</td>
</tr>
</table>

<script language="javascript" type="text/javascript">
function img_popup(img,pop_width,pop_height,attr)
{
options="width="+pop_width+",height="+pop_height+",location=no,menubar=no,directories=no,toolbar=no,scrollbars=yes,resizable=yes,status=no";
parent.window.open("{popuplink}&path="+img+"&attr="+attr, "pop", options);
}
</script>
<br/>

<div style="background-color:#ffffff;border:solid 1px #cccccc;">
<table border="0" cellspacing="1" cellpadding="0" align="center" width="100%" >
<tr><td style="font-size:12px;font-weight:bold;padding:2px;border-bottom:solid 1px #006699" align="left">{table_title}</td></tr>
</table>
<table border="0" cellspacing="1" cellpadding="0" width="100%" style="padding-bottom:3px;border-bottom:solid 1px #006699">
<tr>
<td bgcolor="{th_bg}" colspan="3" valign="top" style="width:45px;font-weight:bold;padding:3px 5px 3px 5px;">{lang_Actions}</td>
{colnames}
</tr>
<!-- END header -->

<!-- BEGIN column_name -->
<td bgcolor="{colhead_bg_color}" style="font-weight:bold;padding:3px;" align="center"><a href="{colhead_order_link}">{colhead_name}&nbsp;{colhead_order_by_img}</a></td>
<!-- END column_name -->

<!-- BEGIN column_field -->
<td bgcolor="{colfield_bg_color}" valign="top" style="padding:0px 2px 0px 2px">{colfield_value}</td>
<!-- END column_field -->

<!-- BEGIN row -->
<tr valign="top">

<td bgcolor="{colfield_bg_color}" align="left"><a title="{colfield_lang_view}" href="{colfield_view_link}"><img src="{colfield_view_img_src}" alt="{colfield_lang_view}" /></a></td>

<td bgcolor="{colfield_bg_color}" align="left"><a title="{colfield_lang_edit}" href="{colfield_edit_link}"><img src="{colfield_edit_img_src}" alt="{colfield_lang_edit}" /></a></td>

<td bgcolor="{colfield_bg_color}" align="left"><a title="{colfield_lang_delete}" href="{colfield_delete_link}" onClick="return window.confirm('{colfield_lang_confirm}')"><img src="{colfield_delete_img_src}" alt="{colfield_lang_delete}" /></a></td>

{colfields}

</tr>
<!-- END row -->

<!-- BEGIN empty_row -->
<tr><td colspan="{colspan}">{lang_no_records}</td></tr>		   
<!-- END empty_row -->

<!-- BEGIN footer --> 
</table>
</div>
<!-- END footer -->
