<?php
	/**************************************************************************\
	* eGroupWare - news headlines                                              *
	* http://www.egroupware.org                                                *
	* Written by Mark Peters <mpeters@satx.rr.com>                             *
	* Based on pheadlines 0.1 19991104 by Dan Steinman <dan@dansteinman.com>   *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: index.php,v 1.37 2004/01/27 18:35:51 reinerj Exp $ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'           => 'headlines',
		'enable_network_class' => True,
		'noheader'             => True,
		'nonavbar'             => True
	);
	include('../header.inc.php');

	if(!count($GLOBALS['phpgw_info']['user']['preferences']['headlines']))
	{
		$GLOBALS['phpgw']->redirect_link('/headlines/preferences.php');
	}
	else
	{
		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();
	}

	if(!$GLOBALS['phpgw_info']['user']['preferences']['headlines']['headlines_layout'])
	{
		$GLOBALS['phpgw']->preferences->add('headlines','headlines_layout','basic');
		$GLOBALS['phpgw']->preferences->save_repository();
		$GLOBALS['phpgw_info']['user']['preferences']['headlines']['headlines_layout'] = 'gray';
	}

	foreach($GLOBALS['phpgw_info']['user']['preferences']['headlines'] as $n => $name)
	{
		if(is_int($n))
		{
			$sites[] = $n;
		}
	}

	$headlines = CreateObject('headlines.headlines');
	$GLOBALS['phpgw']->template->set_file(array(
		'layout_row' => 'layout_row.tpl',
		'form'       => $GLOBALS['phpgw_info']['user']['preferences']['headlines']['headlines_layout'] . '.tpl'
	));
	$GLOBALS['phpgw']->template->set_block('form','channel');
	$GLOBALS['phpgw']->template->set_block('form','row');

	$j = 0;
	$i = count($sites);
	if(is_array($sites))
	{
		foreach($sites as $site)
		{
			$j++;
			$headlines->readtable($site);

			$GLOBALS['phpgw']->template->set_var('channel_url',$headlines->base_url);
			$GLOBALS['phpgw']->template->set_var('channel_title',$headlines->display);

			$links = $headlines->getLinks($site);
			if($links == False)
			{
				$var = Array(
					'item_link'  => '',
					'item_label' => '',
					'error'      => lang('Unable to retrieve links').'.'
				);
				$GLOBALS['phpgw']->template->set_var($var);
				$s .= $GLOBALS['phpgw']->template->parse('o_','row');
			}
			else
			{
				while(list($title,$link) = each($links))
				{
					$var = Array(
						'item_link'  => stripslashes($link),
						'item_label' => stripslashes($title),
						'error'      => ''
					);
					$GLOBALS['phpgw']->template->set_var($var);
					$s .= $GLOBALS['phpgw']->template->parse('o_','row');
				}
			}
			$GLOBALS['phpgw']->template->set_var('rows',$s);
			unset($s);

			$GLOBALS['phpgw']->template->set_var('section_' . $j,$GLOBALS['phpgw']->template->parse('o','channel'));

			if($j == 3 || $i == 1)
			{
				$GLOBALS['phpgw']->template->pfp('out','layout_row');
				$GLOBALS['phpgw']->template->set_var('section_1', '');
				$GLOBALS['phpgw']->template->set_var('section_2', '');
				$GLOBALS['phpgw']->template->set_var('section_3', '');
				$j = 0;
			}
			$i--;
		}
	}
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
