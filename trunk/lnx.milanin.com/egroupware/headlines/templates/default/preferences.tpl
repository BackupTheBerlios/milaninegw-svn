<!-- BEGIN form -->

<br>
 <form method="POST" action="{form_action}">
  <table align="center">
   <tr bgcolor="{th_bg}">
    <td align="center" nobreak>&nbsp;{lang_header}&nbsp;</td>
   </tr>

   <tr>
    <td align="center" bgcolor="{tr_color_1}">
     <select name="headlines[]" multiple size="15">
      {select_options}
     </select>
    </td>
   </tr>

   <tr bgcolor="{tr_color_2}">
    <td align="center">
     <input type="submit" name="save" value="{lang_save}"> &nbsp;
     <input type="submit" name="cancel" value="{lang_cancel}">
    </td>
   </tr>
  </table>
 </form>
 
<!-- END form -->
