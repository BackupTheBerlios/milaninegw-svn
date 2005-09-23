<?php
	/*
	JiNN - Jinn is Not Nuke, a mutli-user, multi-site CMS for eGroupWare
	Copyright (C)2004  <shaman@maxstyle.nl>

	eGroupWare - http://www.egroupware.org

	This file is part of JiNN

	JiNN is free software; you can redistribute it and/or modify it under
	the terms of the GNU General Public License as published by the Free
	Software Foundation; version 2 of the License.

	JiNN is distributed in the hope that it will be useful,but WITHOUT ANY
	WARRANTY; without even the implied warranty of MERCHANTABILITY or 
	FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
	for more details.

	You should have received a copy of the GNU General Public License 
	along with JiNN; if not, write to the Free Software Foundation, Inc.,
	59 Temple Place, Suite 330, Boston, MA 02111-1307  USA

	---------------------------------------------------------------------

    plugin.colorlab.php contains a colorlab plugin for
	JiNN. Copyright(C)2003, 2004 Gabriël Ramaker <gabriel@MAXstyle.nl>
	*/
    $descr= 'Colorlab plugin for JiNN with Flash 6 front-end. This version is not (fully) functional/stable, use at own risk.<br/>
    The Flash(.Fla) source file is located in the \'colorlab\' folder in the JiNN \'plugins\' folder.<br/>';

	$this->plugins['colorlab']['name']				= 'colorlab';
	$this->plugins['colorlab']['title']		    	= 'Colorlab';
	$this->plugins['colorlab']['version']			= '0.02';
	$this->plugins['colorlab']['author']			= 'Gabriël Ramaker';
	$this->plugins['colorlab']['description']		= $descr;
	$this->plugins['colorlab']['enable']			= 1;
	$this->plugins['colorlab']['db_field_hooks']	= array('string',   'varchar','longtext',	'text');
    $this->plugins['colorlab']['config']		    = array(
		'Available_colors' => array(array('Unlimited','User-defined'),'select',''),
		'User_defined_colors' => array('#FFFFFF,#000000,#CCCCCC','area','')
	);

	function plg_fi_colorlab($field_name,$value, $config,$attr_arr)
	{
      $hex_values=explode(',',$config['User_defined_colors']);
      $i=0;
      foreach($hex_values as $val){
          $flaStr.='&v'.$i.'='.substr($val, 1);
          $i++;
      }
      $input='<input type="hidden" name="'.$field_name.'" value="'.$value.'">
              <SCRIPT LANGUAGE="JavaScript1.1" type="text/javascript">
              <!--
              function change'.$field_name.'(val){
                  document.frm.'.$field_name.'.value=val;
              }
              var flash6Installed = false;
              var flash7Installed = false;
              var flash8Installed = false;
              var flash9Installed = false;
              var actualVersion = 0;
              var hasRightVersion = false;
              if(navigator.appVersion.indexOf("MSIE") != -1 && navigator.appVersion.toLowerCase().indexOf("win") != -1 && navigator.appVersion.indexOf("AOL") == -1){
              document.write(\'<SCR\' + \'IPT LANGUAGE=VBScript\> \n\');
              document.write(\'on error resume next \n\');
              document.write(\'flash6Installed = (IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash.6"))) \n\');
              document.write(\'flash7Installed = (IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash.7"))) \n\');
              document.write(\'flash8Installed = (IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash.8"))) \n\');
              document.write(\'flash9Installed = (IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash.9"))) \n\');
              document.write(\'<\/SCR\' + \'IPT\> \n\');
              }
              function detectFlash() {
                if (navigator.plugins) {
                    if (navigator.plugins["Shockwave Flash 2.0"]|| navigator.plugins["Shockwave Flash"]) {
                        var isVersion2 = navigator.plugins["Shockwave Flash 2.0"] ? " 2.0" : "";
                        var flashDescription = navigator.plugins["Shockwave Flash" + isVersion2].description;
                        if(parseInt(flashDescription.substring(16))>=6){hasRightVersion = true;}
                    }
                }
                for (var i = 6; i <= 9; i++) {
                    if (eval("flash" + i + "Installed") == true) actualVersion = i;
                    if(navigator.userAgent.indexOf("WebTV") != -1) actualVersion = 4;
                    if (actualVersion >= 6) {
                       hasRightVersion = true;
                    } else {
                       hasRightVersion = true;
                       }
                }
            }
            detectFlash();
            if(hasRightVersion) {';
	  if($config['Available_colors']=='Unlimited'){
       $input.='document.write(\'<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" WIDTH="515" HEIGHT="120" id="colorlab_v0.02" ALIGN="">\'+
                               \'<PARAM NAME=movie VALUE="'.$GLOBALS['phpgw_info']['server']['webserver_url'].'/jinn/plugins/colorlab/colorlab_v0.02.swf?fld='.$field_name.'&val='.$value.'">\'+
                               \'<PARAM NAME=menu VALUE=false>\'+
                               \'<PARAM NAME=quality VALUE=high>\'+
                               \'<PARAM NAME=wmode VALUE=transparent>\'+
                               \'<PARAM NAME=devicefont VALUE=true>\'+
                               \'<EMBED src="'.$GLOBALS['phpgw_info']['server']['webserver_url'].'/jinn/plugins/colorlab/colorlab_v0.02.swf?fld='.$field_name.'&val='.$value.'" menu=false quality=high wmode=transparent devicefont=true WIDTH="515" HEIGHT="120" NAME="colorlab_v0.02" ALIGN="" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED>\'+
                               \'</OBJECT>\');';
      }else{
       $input.='document.write(\'<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" WIDTH="515" HEIGHT="120" id="colorlab_v0.02" ALIGN="">\'+
                               \'<PARAM NAME=movie VALUE="'.$GLOBALS['phpgw_info']['server']['webserver_url'].'/jinn/plugins/colorlab/colorlab_v0.02p.swf?fld='.$field_name.'&val='.$value.$flaStr.'">\'+
                               \'<PARAM NAME=menu VALUE=false>\'+
                               \'<PARAM NAME=quality VALUE=high>\'+
                               \'<PARAM NAME=wmode VALUE=transparent>\'+
                               \'<PARAM NAME=devicefont VALUE=true>\'+
                               \'<EMBED src="'.$GLOBALS['phpgw_info']['server']['webserver_url'].'/jinn/plugins/colorlab/colorlab_v0.02p.swf?fld='.$field_name.'&val='.$value.$flaStr.'" menu=false quality=high wmode=transparent devicefont=true WIDTH="515" HEIGHT="120" NAME="colorlab_v0.02" ALIGN="" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED>\'+
                               \'</OBJECT>\');';
      }
       $input.='} else {
                document.write(\'Colorlab requires a flash 6 plugin/activeX control, please update your browser and try again.<br>\'+
                               \'<a href="http://www.macromedia.com/shockwave/download/alternates/" target="_blank">download Flash plugin/activeX</a> (all platforms except SGI IRIX, OS/2 & WebTV)<br>\');
                }
                // -->
                </SCRIPT>
                <NOSCRIPT>
                <!--
                Colorlab requires a javascript enabled browser, please update your browser and try again.<br>
                <a href="http://www.mozilla.org/" target="_blank">download Mozilla</a><br>
                <a href="http://channels.netscape.com/ns/browsers/download.jsp" target="_blank">download Netscape</a><br>
                <a href="http://www.opera.com/download/" target="_blank">download Opera</a><br>
                <a href="http://www.apple.com/safari/" target="_blank">download Safari/a><br>
                <a href="http://www.microsoft.com/windows/ie/default.asp" target="_blank">download Internet Explorer/a>
                -->
                </NOSCRIPT>';

       return $input;
	}

	function plg_ro_colorlab($value, $config,$attr_arr)
	{
	   return plg_bv_colorlab($value, $config,$attr_arr);
	   }
	function plg_bv_colorlab($value, $config,$attr_arr)
	{
	   return lang('current color:') . ' <span style="width:10px;background-color:'.$value.'">&nbsp;&nbsp;&nbsp;</span>'.' ('.$value.')';	
	   
	   }

?>
