<!-- BEGIN head --><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xml:lang="{lang_code}" xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<title>{website_title}</title>
		<meta http-equiv="content-type" content="text/html; charset={charset}" />
		<meta name="keywords" content="eGroupWare" />
		<meta name="description" content="eGroupware" />
		<meta name="keywords" content="eGroupWare" />
		<meta name="copyright" content="eGroupWare http://www.egroupware.org (c) 2003" />
		<meta name="language" content="{lang_code}" />
		<meta name="author" content="eGroupWare http://www.egroupware.org" />
		<meta name="robots" content="none" />
		<link rel="icon" href="{img_icon}" type="image/x-ico" />
		<link rel="shortcut icon" href="{img_shortcut}" />
		<link href="{theme_css}" type="text/css" rel="StyleSheet" />
		{slider_effects}
		{simple_show_hide}
		{pngfix}
		{css}
		{java_script}
		
		<script language="javascript" type="text/javascript" src="/egroupware/jscripts/tiny_mce/tiny_mce.js"></script>
		<script language="javascript" type="text/javascript">
			tinyMCE.init({
				theme : "advanced",
				//language : "de",
				//mode : "exact",
				//elements : "news[content],entry[label]",
                                mode: "specific_textareas",
                                editor_selector : "tinyMCE",
				plugins : "table",
				theme_advanced_buttons1_add : "forecolor,backcolor",
				theme_advanced_buttons3_add_before : "tablecontrols,separator",
				theme_advanced_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1", // Theme specific setting CSS classes
				debug : false
			});
		</script>
		
	</head>
	<!-- we don't need body tags anymore, do we?) we do!!! onload!! LK -->
	<body {body_tags}>
<!-- END head -->
