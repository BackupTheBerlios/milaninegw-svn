<!-- BEGIN header -->
<form name=frm method="POST" action="{action_url}">
<table border="0" align="center">
   <tr bgcolor="{th_bg}">
    <td colspan="2" align="center"><font size="+1" color="{th_text}"><b>JiNN - {title}</b></font></td>
   </tr>
<!-- END header -->
<!-- BEGIN body -->
   <tr>
   <td colspan="2">&nbsp;</td>
   </tr>

	<tr bgcolor="{row_on}">
    <td colspan="2"><b>{lang_General_Settings}</b></td>
   </tr>

   <tr bgcolor="{row_off}">
   <td>{lang_Server_Type?}</td>
   <td>
   <select name="newsettings[server_type]">
   <option value="prod"{selected_server_type_prod}>{lang_Production} ({lang_Remote})</option>
   <option value="dev"{selected_server_type_dev}>{lang_Development} ({lang_Local})</option>
   </select>
   </td>
   </tr>

   <tr>
   <td colspan="2">&nbsp;</td>
   </tr>
   

   <tr bgcolor="{row_on}">
   <td colspan="2"><b>{lang_Image_settings}</b></td>
   </tr>
   <tr class="{row_off}">
   <td>{lang_Select_which_graphic_library_JiNN_must_use}</td>
   <td>
   <select name="newsettings[use_magick]">
   <option value="GDLIB" {selected_use_magick_GDLIB}>GDLib</option>
   <option value="MAGICK" {selected_use_magick_MAGICK}>ImageMagick</option>
   </select>
   </td>
   </tr>

	<tr bgcolor="{row_on}">
   <td>
   {lang_Notice_that_JiNN_needs_ImageMagick_5.4.9_or_a_later_version}
   <br/>{lang_Path_to_convert_from_ImageMagick_(_e.g._/usr/X11R6/bin_)}:</td>
   <td><input name="newsettings[imagemagickdir]" size="30" value="{value_imagemagickdir}"></td>
   </tr>
   <tr>
   <td colspan="2">&nbsp;</td>
   </tr>

<!-- END body -->
<!-- BEGIN footer -->
  <tr bgcolor="{th_bg} ">
    <td colspan="2" align="center">
      <input type="submit" name="submit" value="{lang_submit}">
      <input type="submit" name="cancel" value="{lang_cancel}">
    </td>
  </tr>
</table>
</form>
<!-- END footer -->
