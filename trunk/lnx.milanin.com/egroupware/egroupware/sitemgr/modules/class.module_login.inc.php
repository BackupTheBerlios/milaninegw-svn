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

	/* $Id: class.module_login.inc.php,v 1.7 2004/05/19 06:56:38 ralfbecker Exp $ */

class module_login extends Module
{
	function module_login()
	{
		$this->arguments = array();
		if (file_exists(PHPGW_SERVER_ROOT . '/registration'))
		{
			$this->arguments['registration'] = array(
				'type' => 'checkbox',
				'label' => lang('Display link to autoregistration below login form?')
			);
			$config = CreateObject('phpgwapi.config','registration');
			$config = $config->read_repository();
			if ($config['enable_registration'] != 'True')
			{
				$this->arguments['registration']['label'] .= '<br><font color="red">'.
					lang('<b>Autoregistration is not enabled / configured in the registration app !!!</b><br>You need to do so, to get the autoregistration link working.').
					'</font>';
			}
		}
		$this->properties = array();
		$this->title = lang('Login');
		$this->description = lang('This module displays a login form');
	}

	function get_content(&$arguments,$properties)
	{
		$content = '<form name="login" action="'.phpgw_link('/login.php').'" method="post">';
		$content .= '<input type="hidden" name="passwd_type" value="text">';
		$content .= '<input type="hidden" name="phpgw_forward" value="/sitemgr/sitemgr-site/index.php">';
		if (!$GLOBALS['phpgw_info']['user']['domain']='defailt'){
                  $content .= '<input type="hidden" name="logindomain" value="'. $GLOBALS['phpgw_info']['user']['domain'] .'">';
                }
		$content .= '<center><font class="content">' . lang('Login Name') .'<br>';
		$content .= '<input type="text" name="login" size="8" value=""><br>';
		$content .= lang('Password') . '<br>';
		$content .= '<input name="passwd" size="8" type="password"><br>';
		$content .= '<input type="submit" value="' . lang('Login') .'" name="submitit">';
		$content .= '</font></center></form>';
		if (file_exists(PHPGW_SERVER_ROOT . '/registration') && $arguments['registration'])
		{
			$content .= '<center><font class="content">' . lang("Don't have an account?") .'  ';
			$content .= '<a href="'.phpgw_link('/registration/index.php').'">';
			$content .= lang('Register for one now.') . '</a></font></center>';
		}
		return $content;
	}
}
