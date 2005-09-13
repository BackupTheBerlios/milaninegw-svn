<!-- BEGIN poll -->
<table border="0" align="center" width="400"> 
  {titlebar}
  {votes}
  {show_total}
</table>
<!-- END poll -->

<!-- BEGIN title -->
<tr>
  <td colspan="4" bgcolor="{td_color}" align="left">{poll_title}</td>
</tr>
<!-- END title -->

<!-- BEGIN vote -->
 <tr bgcolor="{vote_color}">
  <td>{option_text}</td>
  <td>{poll_bar}</td>
  <td align="right">{percent}%</td>
  <td align="right">{option_count}</td>
 </tr>
<!-- END vote -->

<!-- BEGIN image -->
<img src="{server_url}/polls/images/pollbar.gif" height="12" width="{scale}">
<!-- END image -->

<!-- BEGIN total -->
 <tr bgcolor="{tr_bgcolor}">
  <td colspan="2">&nbsp;</td>
  <td colspan="2" align="right">{lang_total}: {sum}</td>
 </tr>
<!-- END total -->
