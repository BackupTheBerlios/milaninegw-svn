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

// 	$Id: class.module_ytv.inc.php,v 1.3 2004/02/10 14:56:33 ralfbecker Exp $

class module_ytv extends Module 
{
	function module_ytv()
	{
		$this->arguments = array(
// 			'name' => array(
// 				'type' => 'textfield', 
// 				'label' => lang('The person to say ytv to')
// 			)
		);
// 		$this->session = array('name');
		$this->title = lang('YouTube video player');
		$this->description = lang('This is youtube.com grabbing module');
	}

	function get_content(&$arguments,$properties) 
	{
		return '<center>
                 <embed src="/tv/flvplayer.swf?file=/tv/ytv/'.$_REQUEST['ytvid'].'"
                 width="450" height="370" quality="high" type="application/x-shockwave-flash"
                  pluginspage="http://www.macromedia.com/go/getflashplayer" />
                  </center>';
	}
}
