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

	/* $Id: class.module_toc_block.inc.php,v 1.4 2004/02/10 14:56:33 ralfbecker Exp $ */

class module_toc_block extends Module
{
	function module_toc_block()
	{
		$this->arguments = array();
		$this->title = lang('Table of contents');
		$this->description = lang('This module provides a condensed table of contents, meant for side areas');
	}

	function get_content(&$arguments,$properties)
	{
		global $objbo;
		$indexarray = $objbo->getCatLinks(0,True,True);
		$content = "\n".'<table border="0" cellspacing="0" cellpadding="0" width="100%">'.
			'<tr><td>';
		foreach($indexarray as $cat)
		{
			$space = str_pad('',($cat['depth']-1)*18,'&nbsp;');
/*
			$content .= "\n".'<table border="0" cellspacing="0" cellpadding="0" '.
				'width="100%"><tr><td align="right" valign="top" width="5">'.
				$space.'&middot;&nbsp;</td><td width="100%"><b>'.
				$cat['link'].'</b></td></tr></table>';
*/
			$content .= "\n".$space.'&middot;&nbsp;<b>'.$cat['link'].'</b><br />';
		}
		$content .= "\n</td></tr></table>";
		if (count($indexarray)==0)
		{
			$content=lang('You do not have access to any content on this site.');
		}
		return $content;
	}
}
?>
