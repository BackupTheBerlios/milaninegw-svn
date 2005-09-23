<?php
   /*
   JiNN - Jinn is Not Nuke, a mutli-user, multi-site CMS for phpGroupWare
   Copyright (C)2002, 2004 Pim Snel <pim@lingewoud.nl>

   phpGroupWare - http://www.egroupware.org

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

   $this->plugins['timestamp']['name']				= 'timestamp';
   $this->plugins['timestamp']['title']				= 'Timestamp plugin';
   $this->plugins['timestamp']['author']			= 'Pim Snel';
   $this->plugins['timestamp']['version']			= '0.2.1';
   $this->plugins['timestamp']['description']		= 'Make the user choose for a new stamp of saving the exiting stamp';
   $this->plugins['timestamp']['enable']			= 1;
   $this->plugins['timestamp']['db_field_hooks']	= array('timestamp');


   $this->plugins['timestamp']['config']		= array
   (
	  'Default_action'=> array( array('Leave value untouched','New Time Stamp') /* 1st is default the rest are all possibilities */ ,'select',''),
	  'Allow_users_to_choose_action'=> array( array('False','True') /* 1st is default the rest are all possibilities */ ,'select',''),
   );

   function plg_fi_timestamp($field_name,$value,$config,$attr_arr)
   {	

	  global $local_bo;
	  $field_name=substr($field_name,3);	

	  if($config[Default_action]=='Leave value untouched')
	  {	   
		 $input='<input type="hidden" name="FLD'.$field_name.'" value="'.$value.'" />';
		 $input.=$local_bo->common->format_date($value);
	  }
	  else
	  {
		 $input=$local_bo->common->format_date($value);

	  }

	  return $input;
   }

   function plg_ro_timestamp($value,$config,$attr_arr)
   {	
	  return plg_bv_timestamp($value,$config,$attr_arr);
   }

   function plg_bv_timestamp($value,$config,$attr_arr)
   {	
	  global $local_bo;

	  $input=$local_bo->common->format_date($value);

	  return $input;
   }


?>
