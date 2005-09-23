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

	/* $Id: head.inc.php,v 1.3 2004/06/13 20:09:27 ralfbecker Exp $ */

	$bodyheader = ' bgcolor="' . $GLOBALS['phpgw_info']['theme']['bg_color'] . '" alink="'
		. $GLOBALS['phpgw_info']['theme']['alink'] . '" link="' . $GLOBALS['phpgw_info']['theme']['link'] . '" vlink="'
		. $GLOBALS['phpgw_info']['theme']['vlink'] . '"';

	if (! $GLOBALS['phpgw_info']['server']['htmlcompliant'])
	{
		$bodyheader .= '';
	}

	$theme_css = $GLOBALS['phpgw_info']['server']['webserver_url'] . '/phpgwapi/templates/literati/css/'.$GLOBALS['phpgw_info']['user']['preferences']['common']['theme'].'.css';
	if (!file_exists($theme_css))
	{
		$theme_css = $GLOBALS['phpgw_info']['server']['webserver_url'] . '/phpgwapi/templates/literati/css/golden.css';
	}
	$tpl = CreateObject('phpgwapi.Template',PHPGW_TEMPLATE_DIR);
	$tpl->set_unknowns('remove');
	$tpl->set_file(array('head' => 'head.tpl'));

	if ($GLOBALS['phpgw_info']['flags']['app_header'])
	{
		$app = $GLOBALS['phpgw_info']['flags']['app_header'];
	}
	else
	{
		$app = $GLOBALS['phpgw_info']['flags']['currentapp'];
		$app = isset($GLOBALS['phpgw_info']['apps'][$app]) ? $GLOBALS['phpgw_info']['apps'][$app]['title'] : lang($app);
	}
	$var = Array (
		'img_icon'      => PHPGW_IMAGES_DIR . '/favicon.ico',
		'img_shortcut'  => PHPGW_IMAGES_DIR . '/favicon.ico',
		'charset'       => $GLOBALS['phpgw']->translation->charset(),
		'font_family'   => $GLOBALS['phpgw_info']['theme']['font'],
		'website_title' => $GLOBALS['phpgw_info']['server']['site_title'] . ($app ? " [$app]" : ''),
		'body_tags'     => $bodyheader .' '. $GLOBALS['phpgw']->common->get_body_attribs(),
		'theme_css'     => $theme_css,
		'css'           => $GLOBALS['phpgw']->common->get_css(),
		'java_script'   => $GLOBALS['phpgw']->common->get_java_script(),
	);
	$tpl->set_var($var);
	$tpl->pfp('out','head');
	unset($tpl);
?>
