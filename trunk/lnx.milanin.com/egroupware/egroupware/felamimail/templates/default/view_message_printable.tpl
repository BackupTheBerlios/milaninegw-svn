<!-- BEGIN message_main -->
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset={charset}" />
</head>
<body onload="javascript:window.print()">
<STYLE type="text/css">
        .subjectBold
        {
        	FONT-SIZE: 12px;
        	font-weight : bold;
        	font-family : Arial;
        }

        .subject
        {
        	FONT-SIZE: 12px;
        	font-family : Arial;
        }

        .body
        {
        	FONT-SIZE: 12px;
        	font-family : Courier;
        }

        A.head_link
        {
        	color: blue;
        }
</STYLE>
<table border="0" cellpadding="1" cellspacing="0" width="100%" style="table-layout:fixed">

<tr class="th">
	<td style="font-weight:bold;">
		{subject_data}
	</td>
	<td style="text-align:right; width:100px;">
		&nbsp;
	</td>
</tr>
</table>
<div id="tabcontent1" class="inactivetab" bgcolor="white">
<table border="0" width="100%" cellspacing="0" cellpading="0" bgcolor="white" style="table-layout:fixed">
<tr>
	<td>
		&nbsp;
	</td>
</tr>
<tr>
	<td>
{header}
	</td>
</tr>
<tr>
	<td bgcolor="white">
<div class="body">
<!-- Body Begin -->
{body}
<!-- Body End -->
</div>
	</td>
</tr>
</table>
</div>

<!-- END message_main -->

<!-- BEGIN message_raw_header -->
<tr>
	<td bgcolor="white">
		<pre><font face="Arial" size="-1">{raw_header_data}</font></pre>
	</td>
</tr>
<!-- END message_raw_header -->

<!-- BEGIN message_navbar -->
<table border="0" cellpadding="1" cellspacing="0" width="100%">
	<tr bgcolor="{th_bg}">
		<td width="50%">
			{lang_back_to_folder}:&nbsp;<a class="head_link" href="{link_message_list}">{folder_name}</a>
		</td>
		<td align="right">
			{previous_message}
			{next_message}
		</td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td align="right" colspan="2">
			<a class="head_link" href="{link_reply}">
			<!-- <img src="{app_image_path}/sm_reply.gif" height="26" width="28" alt="{lang_reply}" border="0"> -->
			{lang_reply}
			</a>
			&nbsp;|&nbsp;
			<a class="head_link" href="{link_reply_all}">
			<!-- <img src="{app_image_path}/sm_reply_all.gif" height="26" width="28" alt="{lang_reply_all}" border="0"> -->
			{lang_reply_all}
			</a>
			&nbsp;|&nbsp;
			<a class="head_link" href="{link_forward}">
			<!-- <img src="{app_image_path}/sm_forward.gif" height="26" width="28" alt="{lang_forward}" border="0"> -->
			{lang_forward}
			</a>
			&nbsp;|&nbsp;
			<a class="head_link" href="{link_delete}">
			<!-- <img src="{app_image_path}/sm_delete.gif" height="26" width="28" alt="{lang_delete}" border="0"> -->
			{lang_delete}
			</a>
		</td>
	</tr>
</table>
<!-- END message_navbar -->

<!-- BEGIN message_attachement_row -->
<tr>
	<td valign="top">
		<a href="{link_view}" target="{target}"><font size="2" face="{theme_font}">
		<b>{filename}</b></font><a>
	</td> 
	<td>
		<font size="2" face="{theme_font}">
		{mimetype}
		</font>
	</td>
	<td>
		<font size="2" face="{theme_font}">
		{size}
		</font>
	</td>
	<td width="10%" align="center">
		<font size="2" face="{theme_font}">
		<a href="{link_save}">{lang_save}</a>
		</font>
	</td>
</tr>
<!-- END message_attachement_row -->

<!-- BEGIN message_cc -->
<tr>
	<td width="100" style="font-weight:bold;">
		{lang_cc}:
	</td> 
	<td>
		{cc_data}
	</td>
</tr>
<!-- END message_cc -->

<!-- BEGIN message_org -->
<tr>
	<td width="100" style="font-weight:bold;">
		{lang_organisation}:
	</td> 
	<td>
		{organization_data}
	</td>
</tr>
<!-- END message_org -->

<!-- BEGIN message_onbehalfof -->
<tr>
	<td width="100" style="font-weight:bold;">
		{lang_on_behalf_of}:
	</td> 
	<td>
		{onbehalfof_data}
	</td>
</tr>
<!-- END message_onbehalfof -->

<!-- BEGIN message_header -->
<table border="0" cellpadding="1" cellspacing="0" width="100%" style="table-layout:fixed">

<table border="0" cellpadding="1" cellspacing="0" width="100%">
<tr cclass="row_on">
	<td style="text-align:left; width:120px; font-weight:bold;">
		{lang_from}:
	</td>
	<td style="font-weight:bold;">
		{from_data}
	</td>
</tr>

{on_behalf_of_part}

{org_part}

<tr cclass="row_off">
	<td style="font-weight:bold;">
		{lang_to}:
	</td> 
	<td>
		{to_data}
	</td>
</tr>

{cc_data_part}

<tr cclass="row_on">
	<td style="font-weight:bold;">
		{lang_date}:
	</td> 
	<td>
		{date_data}
	</td>
</tr>

</table>
<br>
<!-- END message_header -->

<!-- BEGIN previous_message_block -->
<a class="head_link" href="{previous_url}">{lang_previous_message}</a>
<!-- END previous_message_block -->

<!-- BEGIN next_message_block -->
<a class="head_link" href="{next_url}">{lang_next_message}</a>
<!-- END next_message_block -->
