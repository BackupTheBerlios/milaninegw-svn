<?php
  /**************************************************************************\
  * eGroupWare - E-Mail                                                      *
  * http://www.egroupware.org                                                *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id: hook_home.inc.php,v 1.12 2004/05/04 16:31:05 reinerj Exp $ */
{
	$d1 = strtolower(substr(PHPGW_APP_INC,0,3));
	if($d1 == 'htt' || $d1 == 'ftp' )
	{
		echo "Failed attempt to break in via an old Security Hole!<br>\n";
		$GLOBALS['phpgw']->common->phpgw_exit();
	} unset($d1);

	$tmp_app_inc = $GLOBALS['phpgw']->common->get_inc_dir('comic');

	$GLOBALS['phpgw']->db->query("select * from phpgw_comic "
		."WHERE comic_owner='"
		.$GLOBALS['phpgw_info']["user"]["account_id"]."'");

	if ($GLOBALS['phpgw']->db->num_rows())
	{
		$GLOBALS['phpgw']->db->next_record();

		$data_id      = $GLOBALS['phpgw']->db->f('comic_frontpage');
		$scale        = $GLOBALS['phpgw']->db->f('comic_fpscale');
		$censor_level = $GLOBALS['phpgw']->db->f('comic_censorlvl');

		if ($data_id != -1)
		{
                        $title = lang('Comic');
			$portalbox = CreateObject('phpgwapi.listbox',
				Array(
					'title'	=> $title,
					'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width'	=> '100%',
					'outerborderwidth'	=> '0',
					'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi/templates/phpgw_website','bg_filler.gif')
				)
			);
			$app_id = $GLOBALS['phpgw']->applications->name2id('comic');
			$GLOBALS['portal_order'][] = $app_id;
			$var = Array(
				'up'	=> Array('url'	=> '/set_box.php', 'app'	=> $app_id),
				'down'	=> Array('url'	=> '/set_box.php', 'app'	=> $app_id),
				'close'	=> Array('url'	=> '/set_box.php', 'app'	=> $app_id),
				'question'	=> Array('url'	=> '/set_box.php', 'app'	=> $app_id),
				'edit'	=> Array('url'	=> '/set_box.php', 'app'	=> $app_id)
			);

			while(list($key,$value) = each($var))
			{
				$portalbox->set_controls($key,$value);
			}
			include($tmp_app_inc . '/functions.inc.php');
			echo "\r\n".'<!-- start Comic info -->'."\r\n"
				.$portalbox->draw(comic_display_frontpage($data_id, $scale, $censor_level))
				.'<!-- ends Comic info -->'."\r\n";
		}
	}
}
?>
