<?php
   /*************************************************************************\
   * eGroupWare - HTMLAREA-form-plugin for eGW-jinn                          *
   * The original script is written by interactivetools.com, inc.            *
   * Ported to eGroupWare by Pim Snel info@lingewoud.nl                      *
   * --------------------------------------------                            *
   * http://www.egroupware.org                                               *
   * http://www.interactivetools.com/                                        *
   * http://www.lingewoud.nl                                                 *
   * --------------------------------------------                            *
   * The original script HTMLAREA is distributed under a Open Source-licence *
   * See the readme.txt in the htmlarea-directory for the complete licence   *
   * text.                                                                   *
   * eGroupWare and the jinn are free software; you can                      *
   * redistribute it and/or modify it under the terms of the GNU General     *
   * Public License as published by the Free Software Foundation;            *
   * Version 2 of the License.                                               *
   \*************************************************************************/

   $description = '
   the htmlArea plugin is based on htmlArea v3beta from interactivetools.com
   licenced under the BSD licence.<P>
   htmlArea is a WYSIWYG editor replacement for any textarea field. Instead
   of teaching your software users how to code basic HTML to format their
   content.<P>
   Known issues: 
   <li>all configuration options don\'t work anymore. This will be fixed soon.</li>';

   $this->plugins['htmlArea']['name']			= 'htmlArea';
   $this->plugins['htmlArea']['title']			= 'htmlArea';
   $this->plugins['htmlArea']['version']		= '0.9.0unstable';
   $this->plugins['htmlArea']['enable']			= 1;
   $this->plugins['htmlArea']['author']			= 'Pim Snel';
   $this->plugins['htmlArea']['description']	= $description;
   $this->plugins['htmlArea']['db_field_hooks']	= array
   (
	  'blob',
	  'longtext',
	  'text'
   );

   $this->plugins['htmlArea']['config']		= array
   (
	  'UploadImageBaseDir' => array('','text','maxlength=200 size=30'),
	  'UploadImageBaseURL' => array('','text','maxlength=200 size=30'),
	  'UploadImageRelativePath' => array('','text','maxlength=200 size=30'),
	  'enable_font_buttons'=>array(array('Yes','No'),'select',''),
	  'enable_alignment_buttons'=>array(array('Yes','No'),'select',''),
	  'enable_list_buttons'=>array(array('Yes','No'),'select',''),
	  'enable_html_source_button'=>array(array('Yes','No'),'select',''),
	  'enable_tables_button'=>array(array('Yes','No'),'select',''),
	  'enable_image_button'=>array(array('Yes','No'),'select',''),
	  'enable_color_buttons'=>array(array('Yes','No'),'select',''),
	  'enable_horizontal_ruler_button'=>array(array('Yes','No'),'select',''),
	  'enable_fullscreen_editor_button'=>array(array('Yes','No'),'select',''),
	  'enable_link_button'=>array(array('Yes','No'),'select',''),
	  'custom_css'=>array('','area','')
   );

   function plg_fi_htmlArea($field_name, $value, $config,$attr_arr)
   {

	  global $local_bo;

	  if($local_bo->read_preferences('disable_htmlarea')=='yes')
	  {
		 return;
	  }

	  //	   $editor_url=$GLOBALS['phpgw_info']['server']['webserver_url'].'/jinn/plugins/htmlareaV3/';

	  if($config[enable_image_button]!='No') $bar_image = '"insertimage",';
	  if($config[enable_html_source_button]!='No') $bar_html = '"htmlmode",';
	  if($config[enable_alignment_buttons]!='No') $bar_align = '[ "justifyleft", "justifycenter", "justifyright", "justifyfull", "separator" ],';
	  if($config[enable_font_buttons]!='No') $bar_font = '[ "fontname", "space" ], [ "fontsize", "space" ], [ "formatblock", "space"], 	[ "bold", "italic", "underline", "separator" ], [ "strikethrough", "subscript", "superscript", "linebreak" ],';
	  if($config[enable_list_buttons]!='No') $bar_list = '[ "orderedlist", "unorderedlist", "outdent", "indent", "separator" ],';
	  if($config[enable_tables_button]!='No') $bar_table = '"inserttable",';
	  if($config[enable_color_buttons]!='No') $bar_colors = '[ "forecolor", "backcolor", "textindicator", "separator" ],';
	  if($config[enable_horizontal_ruler_button]!='No') $bar_ruler = '"horizontalrule",';
	  if($config[enable_fullscreen_editor_button]!='No') $bar_fullscreen = '"popupeditor",';
	  if($config[enable_link_button]!='No') $bar_link = '"createlink",';

	  // TODO
	  // make these configuration options
	  // make extra cmscode buttons and extra reset lay-out(remove tags) button
	  // activate all configuration options
	  // import css file options
	  // add special class selectbox
	  $width=470;
	  $height=400;

	  $style='width:100%; min-width:'.$width.'px; height:'.$height.'px;';

	  if (!is_object($GLOBALS['phpgw']->html))
	  {
		 $GLOBALS['phpgw']->html = CreateObject('phpgwapi.html');
	  }

	  $plugins='ContextMenu,UploadImage';

	  $GLOBALS[UploadImageBaseDir]=$config[UploadImageBaseDir];
	  $GLOBALS[UploadImageBaseURL]='http://'.$config[UploadImageBaseURL];
	  $GLOBALS[UploadImageRelativePath]=$config[UploadImageRelativePath];

	  $input = $GLOBALS['phpgw']->html->htmlarea($field_name, $value,$style,false,$plugins);

	  return $input;
   }

   function plg_ro_htmlArea($value, $config,$attr_arr)
   {}

   function plg_bv_htmlArea($value, $config,$attr_arr)
   {}

?>
