<!-- BEGIN header -->
<form name=frm method="POST" action="{action_url}">
<table border="0" align="left">
   <tr bgcolor="{th_bg}">
    <td colspan="2"><font color="{th_text}">&nbsp;<b>{title}</b></font></td>
   </tr>
<!-- END header -->
<!-- BEGIN body -->
<tr bgcolor="{row_on}">
<td>{lang_name_wiki_home_link}:</td>
<td><input name="newsettings[wikihome]" size="30" value="{value_wikihome}"></td>
</tr>

<tr bgcolor="{row_off}">
<td>{lang_allow_anonymous_access}:</td>
<td>
<select name="newsettings[allow_anonymous]">
<option value=""{selected_allow_anonymous_False}>{lang_No}</option>
<option value="True"{selected_allow_anonymous_True}>{lang_Yes}</option>
</select>
</td>
</tr>

<tr bgcolor="{row_on}">
<td>{lang_Anonymous_Session_Type}:</td>
<td>
<select name="newsettings[Anonymous_Session_Type]">
<option value="readonly"{selected_Anonymous_Session_Type_readonly}>{lang_readonly}</option>
<option value="editable"{selected_Anonymous_Session_Type_editable}>{lang_editable}</option>
</select>
</td>
</tr>

<tr bgcolor="{row_off}">
<td>{lang_anonymous_username}:</td>
<td><input name="newsettings[anonymous_username]" size="30" value="{value_anonymous_username}"></td>
</tr>

<tr bgcolor="{row_on}">
<td>{lang_Anonymous_password}:</td>
<td><input name="newsettings[anonymous_password]" size="30" value="{value_anonymous_password}"></td>
</tr>

<tr bgcolor="{row_off}">
<td>{lang_Emailaddress_Administrator}:</td>
<td><input name="newsettings[emailadmin]" size="30" value="{value_emailadmin}"></td>
</tr>

<tr bgcolor="{row_on}">
<td>{lang_InterWikiPrefix}:</td>
<td><input name="newsettings[InterWikiPrefix]" size="30" value="{value_InterWikiPrefix}"></td>
</tr>


<tr bgcolor="{row_off}">
<td>{lang_Enable_Free_Links}:</td>
<td>
<select name="newsettings[Enable_Free_Links]">
<option value=""{selected_Enable_Free_Links_False}>{lang_No}</option>
<option value="True"{selected_Enable_Free_Links_True}>{lang_Yes}</option>
</select>
</td>
</tr>

<tr bgcolor="{row_on}">
<td>{lang_Enable_Wiki_Links}:</td>
<td>
<select name="newsettings[Enable_Wiki_Links]">
<option value=""{selected_Enable_Wiki_Links_False}>{lang_No}</option>
<option value="True"{selected_Enable_Wiki_Links_True}>{lang_Yes}</option>
</select>
</td>
</tr>

<tr bgcolor="{row_off}">
<td>{lang_Automatically_convert_pages_with_wiki-syntax_to_richtext_(if_edited)?}:</td>
<td>
<select name="newsettings[AutoconvertPages]">
<option value="auto"{selected_AutoconvertPages_auto}>{lang_Only_if_browser_supports_a_richtext-editor}</option>
<option value="onrequest"{selected_AutoconvertPages_onrequest}>{lang_No_only_on_request}</option>
<option value="never"{selected_AutoconvertPages_never}>{lang_No_never} {lang_(don't_offer_the_possibility)}</option>
<option value="always"{selected_AutoconvertPages_always}>{lang_Yes_always}</option>
</select>
</td>
</tr>
<!-- END body -->

<!-- BEGIN footer -->
  <tr bgcolor="{th_bg}">
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
