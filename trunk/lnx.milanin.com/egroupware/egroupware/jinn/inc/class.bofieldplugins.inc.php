<?php
   /*
   JiNN - Jinn is Not Nuke, a mutli-user, multi-site CMS for phpGroupWare
   Copyright (C)2002, 2003 Pim Snel <pim@lingewoud.nl>

   eGroupWare - http://www.egroupware.org

   This file is part of JiNN

   JiNN is free software; you can redistribute it and/or modify it under
   the terms of the GNU General Public License as published by the Free
   Software Foundation; Version 2 of the License.

   JiNN is distributed in the hope that it will be useful,but WITHOUT ANY
   WARRANTY; without even the implied warranty of MERCHANTABILITY or 
   FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
   for more details.

   You should have received a copy of the GNU General Public License 
   along with JiNN; if not, write to the Free Software Foundation, Inc.,
   59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
   */

   class bofieldplugins 
   {
	  var $plugins;

	  function bofieldplugins()
	  {
		 $this->include_plugins();
	  }

	  /**
	  * include ALL plugins
	  */
	  function include_plugins()
	  {
		 if ($handle = opendir(PHPGW_SERVER_ROOT.'/jinn/plugins')) {

			/* This is the correct way to loop over the directory. */
			while (false !== ($file = readdir($handle))) 
			{ 
			   if (substr($file,0,7)=='plugin.')
			   {
				  include_once(PHPGW_SERVER_ROOT.'/jinn/plugins/'.$file);
			   }
			}
			closedir($handle); 
		 }
	  }
   }
?>
