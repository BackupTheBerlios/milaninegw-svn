<!-- BEGIN form -->
 <table width="400" border="0" align="center">
  <tr>
   <td colspan="2">
    <table width="100%" border="0">
     <tr>
		{match_left}
		<td align="center">{lang_showing}</td>
		{match_right}
     </tr>
	</table>
   </td>
  </tr>
  <tr bgcolor="{th_bg}">
   <td>{sort_title}</td>
   <td width="50">{lang_actions}</td>
  </tr>
  
  {rows}

 </table>
 
 <form method="POST" action="{add_action}">
  <center><input type="submit" name="add" value="{lang_add}"></center>
 </form>
<!-- END form -->

<!-- BEGIN row -->
  <tr bgcolor="{tr_color}">
   <td>{row_title}</td>
   <td>{row_actions}</td>
  </tr>
<!-- END row -->
