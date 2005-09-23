<?php
   /**************************************************************************\
   * eGroupWare - Jinn Preferences                                            *
   * http://egroupware.org                                                    *
   * Written by Pim Snel <pim@egroupware.org>                                 *
   * --------------------------------------------                             *
   *  This program is free software; you can redistribute it and/or modify it *
   *  under the terms of the GNU General Public License as published by the   *
   *  Free Software Foundation; version 2 of the License.                     *
   \**************************************************************************/

   // In the future these settings go to the plugin file 
   create_section('Images');

   $prev_img = Array(
	  'no' => lang('Never'),
	  'only_tn' => lang('Only if thumnails exits'),
	  'yes' => lang('Yes')
   );

   create_select_box('Preview thumbs or images in form','prev_img',$prev_img,"When you choose 'Never', only links to the images are displayed; when you choose 'Only if thumnails exists' previews are  shown if an thumbnail of the image exists; if you choose 'Yes' all images are shown");

   $max_prev=array(
	  "1"=>"1",
	  "2"=>"2",
	  "3"=>"3",
	  "4"=>"4",
	  "5"=>"5",
	  "10"=>"10",
	  "20"=>"20",
	  "30"=>"30",
	  "-1"=>lang("No max. number")
   );

   create_select_box('Max. number of previews in form','max_prev',$max_prev,'When a lot of images are attached to a record, the form can load very slow. You can set a maximum number of images that is show in the form.');

   create_section('WYSIWYG plugin');

   $disable_htmlarea = Array(
	  'no' => lang('No'),
	  'yes' => lang('Yes')
   );

   create_select_box('Disable the WYSIWYG/HTMLArea Plugin','disable_htmlarea',$disable_htmlarea,"The WYSIWYG plugin makes you edit text like you do in a program like OpenOffice Writer or Word. Some people don't like this feature though, so you can force JiNN not to use it.");

   create_section('JiNN Developer Settings');

   $show_extra_table_info = Array(
	  'no' => lang('No'),
	  'yes' => lang('Yes')
   );

   create_select_box('Show extra table debugging information','table_debugging_info',$show_extra_table_info,"When this is enables information like field length and field type is shown when editing record");

   $activate_alpha_features = Array(
	  'no' => lang('No'),
	  'yes' => lang('Yes')
   );

   create_select_box('Activate experimental features which are in development','experimental',$activate_alpha_features,'Only activate this if you know what your doing. You can destroy your data using experimental features.');
