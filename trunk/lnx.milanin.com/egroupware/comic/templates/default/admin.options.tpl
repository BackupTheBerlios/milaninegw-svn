<center><h2>{title}</h2></center>
<p>
<form method="POST" action="{action_url}">
  <table border="0" cellpadding="0" cellspacing="0" width="85%" align="center">
    <tr>
      <td width="25%" colspan="1" align="left">{censor_label}:</td>
      <td width="75%" colspan="1" align="left">
        <select name="censor_level">
          {censor_options}
        </select>
      </td>
	</tr>
	<tr>
      <td width="25%" colspan="1" align="left">{override_label}:</td>
      <td width="75%" colspan="1" align="left">
        <input type="checkbox" {override_checked} name="override_enabled" value="1">
      </td>
    </tr>
    <tr>
      <td width="25%" colspan="1" align="left">{imgsrc_label}:</td>
      <td width="75%" colspan="1" align="left">
        <select name="image_source">
          {image_options}
        </select>
      </td>
	</tr>
	<tr>
      <td width="25%" colspan="1" align="left">{remote_label}:</td>
      <td width="75%" colspan="1" align="left"><input type="checkbox" {remote_checked} name="remote_enabled" value="1"> 
      </td>
	</tr>
	<tr>
      <td width="25%" colspan="1" align="left">{filesize_label}:</td>
      <td width="75%" colspan="1" align="left">
        <input type="text" name="filesize" value="{filesize}" size="7" maxlength=7>
      </td>
    </tr>
    <tr>
      <td colspan="2" align="left">
		<br />
        <input type="submit" name="submit" value="{action_label}">
        <input type="reset" name="reset" value="{reset_label}">
      </td>
    </tr>
  </table>
</form>
<center>
  <form method="POST" action="{done_url}">
    <input type="submit" name="done" value="{done_label}">
  </form>
</center>
