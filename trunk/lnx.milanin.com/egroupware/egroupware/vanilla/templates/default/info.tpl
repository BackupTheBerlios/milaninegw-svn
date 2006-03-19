<!-- BEGIN discussions -->
<table border="0" width="100%">
 <tr bgcolor="{th_bg}">
  <td>{board_link}</td><td>{config_link}</td>
 </tr>
 <tr bgcolor="{th_bg}">
  <td>{lang_latest_discussions}</td>
  <td>{lang_popular_discussions}</td>
 </tr>
 <tr>
  <td valign="top"><table cellpadding="2px">{latest_discussions}</table></td>
  <td valign="top"><table cellpadding="2px">{popular_discussions}</table></td>
 <tr>
  <td colspan="2">
  <pre>
{debug}
  </pre>
  </td>
 </tr>
</table>
<!-- END discussions -->

<!-- BEGIN latest_discussion -->
<tr class="{row_class}"><td>{ld_name}</td><td>{last_active}</td></tr></td>
<!-- END latest_discussion -->

<!-- BEGIN popular_discussion -->
<tr class="{row_class}"><td>{pd_name}</td><td>{comments_count}</td></tr></td>
<!-- END popular_discussion -->