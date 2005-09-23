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

   // explain extends
   class bogdlib // extends bouser
   {
	  var $verbose        = false;

	  function bogdlib() 
	  {
		 $this->common = CreateObject('jinn.bocommon');
		 $this->current_config=$this->common->get_config();
	  }

	  function Resize( $maxwidth=10000, $maxheight,$imagename,$filetype,$how='keep_aspect') 
	  {
		 $target_temp_file = tempnam ("jinn/temp", "gdlib_");
		 unlink($target_temp_file);
		 $target_temp_file.='.'.$filetype;

		 if(!$maxheight)$maxheight=10000;
		 if(!$maxwidth)$maxwidth=10000;
		 $qual=100;
		 $filename=$imagename;
		 $ext=$filetype;		 

		 list($curwidth, $curheight) = getimagesize($filename);
		 
		 $factor = min( ($maxwidth / $curwidth) , ($maxheight / $curheight) );

		 $sx		= 0;
		 $sy		= 0;
		 $sw		= $curwidth;
		 $sh		= $curheight;

		 $dx		= 0;
		 $dy		= 0;
		 $dw		= $curwidth * $factor;
		 $dh		= $curheight *  $factor;


		 if ($ext == "JPEG") { $src = ImageCreateFromJPEG($filename); }
		 if ($ext == "GIF") { $src = ImageCreateFromGIF($filename); }
		 if ($ext == "PNG") { $src = ImageCreateFromPNG($filename); }

		 if(function_exists('ImageCreateTrueColor')) {
			$dst = ImageCreateTrueColor($dw,$dh);
		 } else {
			$dst = ImageCreate($dw,$dh);
		 }
		 
		 if(function_exists('ImageCopyResampled'))
		 {
			imageCopyResampled($dst,$src,$dx,$dy,$sx,$sy,$dw,$dh,$sw,$sh);
		 }
		 else
		 {
			imageCopyResized($dst,$src,$dx,$dy,$sx,$sy,$dw,$dh,$sw,$sh);
		 }

		 if($ext == "JPEG") ImageJPEG($dst,$target_temp_file,$qual);
		 if($ext == "PNG") ImagePNG($dst,$target_temp_file,$qual);
		 if($ext == "GIF") ImagePNG($dst,$target_temp_file,$qual);

		 ImageDestroy($dst);
		
		 return $target_temp_file;
	  }

	  function Get_Imagetype($file)
	  {
		 $type=exif_imagetype($file);

		 switch($type)
		 {
			case 1: return 'GIF';
			   break;
			case 2: return 'JPEG';
			   break;
			case 3: return 'PNG';
			   break;
			case 4: return 'SWF';
			   break;
			case 5: return 'PSD';
			   break;
			case 6: return 'BMP';
			   break;
			case 7: return 'TIFF_II';
			   break;
			case 8: return 'TIFF_MM';
			   break;
			case 9: return 'JPC';
			   break;
			case 10 :return 'JP2';
			   break;
			case 11 :return 'JPX';
			   break;
			case 12 :return 'JB2';
			   break;
			case 13 :return 'SWC';
			   break;
			case 14 :return 'IFF';
			   break;
			case 15 :return 'WBMP';
			   break;
			case 16 :return 'XBM';
			   break;
			default:
			   return false;
		 }

	  }
   }

?>
