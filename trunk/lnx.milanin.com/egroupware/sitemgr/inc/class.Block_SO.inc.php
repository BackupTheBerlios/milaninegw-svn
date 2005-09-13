<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - Web Content Management                              *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: class.Block_SO.inc.php,v 1.5 2004/02/10 14:56:33 ralfbecker Exp $ */

	class Block_SO
	{
		var $id;
		var $cat_id;
		var $page_id;
		var $area;
		var $module_id;
		var $module_name;
		var $arguments;
		var $sort_order;
		var $title;
		var $view;
		var $state;
		var $version;
		
		function Block_SO()
		{
		}

		function set_version($version)
		{
			$this->arguments = $version['arguments'];
			$this->state = $version['state'];
			$this->version = $version['id'];
		}
	}
?>
