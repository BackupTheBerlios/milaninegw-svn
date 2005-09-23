<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<!-- $Id: main.tpl,v 1.5 2004/02/22 01:46:27 ralfbecker Exp $ -->
<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>{sitename}: {title}</title>
		<meta http-equiv="content-type" content="text/html; charset={charset}" />
		<meta http-equiv="expires" content="0" />
		<meta name="RESOURCE-TYPE" content="DOCUMENT" />
		<meta name="DISTRIBUTION" content="GLOBAL" />
		<meta name="AUTHOR" content="{sitename}" />
		<meta name="COPYRIGHT" content="Copyright (c) {year} by {sitename}" />
		<meta name="DESCRIPTION" content="{slogan}" />
		<meta name="ROBOTS" content="INDEX, FOLLOW" />
		<meta name="REVISIT-AFTER" content="1 DAYS" />
		<meta name="RATING" content="GENERAL" />
		<meta name="GENERATOR" content="eGroupWare Web Site Manager" />
		<meta name="keywords" content="eGroupWare" />
		<meta name="language" content="{lang}" />
		<link rel="icon" href="templates/idots/images/favicon.ico" type="image/x-ico" />
		<link rel="shortcut icon" href="templates/idots/images/favicon.ico" />
		{editmode_styles}
		<link href="templates/idots/style/style.css" type="text/css" rel="StyleSheet" />
		<!-- This solves the Internet Explorer PNG-transparency bug, but only for IE 5.5 and higher -->
		<!--[if gte IE 5.5000]>
		<script src="templates/idots/js/pngfix.js" type=text/javascript>
		</script>
		<![endif]-->
	</head>



<body bgcolor="#ffffff" text="#000000" link="#363636" vlink="#363636" alink="#d5ae83">
<div id="divLogo">
	<a href="index.php"><img src="templates/idots/images/logo.png" border="0" title="{site_name}" /></a>
</div>
<div id="divMain">
	<div id="divAppIconBar">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="180" valign="top" align="left"><img src="templates/idots/images/grey-pixel.png" width="1" height="68" /></td>
				<td>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td width="100%" height="68" align="center">
{contentarea:header}
							</td>
						</tr>
						<tr>
							<td width="100%">&nbsp;</td>
						</tr>
					</table>
				</td>
				<td width="1" valign="top" align="right"><img src="templates/idots/images/grey-pixel.png" width="1" height="68" /></td>
			</tr>
		</table>
	</div>

	<div id="divSubContainer">
		<table width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td id="tdSideboxLeft">
{contentarea:left}
				</td>
				<td id="tdAppbox">
					<div id="divAppboxHeader">{title} {editicons}</div>
					<div id="divAppbox">
						<h3>{subtitle}</h3>
{contentarea:center}
					</div>
				</td>
				<td id="tdSideboxRight">
{contentarea:right}
				</td>
			</tr>
		</table>
	</div>
</div>
<div id="divFooter">
{contentarea:footer}
</div>

</body>
</html>
