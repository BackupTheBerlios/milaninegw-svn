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

	/* $Id: class.module_redirect.inc.php,v 1.5 2004/02/20 11:31:59 ralfbecker Exp $ */

class module_redirect extends Module 
{
	function module_redirect()
	{
		$this->arguments = array(
			'URL' => array(
				'type' => 'textfield',
				'params' => array('size' => 100),
				'label' => lang('The URL to redirect to')
			)
		);
		$this->title = lang('Redirection');
		$this->description = lang('This module lets you define pages that redirect to another URL, if you use it, there should be no other block defined for the page');
	}

	function get_content(&$arguments,$properties) 
	{
		if ($GLOBALS['sitemgr_info']['mode'] != 'Edit')
		{
			$GLOBALS['phpgw']->redirect($arguments['URL']);
		}
		else
		{
			return lang('The URL to redirect to').': <a href="'.$arguments['URL'].'">'.$arguments['URL'].'</a>';
		}
	}
}
