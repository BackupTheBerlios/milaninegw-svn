<br>

<form method="POST" action="{action_url}">

 <table border="0" cellpadding="0" cellspacing="0" width="60%" align="center">
  <tr align="center" bgcolor="{th_bg}">
   <td colspan="3" align="center">
    {template_label}:    
    <select name="headlines_layout" onChange="this.form.submit();">
     {template_options}
    </select>
   </td>
  </tr>

  <tr>
   <td colspan="4">&nbsp;</td>
  </tr>

  <tr>
   <td align="center">{layout_1}</td>
   <td align="center">{layout_2}</td>
   <td align="center">{layout_3}</td>
  </tr>

  <tr>
   <td colspan="2">&nbsp;</td>
  </tr>
<!--
   <tr>
    <td align="center" bgcolor="{tr_color_2}">
     <input type="checkbox" name="mainscreen"{mainscreen_checked}>{lang_mainscreen}
    </td>
   </tr>
-->
  <tr>
   <td align="center" colspan="3">
    <input type="submit" name="save" value="{save_label}"> &nbsp;
    <input type="submit" name="cancel" value="{cancel_label}">
   </td>
  </tr>
 </table>

</form>
