<?php
  /**************************************************************************\
  * eGroupWare - Setup                                                       *
  * http://www.egroupware.org                                                *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: tables_update.inc.php,v 1.4 2004/07/02 22:31:23 ralfbecker Exp $ */

	$test[] = '0.9.13.001';
	function etemplate_upgrade0_9_13_001()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_etemplate','et_data',array(
			'type' => 'text',
			'nullable' => True
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_etemplate','et_size',array(
			'type' => 'char',
			'precision' => '128',
			'nullable' => True
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_etemplate','et_style',array(
			'type' => 'text',
			'nullable' => True
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_etemplate','et_modified',array(
			'type' => 'int',
			'precision' => '4',
			'default' => '0',
			'nullable' => False
		));

		$GLOBALS['setup_info']['etemplate']['currentver'] = '0.9.15.001';
		return $GLOBALS['setup_info']['etemplate']['currentver'];
	}


	$test[] = '0.9.15.001';
	function etemplate_upgrade0_9_15_001()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_etemplate','et_name',array(
			'type' => 'varchar',
			'precision' => '80',
			'nullable' => False
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_etemplate','et_template',array(
			'type' => 'varchar',
			'precision' => '20',
			'nullable' => False,
			'default' => ''
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_etemplate','et_lang',array(
			'type' => 'varchar',
			'precision' => '5',
			'nullable' => False,
			'default' => ''
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_etemplate','et_version',array(
			'type' => 'varchar',
			'precision' => '20',
			'nullable' => False,
			'default' => ''
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_etemplate','et_size',array(
			'type' => 'varchar',
			'precision' => '128',
			'nullable' => True
		));

		$GLOBALS['setup_info']['etemplate']['currentver'] = '0.9.15.002';
		return $GLOBALS['setup_info']['etemplate']['currentver'];
	}
	

	$test[] = '0.9.15.002';
	function etemplate_upgrade0_9_15_002()
	{
		$GLOBALS['setup_info']['etemplate']['currentver'] = '1.0.0';
		return $GLOBALS['setup_info']['etemplate']['currentver'];
	}
?>
