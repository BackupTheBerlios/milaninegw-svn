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

	---------------------------------------------------------------------

    /*-------------------------------------------------------------------
	Hide This Field PLUGIN
	-------------------------------------------------------------------*/
	$this->plugins['hidefield']['name'] 			= 'hidefield';
	$this->plugins['hidefield']['title']			= 'Hide This Field';
	$this->plugins['hidefield']['author']		= 'Pim Snel';
	$this->plugins['hidefield']['version']			= '1.0';
	$this->plugins['hidefield']['enable']			= 1;
	$this->plugins['hidefield']['description']		= 'This just hides the input field for users';
	$this->plugins['hidefield']['db_field_hooks']	= array
	(
	   'longtext',
		'text',
		'string',
		'varchar',
		'char',
		'int',
		'tinyint',
		'blob',
		'date',
		'timestamp'
	);


	function plg_fi_hidefield($field_name,$value, $config,$attr_arr)
	{
	   return '__hide__';
	}
	
	function plg_ro_hidefield($field_name,$value, $config,$attr_arr)
	{
	   return '__hide__';
	}
	
	function plg_bv_hidefield($field_name,$value, $config,$attr_arr)
	{
	   return '__hide__';
	}

 ?>
