<!-- BEGIN admin.tpl -->
<br>
   <form method="POST" action="{action_url}">
   <table border="0" align="center" cellspacing="1" cellpadding="1">
    <tr bgcolor="#EEEEEE">
     <td>{lang_ownernotification}</td>
     <td><input type="checkbox" name="ownernotification"{ownernotification}></td>
    </tr>
    <tr bgcolor="#EEEEEE">
     <td>{lang_groupnotification}</td>
     <td><input type="checkbox" name="groupnotification"{groupnotification}></td>
    </tr>
    <tr bgcolor="#EEEEEE">
     <td>{lang_assignednotification}</td>
     <td><input type="checkbox" name="assignednotification"{assignednotification}></td>
    </tr>
    <tr>
      <td colspan="3" align="center" height="40">
       <input type="submit" name="submit" value="{lang_submit}"> &nbsp;
       <input type="submit" name="cancel" value="{lang_cancel}">
     </td>
    </tr>
   </table>
   </form>
