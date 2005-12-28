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

	/* $Id: class.module_html.inc.php,v 1.8 2004/02/26 15:37:26 ralfbecker Exp $ */

	class module_html extends Module
	{
		function module_html()
		{
			$this->i18n = true;
			$this->arguments = array(
				'htmlcontent' => array(
					'type' => 'textarea',
					'label' => lang('Enter the block content here'),
					'large' => True,	// show label above content
					'i18n' => True,
					'params' => Array('style' => 'width:100%; min-width:500px; height:300px', 'class' => 'tinyMCE' )
				)
			);
			$this->properties = array('striphtml' => array('type' => 'checkbox', 'label' => lang('Strip HTML from block content?')));
			$this->title = lang('HTML module');
			$this->description = lang('This module is a simple HTML editor');
			
		}

		function get_content(&$arguments,$properties)
		{
			if ($properties['striphtml'])
			{
				return $GLOBALS['phpgw']->strip_html($arguments['htmlcontent']);
			}
			// spamsaver emailaddress and activating the links
			if (!is_object($GLOBALS['phpgw']->html))
			{
				$GLOBALS['phpgw']->html = CreateObject('phpgwapi.html');
			}
				
			return $GLOBALS['phpgw']->html->activate_links($arguments['htmlcontent']);
		}
	}
