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

	/* $Id: class.module_lang_block.inc.php,v 1.6.2.1 2004/08/22 11:33:08 ralfbecker Exp $ */

	class module_lang_block extends Module
	{
		function module_lang_block()
		{
			$this->arguments = array();
			$this->properties = array();
			$this->title = lang('Choose language');
			$this->description = lang('This module lets users choose language');
		}
	
		function get_content(&$arguments,$properties)
		{
			if ($GLOBALS['sitemgr_info']['sitelanguages'])
			{
				$content = '<form name="langselect" method="post" action="">';
				$content .= '<select onchange="location.href=this.value" name="language">';
				foreach ($GLOBALS['sitemgr_info']['sitelanguages'] as $lang)
				{
					$selected='';
					if ($lang == $GLOBALS['sitemgr_info']['userlang'])
					{                                                 
						$selected = 'selected="1" ';
					}                                          
					$content .= '<option ' . $selected . 'value="' . str_replace('&','&amp;',$this->link(array(),array('lang'=>$lang))) . '">'.$GLOBALS['Common_BO']->getlangname($lang) . '</option>';
				}
				$content .= '</select>';
				$content .= '</form>';
				
				return $content;
			}
			return lang('No sitelanguages configured');
		}
	}
