<!-- BEGIN addressbook_header -->
<script>
function check_all(which)
{
  for (i=0; i<document.addr_index.elements.length; i++)
  {
    if (document.addr_index.elements[i].type == "checkbox" && document.addr_index.elements[i].name.substring(0,which.length) == which)
    {
      if (document.addr_index.elements[i].checked)
      {
        document.addr_index.elements[i].checked = false;
      }
      else
      {
        document.addr_index.elements[i].checked = true;
      }
    } 
  }
}
</script>
<div align="center">
{lang_showing}
<br>{searchreturn}
{search_filter}
<table class="calDayViewSideBoxes" width="100%" >
<tr><td>{alphalinks}</td>
</tr>
</table>
<table height=5><tr><td></td></tr></table>
<table class="calDayViewShadowBox" width="100%">
<tr>
<td>
<table width="100%" class="calDayViewSideBoxes">
<form name="addr_index" action="{action_url}" method="POST">
<tr bgcolor="{th_bg}">{cols}
  <td class="body">{lang_actions}
  &nbsp;<a href="javascript:check_all('select')"><img src="{check}" border="0" height="16" width="21" alt="{select_all}"></a></td>
</tr>
<!-- END addressbook_header -->

<!-- BEGIN column -->
   <td class="body" valign="top">{col_data}&nbsp;</td>
<!-- END column -->

<!-- BEGIN row -->
  <tr bgcolor="{row_tr_color}">{columns}
   <td valign="top" nowrap>{actions}</td>
  </tr>
<!-- END row -->

<!-- BEGIN delete_block -->
  <tr bgcolor="{row_tr_color}"><td colspan="{column_count}">&nbsp;</td>
   <td align="right"><input type="submit" name="Delete" value="{lang_delete}"></td>
  </tr>
<!-- END delete_block -->

<!-- BEGIN addressbook_footer -->{delete_button}
 </form>
 </table>
</td></tr></table>
 <table border="0" cellspacing="0" cellpadding="2">
	 <tr bgcolor="{th_bg}"> 
     <form action="{add_url}" method="post"><td><input type="submit" name="Add" value="{lang_add}" /></td></form>
     <form action="{vcard_url}"  method="post"><td><input type="submit" name="AddVcard" value="{lang_addvcard}" /></td></form>
     <form action="{import_url}" method="post"><td><input type="submit" name="Import" value="{lang_import}" /></td></form>
</tr>
 </table>
<table  border="0" cellspacing="0" cellpadding="2">
	<tr bgcolor="{th_bg}"> 

<form action="{import_alt_url}" method="post"><td><input type="submit" name="Import" value="{lang_import_alt}" /></td></form>
     <form action="{export_url}" method="post"><td><input type="submit" name="Export" value="{lang_export}" /></td></form>
   </tr>
 </table>
 </div>
<!-- END addressbook_footer -->

<!-- BEGIN addressbook_alpha --><td bgcolor="{charbgcolor}" align="center"><a href="{charlink}"><font color="{charcolor}">{char}</a></font></td>
<!-- END addressbook_alpha -->
