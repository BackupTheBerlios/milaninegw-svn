<?php
   /*
   JiNN - Jinn is Not Nuke, a mutli-user, multi-site CMS for phpGroupWare
   Copyright (C)2002, 2003 Pim Snel <pim@lingewoud.nl>

   phpGroupWare - http://www.phpgroupware.org

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



   class boimagemagick extends bouser
   {
	  var $targetdir      = '';
	  var $imagemagickdir = '/usr/local/bin';
	  var $temp_dir       = '/var/tmp'; // httpd must be able to write there
	  var $file_history   = array();
	  var $temp_file      = '';
	  var $jpg_quality    = '65';
	  var $count          = 0;
	  var $image_data     = array();
	  var $error          = '';
	  var $verbose        = false;

	  function boimagemagick() 
	  {
		 //FIXME check version and leave without doing nothing when lower then 5.4.9

		 $this->common = CreateObject('jinn.bocommon');
		 $this->current_config=$this->common->get_config();
		 $this->imagemagickdir=$this->current_config['imagemagickdir'];
	  }

	  /*
	  Resize(int value, int value, string string)
	  Resize the image to given size
	  possible values:
	  arg1 > x-size, unsigned int
	  arg2 > y-size, unsigned int
	  arg3 > resize method;
	  'keep_aspect' > changes only width or height of image
	  'fit' > fit image to given size
	  */

	  function Resize($x_size=10000, $y_size=10000, $src_temp_file, $filetype, $how='keep_aspect') 
	  {

		 $target_temp_file = tempnam ("jinn/temp", "convert_");
		 unlink($target_temp_file);
		 $target_temp_file.='.'.$filetype;

		 if($this->verbose == TRUE) {
			echo "Resize:\n";
		 }

		 $method = $how=='keep_aspect'?'>':($how=='fit'?'!':'');

		 if($this->verbose == TRUE) {
			echo "  Resize method: {$how}\n";
		 }

		 if($x_size || $y_size)
		 {
			if(!$x_size) $x_size=10000;
			if(!$y_size) $y_size=10000;

			$geometry_option='-geometry';
			$resize_vals="'{$x_size}x{$y_size}{$method}'";
		 }

		 $command = "{$this->imagemagickdir}/convert $geometry_option $resize_vals '{$src_temp_file}' '{$target_temp_file}'";

		 if($this->verbose == TRUE) {
			echo "  Command: {$command}\n";
		 }
		 exec($command, $returnarray, $returnvalue);

		 if($returnvalue) 
		 {
			$this->error .= "ImageMagick: Resize failed\n";
			if($this->verbose == TRUE) 
			{
			   echo "Resize failed\n";
			}
		 } 
		 else 
		 {
			return $target_temp_file;

		 }

	  }

	  function Get_Imagetype($file)
	  {
		 $command = "{$this->imagemagickdir}/identify -format \"%m\" $file";

		 if($this->verbose == TRUE) {
			echo "  Command: {$command}\n";
		 }

		 $returnvalue =`{$command}`;		

		 $returnvalue=explode("\n",$returnvalue);

		 $filetype=$returnvalue[0];

		 return $filetype;
	  }
   }

?>
