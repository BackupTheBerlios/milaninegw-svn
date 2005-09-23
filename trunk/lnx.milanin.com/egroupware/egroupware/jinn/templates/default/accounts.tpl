<!-- BEGIN list -->
<p>
 <div align="center">
  <table border="0" width="80%">
   <tr>
    <td align="left">{left_next_matchs}</td>
    <td align="center">{lang_user_accounts}</td>
    <td align="right">{right_next_matchs}</td>
   </tr>
  </table>
 </div>

 <div align="center">
  <table border="0" width="80%">
   <tr bgcolor="{th_bg}"><form method="POST" action="{actionurl}">
   <input type="hidden" name="site_id" value="{site_id}">
   <input type="hidden" name="object_id" value="{object_id}">
        {hidden_editors}
    <td>{lang_loginid}</td>
    <td>{lang_lastname}</td>
    <td>{lang_firstname}</td>
    <td>{lang_editor}</td>
   </tr>


   {rows}

  </table>
 </div>


  <div align="center">
   <table border="0" width="80%">
    <tr>
     <td align="left">
      {input_add}
      </form>
     </td>
     <td align="right">
      <form method="POST" action="{accounts_url}">
       {input_search}
      </form>
     </td>
    </tr>
   </table>
  </div>
<!-- END list -->

<!-- BEGIN row -->
   <tr bgcolor="{tr_color}">
    <td>{row_loginid}</td>
    <td>{row_lastname}</td>
    <td>{row_firstname}</td>
    <td width="5%">{row_editor}</td>
   </tr>
<!-- END row -->

<!-- BEGIN row_empty -->
   <tr>
    <td colspan="5" align="center">{message}</td>
   </tr>
<!-- END row_empty -->
