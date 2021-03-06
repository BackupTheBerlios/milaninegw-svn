<?php
    /**************************************************************************\
    * eGroupWare - Knowledge Base                                              *
    * http://www.egroupware.org                                                *
    * -----------------------------------------------                          *
    *  This program is free software; you can redistribute it and/or modify it *
    *  under the terms of the GNU General Public License as published by the   *
    *  Free Software Foundation; either version 2 of the License, or (at your  *
    *  option) any later version.                                              *
    \**************************************************************************/

	/* $Id: hook_sidebox_menu.inc.php,v 1.4 2004/05/29 10:18:21 ralfbecker Exp $ */
{
	$menu_title = $GLOBALS['phpgw_info']['apps'][$appname]['title'] . ' '. lang('Menu');
	$file=Array(
		'Main View'					=> $GLOBALS['phpgw']->link('/index.php','menuaction=phpbrain.uikb.index'),
		'New Article'				=> $GLOBALS['phpgw']->link('/index.php','menuaction=phpbrain.uikb.edit_article'),
		'Add Question'				=> $GLOBALS['phpgw']->link('/index.php','menuaction=phpbrain.uikb.add_question'),
		'Maintain Articles'			=> $GLOBALS['phpgw']->link('/index.php','menuaction=phpbrain.uikb.maintain_articles'),
		'Maintain Questions'		=> $GLOBALS['phpgw']->link('/index.php','menuaction=phpbrain.uikb.maintain_questions')
	);
	display_sidebox($appname,$menu_title,$file);

	if($GLOBALS['phpgw_info']['user']['apps']['preferences'])
	{
		$menu_title = lang('Preferences');
		$file = Array(
			'Preferences'		=> $GLOBALS['phpgw']->link('/preferences/preferences.php','appname=phpbrain'),
			'Edit Categories'	=> $GLOBALS['phpgw']->link('/index.php','menuaction=preferences.uicategories.index&cats_app='.$appname.'&cats_level=True&global_cats=True')
		);
		display_sidebox($appname,$menu_title,$file);
	}	

	if ($GLOBALS['phpgw_info']['user']['apps']['admin'])
	{
        $menu_title = 'Administration';
        $file = Array(
			'Configuration'				=> $GLOBALS['phpgw']->link('/index.php','menuaction=admin.uiconfig.index&appname=phpbrain'),
			'Global Categories'			=> $GLOBALS['phpgw']->link('/index.php','menuaction=admin.uicategories.index&appname=phpbrain')
        );
		display_sidebox($appname,$menu_title,$file);
	}
}
?>
