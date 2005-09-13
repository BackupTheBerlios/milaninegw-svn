<!-- BEGIN main -->
<!-- $Id: main.tpl,v 1.5.2.1 2005/02/12 19:52:26 ralfbecker Exp $  -->
 <center>{misc_data}</center>

 <table border="0" width="95%" align="center" cellspacing="0" cellpadding="0">
  <tr bgcolor="{th_bg}">
   <td align="left"><b>FTP</b> - {ftp_location}</td>
   <td align="right">{relogin_link}</td>
  </tr>
  <tr>
   <td colspan="2" align="right">{nextmatchs_left}&nbsp;{nextmatchs_right}</td>
  </tr>
 </table>

<table border="0" width="95%" align="center">

 <tr>
  <td>


   <table border="0" cellpadding="1" cellspacing="1" width="95%" align="center">

    <tr bgcolor="{th_bg}">
     <td>{lang_name}</td>
     <td width="10%" align="center">{lang_owner}</td>
     <td width="10%" align="center">{lang_group}</td>
     <td width="7%" align="center">{lang_permissions}</td>
     <td width="7%" align="center">{lang_size}</td>
     <td width="10%" align="center">{lang_delete}</td>
     <td width="10%" align="center">{lang_rename}</td>
    </tr>

    {rowlist_dir}

    {rowlist_file}

   </table>


  </td>
 </tr>

 <tr width="95%">

  <td align="center"><font color="{em_text_color}">

  {ul_form_open}

  {ul_select}{ul_submit}

  {ul_form_close}
</font>
  </td>

 </tr>

 <tr width="95%">

  <td align="center"><font color="{em_text_color}">

   {crdir_form_open}

   {crdir_textfield}{crdir_submit}

   {crdir_form_close}
</font>
  </td>

 </tr>

</table>

<!-- END main -->

<!-- BEGIN row -->
 <tr bgcolor="{bgcolor}">
  <td>{name}&nbsp;</td>
  <td align="center">{owner}&nbsp;</td>
  <td align="center">{group}&nbsp;</td>
  <td align="center">{permissions}&nbsp;</td>
  <td align="right">{size}&nbsp;</td>
  <td align="center">{del_link}</td>
  <td align="center">{rename_link}</td>
 </tr>
<!-- END row -->
