<center><h2>{title}</h2></center>
<p>
<form method="POST" action="{action_url}">
  <input type="hidden" name="comic_id" value="{comic_id}">
  <input type="hidden" name="returnmain" value="{returnmain}">
  <table border="0" cellpadding="1" cellspacing="1" width="85%" align="center">
    <tr bgcolor="{th_bg}" fgcolor="{th_text}" align="left">
      <td colspan="4" align="left">{layout_label}</td>
    </tr>
	<tr>
		<td width="25%" colspan="1" align="left">{template_label}:</td>
		<td width="25%" colspan="1" align="left">
		  <select name="comic_template">
			{template_options}
		  </select>
		</td>
		<td width="25%" colspan="1" align="left">&nbsp;</td>
		<td width="25%" colspan="1" align="left">&nbsp;</td>
	</tr>
    <tr>
      <td colspan="4" align="center">
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
	  {template_images}
        </table>
      </td>
    </tr>
    <tr bgcolor="{th_bg}" fgcolor="{th_text}" align="left">
      <td colspan="4" align="left">{option_label}</td>
    </tr>
    <tr>
      <td width="25%" colspan="1" align="left">{perpage_label}:</td>
      <td width="25%" colspan="1" align="left">
        <select name="perpage">
          {perpage_options}
        </select>
      </td>
      <td width="25%" colspan="1" align="left">{scale_label}:</td>
      <td width="25%" colspan="1" align="left">
        <input type="checkbox" {scale_checked} name="scale_enabled" value="1">
      </td>
    </tr>
    <tr>
      <td width="25%" colspan="1" align="left">{frontpage_label}:</td>
      <td width="25%" colspan="1" align="left">
        <select name="frontpage">
          {frontpage_options}
        </select>
      </td>
      <td width="25%" colspan="1" align="left">{fpscale_label}:</td>
      <td width="25%" colspan="1" align="left">
        <input type="checkbox" {fpscale_checked} name="fpscale_enabled" value="1">
      </td>
    </tr>
    <tr>
      <td width="25%" colspan="1" align="left">{censor_label}:</td>
      <td width="25%" colspan="1" align="left">
        <select name="censor_level">
          {censor_options}
        </select>
      </td>
    </tr>
    <tr bgcolor="{th_bg}" fgcolor="{th_text}" align="left">
      <td colspan="4" align="left">{comic_label}</td>
    </tr>
    <tr>
      <td colspan="4" align="left">
        <select name="data_ids[]" multiple size="{comic_size}">
          {comic_options}
        </select>
      </td>
    </tr>
    <tr>
      <td colspan="2" align="left">
        <input type="submit" name="submit" value="{action_label}">
      </td>
      <td colspan="2" align="right">
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
