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

   $this->plugins['unserialize']['name']				= 'unserialize';
   $this->plugins['unserialize']['title']				= 'unserialize plugin';
   $this->plugins['unserialize']['version']			= '0.1.1';
   $this->plugins['unserialize']['author']			= 'Pim Snel';
	$this->plugins['unserialize']['author']		= 'Pim Snel';
   $this->plugins['unserialize']['description']		= 'De-serialize a value';
   $this->plugins['unserialize']['enable']			= 1;
   $this->plugins['unserialize']['db_field_hooks']	= array('longtext','text','blob','varchar','string');

   // FIXME ad config:
   // 1 readonly 
   // serialize back again?
   function plg_fi_unserialize($field_name,$value,$config,$attr_arr)
   {	
	  $field_name=substr($field_name,3);	

	  $input=unserialize($value);
	  if(is_array($input)) $input=var_export($input,true);

	  return $input;
   }
   function plg_sf_unserialize($field_name,$HTTP_POST_VARS,$HTTP_POST_FILES,$config)
   {
	  $input=$HTTP_POST_VARS[$field_name];
	  $output=serialize($input);

	  return $output;
   }

?>
