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
	NewLine to Break PLUGIN
	-------------------------------------------------------------------*/
 
	$description = '
	The Newline2Break Plugin is the most simple WYSIWYG plugin there is. The
	only thing it does is replacing \'newlines\' for the Break html-tag. It\'s
	still very handy because creating paragraphs it often the only extra feature
	a webmaster needs. For savety reasons this plugin removes all other html tags
	before storing the data to teh database
	';

	$this->plugins['nl2br']['name']				= 'nl2br';
	$this->plugins['nl2br']['title']			= 'Newline2Break Filter';
	$this->plugins['nl2br']['version']			= '1.0';
	$this->plugins['nl2br']['enable']			= 1;
	$this->plugins['nl2br']['author']			= 'Pim Snel';
	$this->plugins['nl2br']['description']		= $description;
	$this->plugins['nl2br']['db_field_hooks']	= array
	(
		'blob',
		'longtext',
		'text'
	);
	$this->plugins['nl2br']['config']		= array
	(
		'Strip_HTML_TAGS'=>array(array('Yes','No'),'select','')
	);

	function plg_fi_nl2br($field_name, $value, $config,$attr_arr)
	{
		$input='<textarea name="'.$field_name.'" style="width:100%; height:200">'.str_replace('<br />','',$value).'</textarea>';
		return $input;
	}

	function plg_sf_nl2br($key, $HTTP_POST_VARS,$HTTP_POST_FILES,$config)
	{
		$input=$HTTP_POST_VARS[$key];
		if (!$config['Strip_HTML_TAGS'] || $config['Strip_HTML_TAGS']=='Yes')
		{
			$input=strip_tags($input);
		}

		$output=addslashes(nl2br($input));

		return $output;
	 }
	

 ?>
