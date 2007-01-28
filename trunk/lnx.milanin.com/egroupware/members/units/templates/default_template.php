<?php

	global $template;
	global $template_definition;
	
	$template_definition[] = array(
									'id' => 'pageshell',
									'name' => "Page Shell",
									'description' => "The main page shell, including headers and footers.",
									'glossary' => array(
															'{{metatags}}' => 'Page metatags (mandatory) - must be in the "head" portion of the page',
															'{{title}}' => 'Page title',
															'{{menu}}' => 'Menu',
															'{{mainbody}}' => 'Main body',
															'{{sidebar}}' => 'Sidebar'
														)
									);
	
	$template['pageshell'] = <<< END
	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
END;
if ($_SESSION['userid']>0){
$template['pageshell'] .= <<< END
<script language="javascript" type="text/javascript" src="/egroupware/jscripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
	mode : "exact",
	safari_warning : false,
	elements: "home[data],new_weblog_post,profiledetails[biography],profiledetails[careergoals],new_weblog_comment",
	language: "it"
});
</script>

END;
/**/
}else{
$template['pageshell'] .= <<< END
<script language="javascript" type="text/javascript" src="/members/jscripts/humanoid.js"></script>
END;
}
$template['pageshell'] .= <<< END
<link href="/egroupware/sitemgr/sitemgr-site/templates/rhuk_orange_smoothie/css/template_css.css" rel="stylesheet" type="text/css" />
<title>{{title}}</title>
{{metatags}}
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!--meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" /-->
</head>

<body>
<table align="center" width="898"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td id="pagetop"><a href="{{url}}"><!--img src="{{url}}_templates/default/graphics/purplecrayon.gif" alt="Elgg" width="227" height="70" border="0" /--></a></td>
  </tr>
</table>
<table align="center" width="898"  border="0" cellpadding="0" cellspacing="0" background="/egroupware/sitemgr/sitemgr-site/templates/rhuk_orange_smoothie/images/blocks_bg1.gif">
  <tr>
    <td colspan="5" align="left" valign="top" id="mainbody">{{menu}}{{messageshell}}</td>
  </tr>
  <tr>
    <td colspan="5">
    	&nbsp;
    </td>
  </tr>
  <tr>
    <td width="4%" align="left" valign="top" id="mainbody">&nbsp;</td>
    <td width="65%" align="left" valign="top" id="mainbody">{{mainbody}}</td>
    <td width="2%" align="right" valign="top" id="mainbody">&nbsp;</td>
    <td width="25%" align="right" valign="top" id="mainbody">{{sidebar}}</td>
    <td width="4%" align="right" valign="top" id="mainbody">&nbsp;</td>
  </tr>
  <tr>
    <td height="100" colspan="5" align="center" valign="middle">
      Copyright &copy; 2004-2005 ELGG      </td>
<tr>
<td height="100" colspan="5" align="center" valign="middle">
      <table width="100%" align="center" class="infobox" style="text-align:center">
<tbody><tr><th>Copyright &copy; 2005-2006 B.C. Milan In</th>
</tr>
<tr>
<th>
  <table width="100%" style="text-align:center">
  <tr><td><a href="http://www.milanin.com">http://www.milanin.com</a>
- Sede legale Via Tanaro, 22
- 20128 Milano
- CF 97413780152
- <a href="mailto:info@milanin.com">info@milanin.com</a></td></tr>
  </table>
</th>
</tr>
</tbody></table> 

</body>
</html>
	
END;

	$template_definition[] = array(
									'id' => 'css',
									'name' => "Stylesheet",
									'description' => "The Cascading Style Sheet for the template.",
									'glossary' => array()
									);

	$template['css'] = <<< END
	
/*

	Elgg main site CSS file

*/

body			{
					margin: 0% 0% 0% 0%;
					background-color: #ffffff;
					font-family: arial, helvetica;
					font-size: 10pt;
					line-height: 1.2ems;
					padding: 0px;
				}

h1				{
					13pt;
				}
h2				{
					12pt;
				}
h3				{
					11pt;
				}
form			{
					margin: 0px;
					padding: 0px;
				}
				
/* Page top */
#pagetop		{
					background-color: #FFFFFF;
                                        height: 120px;
                                        background-image: url("/egroupware/sitemgr/sitemgr-site/templates/rhuk_orange_smoothie/images/generic_header.jpg");
                                        background-repeat: no-repeat;
  
				}
#pagetop ul		{
					position: absolute;
					right: 0;
					top: 0;
					list-style-type: none;
					margin: 0px;
					padding: 0px;
				}
#pagetop li		{
					font-family: verdana, arial, helvetica, helv;
					color:#ffffff;
					margin: 0;
					padding: 0;
				}
#pagetop li a	{
					display: block;
					text-decoration: none;
					color: #ffa800;
					text-align: center;
					background-color: #444444;
				}
#pagetop li a:hover	{
					color: #000000;
					background-color: #ffa800;
				}
.messages        {
                    background-color: #EDF1F1;
		    font-weight: bold;
		    color: #000000;
		    padding: 0px;
		    margin: 0px;
		    }
.messages ul	{
					padding: 0px;
					margin: 0px;
				}
.menubar		{
					background-color: #aaaaaa;
					padding: 3px;
					border: 0px;
					border-bottom: 1px;
					border-color: #000000;
					border-style: solid;
				}
.menubar a		{
					color: #ffffff;
					padding: 3px;
					text-decoration: none;
				}
.menubar a:hover	{
					text-decoration: underline;
				}
.actionbox		{
					background-color: #ffffff;
					border: 1px;
					border-color: #000000;
					border-style: solid;
					padding: 7px;
					
				}
