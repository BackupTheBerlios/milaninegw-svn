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

	/* $Id: class.module_toc.inc.php,v 1.6 2004/02/10 14:56:33 ralfbecker Exp $ */

	class module_toc extends Module
	{
		function module_toc()
		{
			$this->arguments = array('category_id' => array('type' => 'textfield', 'label' => lang('The category to display, 0 for complete table of contents')));
			$this->title = lang('Table of contents');
			$this->description = lang('This module provides a complete table of contents, it is automatically used by the toc and category_id GET parameters');
		}

		function get_content(&$arguments,$properties)
		{
			global $objbo;
			global $page;
			$category_id = $arguments['category_id'];
			if ($category_id)
			{
				$cat = $objbo->getcatwrapper($category_id);
				if ($cat)
				{
					$page->title = lang('Category').' '.$cat->name;
					$page->subtitle = '<i>'.$cat->description.'</i>';
					$content = '<b><a href="'.sitemgr_link2('/index.php','toc=1').'">' . lang('Up to table of contents') . '</a></b>';
					if ($cat->depth > 1)
					{
						$content .= ' | <b><a href="'.sitemgr_link2('/index.php','category_id='.$cat->parent).'">' . lang('Up to parent') . '</a></b>';
					}
					$children = $objbo->getCatLinks((int) $category_id,false);
					if (count($children))
					{
						$content .= '<br/><br/><b>' . lang('Subcategories') . ':</b><br/>';
						foreach ($children as $cat_id => $child)
						{
							$content .= '<br/>&nbsp;&nbsp;&nbsp;&middot;&nbsp;<b>'.$child['link'].'</b> '.
								$objbo->getEditIconsCat($cat_id).' &ndash; '.$child['description'];
						}
					}
					$content .= '<br/><br/><b>' . lang('Pages') . ':</b><br/>';
					$links = $objbo->getPageLinks($category_id,true);
					if (count($links)>0)
					{
						foreach($links as $page_id => $pg)
						{
							$content .= "\n<br/>".
								'&nbsp;&nbsp;&nbsp;&middot;&nbsp;'.$pg['link'].' '.$objbo->getEditIconsPage($page_id,$cat_id);
							if (!empty($pg['subtitle']))
							{
								$content .= ' &ndash; <i>'.$pg['subtitle'].'</i>';
							}
							$content .= '';
						}
					}
					else
					{
						$content .= '<li>' . lang('There are no pages in this section') . '</li>';
					}
				}
				else
				{
					$content = lang('There was an error accessing the requested page. Either you do not have permission to view this page, or the page does not exist.');
				}
			}
			else
			{
				$content = '<b>' . lang('Choose a category') . ':</b><br/>';
				$links = $objbo->getCatLinks();
				if (count($links)>0)
				{
					foreach($links as $cat_id => $cat)
					{
						$buffer = str_pad('', $cat['depth']*24,'&nbsp;').'&middot;&nbsp;';
						if (!$cat['depth'])
						{
							$buffer = '<br/>'.$buffer;
						}
						$content .= "\n".$buffer.$cat['link'].' '.$objbo->getEditIconsCat($cat_id).
							' &mdash; <i>'.$cat['description'].'</i><br/>';
					}
				}
				else
				{
					$content .= lang('There are no sections available to you.');
				}
			}
			return $content;
	}
}
?>
