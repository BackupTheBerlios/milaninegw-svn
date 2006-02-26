<!-- BEGIN header -->
<form method="POST" action="{action_url}">
<table border="0" align="center">
   <tr class="th">
	   <td colspan="2"><font color="{th_text}">&nbsp;<b>{title}</b></font></td>
   </tr>
   <tr bgcolor="{th_err}">
    <td colspan="2">&nbsp;<b>{error}</b></font></td>
   </tr>
<!-- END header -->
<!-- BEGIN body -->
   <tr class="th">
    <td colspan="2">&nbsp;<b>{lang_Messenger}</b></font></td>
   </tr>
   <tr class="row_off">
    <td>{lang_Use_select_box_for_user_list_in_compose}.</td>
    <td>
     <select name="newsettings[use_selectbox]">
      <option value="" {selected_use_selectbox_False}>{lang_No}</option>
      <option value="True" {selected_use_selectbox_True}>{lang_Yes}</option>
     </select>
    </td>
   </tr>
<!-- END body -->

<!-- BEGIN footer -->
  <tr class="th">
    <td colspan="2">
&nbsp;
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <input type="submit" name="submit" value="{lang_submit}">
      <input type="submit" name="cancel" value="{lang_cancel}">
    </td>
  </tr>
</table>
</form>
<!-- END footer -->
