<!-- BEGIN header -->
{save_messages}
<form method="POST" action="?menuaction=vanilla.uivanilla.save_config">
<table border="0" align="center">

<!-- END header -->


<!-- BEGIN cat_watchers -->
<tr><td valign="top">
<div style="border: 1px solid">
<table border="0" align="center">
<tr>
  <th>{lang_cat_watchers}</th>
  <td>{lang_yes}</td>
  <td>{lang_no}</td>
</tr>
{cat_watchers_list}
</table>
</div>
</td>
<!-- END cat_watchers -->

<!-- BEGIN disc_watchers -->
<td valign="top">
<div style="border: 1px solid">
<table border="0" align="center">
<tr>
  <th>{lang_disc_watchers}</th>
  <td>{lang_remove}</td>
</tr>
{disc_watchers_list}
</table>
</div>
</td>
</tr>
<!-- END disc_watchers -->

<!-- BEGIN cat_watcher -->
  <tr class="{row_class}">
    <td>{cat_name}</td>
    <td>{cat_watch_yes}</td>
    <td>{cat_watch_no}</td>
  </tr>
<!-- END cat_watcher -->

<!-- BEGIN disc_watcher -->
  <tr class="{row_class}">
    <td>{disc_name}</td>
    <td>{disc_watch_remove}</td>
  </tr>
<!-- END disc_watcher -->

<!-- BEGIN body -->

<!-- END body -->


<!-- BEGIN footer -->
<tr><td colspan="2" align="right"><input type="submit" value="{lang_save}"/></td></tr>
</table>
</form>
<!-- END footer -->
