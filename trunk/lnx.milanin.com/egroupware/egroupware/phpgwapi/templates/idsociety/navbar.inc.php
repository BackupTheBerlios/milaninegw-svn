<?php
  /**************************************************************************\
  * phpGroupWare                                                             *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id: navbar.inc.php,v 1.44 2004/04/13 08:17:17 ralfbecker Exp $ */

	function parse_navbar($force = False)
	{
		$tpl = CreateObject('phpgwapi.Template',PHPGW_TEMPLATE_DIR);
		$tpl->set_unknowns('remove');

		$tpl->set_file(
			array(
				'navbar' => 'navbar.tpl'
			)
		);
		$tpl->set_block('navbar','preferences','preferences_icon');

		//$tpl->set_block('navbar','B_powered_top','V_powered_top');
		//$tpl->set_block('navbar','B_num_users','V_num_users');

		$var['img_root'] = PHPGW_IMAGES_DIR;
		$var['table_bg_color'] = $GLOBALS['phpgw_info']['theme']['navbar_bg'];

		$find_single = strrpos($GLOBALS['phpgw_info']['server']['webserver_url'],'/');
		$find_double = strpos(strrev($GLOBALS['phpgw_info']['server']['webserver_url'].' '),'//');
		if($find_double)
		{
			$find_double = strlen($GLOBALS['phpgw_info']['server']['webserver_url']) - $find_double - 1;
		}
		if($find_double)
		{
			if($find_single == $find_double + 1)
			{
				$strip_portion = $GLOBALS['phpgw_info']['server']['webserver_url'];
			}
			else
			{
				$strip_portion = substr($GLOBALS['phpgw_info']['server']['webserver_url'],0,$find_double + 1);
			}
		}
		else
		{
			$strip_portion = '';
		}

		#  echo '<pre>'; print_r($GLOBALS['phpgw_info']['navbar']); echo '</pre>';
		$applications = '';
		foreach($GLOBALS['phpgw_info']['navbar'] as $app => $app_data)
		{
			if ($app != 'home' && $app != 'preferences' && !ereg('about',$app) && $app != 'logout')
			{
				$title = '<img src="' . $app_data['icon'] . '" alt="' . $app_data['title'] . '" title="'
					. $app_data['title'] . '" border="0" name="' . str_replace('-','_',$app) . '">';
				$img_src_over = $app_data['icon_hover'];
				$img_src_out = $app_data['icon'];

				$applications .= '<tr><td><a href="' . $app_data['url'] . '"';
				if (isset($GLOBALS['phpgw_info']['flags']['navbar_target']))
				{
					$applications .= ' target="' . $GLOBALS['phpgw_info']['flags']['navbar_target'] . '"';
				}

				if($img_src_over != '')
				{
					$applications .= ' onMouseOver="' . str_replace('-','_',$app) . ".src='" . $img_src_over . '\'"';
				}
				if($img_src_out != '')
				{
					$applications .= ' onMouseOut="' . str_replace('-','_',$app) . ".src='" . $img_src_out . '\'"';
				}
				$applications .= $app_data['target'] . '>'.$title.'</a></td></tr>'."\r\n";
			}
			else
			{
				$img_src_over = $GLOBALS['phpgw']->common->image_on($app,Array('navbar','nonav'),'-over');
			}
			if($img_src_over != '')
			{
//				if($strip_portion)
//				{
//					$img_src_over = str_replace($strip_portion,'',$img_src_over);
//				}
					
				$pre_load[] = $img_src_over;
			}
		}

		$var['app_images'] = implode("',\r\n'",$pre_load);

		$var['applications'] = $applications;
     
		$var['home_link'] = $GLOBALS['phpgw_info']['navbar']['home']['url'];
		$var['preferences_link'] = $GLOBALS['phpgw_info']['navbar']['preferences']['url'];
		$var['logout_link'] = $GLOBALS['phpgw_info']['navbar']['logout']['url'];
		$var['help_link'] = $GLOBALS['phpgw_info']['navbar']['about']['url'];

		if ($GLOBALS['phpgw_info']['flags']['currentapp'] != 'home')
		{
			$var['welcome_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','welcome2');
			$var['welcome_img_hover'] = $GLOBALS['phpgw']->common->image_on('phpgwapi','welcome2','_over');
		}
		else
		{
			$var['welcome_img'] = $GLOBALS['phpgw']->common->image_on('phpgwapi','welcome2','_over');
			$var['welcome_img_hover'] = $GLOBALS['phpgw']->common->image('phpgwapi','welcome2');
		}

		if ($GLOBALS['phpgw_info']['flags']['currentapp'] != 'preferences')
		{
			$var['preferences_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','preferences2');
			$var['preferences_img_hover'] = $GLOBALS['phpgw']->common->image_on('phpgwapi','preferences2','_over');
		}
		else
		{
			$var['preferences_img'] = $GLOBALS['phpgw']->common->image_on('phpgwapi','preferences2','_over');
			$var['preferences_img_hover'] = $GLOBALS['phpgw']->common->image('phpgwapi','preferences2');
		}

		$var['logout_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','log_out2');
		$var['logout_img_hover'] = $GLOBALS['phpgw']->common->image_on('phpgwapi','log_out2','_over');

		if ($GLOBALS['phpgw_info']['flags']['currentapp'] != 'about')
		{
			$var['about_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','question_mark2');
			$var['about_img_hover'] = $GLOBALS['phpgw']->common->image_on('phpgwapi','question_mark2','_over');
		}
		else
		{
			$var['about_img'] = $GLOBALS['phpgw']->common->image_on('phpgwapi','question_mark2','_over');
			$var['about_img_hover'] = $GLOBALS['phpgw']->common->image('phpgwapi','question_mark2');
		}

		$var['content_spacer_middle_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','content_spacer_middle');
		$var['em_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','em');
		$var['top_spacer_middle_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','top_spacer_middle');
		$var['nav_bar_left_spacer_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','nav_bar_left_spacer');
		$var['nav_bar_left_top_bg_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','nav_bar_left_top_bg');

		// "powered_by_color" and "_size" are is also used by number of current users thing
		$var['powered_by_size'] = '2';
		$var['powered_by_color'] = '#ffffff';
		if ($GLOBALS['phpgw_info']['server']['showpoweredbyon'] == 'top')
		{
			$var['powered_by'] = lang('Powered by phpGroupWare version %1',$GLOBALS['phpgw_info']['server']['versions']['phpgwapi']);
		}
		else
		{
			$var['powered_by'] = '';
		}

		if (substr($GLOBALS['phpgw_info']['server']['login_logo_file'],0,4) == 'http')
		{
			$var['logo_file'] = $GLOBALS['phpgw_info']['server']['login_logo_file'];
		}
		else
		{
			$var['logo_file'] = $GLOBALS['phpgw']->common->image('phpgwapi',$GLOBALS['phpgw_info']['server']['login_logo_file']?$GLOBALS['phpgw_info']['server']['login_logo_file']:'logo');
		}
		$var['logo_url'] = $GLOBALS['phpgw_info']['server']['login_logo_url']?$GLOBALS['phpgw_info']['server']['login_logo_url']:'http://www.eGroupWare.org';
		if (substr($var['logo_url'],0,4) != 'http')
		{
			$var['logo_url'] = 'http://'.$var['logo_url'];
		}
		$var['logo_title'] = $GLOBALS['phpgw_info']['server']['login_logo_title']?$GLOBALS['phpgw_info']['server']['login_logo_title']:'www.eGroupWare.org';

		$tpl->set_var($var);

		if (isset($GLOBALS['phpgw_info']['navbar']['admin']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['show_currentusers'])
		{
			$var['current_users'] = '<a href="' . $GLOBALS['phpgw']->link('/index.php','menuaction=admin.uicurrentsessions.list_sessions')
				. '">&nbsp;' . lang('Current users') . ': ' . $GLOBALS['phpgw']->session->total() . '</a>';
			$tpl->set_var($var);
		}
		else
		{
			$var['current_users'] = '';
			$tpl->set_var($var);
		}

		$var['user_info_name'] = $GLOBALS['phpgw']->common->display_fullname();
		$now = time();
		$var['user_info_date'] =
			lang($GLOBALS['phpgw']->common->show_date($now,'l')) . ' '
			. $GLOBALS['phpgw']->common->show_date($now,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
		$var['user_info'] = $var['user_info_name'] .' - ' .$var['user_info_date'];
		$var['user_info_size'] = '2';
		$var['user_info_color'] = '#000000';

		// Maybe we should create a common function in the phpgw_accounts_shared.inc.php file
		// to get rid of duplicate code.
		if ($GLOBALS['phpgw_info']['user']['lastpasswd_change'] == 0)
		{
			$api_messages = lang('You are required to change your password during your first login')
				. '<br> Click this image on the navbar: <img src="'
				. $GLOBALS['phpgw']->common->image('preferences','navbar.gif').'">';
		}
		elseif ($GLOBALS['phpgw_info']['user']['lastpasswd_change'] < time() - (86400*30))
		{
			$api_messages = lang('it has been more then %1 days since you changed your password',30);
		}
 
		// This is gonna change
		if (isset($cd))
		{
			$var['messages'] = $api_messages . '<br>' . checkcode($cd);
		}

		$var['th_bg'] = $GLOBALS['phpgw_info']['theme']['th_bg'];
		if (isset($GLOBALS['phpgw_info']['flags']['app_header']))
		{
			$var['current_app_header'] = $GLOBALS['phpgw_info']['flags']['app_header'];
		}
		else
		{
			$tpl->set_block('navbar','app_header','app_header');
			$var['app_header'] = '<br>';
		}

		$tpl->set_var($var);
		// check if user is allowed to change his prefs
		if ($GLOBALS['phpgw_info']['user']['apps']['preferences'])
		{
			$tpl->parse('preferences_icon','preferences');
		}
		else
		{
			$tpl->set_var('preferences_icon','');
		}
		$tpl->pfp('out','navbar');
		// If the application has a header include, we now include it
		if (!@$GLOBALS['phpgw_info']['flags']['noappheader'] && @isset($_GET['menuaction']))
		{
			list($app,$class,$method) = explode('.',$_GET['menuaction']);
			if (is_array($GLOBALS[$class]->public_functions) && $GLOBALS[$class]->public_functions['header'])
			{
				$GLOBALS[$class]->header();
			}
		}
		$GLOBALS['phpgw']->hooks->process('after_navbar');
		return;
	}

	function parse_navbar_end()
	{
		$tpl = CreateObject('phpgwapi.Template',PHPGW_TEMPLATE_DIR);
		$tpl->set_unknowns('remove');

		$tpl->set_file(array('footer' => 'footer.tpl'));
		$tpl->set_block('footer','B_powered_bottom','V_powered_bottom');

		if ($GLOBALS['phpgw_info']['server']['showpoweredbyon'] == 'bottom')
		{
			$var = Array(
				'powered'  => lang('Powered by phpGroupWare version %1', $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']),
				'img_root' => PHPGW_IMAGES_DIR,
				'power_backcolor' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'power_textcolor' => $GLOBALS['phpgw_info']['theme']['navbar_text']
//				'version'  => $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']
			);
			$tpl->set_var($var);
 			$tpl->parse('V_powered_bottom','B_powered_bottom');
		}
		else
		{
			$tpl->set_var('V_powered_bottom','');
		}

		$GLOBALS['phpgw']->hooks->process('navbar_end');
		$tpl->pfp('out','footer');
	}
