<!-- BEGIN manageheader -->

<script language="JavaScript" type="text/javascript">
<!--
  {js_default_db_ports}
  function setDefaultDBPort(selectBox,portField)
  {
	//alert("select: " + selectBox + "; portField: " + portField);
    if(selectBox.selectedIndex != -1 && selectBox.options[selectBox.selectedIndex].value)
	{
		//alert("value = " + selectBox.options[selectBox.selectedIndex].value);
		portField.value = default_db_ports[selectBox.options[selectBox.selectedIndex].value];
	}
    return false;
  }
//-->
</script>

<table border="0" width="90%" cellspacing="0" cellpadding="0" align="center">
<tbody><tr><td>

{detected}

	<tr class="th">
    <th colspan="2">{lang_settings}</th>
  </tr>
   <form name="domain_settings" action="manageheader.php" method="post">
    <input type="hidden" name="setting[write_config]" value="true">
  <tr>
    <td colspan="2"><b>{lang_serverroot}</b>
      <br><input type="text" name="setting[server_root]" size="80" value="{server_root}">
    </td>
  </tr>
  <tr>
    <td colspan="2"><b>{lang_includeroot}</b><br><input type="text" name="setting[include_root]" size="80" value="{include_root}"></td>
  </tr>
  <tr>
    <td colspan="2"><b>{lang_adminuser}</b><br><input type="text" name="setting[HEADER_ADMIN_USER]" size="30" value="{header_admin_user}"></td>
  </tr>
  <tr>
    <td colspan="2"><b>{lang_adminpass}</b><br><input type="password" name="setting[HEADER_ADMIN_PASSWORD]" size="30" value="{header_admin_password}"><input type="hidden" name="setting[HEADER_ADMIN_PASS]" value="{header_admin_pass}"></td>
  </tr>
  <tr>
    <td colspan="2"><b>{lang_setup_acl}</b><br><input type="text" name="setting[setup_acl]" size="30" value="{setup_acl}"></td>
  </tr>
  <tr>
    <td><b>{lang_persist}</b><br>
      <select type="checkbox" name="setting[db_persistent]">
        <option value="True"{db_persistent_yes}>{lang_Yes}</option>
        <option value="False"{db_persistent_no}>{lang_No}</option>
      </select>
    </td>
    <td>{lang_persistdescr}</td>
  </tr>
  <tr>
    <td><b>{lang_sesstype}</b><br>
      <select name="setting[sessions_type]">
{session_options}
      </select>
    </td>
    <td>{lang_sesstypedescr}</td>
  </tr>
  <tr>
    <td><b>{lang_enablemcrypt}</b><br>
      <select name="setting[enable_mcrypt]">
        <option value="True"{mcrypt_enabled_yes}>{lang_Yes}</option>
        <option value="False"{mcrypt_enabled_no}>{lang_No}</option>
      </select>
    </td>
    <td>{lang_mcrypt_warning}</td>
  </tr>
  <tr>
    <td><b>{lang_mcryptversion}</b><br><input type="text" name="setting[mcrypt_version]" value="{mcrypt}"></td>
    <td>{lang_mcryptversiondescr}</td>
  </tr>
  <tr>
    <td><b>{lang_mcryptiv}</b><br><input type="text" name="setting[mcrypt_iv]" value="{mcrypt_iv}" size="30"></td>
    <td>{lang_mcryptivdescr}</td>
  </tr>
  <tr>
    <td><b>{lang_domselect}</b><br>
      <select name="setting[domain_selectbox]">
        <option value="True"{domain_selectbox_yes}>{lang_Yes}</option>
        <option value="False"{domain_selectbox_no}>{lang_No}</option>
      </select></td><td>&nbsp;
    </td>
  </tr>
{domains}{comment_l}
  <tr class="th">
    <td colspan="2"><input type="submit" name="adddomain" value="{lang_adddomain}"></td>
  </tr>{comment_r}
  <tr>
    <td colspan="2">{errors}</td>
  </tr>
{formend}
  <tr>
    <td colspan="3">
 <form action="index.php" method="post">
  <br>{lang_finaldescr}<br>
  <input type="hidden" name="FormLogout"  value="header">
  <input type="hidden" name="ConfigLogin" value="Login">
  <input type="hidden" name="FormUser"    value="{FormUser}">
  <input type="hidden" name="FormPW"      value="{FormPW}">
  <input type="hidden" name="FormDomain"  value="{FormDomain}">
  <input type="submit" name="junk"        value="{lang_continue}">
 </form>
    </td>
  </tr>
  <tr class="banner">
    <td colspan="3">&nbsp;</td>
  </tr>
</table>
</body>
</html>
<!-- END manageheader -->

<!-- BEGIN domain -->
  <tr class="th">
    <td>{lang_domain}:</td>&nbsp;<td><input name="domains[{db_domain}]" value="{db_domain}">&nbsp;&nbsp;<input type="checkbox" name="deletedomain[{db_domain}]">&nbsp;<font color="fefefe">{lang_delete}</font></td>
  </tr>
  <tr>
    <td><b>{lang_dbtype}</b><br>
      <select name="setting_{db_domain}[db_type]" onchange="setDefaultDBPort(this,this.form['setting_{db_domain}[db_port]']);">
{dbtype_options}
      </select>
    </td>
    <td>{lang_whichdb}</td>
  </tr>
  <tr>
    <td><b>{lang_dbhost}</b><br><input type="text" name="setting_{db_domain}[db_host]" value="{db_host}"></td><td>{lang_dbhostdescr}</td>
  </tr>
  <tr>
  <tr>
    <td><b>{lang_dbport}</b><br><input type="text" name="setting_{db_domain}[db_port]" value="{db_port}"></td><td>{lang_dbportdescr}</td>
  </tr>
  <tr>
    <td><b>{lang_dbname}</b><br><input type="text" name="setting_{db_domain}[db_name]" value="{db_name}"></td><td>{lang_dbnamedescr}</td>
  </tr>
  <tr>
    <td><b>{lang_dbuser}</b><br><input type="text" name="setting_{db_domain}[db_user]" value="{db_user}"></td><td>{lang_dbuserdescr}</td>
  </tr>
  <tr>
    <td><b>{lang_dbpass}</b><br><input type="password" name="setting_{db_domain}[db_pass]" value="{db_pass}"></td><td>{lang_dbpassdescr}</td>
  </tr>
  <tr>
    <td><b>{lang_configuser}</b><br><input type="text" name="setting_{db_domain}[config_user]" value="{config_user}"></td>
  </tr>
  <tr>
    <td><b>{lang_configpass}</b><br><input type="password" name="setting_{db_domain}[config_pass]" value="{config_pass}"><input type="hidden" name="setting_{db_domain}[config_password]" value="{config_password}"></td>
    <td>{lang_passforconfig}</td>
  </tr>
<!-- END domain -->

</td></tr>
</tbody>

</table>
