<?php
  /**************************************************************************\
  * phpGroupWare - Calendar's Sidebox-Menu for idots-template                *
  * http://www.phpgroupware.org                                              *
  * Written by Pim Snel <pim@lingewoud.nl>                                   *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: hook_sidebox_menu.inc.php,v 1.11 2004/06/23 19:53:32 mipmip Exp $ */
{
	
	$menu_title = lang('JiNN Editors Menu');
	$file = Array(
			'Browse current object' => array(
				'link'=>$GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiuser.browse_objects'),
				'icon'=>'browse',
				'text'=>'Browse current object'
				),
			'Add new entry' => array(
				'text'=>'Add new entry',
				'icon'=>'new',
				'link'=>$GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiu_edit_record.display_form')
				)
		     );


	if($GLOBALS[uiuser]->bo->site[website_url])
	{
		$file['_NewLine_']='_NewLine_'; // give a newline
		$file['Preview Website']=array(
				'link'=>$GLOBALS[uiuser]->bo->site[website_url],
				'text'=>'Preview Website',
				'target'=>'_blank',
				'icon'=>'view'
				);

	}
	elseif($GLOBALS[local_bo]->site[website_url])
	{
		$file['_NewLine_']='_NewLine_'; // give a newline
		$file['Preview Website']=array(
				'link'=>$GLOBALS[local_bo]->site[website_url],
				'text'=>'Preview Website',
				'icon'=>'view',
				'target'=>'_blank'
				);
	}

	display_sidebox($appname,$menu_title,$file);

	$menu_title = lang('JiNN Preferences');
	$file = Array(
		'General Preferences' => array(
		'link'=>$GLOBALS['phpgw']->link('/preferences/preferences.php','appname=jinn'),
		'icon'=>'configure',
		'text'=>'General Preferences'
		),
		'Configure this Object List View'=> array(
		'link'=>$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.uiuser.config_objects'),
		'text'=>'Configure this Object List View',
		'icon'=>'configure_toolbars'
		)
	);

	display_sidebox($appname,$menu_title,$file);



	if ($GLOBALS['phpgw_info']['user']['apps']['admin'])
	{
		$menu_title = lang('Administration');
		$file = Array(
			'Global Configuration' => array(
			'link'=>$GLOBALS['phpgw']->link('/index.php','menuaction=admin.uiconfig.index&appname=' . $appname),
			'text'=>'Global Configuration',
			'icon'=>'configure'
			),
			'Add Site' => array(
			'link' => $GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiadmin.add_edit_site'),
			'text'=>'Add Site',
			'icon'=>'new'
			),
			'Browse through sites' => array(
			'link'=>$GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiadmin.browse_phpgw_jinn_sites'),
			'text'=>'Browse through sites',
			'icon'=>'browse'
			),
			'Import JiNN Site' => array(
			'link'=>$GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiadmin.import_phpgw_jinn_site'),
			'text'=>'Import JiNN Site',
			'icon'=>'fileopen'
			),
			'Access Rights' => array(
			'link'=>$GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiadmin.access_rights'),
			'text'=>'Access Rights',
			'icon'=>'groupevent'
			),
			'_NewLine_', // give a newline
			'Edit this Site' => array(
			'link'=>$GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiadmin.edit_this_jinn_site'),
			'text'=>'Edit this Site',
			'icon'=>'edit'
			),
			'Edit this Site Object' => array(
			'link'=>$GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiadmin.edit_this_jinn_site_object'),
			'text'=>'Edit this Site Object',
			'icon'=>'edit'
			)
		);
		display_sidebox($appname,$menu_title,$file);

		if($GLOBALS[local_bo]->common->prefs['experimental']=='yes')
		{
		   $menu_title = lang('Developer Links');
		   $file = Array(
			  'Site Media and Documents' => array
			  (
				 'link'=>$GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiumedia.index'),
				 'text'=>'Site Media and Documents',
				 'icon'=>'thumbnail'
			  ),
		   );
		   display_sidebox($appname,$menu_title,$file);
		}

		
	}

}
?>
