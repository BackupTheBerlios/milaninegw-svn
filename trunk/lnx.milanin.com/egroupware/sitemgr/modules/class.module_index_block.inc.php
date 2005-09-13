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

	/* $Id: class.module_index_block.inc.php,v 1.6 2004/05/08 16:31:19 ralfbecker Exp $ */

	class module_index_block extends Module
	{
		function module_index_block()
		{
			$this->arguments = array(
				'sub_cats' => array(
					'type' => 'checkbox',
					'label' => lang('Show subcategories')
				),
				'no_full_index' => array(
					'type' => 'checkbox',
					'label' => lang('No link to full index')
				),
			);
			$this->title = 'Root Site Index';
			$this->description = lang('This module displays the root categories, its pages and evtl. subcategories. It is meant for side areas');
		}

		function get_content(&$arguments,$properties)
		{
			global $objbo;
			$indexarray = $objbo->getIndex(False,!@$arguments['sub_cats'],True);
			$subcatname = $catname = '';
			foreach($indexarray as $temppage)
			{
				if ($catname != $temppage['catname'] && $temppage['catdepth'] == 1) //category name change
				{
					if ($catname != '') //not the first name change
					{
						$content .= "\n</div>\n<br />";
					}
					$content .= "\n".'<div style="position: relative; left: '.max($temppage['catdepth']*15-30,0).'px;">';
					$catname = $temppage['catname'];
					$content .= "\n\t<b>$temppage[catlink]</b><br />";
					$subcatname = '';
				}
				if ($temppage['catdepth'] == 1)
				{
					// dont show no pages availible in Production mode, just ignore it
					if ($GLOBALS['sitemgr_info']['mode'] == 'Edit' ||
						$temppage['page_id'] && $temppage['pagelink'] != lang('No pages available'))
					{
						$content .= "\n\t&nbsp;&middot;&nbsp;$temppage[pagelink]<br />";
					}
				}
				elseif ($subcatname != $temppage['catname'] && $temppage['catdepth'] == 2)
				{
					$content .= "\n\t&nbsp;&middot;&nbsp;".str_replace('</a>',' ...</a>',$temppage[catlink]).'<br />';
					$subcatname = $temppage['catname'];
				}
			}
			if (count($indexarray))
			{
				$content .= "\n</div>";
				if (!$arguments['no_full_index'])
				{
					$content .= "\n".'<br /><i><a href="'.sitemgr_link2('/index.php','index=1').'"><font size="1">(' . lang('View full index') . ')</font></a></i>';
				}
			}
			else
			{
				$content=lang('You do not have access to any content on this site.');
			}
			return $content;
		}
	}
?>
