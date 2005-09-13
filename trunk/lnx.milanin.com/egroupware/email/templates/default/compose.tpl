<!-- begin compose.tpl -->
<script type="text/javascript">
<!--
	self.name="first_Window";
	function addybook(extraparm)
	{
		Window1=window.open('{js_addylink}'+extraparm,"Search","width={jsaddybook_width},height={jsaddybook_height},toolbar=no,scrollbars=yes,resizable=yes");
	}
	function attach_window(url)
	{
		document.{form1_name}.attached_filenames.value="";
		awin = window.open(url,"attach","width=500,height=400,toolbar=no,resizable=yes");
	}
	function spellcheck()
	{
		document.doit.btn_spellcheck.value = "'Spell Check*'";
		document.doit.submit() ;
	}
	function send()
	{
		if (document.doit.to.value == "") {
			alert('Please enter a email address in the To box');
		} else {
			document.doit.submit();
		}
	}
	function get_utz()
	{
		var time=new Date();
		var tz_offset = (((time.getTimezoneOffset() * 100) / 60) * (-1));
		var tz_offset_abs;
		var tz_sign, tz_4mail;
		if (tz_offset >= 0) {
			tz_sign="plus";
			tz_offset_abs = tz_offset;
		} else {
			tz_sign="minus";
			tz_offset_abs = (tz_offset * (-1));
		}
		if (tz_offset == 0) {
			tz_4mail = "plus0000";
		} else if (tz_offset_abs.toString().length == 3) {
			tz_4mail = tz_sign+"0"+tz_offset_abs;
		} else {
			tz_4mail = tz_sign+tz_offset_abs+"";
		}
		return tz_4mail;
	}
-->
</script>

{widget_toolbar}

<!-- style specially for the return to FOLDER_LINK to get a better color for that link using class in that A element -->
<STYLE type="text/css">
<!--  
  a.c_nicecolor:link { color: {buttons_text_color}; }
-->
</STYLE>

<table border="0" cellpadding="1" cellspacing="1" width="100%" align="center">
<!--  <form enctype="multipart/form-data" name="_ form1_name _" action="_form1_action_" method="_ form1_method _"> -->
<form enctype="application/x-www-form-urlencoded" name="{form1_name}" action="{form1_action}" method="{form1_method}">
<script type="text/javascript">
<!--
document.write("<input type=\"hidden\" name=\"utz\" value=\""+get_utz()+"\">");
-->
</script>
<tr bgcolor="{buttons_bgcolor}" class="{buttons_bgcolor_class}">
	<td colspan="2">
		<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr bgcolor="{buttons_bgcolor}" class="{buttons_bgcolor_class}">
			<td width="20%" align="left">
				<font face="{toolbar_font}">
					{addressbook_button}
				</font>
			</td>
			<td width="20%" align="center">
				<font face="{toolbar_font}">
					{spellcheck_button}
				</font>
			</td>
			<td width="*" align="right">
				&nbsp;
			</td>
			<td width="20%" align="right">
				<font face="{toolbar_font}">
					{send_button}
				</font>
			</td>
			<td width="50">
				&nbsp;
			</td>
			
		</tr>
		</table>
	</td>
</tr>
<tr bgcolor="{to_boxs_bgcolor}" class="{to_boxs_bgcolor_class}">
	<td width="20%" align="left">
		<font size="2" face="{to_boxs_font}"><strong>&nbsp;{to_box_desc}</strong></font>
	</td>
	<td width="80%" align="left">
		<input type="text" name="{to_box_name}" size="80" value="{to_box_value}">
	</td>
</tr>
<tr bgcolor="{to_boxs_bgcolor}" class="{to_boxs_bgcolor_class}">
	<td>
		<font size="2" face="{to_boxs_font}"><strong>&nbsp;{cc_box_desc}</strong></font>
	</td>
	<td>
		<input type="text" name="{cc_box_name}" size="80" value="{cc_box_value}">
	</td>
</tr>
<tr bgcolor="{to_boxs_bgcolor}" class="{to_boxs_bgcolor_class}">
	<td>
		<font size="2" face="{to_boxs_font}"><strong>&nbsp;{bcc_box_desc}</strong></font>
	</td>
	<td>
		<input type="text" name="{bcc_box_name}" size="80" value="{bcc_box_value}">
	</td>
</tr>
<tr bgcolor="{to_boxs_bgcolor}" class="{to_boxs_bgcolor_class}">
	<td>
		<font size="2" face="{to_boxs_font}">
		<strong>&nbsp;{subj_box_desc}</strong></font>
	</td>
	<td>
		<input type="text" name="{subj_box_name}" size="80" value="{subj_box_value}">
	</td>
</tr>

<!-- BEGIN B_checkbox_sig -->
<tr bgcolor="{to_boxs_bgcolor}" class="{to_boxs_bgcolor_class}">
	<td colspan="1">
		<font size="2" face="{to_boxs_font}"><strong>&nbsp;{checkbox_sig_desc}</strong></font>
	</td>
	<td colspan="1">
		<input type="checkbox" name="{checkbox_sig_name}" value="{checkbox_sig_value}" {ischecked_checkbox_sig}>
	</td>
</tr>
<!-- END B_checkbox_sig -->
<tr bgcolor="{to_boxs_bgcolor}" class="{to_boxs_bgcolor_class}">
	<td colspan="1">
		<font size="2" face="{to_boxs_font}"><strong>&nbsp;{checkbox_req_notify_desc}</strong></font>
	</td>
	<td colspan="1">
	<input type="checkbox" name="{checkbox_req_notify_name}" value="{checkbox_req_notify_value}" {ischecked_checkbox_req_notify}>
	</td>
</tr>
<tr bgcolor="{to_boxs_bgcolor}" class="{to_boxs_bgcolor_class}">
	 <td>
		 <font size="2" face="{to_boxs_font}">
		 {attachfile_js_button}
		 </font>
	 </td>
	 <td>
	 	<input type="text" size="80" name="attached_filenames" onClick="javascript:{attachfile_js_onclick}">
	<td>
 </tr>
 </table>
<!-- this textarea should be 78 chars each line to conform with RFC822 old line length standard 78+CR+LF= 80 char line -->
<!-- when used with enctype multipart/form-data and wrap=hard this will add the hard wrap CRLF to the end of each line -->
<!-- NEW we no longer wrap=hard here, wordwrap line lengths taken care of in code now -->
 <table border="0" cellpadding="1" cellspacing="1" width="100%" align="center">
<tr align="center">
	<td>
		<!-- <textarea name="_body_box_name_" cols="84" rows="15" wrap="hard">_body_box_value_</textarea> -->
		<textarea name="{body_box_name}" cols="88" rows="15">{body_box_value}</textarea> 
		<!-- <textarea name="_body_box_name_" cols="78" rows="20" wrap="hard">_body_box_value_</textarea> -->
	</td>
</tr>
</table>
</form>

<script type="text/javascript">
  document.doit.body.focus();
  if(document.doit.subject.value == "") document.doit.subject.focus();
  if(document.doit.to.value == "") document.doit.to.focus();
</script>
<!-- end compose.tpl -->
