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

	/* $Id: class.module_index.inc.php,v 1.6 2004/02/22 01:46:23 ralfbecker Exp $ */

	class module_index extends Module
	{
		function module_index()
		{
			$this->arguments = array();
			$this->title = "Site Index";
			$this->description = lang('This module provides the site index, it is automatically used by the index GET parameter');
		}

		function get_content(&$arguments,$properties)
		{
			global $objbo;
			$indexarray = $objbo->getIndex();
			$catname = '';
			foreach($indexarray as $temppage)
			{
				if ($catname!=$temppage['catname']) //category name change
				{
					if ($catname!='') //not the first name change
					{
						$content .= "\n</div>";
					}
					$content .= "\n".'<div style="position: relative; left: '.($temppage['catdepth']*15-15).'px;">'."\n";
					$catname = $temppage['catname'];
					if ($temppage['catdepth'])
					{
						$content .= "\t&middot;&nbsp;";
					}
					$content .= "<b>$catname</b> ".$objbo->getEditIconsCat($temppage['cat_id']).' &ndash; <i>'.
						$temppage['catdescrip'].'</i>'."\n";
				}
				$content .= "\n\t".'<div style="position: relative; left: 15px">&middot;&nbsp;'.$temppage['pagelink'];

				if ($temppage['page_id'])
				{
					$content .= ' '.$objbo->getEditIconsPage($temppage['page_id'],$temppage['cat_id']);
				}
				$content .= '</div>';
			}
			if (!count($indexarray))
			{
				$content=lang('You do not have access to any content on this site.');
			}
			else
			{
				$content .= '</div>';
			}
			return $content;
	}
}
?>
