<?php
	/**************************************************************************\
	* Anglemail - setup files for eGroupWare - DB Table Update                 *
	* http://www.anglemail.org                                                 *
	* eGroupWare                                                               *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: tables_update.inc.php,v 1.8.2.1 2004/10/23 15:06:28 ralfbecker Exp $ */

	/*
	$test[] = '0.9.13.002';
	function email_upgrade0_9_13_002()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_anglemail' => array(
				'fd' => array(
					'account_id' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false),
					'data_key' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False, 'default' => ''),
					'content' => array('type' => 'text', 'nullable' => False, 'default' => ''),
				),
				'pk' => array('account_id', 'data_key'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		//$GLOBALS['setup_info']['email']['currentver'] = '0.9.13.110805';
		$GLOBALS['setup_info']['email']['currentver'] = '0.9.13.003';
		return $GLOBALS['setup_info']['email']['currentver'];
	}
	*/

	$test[] = '0.9.13.002';
	function email_upgrade0_9_13_002()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_anglemail', array(
				'fd' => array(
					'account_id' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false),
					'data_key' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False, 'default' => ''),
					'content' => array('type' => 'text', 'nullable' => False, 'default' => ''),
				),
				'pk' => array('account_id', 'data_key'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		//$GLOBALS['setup_info']['email']['currentver'] = '0.9.13.110805';
		$GLOBALS['setup_info']['email']['currentver'] = '0.9.13.003';
		return $GLOBALS['setup_info']['email']['currentver'];
	}

	$test[] = '0.9.13.003';
	function email_upgrade0_9_13_003()
	{
		// hook changes only, no table changes
		$GLOBALS['setup_info']['email']['currentver'] = '0.9.13.004';
		return $GLOBALS['setup_info']['email']['currentver'];
	}

	$test[] = '0.9.13.004';
	function email_upgrade0_9_13_004()
	{
		if ($GLOBALS['phpgw_setup']->oProc->sType != 'pgsql')
		{
			$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_anglemail','content',array('type' => 'blob','nullable' => False,'default' => ''));
		}
		else	// postgres cant do that, as it cant cast from text to blob(bytea)
		{
			$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_anglemail');
			$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_anglemail',array(
				'fd' => array(
					'account_id' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false),
					'data_key' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False, 'default' => ''),
					'content' => array('type' => 'blob', 'nullable' => False, 'default' => ''),
				),
				'pk' => array('account_id', 'data_key'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			));
		}			
		$GLOBALS['setup_info']['email']['currentver'] = '0.9.13.005';
		return $GLOBALS['setup_info']['email']['currentver'];
	}

	$test[] = '0.9.13.005';
	function email_upgrade0_9_13_005()
	{
		$GLOBALS['setup_info']['email']['currentver'] = '1.0.0';
		return $GLOBALS['setup_info']['email']['currentver'];
	}
?>
