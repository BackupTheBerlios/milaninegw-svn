<?php
	/**************************************************************************\
	* eGroupWare - headlines                                                   *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: preferences.php,v 1.28 2004/01/27 18:35:51 reinerj Exp $ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'              => 'headlines',
		'noheader'                => True,
		'nonavbar'                => True,
		'enable_nextmatchs_class' => True
	);
	include('../header.inc.php');

	if($_POST['cancel'] || $_POST['save'])
	{
		if($_POST['save'])
		{
			if(is_array($GLOBALS['phpgw_info']['user']['preferences']['headlines']))
			{
				foreach($GLOBALS['phpgw_info']['user']['preferences']['headlines'] as $n => $name)
				{
					if($n != 'headlines_layout')
					{
						$GLOBALS['phpgw']->preferences->delete('headlines',$n);
					}
				}
			}

			if(is_array($_POST['headlines']))
			{
				foreach($_POST['headlines'] as $n)
				{
					$GLOBALS['phpgw']->preferences->add('headlines',$n,'True');
				}
			}

//			$GLOBALS['phpgw']->preferences->add('headlines', 'mainscreen_showheadlines',True);
			$GLOBALS['phpgw']->preferences->save_repository(True);
		}
		$GLOBALS['phpgw']->redirect_link('/headlines/index.php');
	}

	$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Headline preferences');
	$GLOBALS['phpgw']->common->phpgw_header();
	echo parse_navbar();

	$GLOBALS['phpgw']->template->set_file(array('form' => 'preferences.tpl'));

	$GLOBALS['phpgw']->template->set_var('form_action',$GLOBALS['phpgw']->link('/headlines/preferences.php'));
	$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
	$GLOBALS['phpgw']->template->set_var('lang_header',lang('select headline news sites'));

	$GLOBALS['phpgw']->db->query('SELECT con,display FROM phpgw_headlines_sites ORDER BY display asc',__LINE__,__FILE__);
	while($GLOBALS['phpgw']->db->next_record())
	{
		$html_select .= '<option value="' . $GLOBALS['phpgw']->db->f('con') . '"';

		if($GLOBALS['phpgw_info']['user']['preferences']['headlines'][$GLOBALS['phpgw']->db->f('con')])
		{
			$html_select .= ' selected';
		}
		$html_select .= '>' . $GLOBALS['phpgw']->db->f('display') . '</option>'."\n";
	}
	$GLOBALS['phpgw']->template->set_var('select_options',$html_select);

	$GLOBALS['phpgw']->template->set_var('tr_color_1',$GLOBALS['phpgw']->nextmatchs->alternate_row_color());
	$GLOBALS['phpgw']->template->set_var('tr_color_2',$GLOBALS['phpgw']->nextmatchs->alternate_row_color());

	$GLOBALS['phpgw']->template->set_var('lang_save',lang('Save'));
	$GLOBALS['phpgw']->template->set_var('lang_cancel',lang('Cancel'));

	$GLOBALS['phpgw']->template->pparse('out','form');
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
