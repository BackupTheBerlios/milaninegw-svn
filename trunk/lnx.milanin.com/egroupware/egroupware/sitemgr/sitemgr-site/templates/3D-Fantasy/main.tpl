<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<!-- $Id: main.tpl,v 1.10 2004/02/22 01:46:25 ralfbecker Exp $ -->
<html>
	<head>
		<title>{sitename}: {title}</title>
		<meta http-equiv="Content-Type" content="text/html; charset={charset}">
		<meta http-equiv="EXPIRES" content="0">
		<meta name="RESOURCE-TYPE" content="DOCUMENT">
		<meta name="DISTRIBUTION" content="GLOBAL">
		<meta name="AUTHOR" content="{sitename}">
		<meta name="COPYRIGHT" content="Copyright (c) {year} by {sitename}">
		<meta name="DESCRIPTION" content="{slogan}">
		<meta name="ROBOTS" content="INDEX, FOLLOW">
		<meta name="REVISIT-AFTER" content="1 DAYS">
		<meta name="RATING" content="GENERAL">
		<meta name="GENERATOR" content="eGroupWare Web Site Manager">
		<meta name="language" content="{lang}" />
		{editmode_styles}
		<LINK REL="StyleSheet" HREF="templates/3D-Fantasy/style/style.css" TYPE="text/css">
	</head>
<body bgcolor="#ffffff" text="#000000" link="#363636" vlink="#363636" alink="#d5ae83">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td width="15" height="15"><img src="templates/3D-Fantasy/images/up-left2.gif" alt="" border="0"></td>
		<td><img src="templates/3D-Fantasy/images/up2.gif" width="100%" height="15"></td>
		<td><img src="templates/3D-Fantasy/images/up-right2.gif" width="15" height="15" alt="" border="0"></td>
	</tr><tr>
		<td background="templates/3D-Fantasy/images/left2.gif" width="15">&nbsp;</td>
		<td bgcolor="ffffff" width="100%">
			<table width="100%" border="0" align="center" bgcolor="#ffffff">
			<tr>
				<td>
					<a href="index.php"><img src="templates/3D-Fantasy/images/logo.gif" border="0" alt="Welcome to {sitename}"></a>
				</td><td>
					{contentarea:header}
				</td>
			</tr>
			</table>
		</td>
		<td background="templates/3D-Fantasy/images/right2.gif">&nbsp;</td>
	</tr><tr>
		<td width="15" height="15"><img src="templates/3D-Fantasy/images/down-left2.gif" alt="" border="0"></td>
		<td height="15" background="templates/3D-Fantasy/images/down2.gif"></td>
		<td width="15" height="15"><img src="templates/3D-Fantasy/images/down-right2.gif" alt="" border="0"></td>
	</tr>
</table>
<br>
<table cellpadding="0" cellspacing="0" width="99%" border="0" align="center" bgcolor="#ffffff">
	<tr>
		<td bgcolor="#ffffff" valign="top">
			{contentarea:left}
		</td>
		<td valign="top"><img src="images/pix.gif" width="10" height="1" border="0" alt=""></td>
		<td width="100%" valign="top">
			<h1>{title} {editicons}</h1>
			<h3>{subtitle}</h3>
			{contentarea:center}
		</td>
		<td><img src="images/pix.gif" width="10" height="1" border="0" alt=""></td>
		<td valign="top">
			{contentarea:right}
		</td>
	</tr>
</table>
<br>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td width="15" height="15"><img src="templates/3D-Fantasy/images/up-left2.gif" alt="" border="0"></td>
		<td><img src="templates/3D-Fantasy/images/up2.gif" width="100%" height="15"></td>
		<td><img src="templates/3D-Fantasy/images/up-right2.gif" width="15" height="15" alt="" border="0"></td>
	</tr><tr>
		<td background="templates/3D-Fantasy/images/left2.gif" width="15">&nbsp;</td>
		<td bgcolor="ffffff" width="100%" align="center"><font class="tiny">{contentarea:footer}</font></td>
		<td background="templates/3D-Fantasy/images/right2.gif">&nbsp;</td>
	</tr><tr>
		<td width="15" height="15"><img src="templates/3D-Fantasy/images/down-left2.gif" alt="" border="0"></td>
		<td height="15" background="templates/3D-Fantasy/images/down2.gif"></td>
		<td width="15" height="15"><img src="templates/3D-Fantasy/images/down-right2.gif" alt="" border="0"></td>
	</tr>
</table>
</body>
</html>
