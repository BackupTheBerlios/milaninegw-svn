<?php
   /*
   JiNN - Jinn is Not Nuke, a mutli-user, multi-site CMS for eGroupWare
   Copyright (C)2002, 2004 Pim Snel <pim@lingewoud.nl>

   eGroupWare - http://www.egroupware.org

   This file is part of JiNN

   JiNN is free software; you can redistribute it and/or modify it under
   the terms of the GNU General Public License as published by the Free
   Software Foundation; either version 2 of the License, or (at your 
   option) any later version.

   JiNN is distributed in the hope that it will be useful,but WITHOUT ANY
   WARRANTY; without even the implied warranty of MERCHANTABILITY or 
   FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
   for more details.

   You should have received a copy of the GNU General Public License 
   along with JiNN; if not, write to the Free Software Foundation, Inc.,
   59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
   */

   /* 
   plugin.imagepath.php contains the standard image-upload plugin for 
   JiNN number off standardly available 
   plugins for JiNN. 
   */

   $this->plugins['imagepath']['name']				= 'imagepath';
   $this->plugins['imagepath']['title']				= 'ImagePath plugin';
   $this->plugins['imagepath']['author']			= 'Pim Snel';
   $this->plugins['imagepath']['version']			= '0.9.3';
   $this->plugins['imagepath']['enable']			= 1;

   $this->plugins['imagepath']['description']		= '
   plugin for uploading/resizing images and storing their imagepaths in
   to database, using default uploadpath for site or object';

   $this->plugins['imagepath']['db_field_hooks']	= array
   (
	  'text',
	  'longtext',
	  'varchar',
	  'bpchar',
	  'string',
	  'blob'
   );

   /* ATTENTION: spaces and special character are not allowed in config array 
   use underscores for spaces */
   $this->plugins['imagepath']['config']		= array
   (
	  /* array('default value','input field type', 'extra html properties')*/
	  'Max_files' => array('3','text','maxlength=2 size=2'), 
	  'Max_image_width' => array('','text','maxlength=4 size=4'),
	  'Max_image_height' => array('','text','maxlength=4 size=4'),
	  'Image_filetype' => array(array('png','gif','jpg'),'select','maxlength=3 size=3'),
	  'Generate_thumbnail' => array( array('False','True') /* 1st is default the rest are all possibilities */ ,'select',''),
	  'Max_thumbnail_width' => array('100','text','maxlength=3 size=3'),
	  'Max_thumbnail_height'=> array('100','text','maxlength=3 size=3'),
	  'Allow_other_images_sizes'=> array( array('False','True') /* 1st is default the rest are all possibilities */ ,'select',''),
   );

   function plg_fi_imagepath($field_name,$value,$config,$attr_arr)
   {	
	  global $local_bo;

	  $field_name=substr($field_name,3);	

	  if($local_bo->common->so->config[server_type]=='dev')
	  {
		 $field_prefix='dev_';
	  }

	  if($local_bo->site_object[$field_prefix.'upload_path'])
	  {
		 $upload_path=$local_bo->site_object[$field_prefix.'upload_path'];
	  }
	  elseif($local_bo->site[$field_prefix.'upload_path'])
	  {
		 $upload_path=$local_bo->site[$field_prefix.'upload_path'];
	  }

	  /* Check if everything is set to upload files */ 
	  if(!$upload_path)
	  {
		 $input=lang('The path to upload images is not set, please contact your JiNN administrator.');
		 return $input;
	  }
	  elseif(!file_exists($upload_path))
	  {
		 $input=lang('The path to upload images is not correct, please contact your JiNN administrator.');
		 return $input;
	  }
	  elseif(!is_dir($upload_path.SEP.'/normal_size') && !mkdir($upload_path.SEP.'/normal_size', 0755))
	  {
		 $input=lang('The image normal_size-directory subdirectory does not exist and cannot be created ...');
		 $input.=lang('Please contact Administrator with this message.');
		 return $input;
	  }
	  elseif(!touch($upload_path.SEP.'normal_size'.SEP.'JiNN_write_test'))
	  {
		 $input=lang('The image_path normal_size subdirectory is not writable by the webserver ...');
		 $input.=lang('please contact Administrator with this message');
		 return $input;
	  }

	  /* everything ok, remove temporary file */
	  unlink($upload_path.SEP.'normal_size'.SEP.'JiNN_write_test');

	  if($config['Generate_thumbnail']=='True')
	  {
		 if(!is_dir($upload_path.SEP.'thumb') && !mkdir($upload_path.SEP.'thumb', 0755))
		 {
			$input= lang("thumb directory does not exist or is not correct ...");
			$input.=lang('please contact Administrator with this message');
			return $input;
		 }
		 elseif(!touch($upload_path.SEP.'thumb'.SEP.'JiNN_write_test'))
		 {
			$input=lang('The image_path thumb subdirectory is not writable by the webserver ...');
			$input.=lang('please contact Administrator with this message');
			return $input;
		 }

		 /* everything ok, remove temporary file */
		 unlink($upload_path.SEP.'thumb'.SEP.'JiNN_write_test');
	  }

	  $table_style='';
	  $cell_style='style="border-width:1px;border-style:solid;border-color:grey"';
	  $img_style='style="border-style:solid;border-width:1px;border-color:#000000"';

	  $input.='<table '.$table_style.' cellpadding="3" width="100%">';
		 /****************************************
		 * if value is set, show existing images *
		 ****************************************/	
		 if(trim($value))// FIXME or rather TESTME
		 {
			$input.='<input type="hidden" name="IMG_ORG'.$field_name.'" value="'.$value.'">';

			$value=explode(';',$value);

			if (is_array($value) && count($value)>0)
			{
			   $i=0;

			   $max_prev=$local_bo->read_preferences('max_prev');

			   foreach($value as $img_path)
			   {
				  $i++;

				  unset($imglink); 
				  unset($thumblink); 
				  unset($popup); 

				  /* check for image and create previewlink */
				  if(is_file($upload_path . SEP . $img_path))
				  {
					 $imglink=$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.uiuser.file_download&file='.$upload_path.SEP.$img_path);
					 // FIXME move code to class
					 $image_size=getimagesize($upload_path . SEP. $img_path);
					 $pop_width = ($image_size[0]+50);
					 $pop_height = ($image_size[1]+50);

					 $popup = "img_popup('".base64_encode($imglink)."','$pop_width','$pop_height');";
			   
				  }

				  /* check for thumb and create previewlink */
				  if(is_file($upload_path . SEP . str_replace('normal_size','thumb',$img_path)))
				  {
					 $tmpthumbpath=$upload_path.SEP.str_replace('normal_size','thumb',$img_path);
					 $thumblink=$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.uiuser.file_download&file='.$tmpthumbpath);
				  }

				  $input.='<tr><td '.$cell_style.' valign="top">'.$i.'.</td><td '.$cell_style.'>';

						// if URL exists show link or if set show image in form
						if($local_bo->read_preferences('prev_img')!='no' &&  ($max_prev>=$i || $max_prev==-1) && $imglink) 
						{	
						   if($local_bo->read_preferences('prev_img')=='yes')
						   {
							  if($thumblink)
							  {
								 $input.='<a href="javascript:'.$popup.'"><img src="'.$thumblink.'" alt="preview" '.$img_style.' /></a>';
							  }
							  else
							  {
								 $input.='<img src="'.$imglink.'" alt="preview" '.$img_style.' />';
							  }
						   }
						   elseif($local_bo->read_preferences('prev_img')=='only_tn' && $thumblink)
						   {
							  $input.='<a href="javascript:'.$popup.'"><img src="'.$thumblink.'" alt="preview" '.$img_style.' /></a>';
						   }
						   else
						   {
							  $input.='<b><a href="javascript:'.$popup.'">'.$img_path.'</a></b>';
						   }
						}
						else  
						{
						   if($imglink)
						   {
								 $input.='<b><a href="javascript:'.$popup.'">'.$img_path.'</a></b>';
						   }
						   else
						   {
							  $input.='<b>'.$img_path.'</b>';
						   }
						}

						$input.='</td><td '.$cell_style.' valign="top"><input type="checkbox" value="'.$img_path.'" name="IMG_DEL'.$field_name.$i.'"> '.lang('remove').'</td></tr>';
			   }
			}
		 }

		 /***************************************
		 * get max images, set max 5 filefields *
		 ***************************************/
		 if (is_numeric($config[Max_files])) 
		 {
			if ($config[Max_files]>30) $num_input=30;
			else $num_input =$config[Max_files];
		 }
		 else 
		 {
			$num_input=10;
		 }

		 for($i=1;$i<=$num_input;$i++)
		 {
			$input.='<tr><td colspan="3" '.$cell_style.'>';
				  if($num_input==1) 
				  {
					 $input .=lang('add image').'<input type="file" name="IMG_SRC'.$field_name.$i.'">';
				  }
				  else
				  {
					 $input.=lang('add image %1', $i).' <input type="file" name="IMG_SRC'.$field_name.$i.'">';
				  }

				  /* when user is allowed to give own image sizes */
				  if($config['Allow_other_images_sizes']=='True')
				  {
					 $input.= '<table>';
						$input.='<tr><td>'.lang('Optional max. height').'('.lang('default').':'.$config['Max_image_height'].')</td><td><input type="text" size="3" name="IMG_HEI'.$field_name.$i.'"></td></tr>';
						$input.='<tr><td>'.lang('Optional max. width').'('.lang('default').':'.$config['Max_image_width'].')</td><td><input type="text" size="3" name="IMG_WID'.$field_name.$i.'"></td></tr>';
						$input.='</table>';
				  }

				  $input.='</td></tr>';			
		 }
		 $input.='</table>';
	  $input.='<input type="hidden" name="FLD'.$field_name.'" value="">';
	  return $input;
   }

   function plg_sf_imagepath($field_name,$HTTP_POST_VARS,$HTTP_POST_FILES,$config)
   /****************************************************************************\
   * main image data function                                                   *
   \****************************************************************************/
   {
	  global $local_bo;

	  /* choose image library to use */
	  if($local_bo->common->so->config[use_magick]=='MAGICK')
	  {
		 $graphic=CreateObject('jinn.boimagemagick');
	  }
	  else
	  {
		 $graphic=CreateObject('jinn.bogdlib');
	  }

	  if($local_bo->common->so->config[server_type]=='dev')
	  {
		 $field_prefix='dev_';
	  }

	  if($local_bo->site_object[$field_prefix.'upload_path'])
	  {
		 $upload_path=$local_bo->site_object[$field_prefix.'upload_path'];
	  }
	  elseif($local_bo->site[$field_prefix.'upload_path'])
	  {
		 $upload_path=$local_bo->site[$field_prefix.'upload_path'];
	  }

	  $images_to_delete=$local_bo->common->filter_array_with_prefix($HTTP_POST_VARS,'IMG_DEL');

	  if (count($images_to_delete)>0){

		 $image_path_changed=True;
		 // delete from harddisk
		 foreach($images_to_delete as $image_to_delete)
		 {
			if (!@unlink($upload_path.'/'.$image_to_delete)) $unlink_error++;
			// if generate thumb
			if (!@unlink($upload_path.'/'.$thumb_to_delete)) $unlink_error++;
		 }

		 $images_org=explode(';',$HTTP_POST_VARS['IMG_ORG'.substr($field_name,3)]);

		 foreach($images_org as $image_org)
		 {
			if (!in_array($image_org,$images_to_delete))
			{
			   if ($image_path_new) $image_path_new.=';';
			   $image_path_new.=$image_org;
			}
		 }
	  }
	  else
	  {
		 $image_path_new.=$HTTP_POST_VARS['IMG_ORG'.substr($field_name,3)];
	  }

	  /* make array again of the original images*/
	  $images_array=explode(';',$image_path_new);
	  unset($image_path_new);

	  /* finally adding new image and if neccesary a new thumb */
	  $images_to_add=$local_bo->common->filter_array_with_prefix($HTTP_POST_FILES,'IMG_SRC'.substr($field_name,3));


	  $images_to_add_hei=$local_bo->common->filter_array_with_prefix($HTTP_POST_VARS,'IMG_HEI'.substr($field_name,3));
	  $images_to_add_wid=$local_bo->common->filter_array_with_prefix($HTTP_POST_VARS,'IMG_WID'.substr($field_name,3));

	  // quick check for new images
	  if(is_array($images_to_add))
	  foreach($images_to_add as $imagecheck)
	  {
		 if($imagecheck['name']) $num_img_to_add++;

	  }

	  if ($num_img_to_add)
	  {
		 if($config['Generate_thumbnail'])
		 {
			if(!$config['Max_thumbnail_width'] && $config['Max_thumbnail_height'])
			{
			   $config['Max_thumbnail_width']='100';
			}
		 }

		 $img_position=0;
		 foreach($images_to_add as $add_image)
		 {
			if($add_image['name'])
			{

			   if($images_to_add_hei[$img_position] || $images_to_add_wid[$img_position])
			   {
				  /* set user size */
				  $img_size = GetImageSize($add_image['tmp_name']);
				  if ($images_to_add_wid[$img_position] && $img_size[0] > $images_to_add_wid[$img_position])
				  {
					 $new_img_width=$images_to_add_wid[$img_position];
				  }

				  if ($images_to_add_hei[$img_position] && $img_size[1] > $images_to_add_hei[$img_position])
				  {
					 $new_img_height=$images_to_add_hei[$img_position];
				  }
			   }
			   else
			   {
				  /* default set size */
				  $img_size = GetImageSize($add_image['tmp_name']);
				  if ($config['Max_image_width'] && $img_size[0] > $config['Max_image_width'])
				  {
					 $new_img_width=$config['Max_image_width'];
				  }

				  if ($config['Max_image_height'] && $img_size[1] > $config['Max_image_height'])
				  {
					 $new_img_height=$config['Max_image_height'];
				  }
			   }

			   /* get original type */
			   $filetype=$graphic->Get_Imagetype($add_image['tmp_name']);	
			   if(!$filetype)
			   {
				  die(lang("The file you uploaded named %1 is not an imagefile, is corrupt, or the filetype is not supported by JiNN. If this error repeates, please check your ImageMagick installation.  Older version of ImageMagick are known not work properly with JiNN. Be sure to install at least Version 5.4.9 or higher",$add_image['name']));
			   }
			   elseif($filetype!='JPEG' && $filetype!='GIF' && $filetype!='PNG')
			   {
				  $filetype='PNG';
				  $new_temp_file=$graphic->Resize($new_img_width,$new_img_height,$add_image['tmp_name'],$filetype);
				  if(!$new_temp_file) die(lang('the resize process failed, please contact the administrator'));

			   }
			   elseif($new_img_width || $new_img_height)
			   {
				  $target_image_name.='.'.$filetype;
				  $new_temp_file=$graphic->Resize($new_img_width,$new_img_height,$add_image['tmp_name'],$filetype);
				  if(!$new_temp_file) die(lang('the resize process failed, please contact the administrator'));
			   }
			   else
			   {
				  $new_temp_file=$add_image['tmp_name']; // just copy
			   }

			   /* if thumb */
			   if($config['Generate_thumbnail']=='True')
			   {
				  //generate thumb
				  $new_temp_thumb=$graphic->Resize($config['Max_thumbnail_width'],
				  $config['Max_thumbnail_height'],$add_image['tmp_name'],$filetype);
			   }

			   $target_image_name = time() . ereg_replace("[^a-zA-Z0-9_.]", '_', $add_image['name']);

			   if(substr(substr($target_image_name,-4),0,1) =='.') 
			   {
				  $target_image_name = substr($target_image_name,0,(strlen($target_image_name)-3)).$filetype;	
			   }
			   else $target_image_name .='.'.$filetype;

			   if(is_file($upload_path . SEP . 'normal_size' . SEP .$target_image_name))
			   {
				  $target_image_name='another_'.$target_image_name;
			   }

			   if (copy($new_temp_file, $upload_path.SEP.'normal_size'.SEP.$target_image_name))
			   {
				  $images_array[$img_position]='normal_size'.SEP.$target_image_name;
				  if($config['Generate_thumbnail'])
				  {
					 copy($new_temp_thumb, $upload_path.SEP.'thumb'.SEP.$target_image_name);
				  }
			   }
			   else
			   {
				  die('failed copying '.$new_temp_file.' to '.$upload_path.SEP.'normal_size'.SEP.$target_image_name.' ...');
			   }
			}

			$img_position++;
		 }
	  }

	  if(is_array($images_array))
	  {
		 //check max images
		 if( count($images_array) > $config[Max_files] )
		 {
			$images_array=array_slice($images_array, 0, $config[Max_files]);
		 }	

		 foreach ($images_array as $image_string)
		 {
			if($image_path_new) $image_path_new .= ';';
			$image_path_new.=$image_string;
		 }						
	  }

	  // make return array for storage
	  if($image_path_new)
	  {
		 return $image_path_new;
	  }
	  elseif($image_path_changed)
	  {
		 return null;
	  }

	  return '-1'; /* return -1 when there no value to give but the function finished succesfully */
   }


   function plg_ro_imagepath($value,$config,$where_val_enc)
   {

	  global $local_bo;
	  $field_name=substr($field_name,3);	

	  if($local_bo->common->so->config[server_type]=='dev')
	  {
		 $field_prefix='dev_';
	  }

	  if($local_bo->site_object[$field_prefix.'upload_path'])
	  {
		 $upload_path=$local_bo->site_object[$field_prefix.'upload_path'];
	  }
	  elseif($local_bo->site[$field_prefix.'upload_path'])
	  {
		 $upload_path=$local_bo->site[$field_prefix.'upload_path'];
	  }

	  $table_style='';
	  $cell_style='style="border-width:1px;border-style:solid;border-color:grey"';
	  $img_style='style="border-style:solid;border-width:1px;border-color:#000000"';

	  $input.='<table '.$table_style.' cellpadding="3" width="100%">';
	  if(trim($value))// FIXME or rather TESTME
	  {
		 $input.='<input type="hidden" name="IMG_ORG'.$field_name.'" value="'.$value.'">';

		 $value=explode(';',$value);

		 if (is_array($value) && count($value)>0)
		 {
			$i=0;

			$max_prev=$local_bo->read_preferences('max_prev');

			foreach($value as $img_path)
			{
			   $i++;

			   unset($imglink); 
			   unset($thumblink); 
			   unset($popup); 

			   /* check for image and create previewlink */
			   if(is_file($upload_path . SEP . $img_path))
			   {
				  $imglink=$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.uiuser.file_download&file='.$upload_path.SEP.$img_path);
				  // FIXME move code to class
				  $image_size=getimagesize($upload_path . SEP. $img_path);
				  $pop_width = ($image_size[0]+50);
				  $pop_height = ($image_size[1]+50);

				  $popup = "img_popup('".base64_encode($imglink)."','$pop_width','$pop_height');";

			   }

			   /* check for thumb and create previewlink */
			   if(is_file($upload_path . SEP . str_replace('normal_size','thumb',$img_path)))
			   {
				  $tmpthumbpath=$upload_path.SEP.str_replace('normal_size','thumb',$img_path);
				  $thumblink=$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.uiuser.file_download&file='.$tmpthumbpath);
			   }

			   $input.='<tr><td '.$cell_style.' valign="top">'.$i.'.</td><td '.$cell_style.'>';

					 // if URL exists show link or if set show image in form
					 if($local_bo->read_preferences('prev_img')!='no' &&  ($max_prev>=$i || $max_prev==-1) && $imglink) 
					 {	
						if($local_bo->read_preferences('prev_img')=='yes')
						{
						   if($thumblink)
						   {
							  $input.='<a href="javascript:'.$popup.'"><img src="'.$thumblink.'" alt="preview" '.$img_style.' /></a>';
						   }
						   else
						   {
							  $input.='<img src="'.$imglink.'" alt="preview" '.$img_style.' />';
						   }
						}
						elseif($local_bo->read_preferences('prev_img')=='only_tn' && $thumblink)
						{
						   $input.='<a href="javascript:'.$popup.'"><img src="'.$thumblink.'" alt="preview" '.$img_style.' /></a>';
						}
						else
						{
						   $input.='<b><a href="javascript:'.$popup.'">'.$img_path.'</a></b>';
						}
					 }
					 else  
					 {
						if($imglink)
						{
						   $input.='<b><a href="javascript:'.$popup.'">'.$img_path.'</a></b>';
						}
						else
						{
						   $input.='<b>'.$img_path.'</b>';
						}
					 }

					 $input.='</td></tr>';
			}
		 }
	  }

		 $input.='</table>';

	return $input;


   }

   
   function plg_bv_imagepath($value,$config,$where_val_enc)
   {

	  global $local_bo;
	  $field_name=substr($field_name,3);	

	  if($local_bo->common->so->config[server_type]=='dev')
	  {
		 $field_prefix='dev_';
	  }

	  if($local_bo->site_object[$field_prefix.'upload_path'])
	  {
		 $upload_path=$local_bo->site_object[$field_prefix.'upload_path'];
	  }
	  elseif($local_bo->site[$field_prefix.'upload_path'])
	  {
		 $upload_path=$local_bo->site[$field_prefix.'upload_path'];
	  }

	  /* if value is set, show existing images */	
	  if($value)
	  {
		 $value=explode(';',$value);

		 /* there are more images */
		 if (is_array($value))
		 {
			$i=0;
			foreach($value as $img_path)
			{
			   $i++;

			   unset($imglink); 
			   unset($popup); 

			   /* check for image and create previewlink */
			   if(is_file($upload_path . SEP . $img_path))
			   {
				  
				  $imglink=$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.uiuser.file_download&file='.$upload_path.SEP.$img_path);

				  // FIXME move code to class
				  $image_size=getimagesize($upload_path . SEP. $img_path);
				  $pop_width = ($image_size[0]+50);
				  $pop_height = ($image_size[1]+50);

				  $popup = "img_popup('".base64_encode($imglink)."','$pop_width','$pop_height');";
			   }
			   
			   if($imglink) $display.='<a href="javascript:'.$popup.'">'.$i.'</a>';
			   else $display.=' '.$i;
			   $display.=' ';

			}
		 }
	  }

	  return $display;


   }

?>
