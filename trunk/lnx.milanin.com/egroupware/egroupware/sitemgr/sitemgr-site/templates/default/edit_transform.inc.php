<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - Web Content Management                              *
	* http://www.egroupware.org                                                *
	* Copyright (c) 2004 by RalfBecker@outdoor-training.de                     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: edit_transform.inc.php,v 1.5 2004/04/04 09:16:51 ralfbecker Exp $ */

class edit_transform
{
	function edit_transform()
	{
		if (!is_object($GLOBALS['phpgw']->html))
		{
			$GLOBALS['phpgw']->html = CreateObject('phpgwapi.html');
		}
		$this->modulebo = &$GLOBALS['Common_BO']->modules;
		$this->content_ui = CreateObject('sitemgr.Content_UI');
	}

	function apply_transform($title,$content,$block)
	{
		$class = $block->view == SITEMGR_VIEWABLE_ANONYMOUS ? 'editAnonymous' : 'edit';
		$frame = '<div class="'.$class.'"><div class="editIcons">';
		$frame .= '<span class="editIconText" title="'.
			lang('Module: %1, Scope: %2, Contentarea: %3, Viewable: %4',$block->module_name,
			$block->page_id ? lang('Page') : lang('Site wide'),$block->area,
			$GLOBALS['Common_BO']->viewable[$block->view]).
			'">'.$block->module_name."</span>\n";

		$frame .= $GLOBALS['objbo']->get_icons(array(
			'up.button' => array(lang('Move block up (decrease sort order)').": $block->sort_order-1",'sort_order' => -1),
			'down.button' => array(lang('Move block down (increase sort order)').": $block->sort_order+1",'sort_order' => 1),
			'edit' => array(lang('Edit this block')),
			'delete' => array(lang('Delete this block'),'confirm'=>lang('Do you realy want to delete this block?'),'deleteBlock' => $block->id),
		),array(
			'menuaction' => 'sitemgr.Content_UI.manage',
			'block_id' => $block->id
		));
		$frame .= "</div>\n";
		return $frame . $content . '</div>';
	}

	function area_transform($contentarea,$content,$page)
	{
		$frame = '<div class="editContentarea"><div class="editIcons">';
		//$frame .= $GLOBALS['phpgw']->html->image('sitemgr','question.button',
		//	lang('Contentarea').': '.$contentarea);
		$frame .= '<span class="editIconText" title="'.lang('Contentarea').': '.$contentarea.'">'.$contentarea."</span>\n";

		$permittedmodules = $this->modulebo->getcascadingmodulepermissions($contentarea,$page->cat_id);

		$link_data['menuaction'] = 'sitemgr.Content_UI.manage';
		if ($page->id || !$page->cat_id)
		{
			$link_data['page_id'] = intval($page->id);
		}
		else
		{
			$link_data['cat_id'] = $page->cat_id;
		}

		if ($permittedmodules)
		{
			$frame .= ' <select onchange="if (this.value > 0) window.open(\''.$GLOBALS['phpgw']->link('/index.php',$link_data).'&area='.$contentarea.'&add_block=\'+this.value,\'editwindow\',\'width=800,height=600,scrollbars=yes,resizable=yes\')">' .
				'<option value="0">'.lang('Add block ...').'</option>'.
				$this->content_ui->inputmoduleselect($permittedmodules) .
				'</select>';
		}
		$frame .= "</div>\n";
		return $frame . $content . '</div>';
	}
}
