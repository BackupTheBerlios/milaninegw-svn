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
	HTML Special Character PLUGIN
	-------------------------------------------------------------------*/
	$description = 'Converts special characters like &euro; to HTML entitities so they appear like theay should appear.';

	$this->plugins['htmlspecialchars']['name']			= 'htmlspecialchars';
	$this->plugins['htmlspecialchars']['title']			= 'HTML Special Character Filter';
   $this->plugins['htmlspecialchars']['author']			= 'Pim Snel';
	$this->plugins['htmlspecialchars']['version']		= '1.0';
	$this->plugins['htmlspecialchars']['enable']		= 1;
	$this->plugins['htmlspecialchars']['description']	= $description;
	$this->plugins['htmlspecialchars']['db_field_hooks']= array
	(
		'char',
		'varchar',
	   'string',
		'blob',
		'longtext',
		'text'
	);
	$this->plugins['htmlspecialchars']['config']		= array
	(
		'Strip_HTML_TAGS'=>array(array('Yes','No'),'select','')
	);
	/*
	function plg_fi_nl2br($field_name, $value, $config)
	{
		$input='<textarea name="'.$field_name.'" style="width:100%; height:200">'.str_replace('<br />','',$value).'</textarea>';
		return $input;
	}
	*/
	function plg_sf_htmlspecialchars($key, $HTTP_POST_VARS,$HTTP_POST_FILES,$config)
	{
		$input=$HTTP_POST_VARS[$key];
		if (!$config['Strip_HTML_TAGS'] || $config['Strip_HTML_TAGS']=='Yes')
		{
			$input=strip_tags($input);
		}

		$entities = array(
			63 => 'euro',
			128 => 'euro',
			130 => 'sbquo',
			131 => 'fnof',
			132 => 'bdquo',
			133 => 'hellip',
			134 => 'dagger',
			135 => 'Dagger',
			136 => 'circ',
			137 => 'permil',
			138 => 'Scaron',
			139 => 'lsaquo',
			140 => 'OElig',
			145 => 'lsquo',
			146 => 'rsquo',
			147 => 'ldquo',
			148 => 'rdquo',
			149 => 'bull',
			150 => 'ndash',
			151 => 'mdash',
			152 => 'tilde',
			153 => 'trade',
			154 => 'scaron',
			155 => 'rsaquo',
			156 => 'oelig',
			159 => 'Yuml'
		);

		$new_input = '';
		for($i = 0; $i < strlen($input); $i++)
		{
			$num = ord($input{$i});
			//die($num);
			if(array_key_exists($num, $entities))
			{
				$new_input .= '&'.$entities[$num].';';
			}
			elseif($num < 127 || $num > 159)
			{
				$new_input .= $input{$i};
			}
		}
		//	return htmlentities($new_input);
		//	}



		$output=addslashes(htmlentities($new_input));

		return $output;
	}

 ?>