.actionbox caption	{
					background-color: #aaaaaa;
					color: #bf2868;
					vertical-align: middle;
					text-align: center;
					padding: 4px;
					font-weight: bold;
					border: 1px;
					border-color: #000000;
					border-style: solid;
					border-bottom: 0px;
					font-size: 1.1em;
					
				}
.infobox		{
					background-color: #ffffff;
					border: 1px;
					border-color: #000000;
					border-style: solid;
					padding: 7px;
				}
.infobox caption	{
					background-color: #EDF1F1;
					color: #bf2868;
					vertical-align: middle;
					text-align: center;
					padding: 4px;
					font-weight: bold;
					border: 1px;
					border-color: #000000;
					border-style: solid;
					border-bottom: 0px;
					font-size: 1.1em;
				}
.profiletable 	{
					border: 1px;
					border-style: solid;
					border-color: #888888;
			  	}
.profiletable .fieldname	{
					color: #bf2868;
				}
.profiletable .caption	{
					background-color: #aaaaaa;
					color: #bf2868;
					vertical-align: middle;
					text-align: center;
					padding: 4px;
					font-weight: bold;
					border: 1px;
					border-color: #000000;
					border-style: solid;
					border-bottom: 0px;
					font-size: 1.1em;
					
}
	
END;

	$template_definition[] = array(
									'id' => 'infobox',
									'name' => "Information Box",
									'description' => "A box containing a caption and some text, used extensively throughout the site. For example, the 'friends' box and most page bodies are info boxes. Of course, you can alter this template however you wish - it doesn't need to be an actual box.",
									'glossary' => array(
															'{{name}}' => 'The title',
															'{{contents}}' => 'The contents of the box'
														)
									);

	$template['infobox'] = <<< END
	
<table class="infobox" width="100%">
	<caption align="top">
		{{name}}
	</caption>
	<tr>
		<td>
{{contents}}
		</td>
	</tr>
</table><br />
	
END;

	$template_definition[] = array(
									'id' => 'messageshell',
									'name' => "System message shell",
									'description' => "A list of system messages will be placed within the message shell.",
									'glossary' => array(
															'{{messages}}' => 'The messages'
														)
									);

	$template['messageshell'] = <<< END
	
	<table width="100%" class="messages">
		<tr>
			<td>
				<ul>
					{{messages}}
				</ul>
			</td>
		</tr>
	</table>
	
END;

	$template_definition[] = array(
									'id' => 'messages',
									'name' => "Individual system messages",
									'description' => "Each individual system message.",
									'glossary' => array(
															'{{message}}' => 'The system message'
														)
									);

	$template['messages'] = <<< END

	<li>
		{{message}}
	</li>	
	
END;
	

	$template_definition[] = array(
									'id' => 'menu',
									'name' => "Menubar shell",
									'description' => "A list of menu items will be placed within the menubar shell.",
									'glossary' => array(
															'{{menuitems}}' => 'The menu items'
														)
									);

	$template['menu'] = <<< END
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="navcontent">
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td class="navbg_main">
			<table width="100%" class="moduletable">
                          <tr>
                            <td>
                              <table cellspacing="2" cellpadding="2">
                                <tr>
                                  {{menuitems}}
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </table>
                    </td>
                  </tr>
                </table>
              </td>
          </tr>
	</table>
END;

	$template_definition[] = array(
									'id' => 'menuitem',
									'name' => "Individual menu item",
									'description' => "This is the template for each individual menu item. A series of these is placed within the menubar shell template.",
									'glossary' => array(
															'{{location}}' => 'The URL of the menu item',
															'{{menu}}' => 'The menu item\'s name'
														)
									);

	$template['menuitem'] = <<< END
	
	<td style="border-style:  solid solid solid solid ; border-width: thin;"><a href="{{location}}">{{name}}</a></td>
	
END;

	$template_definition[] = array(
									'id' => 'databox',
									'name' => "Data input box (two columns)",
									'description' => "This is mostly used whenever some input is taken from the user. For example, each of the fields in the profile edit screen is a data input box.",
									'glossary' => array(
															'{{name}}' => 'The name for the data we\'re inputting',
															'{{column1}}' => 'The first item of data',
															'{{column2}}' => 'The second item of data'
														)
									);

	$template['databox'] = <<< END

<table width="95%" class="profiletable" align="center" style="margin-bottom: 3px">
	<tr>
		<td rowspan="2" width="15%" class="fieldname">
			{{name}}
		</td>
		<td width="85%">
			{{column1}}
		</td></tr><tr>
		<td width="85%">
			{{column2}}
		</td>
	</tr>
</table>
	
END;

	$template_definition[] = array(
									'id' => 'databox1',
									'name' => "Data input box (one column)",
									'description' => "A single-column version of the data box.",
									'glossary' => array(
															'{{name}}' => 'The name of the data we\'re inputting',
															'{{column1}}' => 'The data itself'
														)
									);

	$template['databox1'] = <<< END

<table width="95%" class="profiletable" align="center" style="margin-bottom: 3px">
	<tr>
		<td width="20%" class="fieldname">
			{{name}}
		</td>
		<td width="80%">
			{{column1}}
		</td>
	</tr>
</table>
	
END;

	$template_definition[] = array(
									'id' => 'databoxvertical',
									'name' => "Data input box (vertical)",
									'description' => "A slightly different version of the data box, used on this edit page amongst other places.",
									'glossary' => array(
															'{{name}}' => 'Name of the data we\'re inputting',
															'{{contents}}' => 'The data itself'
														)
									);

	$template['databoxvertical'] = <<< END
	
<table width="95%" class="profiletable" align="center" style="margin-bottom: 3px">
	<tr>
		<td align="center" class="fieldname">
			{{name}}
		</td>
	</tr>
	<tr>
		<td >
			{{contents}}
		</td>
	</tr>
</table>
	
END;

?>
