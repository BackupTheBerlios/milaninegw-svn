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
   Maintainance of this version stops please use "htmlArea" instead.

   Old non-api functionality is removed

   the htmlAreaV3 plugin is based on htmlArea v3 from interactivetools.com
   licenced under the BSD licence.<P>
   htmlArea is a WYSIWYG editor replacement for any textarea field. Instead
   of teaching your software users how to code basic HTML to format their
   content.<P>
   Known issues: 
   <li>When you use the setting "Use new api class" all other 
   configuration options don\'t work anymore.</li>
   <li>When you disable this option, there are known problems in IE. The old one will never be updated. the configuration options will be activated in the future for the api class.</li>';

   $this->plugins['htmlAreaV3']['name']			= 'htmlAreaV3';
   $this->plugins['htmlAreaV3']['title']			= 'htmlArea v3';
   $this->plugins['htmlAreaV3']['author']			= 'Pim Snel';
   $this->plugins['htmlAreaV3']['version']			= '0.8.5';
   $this->plugins['htmlAreaV3']['enable']			= 1;
   $this->plugins['htmlAreaV3']['description']		= $description;
   $this->plugins['htmlAreaV3']['db_field_hooks']	= array
   (
	  'blob',
	  'longtext',
	  'text'
   );

   $this->plugins['htmlAreaV3']['config']		= array
   (
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
	  //		'use_new_api_class'=>array(array('Yes','No'),'select',''),
	  'custom_css'=>array('','area','')
   );

   function plg_fi_htmlAreaV3($field_name, $value, $config,$attr_arr)
   {

	  global $local_bo;

	  if($local_bo->read_preferences('disable_htmlarea')=='yes')
	  {
		 return;
	  }
	  
	  $editor_url=$GLOBALS['phpgw_info']['server']['webserver_url'].'/jinn/plugins/htmlareaV3/';

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

	  /*********************************************************************\
	  * $input['field'] will be rendered in the form                        *
	  \*********************************************************************/
	  // TODO
	  // make these configuration options
	  // make extra cmscode buttons and extra reset lay-out(remove tags) button
	  // activate all configuration options
	  // import css file options
	  // add special class selectbox
	  $width=470;
	  $height=300;

	  $style='width:100%; min-width:'.$width.'px; height:'.$height.'px;';

	  if (!is_object($GLOBALS['phpgw']->html))
	  {
		 $GLOBALS['phpgw']->html = CreateObject('phpgwapi.html');
	  }
	  $input = $GLOBALS['phpgw']->html->htmlarea($field_name, $value,$style);

	  return $input;
   }

?>
