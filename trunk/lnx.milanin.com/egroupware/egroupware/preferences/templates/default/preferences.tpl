<br>
<center><b>{messages}</b></center>

<!-- BEGIN form -->
<form method="POST" action="{action_url}">

<table border="0">
 <tr>
  <td colspan="2">{tabs}</td>
 </tr>

<!-- BEGIN list -->
 <tr bgcolor="{th_bg}">
  <td colspan="2"><b>{list_header}</b></td>
 </tr>
{rows}
<!-- END list -->

 <tr height="30" valign="bottom">
  <td align="left">
   <input type="submit" name="submit" value="{lang_submit}"> &nbsp;
   <input type="submit" name="cancel" value="{lang_cancel}">
  </td>
  <td align="right">&nbsp; {help_button}</td>
 </tr>
</table>

</form>
<!-- END form -->

<!-- BEGIN row -->
 <tr bgcolor="{tr_color}">
  <td>{row_name}</td>
  <td>{row_value}</td>
 </tr>
<!-- END row -->

<!-- BEGIN help_row -->
  <tr bgcolor="{tr_color}">
  <td><b>{row_name}<b></td>
  <td>{row_value}</td>
 </tr>
 <tr bgcolor="{tr_color}">
  <td colspan="2">{help_value}</td>
 </tr>
<!-- END help_row -->
