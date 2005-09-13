<!-- BEGIN listheader -->
<table border="0" cellspacing="1" cellpadding="0" style="width:570px;background-color:#ffffff;border:solid 1px #cccccc;margin:3px 0px 3px 0px">
<tr><td style="font-size:12px;font-weight:bold;padding:2px;border-bottom:solid 1px #006699" align="left">{table_title}</td></tr>
</table>
<table border="0" cellspacing="1" cellpadding="0" style="width:570px;">

<tr style="font-weight:bold;padding:3px;">
		<td colspan="2" valign="top" style="background-color:{bgclr};font-weight:bold;padding:3px;"><input type="button" value="{lang_add_object}" onclick="document.location.href='{link_add_object}'" style="color:white;background-color:#006699"></td>
		{fieldnames}
	</tr>
<!-- END listheader -->

<!-- BEGIN rows -->
<tr valign="top">
<td style="background-color:{bgclr}" align="left"><a href="{link_edit}">{lang_edit}</a></td>
<td style="background-color:{bgclr}" align="left"><a href="{link_del}" onClick="return window.confirm('confirm_del');">{lang_del}</a></td>
{row}
</tr>
<!-- END rows -->

<!-- BEGIN listfooter -->
</table>
{msg}
<!-- END listfooter -->
