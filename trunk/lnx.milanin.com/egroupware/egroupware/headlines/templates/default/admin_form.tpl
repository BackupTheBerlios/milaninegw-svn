<!-- BEGIN form -->
<br>

  <center>{messages}</center>

  <form method="POST" action="{action_url}">
   <table border="0" width="65%" align="center">
    <tr bgcolor="{th_bg}">
     <td colspan="2">&nbsp;{lang_header}</td>
    </tr>
    <tr bgcolor="{row_on}">
     <td>&nbsp;{lang_display}</td>
     <td>{input_display}</td>
    </tr>
    <tr bgcolor="{row_off}">
     <td>&nbsp;{lang_base_url}</td>
     <td>{input_base_url}</td>
    </tr>
    <tr bgcolor="{row_on}">
     <td>&nbsp;{lang_news_file}</td>
     <td>{input_news_file}</td>
    </tr>
    <tr bgcolor="{row_off}">
     <td>&nbsp;{lang_minutes}</td>
     <td>{input_minutes}</td>
    </tr>
    <tr bgcolor="{row_on}">
     <td>&nbsp;{lang_listings}</td>
     <td>{input_listings}</td>
    </tr>
    <tr bgcolor="{row_off}">
     <td>&nbsp;{lang_type}</td>
     <td>{input_type}</td>
    </tr>
    {buttons}
    <tr>
     <td colspan="2">&nbsp;</td>
    </tr>
    <tr bgcolor="{th_bg2}">
     <td colspan="2">&nbsp;{lang_current_cache}</td>
    </tr>
    {listing_rows}
    {cancel}
   </table>
  </form>

<!-- END form -->

<!-- BEGIN buttons -->
    <tr>
     <td colspan="2" align="left" height="40">
      <input type="submit" name="save" value="{lang_save}"> &nbsp;
      <input type="submit" name="cancel" value="{lang_cancel}">
     </td>
    </tr>
<!-- END buttons -->

<!-- BEGIN cancel -->
    <tr>
     <td colspan="2" height="40">
      <input type="submit" name="cancel" value="{lang_cancel}">
     </td>
    </tr>
<!-- END cancel -->

<!-- BEGIN listing_row -->
 <tr bgcolor="{tr_color}">
  <td colspan="2">{value}&nbsp;</td>
 </tr> 
<!-- END listing_row -->
