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

	/* $Id: class.module_download.inc.php,v 1.7 2004/04/14 13:57:24 ralfbecker Exp $ */

class module_download extends Module
{
	function module_download()
	{
		$this->arguments = array(
			'path' => array(
				'type' => 'textfield', 
				'label' => lang('The path to the file to be downloaded')
			),
			'file' => array(
				'type' => 'textfield', 
				'label' => lang('The file to be downloaded')
			),
			'text' => array(
				'type' => 'textfield',
				'label' => lang('The text for the link, if empty the module returns the raw URL (without a link)')
			),
			'op' => array(
				'type' => 'select',
				'label' => lang('Should the file be viewed in the browser or downloaded'),
				'options' => array(1 => lang('viewed'), 2 => lang('downloaded'))
			)
		);
		$this->post = array('name' => array('type' => 'textfield'));
		$this->session = array('name');
		$this->title = lang('File download');
		$this->description = lang('This module create a link for downloading a file from the VFS');
	}

	function get_content(&$arguments,$properties) 
	{
		$linkdata['menuaction'] = 'filemanager.uifilemanager.view';
		$linkdata['path'] = rawurlencode(base64_encode($arguments['path']));
		$linkdata['file'] = rawurlencode(base64_encode($arguments['file']));

		if ($arguments['op'] == 2)
		{
				$linkdata['download'] = 1;
		}
		return $arguments['text'] ?
			('<a href="' . phpgw_link('/index.php',$linkdata) . '">' . $arguments['text'] . '</a>') :
			phpgw_link('/index.php',$linkdata);
	}
}
